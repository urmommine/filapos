<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * Atribut yang harus disembunyikan untuk serialisasi.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Atribut yang harus di-cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Menentukan apakah user bisa mengakses panel Filament.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        // Admin dan Superadmin dapat mengakses panel admin
        // Kasir hanya dapat mengakses POS
        return $this->isAdmin() || $this->isSuperAdmin();
    }

    /**
     * Cek apakah user adalah admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Cek apakah user adalah superadmin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'superadmin';
    }

    /**
     * Cek apakah user adalah kasir.
     */
    public function isKasir(): bool
    {
        return $this->role === 'kasir';
    }

    /**
     * Relasi ke pesanan yang diproses oleh user (kasir) ini.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}

