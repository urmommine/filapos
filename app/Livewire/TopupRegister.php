<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.topup')]
#[Title('Daftar Akun Member - FilaPOS Games Hub')]
class TopupRegister extends Component
{
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $errorMessage = '';

    public function mount(): void
    {
        if (Auth::check()) {
            $this->redirect(route('topups'));
        }
    }

    public function register(): void
    {
        $this->errorMessage = '';

        $cleanPhone = preg_replace('/[^0-9]/', '', $this->phone);
        // Standarisasi jika diawali 62 atau 08
        if (str_starts_with($cleanPhone, '62')) {
            $cleanPhone = '0' . substr($cleanPhone, 2);
        }
        $this->phone = $cleanPhone;

        $this->validate([
            'name' => 'required|string|min:3|max:100',
            'email' => 'required|email|max:150|unique:users,email',
            'phone' => 'required|numeric|digits_between:9,15|unique:users,phone',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.min' => 'Nama minimal 3 karakter.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar. Silakan masuk.',
            'phone.required' => 'Nomor WhatsApp wajib diisi.',
            'phone.unique' => 'Nomor WhatsApp ini sudah terdaftar.',
            'phone.digits_between' => 'Nomor WhatsApp harus antara 9-15 digit angka.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $user = User::create([
            'name' => trim($this->name),
            'email' => strtolower(trim($this->email)),
            'phone' => $this->phone,
            'password' => Hash::make($this->password),
            'role' => 'user', // Role Member PPOB
            'balance' => 0,
        ]);

        Auth::login($user);
        request()->session()->regenerate();

        $this->redirect(route('topups'));
    }

    public function render()
    {
        return view('livewire.topup-register');
    }
}
