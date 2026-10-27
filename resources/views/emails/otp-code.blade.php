# Halo!

Anda menerima email ini karena kami menerima permintaan reset password untuk akun POS Anda.

Gunakan kode OTP berikut untuk melanjutkan proses reset password:

<div style="text-align: center; margin: 30px 0;">
    <h1 style="letter-spacing: 5px; font-size: 32px; color: #84cc16; background: #f8fafc; padding: 20px; border-radius: 12px; display: inline-block; border: 2px solid #ecfccb; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
        {{ $otp }}
    </h1>
</div>

<x-mail::button :url="config('app.url') . '/pos/login'" color="success">
    Buka Halaman Login POS
</x-mail::button>

Kode ini akan berlaku selama **10 menit**. Jangan berikan kode ini kepada siapapun demi keamanan akun Anda.

Jika Anda tidak merasa melakukan permintaan ini, abaikan email ini.

Terima kasih,<br>
{{ config('app.name') }}
