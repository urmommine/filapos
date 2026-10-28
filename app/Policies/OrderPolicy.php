<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;

class OrderPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isSuperAdmin() || $user->isTamu();
    }

    /**
     * Determine whether the user can view the model.
     * Tamu hanya boleh melihat transaksi mulai tanggal 20 Oktober 2026.
     * Transaksi sebelum tanggal 20 dilarang dilihat.
     */
    public function view(User $user, Order $order): bool
    {
        if ($user->isAdmin() || $user->isSuperAdmin()) {
            return true;
        }

        if ($user->isTamu()) {
            $cutoffDate = Carbon::create(2026, 10, 20)->startOfDay();
            return $order->created_at >= $cutoffDate;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false; // Order dibuat melalui sistem POS terminal
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Order $order): bool
    {
        return false; // Order bersifat immutable (tidak diubah manual)
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Order $order): bool
    {
        return $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Order $order): bool
    {
        return $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Order $order): bool
    {
        return $user->isSuperAdmin() && ! $user->isTamu();
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }

    public function forceDeleteAny(User $user): bool
    {
        return false;
    }
}
