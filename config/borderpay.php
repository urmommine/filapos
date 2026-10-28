<?php

return [
    /*
    |--------------------------------------------------------------------------
    | BorderPay API Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi integrasi Payment Gateway BorderPay (v1).
    | Kredensial dapat dikonfigurasi melalui .env atau ditimpa
    | melalui menu Pengaturan Toko (StoreSettings) di Filament Admin.
    |
    */

    'api_key' => env('BORDERPAY_API_KEY', ''),

    'base_url' => env('BORDERPAY_BASE_URL', 'https://borderpay.id/api/v1'),

    'mode' => env('BORDERPAY_MODE', 'live'), // live or sandbox

    'timeout' => env('BORDERPAY_TIMEOUT', 15), // HTTP timeout in seconds
];
