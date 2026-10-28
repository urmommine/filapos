<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    public function mount(): void
    {
        parent::mount();

        if (auth()->user()?->isTamu()) {
            Notification::make()
                ->warning()
                ->title('Akses Terbatas: Akun Tamu')
                ->body('Perhatian: Anda hanya dapat melihat riwayat transaksi hingga tanggal 20 Oktober 2026. Riwayat transaksi ke belakang sebelum tanggal 20 tidak dapat diakses!')
                ->persistent()
                ->send();
        }
    }
}
