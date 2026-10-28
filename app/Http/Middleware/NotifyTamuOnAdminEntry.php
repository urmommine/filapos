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

                $body = '<div class="space-y-2 text-xs leading-relaxed">'
                    . '<p class="text-gray-700 dark:text-gray-200">Anda login dalam <strong>Mode Tamu (Read-Only)</strong>. Berikut rincian hak akses Anda:</p>'
                    . '<div>'
                    . '<p class="font-semibold text-emerald-600 dark:text-emerald-400">✅ Fitur yang Dapat Diakses:</p>'
                    . '<ul class="list-disc list-inside space-y-0.5 text-gray-600 dark:text-gray-300">'
                    . '<li>Ringkasan statistik penjualan pada Dashboard</li>'
                    . '<li>Katalog Produk & Kategori (hanya melihat data)</li>'
                    . '<li>Riwayat Transaksi (hanya transaksi mulai 20 Okt 2026)</li>'
                    . '</ul>'
                    . '</div>'
                    . '<div>'
                    . '<p class="font-semibold text-amber-600 dark:text-amber-400">🔒 Dibatasi / Disembunyikan (Privasi):</p>'
                    . '<ul class="list-disc list-inside space-y-0.5 text-gray-600 dark:text-gray-300">'
                    . '<li>Transaksi sebelum 20 Okt 2026 disembunyikan untuk privasi</li>'
                    . '<li>Menu Pengguna & Pelanggan di-hide total</li>'
                    . '<li>Aksi tambah, ubah, dan hapus data dinonaktifkan</li>'
                    . '</ul>'
                    . '</div>'
                    . '</div>';

                Notification::make()
                    ->title("Selamat datang, {$user->name}!")
                    ->icon('heroicon-o-shield-check')
                    ->iconColor('info')
                    ->body($body)
                    ->info()
                    ->duration(12000)
                    ->send();
            }
        }

        return $next($request);
    }
}
