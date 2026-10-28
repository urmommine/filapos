<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Background Payment Checker Tanpa Webhook (Berjalan Tiap 10 Detik)
Schedule::command('ppob:check-pending')->everyTenSeconds()->withoutOverlapping();
