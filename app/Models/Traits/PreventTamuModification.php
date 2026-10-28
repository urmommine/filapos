<?php

namespace App\Models\Traits;

use Illuminate\Auth\Access\AuthorizationException;

trait PreventTamuModification
{
    /**
     * Otomatis dijalankan saat model Eloquent di-boot.
     * Mencegah akun Tamu melakukan create, update, atau delete di level ORM.
     */
    public static function bootPreventTamuModification(): void
    {
        static::creating(function ($model) {
            if (auth()->check() && method_exists(auth()->user(), 'isTamu') && auth()->user()->isTamu()) {
                throw new AuthorizationException('Akses Ditolak: Akun Tamu (Read-Only) tidak diizinkan menambah data.');
            }
        });

        static::updating(function ($model) {
            if (auth()->check() && method_exists(auth()->user(), 'isTamu') && auth()->user()->isTamu()) {
                throw new AuthorizationException('Akses Ditolak: Akun Tamu (Read-Only) tidak diizinkan mengubah data.');
            }
        });

        static::deleting(function ($model) {
            if (auth()->check() && method_exists(auth()->user(), 'isTamu') && auth()->user()->isTamu()) {
                throw new AuthorizationException('Akses Ditolak: Akun Tamu (Read-Only) tidak diizinkan menghapus data.');
            }
        });
    }
}
