<?php

namespace App\Livewire;

use App\Services\MeloStoreService;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.topup')]
class CheckUsername extends Component
{
    public array $gamesList = [];
    public string $selectedGame = 'mobile-legends';
    public string $searchGame = '';
    public string $target = '';
    public string $zone = '';

    public bool $isChecking = false;
    public bool $hasChecked = false;
    public bool $isValid = false;
    public string $nickname = '';
    public string $message = '';
    public ?array $verifiedData = null;

    public function mount(): void
    {
        $allCategories = MeloStoreService::getGameCategories();
        // Cek username hanya berlaku untuk brand/layanan dalam kategori Game
        $this->gamesList = array_values(array_filter($allCategories, function ($item) {
            return strtolower($item['category'] ?? '') === 'game';
        }));

        $validSlugs = array_column($this->gamesList, 'slug');

        $queryGame = request()->query('game');
        $queryTarget = request()->query('target');
        $queryZone = request()->query('zone');

        if (!empty($queryGame) && in_array($queryGame, $validSlugs)) {
            $this->selectedGame = $queryGame;
        } elseif (!in_array($this->selectedGame, $validSlugs) && !empty($this->gamesList)) {
            $this->selectedGame = $this->gamesList[0]['slug'] ?? 'mobile-legends';
        }

        if (!empty($queryTarget)) {
            $this->target = trim($queryTarget);
        }
        if (!empty($queryZone)) {
            $this->zone = trim($queryZone);
        }

        if (!empty($this->target)) {
            $this->checkAccount();
        }
    }

    public function selectGame(string $slug): void
    {
        $validSlugs = array_column($this->gamesList, 'slug');
        if (!in_array($slug, $validSlugs)) {
            return;
        }

        $this->selectedGame = $slug;
        $this->hasChecked = false;
        $this->isValid = false;
        $this->nickname = '';
        $this->message = '';
        $this->verifiedData = null;
    }

    public function checkAccount(): void
    {
        $this->target = trim($this->target);
        $this->zone = trim($this->zone);

        if (empty($this->target)) {
            $this->hasChecked = true;
            $this->isValid = false;
            $this->nickname = '';
            $this->message = 'Silakan masukkan User ID / Player ID akun.';
            return;
        }

        $this->isChecking = true;
        $this->hasChecked = true;
        $this->message = 'Sedang memeriksa ke server resmi game...';

        try {
            $result = MeloStoreService::checkNickname($this->selectedGame, $this->target, $this->zone);

            if ($result['success']) {
                $this->isValid = true;
                $this->nickname = $result['nickname'];
                $this->message = 'Akun terverifikasi dan valid!';
                $this->verifiedData = [
                    'nickname' => $result['nickname'],
                    'game' => $this->getSelectedGameName(),
                    'slug' => $this->selectedGame,
                    'target' => $this->target,
                    'zone' => $this->zone,
                    'checked_at' => now()->translatedFormat('d M Y, H:i:s'),
                ];
            } else {
                $this->isValid = false;
                $this->nickname = '';
                $this->message = $result['message'] ?? 'ID atau Zone ID tidak ditemukan di server game.';
                $this->verifiedData = null;
            }
        } catch (\Throwable $e) {
            Log::error('CheckUsername checkAccount error: ' . $e->getMessage());
            $this->isValid = false;
            $this->nickname = '';
            $this->message = 'Gagal menghubungi server verifikasi Melostore.';
            $this->verifiedData = null;
        } finally {
            $this->isChecking = false;
        }
    }

    public function getSelectedGameName(): string
    {
        foreach ($this->gamesList as $g) {
            if ($g['slug'] === $this->selectedGame) {
                return $g['name'];
            }
        }
        return ucwords(str_replace('-', ' ', $this->selectedGame));
    }

    public function render()
    {
        $filteredGames = $this->gamesList;
        if (!empty(trim($this->searchGame))) {
            $q = strtolower(trim($this->searchGame));
            $filteredGames = array_filter($filteredGames, function ($g) use ($q) {
                return str_contains(strtolower($g['name']), $q);
            });
        }

        return view('livewire.check-username', [
            'displayGames' => $filteredGames,
            'selectedGameName' => $this->getSelectedGameName(),
        ]);
    }
}
