<?php

namespace App\Services;

use App\Models\StoreSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MeloStoreService
{
    public const SETTING_API_KEY = 'melostore_api_key';
    public const SETTING_SECRET_KEY = 'melostore_secret_key';
    public const SETTING_MODE = 'melostore_mode'; // 'sandbox' atau 'live'
    public const SETTING_MIN_PRICE = 'melostore_min_price';
    public const SETTING_MARGIN = 'melostore_margin_percent';

    /**
     * Dapatkan Base URL Melostore H2H API.
     */
    public static function getBaseUrl(): string
    {
        return rtrim(config('melostore.base_url', 'https://api.melostore.id'), '/');
    }

    /**
     * Dapatkan API Key Melostore.
     */
    public static function getApiKey(): string
    {
        $fromDb = StoreSetting::get(self::SETTING_API_KEY);
        return !empty($fromDb) ? $fromDb : (string) config('melostore.api_key', '');
    }

    /**
     * Dapatkan Secret Key Melostore.
     */
    public static function getSecretKey(): string
    {
        $fromDb = StoreSetting::get(self::SETTING_SECRET_KEY);
        return !empty($fromDb) ? $fromDb : (string) config('melostore.secret_key', '');
    }

    /**
     * Apakah dalam mode sandbox.
     */
    public static function isSandbox(): bool
    {
        $mode = StoreSetting::get(self::SETTING_MODE);
        if ($mode !== null) {
            return $mode === 'sandbox';
        }
        return (bool) config('melostore.sandbox', true);
    }

    /**
     * Dapatkan header autentikasi HTTP.
     */
    protected static function getHeaders(): array
    {
        return [
            'X-API-Key' => self::getApiKey(),
            'X-Secret-Key' => self::getSecretKey(),
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36 FilaPOS/1.0',
        ];
    }

    /**
     * Hitung harga jual dengan batas minimal Rp 5.000 dan margin.
     */
    public static function calculateSellingPrice(float $modalPrice): float
    {
        $minPrice = (float) (StoreSetting::get(self::SETTING_MIN_PRICE) ?? config('melostore.min_price', 5000));
        $marginPercent = (float) (StoreSetting::get(self::SETTING_MARGIN) ?? config('melostore.margin_percent', 5));

        // Margin persentase dan dibulatkan ke ratusan terdekat
        $withMargin = ceil(($modalPrice * (1 + ($marginPercent / 100))) / 100) * 100;

        // Terapkan batas harga minimal (sesuai instruksi min Rp 5.000)
        return max($minPrice, $withMargin);
    }

    /**
     * Ambil daftar brand/kategori game dari Melostore H2H.
     */
    public static function getGameCategories(bool $forceRefresh = false): array
    {
        $cacheKey = 'melostore_game_categories_v5';
        $ttl = now()->addMinutes((int) config('melostore.categories_cache_minutes', 30));

        if ($forceRefresh) {
            Cache::forget($cacheKey);
        } elseif (Cache::has($cacheKey)) {
            $cached = Cache::get($cacheKey);
            if (is_array($cached) && count($cached) > 20) {
                return $cached;
            }
        }

        $apiKey = self::getApiKey();
        $secretKey = self::getSecretKey();

        if (empty($apiKey) || empty($secretKey)) {
            return self::getFallbackGames();
        }

        try {
            $response = Http::withHeaders(self::getHeaders())
                ->timeout(20)
                ->get(self::getBaseUrl() . '/api/v1/h2h/pricelists/categories');

            if ($response->successful() && isset($response->json()['data'])) {
                $allBrands = $response->json()['data'];
                $games = [];

                $popularSlugs = [
                    'mobile-legends' => 1,
                    'free-fire' => 2,
                    'free-fire-max' => 3,
                    'genshin-impact' => 4,
                    'pubg-mobile' => 5,
                    'valorant' => 6,
                    'honor-of-kings' => 7,
                    'roblox' => 8,
                    'call-of-duty-mobile' => 9,
                    'steam-wallet-code-idr' => 10,
                    'google-play-indonesia' => 11,
                    'dana' => 12,
                ];

                foreach ($allBrands as $b) {
                    $rawCat = $b['category']['name'] ?? ($b['category'] ?? '');
                    $category = 'Game';
                    if (stripos($rawCat, 'airtime') !== false || stripos($rawCat, 'pulsa') !== false || stripos($rawCat, 'data') !== false) {
                        $category = 'Pulsa & Data';
                    } elseif (stripos($rawCat, 'gift') !== false) {
                        $category = 'Gift Card';
                    } elseif (stripos($rawCat, 'ewallet') !== false || stripos($rawCat, 'wallet') !== false) {
                        $category = 'E-Wallet';
                    } elseif (stripos($rawCat, 'pln') !== false || stripos($rawCat, 'listrik') !== false) {
                        $category = 'PLN';
                    } elseif (stripos($rawCat, 'social') !== false || stripos($rawCat, 'chat') !== false) {
                        $category = 'Social';
                    } elseif (stripos($rawCat, 'aplikasi') !== false || stripos($rawCat, 'langganan') !== false) {
                        $category = 'Aplikasi';
                    }

                    $slug = $b['slug'] ?? '';
                    $order = $popularSlugs[$slug] ?? 999;

                    // Simpan hanya atribut esensial agar ukuran JSON HTML ringan
                    $games[] = [
                        'brand_id' => $b['brand_id'] ?? $b['id'] ?? null,
                        'name' => $b['name'],
                        'slug' => $slug,
                        'category' => $category,
                        'thumbnail' => self::getGameIconUrl($slug),
                        '_order' => $order,
                    ];
                }

                // Sort popular games first, then alphabetically
                usort($games, function ($a, $b) {
                    if ($a['_order'] === $b['_order']) {
                        return strcasecmp($a['name'], $b['name']);
                    }
                    return $a['_order'] <=> $b['_order'];
                });

                // Hapus _order setelah diurutkan agar payload bersih
                $games = array_map(function ($g) {
                    unset($g['_order']);
                    return $g;
                }, $games);

                if (count($games) > 20) {
                    Cache::put($cacheKey, $games, $ttl);
                    return $games;
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Melostore getGameCategories failed: ' . $e->getMessage());
        }

        return self::getFallbackGames();
    }

    /**
     * Ambil daftar produk dan nominal per game brand.
     */
    public static function getGameProducts(string $brandSlug, bool $forceRefresh = false): array
    {
        $cacheKey = "melostore_products_v5_{$brandSlug}";
        $ttl = now()->addMinutes((int) config('melostore.products_cache_minutes', 5));

        if ($forceRefresh) {
            Cache::forget($cacheKey);
        } elseif (Cache::has($cacheKey)) {
            $cached = Cache::get($cacheKey);
            if (is_array($cached) && !empty($cached['products'])) {
                return $cached;
            }
        }

        $apiKey = self::getApiKey();
        $secretKey = self::getSecretKey();

        if (empty($apiKey) || empty($secretKey)) {
            return self::getFallbackProducts($brandSlug);
        }

        try {
            $response = Http::withHeaders(self::getHeaders())
                ->timeout(20)
                ->get(self::getBaseUrl() . "/api/v1/h2h/pricelists/{$brandSlug}");

            if ($response->successful() && isset($response->json()['data'])) {
                $json = $response->json();
                $rawProducts = $json['data'] ?? [];
                $meta = $json['meta'] ?? [];

                // Fields form inquiry (contoh: ID dan Zone)
                $inquiryFields = [];
                if (!empty($meta['inquiry_forms'])) {
                    $firstForm = reset($meta['inquiry_forms']);
                    $inquiryFields = $firstForm['fields'] ?? [];
                }

                $formattedProducts = [];
                foreach ($rawProducts as $p) {
                    if (($p['status'] ?? 'active') !== 'active') {
                        continue;
                    }

                    $modalPrice = (float) ($p['price'] ?? 0);
                    $sellingPrice = self::calculateSellingPrice($modalPrice);

                    $formattedProducts[] = [
                        'sku_code' => $p['sku_code'],
                        'name' => $p['name'],
                        'type' => $p['type_name'] ?? 'Regular',
                        'modal_price' => $modalPrice,
                        'price' => $sellingPrice,
                        'formatted_price' => 'Rp ' . number_format($sellingPrice, 0, ',', '.'),
                        'server_code' => $p['server_code'] ?? 'global',
                        'server_name' => $p['server_name'] ?? 'Global',
                    ];
                }

                // Urutkan berdasarkan harga jual terendah ke tertinggi
                usort($formattedProducts, fn($a, $b) => $a['price'] <=> $b['price']);

                $result = [
                    'brand' => [
                        'name' => $json['category']['name'] ?? ucwords(str_replace('-', ' ', $brandSlug)),
                        'slug' => $brandSlug,
                        'thumbnail' => self::getGameIconUrl($brandSlug),
                    ],
                    'inquiry_fields' => !empty($inquiryFields) ? $inquiryFields : self::getDefaultInquiryFields($brandSlug),
                    'products' => $formattedProducts,
                ];

                if (!empty($formattedProducts)) {
                    Cache::put($cacheKey, $result, $ttl);
                }

                return $result;
            }
        } catch (\Throwable $e) {
            Log::warning("Melostore getGameProducts({$brandSlug}) failed: " . $e->getMessage());
        }

        return self::getFallbackProducts($brandSlug);
    }

    /**
     * Validasi dan Cek Nickname Akun Game.
     */
    public static function checkNickname(string $gameCode, string $target, ?string $zone = null): array
    {
        $apiKey = self::getApiKey();
        $secretKey = self::getSecretKey();

        // Mode simulasi/fallback hanya jika kredensial belum ada
        if (empty($apiKey) || empty($secretKey)) {
            if (strlen(trim($target)) < 4) {
                return [
                    'success' => false,
                    'message' => 'User ID game minimal 4 karakter.',
                ];
            }

            // Simulasi nama pemain game yang realistis
            $demoNicknames = ['SkyWalker_ID', 'GarudaGamer', 'DragonSlayer', 'ProPlayer_99', 'ShadowHunter', 'RatuMabar', 'CyberKnight'];
            $sampleNick = $demoNicknames[abs(crc32($target)) % count($demoNicknames)];

            return [
                'success' => true,
                'nickname' => $sampleNick,
                'customer_target' => $target,
                'customer_target_zone' => $zone,
                'message' => 'Akun terverifikasi (Sandbox Mode)',
            ];
        }

        try {
            $payload = [
                'game_code' => $gameCode,
                'customer_target' => trim($target),
            ];
            if (!empty($zone)) {
                $payload['customer_target_zone'] = trim($zone);
            }

            $response = Http::withHeaders(self::getHeaders())
                ->timeout(10)
                ->post(self::getBaseUrl() . '/api/v1/h2h/check-nickname', $payload);

            if ($response->successful()) {
                $data = $response->json();
                $nickname = $data['data']['nickname'] ?? $data['data']['username'] ?? $data['data']['name'] ?? null;
                if (!empty($nickname)) {
                    return [
                        'success' => true,
                        'nickname' => $nickname,
                        'customer_target' => $target,
                        'customer_target_zone' => $zone,
                        'message' => 'Akun berhasil terverifikasi.',
                    ];
                }
            }

            $errMsg = $response->json()['message'] ?? 'User ID atau Zone tidak ditemukan.';
            return [
                'success' => false,
                'message' => $errMsg,
            ];
        } catch (\Throwable $e) {
            Log::error('Melostore checkNickname error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Gagal menghubungkan ke server verifikasi game.',
            ];
        }
    }

    /**
     * Buat transaksi top up game ke Melostore H2H.
     */
    public static function createTransaction(
        string $skuCode,
        string $target,
        ?string $zone,
        string $buyerTrxId,
        array $additionalData = []
    ): array {
        $apiKey = self::getApiKey();
        $secretKey = self::getSecretKey();
        $sandbox = self::isSandbox();

        // Simulasi sukses jika sandbox / belum ada API Key
        if (empty($apiKey) || empty($secretKey)) {
            return [
                'success' => true,
                'trx_id' => 'MS-' . strtoupper(bin2hex(random_bytes(6))),
                'buyer_trx_id' => $buyerTrxId,
                'status' => 'success',
                'sn' => 'SN' . rand(10000000000, 99999999999),
                'message' => 'Top up game berhasil diproses (Simulasi Demo Sandbox)',
            ];
        }

        try {
            $payload = [
                'sku_code' => $skuCode,
                'customer_target' => trim($target),
                'buyer_trx_id' => $buyerTrxId,
                'sandbox_mode' => $sandbox,
            ];

            if (!empty($zone)) {
                $payload['customer_target_zone'] = trim($zone);
            }

            if (!empty($additionalData)) {
                $payload['additional_data'] = $additionalData;
            }

            $response = Http::withHeaders(self::getHeaders())
                ->timeout(15)
                ->post(self::getBaseUrl() . '/api/v1/h2h/transaction', $payload);

            if ($response->successful()) {
                $resData = $response->json()['data'] ?? [];
                return [
                    'success' => true,
                    'trx_id' => $resData['id'] ?? $resData['transaction_id'] ?? null,
                    'buyer_trx_id' => $buyerTrxId,
                    'status' => $resData['status'] ?? 'processing',
                    'sn' => $resData['sn'] ?? $resData['serial_number'] ?? null,
                    'message' => 'Pesanan berhasil dikirim ke provider.',
                    'raw' => $resData,
                ];
            }

            $errMessage = $response->json()['message'] ?? 'Provider menolak transaksi.';
            Log::error('Melostore createTransaction error response: ' . $response->body());

            return [
                'success' => false,
                'message' => $errMessage,
                'status' => 'failed',
            ];
        } catch (\Throwable $e) {
            Log::error('Melostore createTransaction exception: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Terjadi gangguan koneksi ke provider Melostore.',
                'status' => 'error',
            ];
        }
    }

    /**
     * Dapatkan URL Icon Game / Brand Lokal Teroptimasi (0ms latency, WebP ultra-ringan).
     */
    public static function getGameIconUrl(string $slug): string
    {
        $baseDir = public_path('images/games');
        
        // 1. Direct file match
        if (file_exists($baseDir . '/' . $slug . '.webp')) {
            return '/images/games/' . $slug . '.webp';
        }

        // 2. Prefix mapping for related brands / country variants
        $prefixMap = [
            'mobile-legends' => 'mobile-legends.webp',
            'free-fire' => 'free-fire.webp',
            'pubg-mobile' => 'pubg-mobile.webp',
            'genshin-impact' => 'genshin-impact.webp',
            '8-ball-pool' => '8-ball-pool.webp',
            'valorant' => 'valorant.webp',
            'roblox' => 'roblox.webp',
            'honor-of-kings' => 'honor-of-kings.webp',
            'call-of-duty' => 'call-of-duty-mobile.webp',
            'clash-of-clans' => 'clash-of-clans.webp',
            'brawl-stars' => 'brawl-stars.webp',
            'point-blank' => 'point-blank.webp',
            'steam' => 'steam-wallet-code-idr.webp',
            'google-play' => 'google-play-indonesia.webp',
            '3-' => '3-4.webp',
            'dana' => 'dana.webp',
            'telkomsel' => 'telkomsel.webp',
            'indosat' => 'indosat.webp',
            'xl' => 'xl.webp',
            'axis' => 'axis.webp',
            'pln' => 'pln.webp',
            'gopay' => 'gopay.webp',
            'ovo' => 'ovo.webp',
            'shopee' => 'shopeepay.webp',
        ];

        foreach ($prefixMap as $prefix => $file) {
            if (str_starts_with($slug, $prefix) || str_contains($slug, $prefix)) {
                if (file_exists($baseDir . '/' . $file)) {
                    return '/images/games/' . $file;
                }
            }
        }

        // 3. Fallback placeholder
        return '';
    }

    /**
     * Default Inquiry Fields (User ID & Zone).
     */
    public static function getDefaultInquiryFields(string $slug): array
    {
        $multiFieldSlugs = [
            'mobile-legends', 'genshin-impact', 'honkai-star-rail', 'ragnarok',
            'lifeafter', 'dragon-raja', 'tower-of-fantasy', 'aether-gazer', 
            'metal-slug-awakening', 'point-blank', 'seal-m', 'undawn', 'night-crows'
        ];

        foreach ($multiFieldSlugs as $mf) {
            if (str_contains($slug, $mf)) {
                return [
                    ['key' => 'customer_target', 'name' => 'User ID', 'type' => 'text', 'placeholder' => 'Contoh: 12345678'],
                    ['key' => 'customer_target_zone', 'name' => 'Zone / Server ID', 'type' => 'text', 'placeholder' => 'Contoh: 2045'],
                ];
            }
        }

        return [
            ['key' => 'customer_target', 'name' => 'User ID / Player ID', 'type' => 'text', 'placeholder' => 'Masukkan User ID / No. Akun'],
        ];
    }

    /**
     * Fallback Popular Games & Digital Brands List.
     */
    public static function getFallbackGames(): array
    {
        return [
            [
                'brand_id' => 1,
                'name' => 'Mobile Legends',
                'slug' => 'mobile-legends',
                'category' => 'Game',
                'publisher' => 'Moonton',
                'icon' => 'mobile-legends',
                'badge' => 'Populer',
            ],
            [
                'brand_id' => 2,
                'name' => 'Free Fire',
                'slug' => 'free-fire',
                'category' => 'Game',
                'publisher' => 'Garena',
                'icon' => 'free-fire',
                'badge' => 'Hot',
            ],
            [
                'brand_id' => 3,
                'name' => 'Genshin Impact',
                'slug' => 'genshin-impact',
                'category' => 'Game',
                'publisher' => 'HoYoverse',
                'icon' => 'genshin-impact',
                'badge' => 'RPG',
            ],
            [
                'brand_id' => 4,
                'name' => 'PUBG Mobile',
                'slug' => 'pubg-mobile',
                'category' => 'Game',
                'publisher' => 'Tencent Games',
                'icon' => 'pubg-mobile',
                'badge' => 'Action',
            ],
            [
                'brand_id' => 5,
                'name' => 'Valorant',
                'slug' => 'valorant',
                'category' => 'Game',
                'publisher' => 'Riot Games',
                'icon' => 'valorant',
                'badge' => 'PC FPS',
            ],
            [
                'brand_id' => 6,
                'name' => 'Honor of Kings',
                'slug' => 'honor-of-kings',
                'category' => 'Game',
                'publisher' => 'Level Infinite',
                'icon' => 'honor-of-kings',
                'badge' => 'MOBA',
            ],
            [
                'brand_id' => 7,
                'name' => 'Roblox',
                'slug' => 'roblox',
                'category' => 'Game',
                'publisher' => 'Roblox Corp',
                'icon' => 'roblox',
                'badge' => 'Sandbox',
            ],
            [
                'brand_id' => 8,
                'name' => 'Call of Duty Mobile',
                'slug' => 'cod-mobile',
                'category' => 'Game',
                'publisher' => 'Garena',
                'icon' => 'cod-mobile',
                'badge' => 'FPS',
            ],
            [
                'brand_id' => 9,
                'name' => 'Steam Wallet (IDR)',
                'slug' => 'steam-wallet',
                'category' => 'Gift Card',
                'publisher' => 'Valve',
                'icon' => 'steam-wallet',
                'badge' => 'Voucher',
            ],
            [
                'brand_id' => 10,
                'name' => 'Google Play Code',
                'slug' => 'google-play',
                'category' => 'Gift Card',
                'publisher' => 'Google LLC',
                'icon' => 'google-play',
                'badge' => 'Voucher',
            ],
            [
                'brand_id' => 11,
                'name' => 'Saldo DANA',
                'slug' => 'dana',
                'category' => 'Ewallet',
                'publisher' => 'DANA Indonesia',
                'icon' => 'dana',
                'badge' => 'Instan',
            ],
            [
                'brand_id' => 12,
                'name' => 'Pulsa & Data All-Op',
                'slug' => 'pulsa-all',
                'category' => 'Airtime & Data (Pulsa & Data)',
                'publisher' => 'Multi Operator',
                'icon' => 'pulsa-all',
                'badge' => 'PPOB',
            ],
        ];

        foreach ($games as &$g) {
            $g['thumbnail'] = self::getGameIconUrl($g['slug']);
        }

        return $games;
    }

    /**
     * Fallback Denominations & Prices (all >= Rp 5.000).
     */
    public static function getFallbackProducts(string $brandSlug): array
    {
        $brandName = ucwords(str_replace('-', ' ', $brandSlug));

        $templates = [
            'mobile-legends' => [
                ['sku' => 'ML-3', 'name' => '3 Diamonds', 'modal' => 1100],
                ['sku' => 'ML-11', 'name' => '11 Diamonds + 1 Bonus', 'modal' => 3400],
                ['sku' => 'ML-19', 'name' => '19 Diamonds + 2 Bonus', 'modal' => 5200],
                ['sku' => 'ML-28', 'name' => '28 Diamonds + 3 Bonus', 'modal' => 7800],
                ['sku' => 'ML-44', 'name' => '44 Diamonds + 4 Bonus', 'modal' => 11800],
                ['sku' => 'ML-86', 'name' => '86 Diamonds', 'modal' => 19500],
                ['sku' => 'ML-172', 'name' => '172 Diamonds', 'modal' => 39000],
                ['sku' => 'ML-257', 'name' => '257 Diamonds', 'modal' => 58500],
                ['sku' => 'ML-WEEKLY', 'name' => 'Weekly Diamond Pass (WDP)', 'modal' => 27500],
                ['sku' => 'ML-TWILIGHT', 'name' => 'Twilight Pass', 'modal' => 135000],
            ],
            'free-fire' => [
                ['sku' => 'FF-5', 'name' => '5 Diamonds', 'modal' => 900],
                ['sku' => 'FF-12', 'name' => '12 Diamonds', 'modal' => 1800],
                ['sku' => 'FF-50', 'name' => '50 Diamonds', 'modal' => 6700],
                ['sku' => 'FF-70', 'name' => '70 Diamonds', 'modal' => 9200],
                ['sku' => 'FF-140', 'name' => '140 Diamonds', 'modal' => 18500],
                ['sku' => 'FF-355', 'name' => '355 Diamonds', 'modal' => 46000],
                ['sku' => 'FF-MEMBER-M', 'name' => 'Membership Mingguan', 'modal' => 28000],
            ],
            'genshin-impact' => [
                ['sku' => 'GI-60', 'name' => '60 Genesis Crystals', 'modal' => 14000],
                ['sku' => 'GI-300', 'name' => '300+30 Genesis Crystals', 'modal' => 69000],
                ['sku' => 'GI-BLESSING', 'name' => 'Blessing of the Welkin Moon', 'modal' => 68000],
            ],
        ];

        $items = $templates[$brandSlug] ?? [
            ['sku' => "{$brandSlug}-1", 'name' => 'Tier Starter', 'modal' => 4500],
            ['sku' => "{$brandSlug}-2", 'name' => 'Tier Basic', 'modal' => 10000],
            ['sku' => "{$brandSlug}-3", 'name' => 'Tier Advanced', 'modal' => 25000],
            ['sku' => "{$brandSlug}-4", 'name' => 'Tier Pro', 'modal' => 50000],
        ];

        $products = [];
        foreach ($items as $item) {
            $sellingPrice = self::calculateSellingPrice((float) $item['modal']);
            $products[] = [
                'sku_code' => $item['sku'],
                'name' => $item['name'],
                'type' => 'Diamonds',
                'modal_price' => (float) $item['modal'],
                'price' => $sellingPrice,
                'formatted_price' => 'Rp ' . number_format($sellingPrice, 0, ',', '.'),
                'server_code' => 'global',
                'server_name' => 'Global',
            ];
        }

        return [
            'brand' => [
                'name' => $brandName,
                'slug' => $brandSlug,
                'thumbnail' => self::getGameIconUrl($brandSlug),
            ],
            'inquiry_fields' => self::getDefaultInquiryFields($brandSlug),
            'products' => $products,
        ];
    }
}
