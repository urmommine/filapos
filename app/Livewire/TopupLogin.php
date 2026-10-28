<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.topup')]
#[Title('Masuk Akun Member - FilaPOS Games Hub')]
class TopupLogin extends Component
{
    public string $identifier = '';
    public string $password = '';
    public bool $remember = true;
    public string $errorMessage = '';

    public function mount(): void
    {
        if (Auth::check()) {
            $this->redirect(route('topups'));
        }
    }

    public function login(): void
    {
        $this->errorMessage = '';
        $this->validate([
            'identifier' => 'required|string',
            'password' => 'required|string',
        ], [
            'identifier.required' => 'Email atau Nomor WhatsApp wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $id = trim($this->identifier);

        // Cari user berdasarkan email ATAU nomor telepon
        $user = User::where('email', $id)
            ->orWhere('phone', $id)
            ->first();

        if (!$user || !Hash::check($this->password, $user->password)) {
            $this->errorMessage = 'Email/No. WhatsApp atau kata sandi yang Anda masukkan salah.';
            return;
        }

        Auth::login($user, $this->remember);
        request()->session()->regenerate();

        $this->redirect(session()->pull('url.intended', route('topups')));
    }

    public function render()
    {
        return view('livewire.topup-login');
    }
}
