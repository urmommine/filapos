<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;

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

            if (Auth::user()->isAdmin() || Auth::user()->isSuperAdmin()) {
                return redirect()->intended('/admin');
            }

            return redirect()->intended('/pos');
        }

        $this->addError('email', 'Kredensial yang diberikan tidak cocok dengan catatan kami.');
    }

    public function render()
    {
        return view('livewire.auth.pos-login');
    }
}
