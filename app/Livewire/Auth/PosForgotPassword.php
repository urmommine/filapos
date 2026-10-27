<?php

namespace App\Livewire\Auth;

use Livewire\Component;

use App\Models\User;
use App\Mail\SendOtpMail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Layout;
use Illuminate\Validation\ValidationException;

#[Layout('layouts.pos-auth')]
class PosForgotPassword extends Component
{
    public string $email = '';
    public string $otp = '';
    public string $password = '';
    public string $password_confirmation = '';
    
    public int $step = 1; // 1: Email, 2: OTP, 3: Password

    public function sendOtp()
    {
        $this->validate(['email' => 'required|email|exists:users,email']);

        // Generate 6 digit OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store OTP in cache for 10 minutes
        Cache::put('otp_' . $this->email, $otp, now()->addMinutes(10));

        // Send Email
        try {
            Mail::to($this->email)->send(new SendOtpMail($otp));
            $this->step = 2;
            $this->dispatch('toastMagic', status: 'success', title: 'Sukses', message: 'Kode OTP telah dikirim ke email Anda.');
        } catch (\Exception $e) {
            $this->dispatch('toastMagic', status: 'error', title: 'Error', message: 'Gagal mengirim email. Periksa konfigurasi mail Anda.');
        }
    }

    public function verifyOtp()
    {
        $this->validate(['otp' => 'required|numeric|digits:6']);

        $cachedOtp = Cache::get('otp_' . $this->email);

        if ($cachedOtp && $cachedOtp === $this->otp) {
            $this->step = 3;
            $this->dispatch('toastMagic', status: 'success', title: 'Sukses', message: 'OTP Berhasil diverifikasi.');
        } else {
            throw ValidationException::withMessages([
                'otp' => 'Kode OTP salah atau sudah kadaluwarsa.',
            ]);
        }
    }

    public function resetPassword()
    {
        $this->validate([
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::where('email', $this->email)->first();
        $user->password = Hash::make($this->password);
        $user->save();

        // Clear OTP from cache
        Cache::forget('otp_' . $this->email);

        $this->dispatch('toastMagic', status: 'success', title: 'Sukses', message: 'Password berhasil diperbarui! Silakan login.');
        return $this->redirect(route('login'), navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.pos-forgot-password');
    }
}
