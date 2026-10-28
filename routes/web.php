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
        if (Auth::user()->role === 'user') {
            return redirect('/topups');
        }
        return redirect('/pos');
    }
    return view('landing');
})->name('landing');

// Public PPOB Game Top-up Portal
Route::get('/topups', \App\Livewire\TopupGame::class)->name('topups');
Route::get('/topups/check-username', \App\Livewire\CheckUsername::class)->name('topups.check-username');

// PPOB Member Authentication & Account
Route::get('/topups/login', \App\Livewire\TopupLogin::class)->name('topups.login');
Route::get('/topups/register', \App\Livewire\TopupRegister::class)->name('topups.register');
Route::get('/topups/account', \App\Livewire\TopupAccount::class)->name('topups.account');
Route::post('/topups/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('topups');
})->name('topups.logout');

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

