<?php

namespace App\Http\Middleware;

use Closure;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NotifyTamuOnAdminEntry
{
    /**
     * Tampilkan toast notifikasi saat user dengan role tamu membuka admin panel.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Hanya jalankan untuk user dengan role 'tamu' pada request GET halaman utama (bukan background Livewire update)
        if ($user && $user->isTamu() && $request->isMethod('GET') && ! $request->hasHeader('X-Livewire')) {
            $lastShown = session('tamu_welcome_notified_at');

            // Tampilkan jika belum pernah di sesi ini, atau jika reload/buka ulang setelah jeda 60 detik
            if (! $lastShown || now()->diffInSeconds($lastShown) > 60) {
                session()->put('tamu_welcome_notified_at', now());

                $body = 'Anda login dengan akun <strong>Tamu (Read-Only)</strong>. Sebagian menu dan data tidak ditampilkan demi menjaga privasi dan keamanan pengguna.';

                Notification::make()
                    ->title("Selamat datang, {$user->name}!")
                    ->icon('heroicon-o-shield-check')
                    ->iconColor('info')
                    ->body($body)
                    ->info()
                    ->duration(7000)
                    ->send();
            }
        }

        return $next($request);
    }
}
