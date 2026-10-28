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
        'phone',
        'password',
        'role',
        'balance',
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
            'balance' => 'decimal:2',
        ];
    }

    /**
     * Menentukan apakah user bisa mengakses panel Filament.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        // Admin, Superadmin, dan Tamu dapat mengakses panel admin
        // Kasir hanya dapat mengakses POS
        // User (member PPOB) hanya mengakses portal frontend
        return $this->isAdmin() || $this->isSuperAdmin() || $this->isTamu();
    }

    /**
     * Cek apakah user adalah tamu (guest view-only).
     */
    public function isTamu(): bool
    {
        return $this->role === 'tamu';
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
     * Cek apakah user adalah member PPOB biasa.
     */
    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    /**
     * Format saldo ke format Rupiah.
     */
    public function getFormattedBalanceAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->balance, 0, ',', '.');
    }

    /**
     * Cek apakah saldo mencukupi untuk nominal tertentu.
     */
    public function hasSufficientBalance(float $amount): bool
    {
        return (float) $this->balance >= $amount;
    }

    /**
     * Relasi ke riwayat mutasi dompet/saldo.
     */
    public function walletTransactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class);
    }

    /**
     * Relasi ke pesanan yang diproses oleh user (kasir) ini.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}

