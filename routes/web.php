<?php

use App\Livewire\PosTerminal;
use App\Http\Controllers\ReceiptController;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    if (Auth::check()) {
        if (Auth::user()->isAdmin() || Auth::user()->isSuperAdmin()) {
            return redirect('/admin');
        }
        return redirect('/pos');
    }
    return redirect()->route('login');
});

// POS Auth Routes
Route::get('/pos/login', \App\Livewire\Auth\PosLogin::class)->name('login')->middleware('guest');
Route::get('/pos/forgot-password', \App\Livewire\Auth\PosForgotPassword::class)->name('password.request')->middleware('guest');

// POS Routes (requires authentication)
Route::middleware(['auth'])->group(function () {
    Route::get('/pos', PosTerminal::class)->name('pos');
    Route::get('/pos/receipt/{order}', App\Livewire\Receipt::class)->name('pos.receipt');
    // Route::get('/pos/receipt/{order}/print', [ReceiptController::class, 'print'])->name('pos.receipt.print');
});

// Logout pos
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('login');
})->name('logout');

// Payment Gateway Verification
Route::get('/.well-known/solosmartpay.txt', function () {
    return response("ssp-verify-652abe13a6ed0974e1b4b388", 200)
        ->header('Content-Type', 'text/plain');
});

