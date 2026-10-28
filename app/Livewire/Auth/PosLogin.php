<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Validation\ValidationException;

#[Layout('layouts.pos-auth')]
class PosLogin extends Component
{
    public string $email = '';

    public string $password = '';

    public bool $remember = false;

    public function login()
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            session()->regenerate();

            if (Auth::user()->role === 'user') {
                Auth::logout();
                session()->invalidate();
                session()->regenerateToken();

                throw ValidationException::withMessages([
                    'email' => 'Akses Ditolak: Akun Member PPOB tidak memiliki izin mengakses Terminal Kasir.',
                ]);
            }

            if (Auth::user()->isAdmin() || Auth::user()->isSuperAdmin()) {
                return redirect()->intended('/admin');
            }

            $this->redirect(route('pos'), navigate: true);
        } else {
            throw ValidationException::withMessages([
                'email' => 'Email atau Password Salah! Silakan coba lagi.',
            ]);
        }
    }
    
    public function render()
    {
        return view('livewire.auth.pos-login');
    }
}
