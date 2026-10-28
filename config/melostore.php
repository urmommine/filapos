<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Melostore H2H Public API Configuration
    |--------------------------------------------------------------------------
    */
    'base_url' => env('MELOSTORE_BASE_URL', 'https://api.melostore.id'),
    'api_key' => env('MELOSTORE_API_KEY', ''),
    'secret_key' => env('MELOSTORE_SECRET_KEY', ''),
    'sandbox' => env('MELOSTORE_SANDBOX', true),

    /*
    |--------------------------------------------------------------------------
    | Pricing & Margin Strategy
    |--------------------------------------------------------------------------
    | min_price: Batas harga terendah untuk seluruh item top up (default Rp 5.000)
    | margin_percent: Margin keuntungan dalam persen jika modal di atas min_price
    */
    'min_price' => (float) env('MELOSTORE_PRICE_MIN', 5000),
    'margin_percent' => (float) env('MELOSTORE_MARGIN_PERCENT', 5),
    'products_cache_minutes' => (int) env('MELOSTORE_PRODUCTS_CACHE_MINUTES', 5),
    'categories_cache_minutes' => (int) env('MELOSTORE_CATEGORIES_CACHE_MINUTES', 30),
];
