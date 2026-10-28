<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (request()->header('X-Forwarded-Proto') === 'https') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // Kunci mutlak keamanan akun Tamu: hanya izinkan view / viewAny.
        // Blokir mutlak create, update, delete, deleteAny, forceDelete, restore, dll.
        \Illuminate\Support\Facades\Gate::before(function ($user, $ability) {
            if ($user && method_exists($user, 'isTamu') && $user->isTamu()) {
                if (in_array($ability, ['view', 'viewAny'])) {
                    return null;
                }

                return false;
            }
        });
    }
}
