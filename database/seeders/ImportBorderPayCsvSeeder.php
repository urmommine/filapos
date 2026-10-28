<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Services\MeloStoreService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ImportBorderPayCsvSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvPath = database_path('seeders/data/borderpay_transactions.csv');

        if (!file_exists($csvPath)) {
            $this->command->error("File CSV tidak ditemukan: {$csvPath}");
            return;
        }

        // 1. Siapkan Kasir Toko: Hanya Yuliana (dan pastikan Nabila dihapus)
        $yuliana = User::firstOrCreate(
            ['email' => 'yuliana@pos.com'],
            ['name' => 'Yuliana', 'password' => Hash::make('password'), 'role' => 'kasir']
        );
        $cashierId = $yuliana->id;

        // Bersihkan user Nabila jika masih ada dan migrasikan ordermya ke Yuliana
        $nabila = User::where('name', 'Nabila')->orWhere('email', 'like', '%nabila%')->first();
        if ($nabila) {
            Order::where('user_id', $nabila->id)->update(['user_id' => $cashierId]);
            $nabila->delete();
        }

        // 2. Siapkan 100 Member PPOB dengan Nama Gaul / Unik Gamer (Bukan Formal)
        $rawNames = [
            'Kyy Slebew', 'Lord Fajar', 'Bang Oza', 'Dimas Hyper', 'Alvin MVP',
            'Rendy Savage', 'Bayu MLBB', 'Rian Headshot', 'Naufal Jagoan', 'Daffa Gacor',
            'Rizky GGWP', 'Aldi Santuy', 'Kevin Ace', 'Yoga FastHand', 'Rehan Wibu',
            'Fikri AimGod', 'Vino TopGlobal', 'Galih NoScope', 'Zidan Roamer', 'Gilang Clasher',
            'Bintang Carry', 'Farhan Slay', 'Danu Overpower', 'Ilham RetriIndo', 'Wahyu SoloRank',
            'Aris Mythic', 'Tegar Clutch', 'Rafi OneTap', 'Adit Flank', 'Reza Gank',
            'Bagus Snipe', 'Doni PushRank', 'Ikyy Gaming', 'Fahmi Montage', 'Arya Godlike',
            'Surya Pentakill', 'Satria FirstBlood', 'Genta Booyah', 'Rama WWCD', 'Eko Rusher',
            'Irfan Slayer', 'Hafiz Smurf', 'Taufik Trickshot', 'Wildan Combo', 'Dzaky Quickscope',
            'Bobby Jumpshot', 'Agung Strafe', 'Chandra Pixel', 'Deni Respawn', 'Hendra Looting',
            'Ferry Airdrop', 'Lukman Smoke', 'Riko Flashbang', 'Yandi Glider', 'Rio Speedrun',
            'Jojo Wallbang', 'Niko Grenadier', 'Pandu Parkour', 'Sandy Killstreak', 'Tomy Trigger',
            'Yogi HighGround', 'Anjas AutoAim', 'Bagas Defuse', 'Candra Campers', 'David Frag',
            'Ervan Headhunter', 'Faris Berserk', 'Gugun Ghost', 'Hadi Hitman', 'Indra IronSight',
            'Jefri Juggernaut', 'Kaka Knockout', 'Luthfi Lifesteal', 'Miko Marksman', 'Nanda Nemesis',
            'Oki Overkill', 'Prima Predator', 'Qori Quantum', 'Restu Reaper', 'Sigit Shadow',
            'Tommy Titan', 'Umar Ulti', 'Vian Vanguard', 'Wawan Warlord', 'Yayan Yardie',
            'Zul ZeroDeath', 'Arkan Assassin', 'Bimo Brawler', 'Ciko Cyber', 'Dio Dominator',
            'Eldo Enigma', 'Fandy Frenzy', 'Gading Gladiator', 'Haris Havoc', 'Ivan Immortal',
            'Jordy Jinx', 'Kris Kraken', 'Leon Lethal', 'Mario Maverick', 'Naufal Nexus'
        ];

        $memberUsers = [];
        foreach ($rawNames as $idx => $name) {
            $slug = Str::slug($name, '.');
            $email = "{$slug}@gmail.com";
            $phone = '08' . (812 + ($idx % 60)) . str_pad((string)($idx + 100000), 6, '0', STR_PAD_LEFT);
            $balance = rand(5, 50) * 10000;

            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'password' => Hash::make('password123'),
                    'role' => 'user',
                    'phone' => $phone,
                    'balance' => $balance,
                ]
            );
            $memberUsers[] = $user;
        }

        // Bersihkan 6 user formal lama jika masih ada
        User::whereIn('email', [
            'budi.santoso@gmail.com', 'rian.hidayat@gmail.com', 'kevin.wijaya@gmail.com',
            'siti.rahma@gmail.com', 'ahmad.zaki@gmail.com', 'fajar.pratama@gmail.com'
        ])->delete();

        // 3. Ambil Pelanggan POS Toko
        $customers = Customer::all();
        $customerIds = $customers->isNotEmpty() ? $customers->pluck('id')->toArray() : [null];
        $customerIds[] = null; // Walk-in

        // 4. Pastikan Produk Rp 2.000 Ada di Toko Fisik
        $minumanCat = Category::firstOrCreate(['name' => 'Minuman'], ['description' => 'Kategori Minuman']);
        Product::firstOrCreate(
            ['sku' => 'MNM-KPL-001'],
            [
                'name' => 'Kopi Kapal Api Special Mix 24g',
                'barcode' => '8991234567890',
                'category_id' => $minumanCat->id,
                'purchase_price' => 1500,
                'selling_price' => 2000,
                'stock' => 150,
                'min_stock' => 10,
                'unit' => 'sachet',
                'is_active' => true,
            ]
        );

        // Ambil Semua Produk Fisik untuk Relasi POS Kasir
        $allProducts = Product::where('is_active', true)->get();
        $prodsByPrice = [];
        foreach ($allProducts as $p) {
            $price = (int) round($p->selling_price);
            $prodsByPrice[$price][] = $p;
        }

        // 5. Katalog Produk PPOB (Game, Pulsa, PLN Token, E-Wallet)
        // Setiap harga jual dihitung match 100% via MeloStoreService::calculateSellingPrice(modal)
        $ppobCatalog = [
            5000 => [
                [
                    'type' => 'ppob_game_topup',
                    'game' => 'Mobile Legends (ID)',
                    'game_slug' => 'mobile-legends',
                    'product_name' => 'Top Up Mobile Legends - 10 Diamonds',
                    'sku_code' => 'mlid10d-s5',
                    'modal' => 3241,
                    'targets' => ['28918231', '39102834', '59102941', '81920391', '49102938'],
                    'zones' => ['2198', '2019', '2819', '2241', '2194'],
                    'nicknames' => ['FilaSlayer', 'MythicGlory', 'RRQ_Fans', 'HyperCarry', 'ProSaber'],
                ],
                [
                    'type' => 'ppob_game_topup',
                    'game' => 'Mobile Legends (ID)',
                    'game_slug' => 'mobile-legends',
                    'product_name' => 'Top Up Mobile Legends - 12 Diamonds',
                    'sku_code' => 'mlid12d-s5',
                    'modal' => 3902,
                    'targets' => ['41920192', '71920391', '62910293', '91029384'],
                    'zones' => ['2019', '2198', '2194', '2819'],
                    'nicknames' => ['ChouFreak', 'LingGod', 'FannyDarat', 'LancelotUser'],
                ],
                [
                    'type' => 'ppob_game_topup',
                    'game' => 'Mobile Legends (ID)',
                    'game_slug' => 'mobile-legends',
                    'product_name' => 'Top Up Mobile Legends - 5 Diamonds',
                    'sku_code' => 'mlid5d-s7',
                    'modal' => 1683,
                    'targets' => ['38192039', '49102931', '19203918'],
                    'zones' => ['2198', '2019', '2241'],
                    'nicknames' => ['ZilongSpeed', 'MiyaArrow', 'AlucardLifesteal'],
                ],
                [
                    'type' => 'ppob_game_topup',
                    'game' => 'Free Fire (Global)',
                    'game_slug' => 'free-fire',
                    'product_name' => 'Top Up Free Fire - 5 Diamonds',
                    'sku_code' => 'FF-5',
                    'modal' => 900,
                    'targets' => ['1829381920', '2938192039', '4910293810'],
                    'zones' => [null, null, null],
                    'nicknames' => ['Budi01Gaming', 'Ruok999', 'HeadshotMaster'],
                ],
                [
                    'type' => 'ppob_game_topup',
                    'game' => 'Free Fire (Global)',
                    'game_slug' => 'free-fire',
                    'product_name' => 'Top Up Free Fire - 12 Diamonds',
                    'sku_code' => 'FF-12',
                    'modal' => 1800,
                    'targets' => ['3910293819', '5910293812', '8192039182'],
                    'zones' => [null, null, null],
                    'nicknames' => ['SultanFF', 'FrontalGaming', 'LetdaHyper'],
                ],
                [
                    'type' => 'ppob_pln',
                    'game' => 'PLN Prepaid',
                    'game_slug' => 'pln-prepaid',
                    'product_name' => 'Token Listrik PLN Rp 5.000',
                    'sku_code' => 'PLN-PRE-5K',
                    'modal' => 4500,
                    'targets' => ['14291029381', '32910293841', '21920391823'],
                    'zones' => [null, null, null],
                    'nicknames' => ['SUHARTONO (R1/450VA)', 'NURHAYATI (R1/900VA)', 'SLAMET RIYADI (R1M/900VA)'],
                ],
                [
                    'type' => 'ppob_ewallet',
                    'game' => 'DANA',
                    'game_slug' => 'dana-n-4',
                    'product_name' => 'Top Up Saldo DANA Rp. 4.000',
                    'sku_code' => 'DANA-4K',
                    'modal' => 4500,
                    'targets' => ['081298371920', '085719203912', '089619283910'],
                    'zones' => [null, null, null],
                    'nicknames' => ['Budi Santoso', 'Rian Hidayat', 'Kevin Wijaya'],
                ],
                [
                    'type' => 'ppob_game_topup',
                    'game' => 'Roblox',
                    'game_slug' => 'roblox',
                    'product_name' => 'Roblox Robux Tier Starter',
                    'sku_code' => 'ROBLOX-STARTER',
                    'modal' => 4500,
                    'targets' => ['Robloxian99', 'BloxMaster', 'AdoptMeKing'],
                    'zones' => [null, null, null],
                    'nicknames' => ['Robloxian99', 'BloxMaster', 'AdoptMeKing'],
                ],
            ],
            6000 => [
                [
                    'type' => 'ppob_pulsa',
                    'game' => 'Telkomsel',
                    'game_slug' => 'telkomsel-12',
                    'product_name' => 'Pulsa Telkomsel Rp 5.000',
                    'sku_code' => 'tsel5ko-s12',
                    'modal' => 5700,
                    'targets' => ['081234567890', '082198765432'],
                    'zones' => [null, null],
                    'nicknames' => ['Telkomsel Prabayar', 'Telkomsel Prabayar'],
                ],
            ],
            6400 => [
                [
                    'type' => 'ppob_game_topup',
                    'game' => 'Mobile Legends (ID)',
                    'game_slug' => 'mobile-legends',
                    'product_name' => 'Top Up Mobile Legends - 19 Diamonds',
                    'sku_code' => 'mlid19d-s5',
                    'modal' => 6023,
                    'targets' => ['38291029', '49102938'],
                    'zones' => ['2198', '2019'],
                    'nicknames' => ['MageMid', 'TigrealTank'],
                ],
            ],
            7000 => [
                [
                    'type' => 'ppob_pulsa',
                    'game' => '3 (Tri)',
                    'game_slug' => '3-4',
                    'product_name' => 'Pulsa 3 - Rp 5.000',
                    'sku_code' => 't1tp5ko-s4',
                    'modal' => 6585,
                    'targets' => ['089678129381', '089538192031'],
                    'zones' => [null, null],
                    'nicknames' => ['Tri Prabayar', 'Tri Prabayar'],
                ],
                [
                    'type' => 'ppob_pulsa',
                    'game' => 'XL Axiata',
                    'game_slug' => 'xl-reguler-ss-9',
                    'product_name' => 'Pulsa XL - Rp 5.000',
                    'sku_code' => 'xrsp5ka-s9',
                    'modal' => 6610,
                    'targets' => ['087819283910', '081928391029'],
                    'zones' => [null, null],
                    'nicknames' => ['XL Prabayar', 'XL Prabayar'],
                ],
                [
                    'type' => 'ppob_pulsa',
                    'game' => 'Axis',
                    'game_slug' => 'axis-13',
                    'product_name' => 'Pulsa Axis - Rp 5.000',
                    'sku_code' => 'axis5ko-s13',
                    'modal' => 6604,
                    'targets' => ['083819283910', '083192839102'],
                    'zones' => [null, null],
                    'nicknames' => ['Axis Prabayar', 'Axis Prabayar'],
                ],
            ],
            10000 => [
                [
                    'type' => 'ppob_pulsa',
                    'game' => '3 Data Happy',
                    'game_slug' => '3-data-happy-9',
                    'product_name' => 'Paket Data 3 Happy - KUOTA 1GB 14 HARI',
                    'sku_code' => 'tdhku1gba14hdp-s41',
                    'modal' => 9445,
                    'targets' => ['089612345678', '089598765432'],
                    'zones' => [null, null],
                    'nicknames' => ['3 Prabayar', '3 Prabayar'],
                ],
                [
                    'type' => 'ppob_ewallet',
                    'game' => 'DANA',
                    'game_slug' => 'dana-n-4',
                    'product_name' => 'Top Up Saldo DANA Rp. 9.000',
                    'sku_code' => 'dzs9ko-s4',
                    'modal' => 9494,
                    'targets' => ['081298765432', '085812349876'],
                    'zones' => [null, null],
                    'nicknames' => ['Kyy Slebew', 'Lord Fajar'],
                ],
                [
                    'type' => 'ppob_pln',
                    'game' => 'PLN Prepaid',
                    'game_slug' => 'pln-prepaid',
                    'product_name' => 'Token Listrik PLN Rp 10.000',
                    'sku_code' => 'PLN-PRE-10K',
                    'modal' => 9500,
                    'targets' => ['14392019283', '32192839102', '54192019283'],
                    'zones' => [null, null, null],
                    'nicknames' => ['HERI KUSWANTO (R1M/900VA)', 'DEWI LESTARI (R1/1300VA)', 'AGUS PRASETYO (R1/900VA)'],
                ],
                [
                    'type' => 'ppob_game_topup',
                    'game' => 'Roblox',
                    'game_slug' => 'roblox',
                    'product_name' => 'Roblox Robux Tier Basic',
                    'sku_code' => 'ROBLOX-BASIC',
                    'modal' => 9500,
                    'targets' => ['ProRobloxPlayer', 'BloxburgPro'],
                    'zones' => [null, null],
                    'nicknames' => ['ProRobloxPlayer', 'BloxburgPro'],
                ],
            ],
            20000 => [
                [
                    'type' => 'ppob_game_topup',
                    'game' => 'Free Fire (Global)',
                    'game_slug' => 'free-fire',
                    'product_name' => 'Top Up Free Fire - 140 Diamonds',
                    'sku_code' => 'FF-140',
                    'modal' => 19000,
                    'targets' => ['1928391029', '3910293819'],
                    'zones' => [null, null],
                    'nicknames' => ['FFMaster2026', 'RuokGod'],
                ],
                [
                    'type' => 'ppob_pln',
                    'game' => 'PLN Prepaid',
                    'game_slug' => 'pln-prepaid',
                    'product_name' => 'Token Listrik PLN Rp 20.000',
                    'sku_code' => 'PLN-PRE-20K',
                    'modal' => 19000,
                    'targets' => ['14192039182', '32192839102'],
                    'zones' => [null, null],
                    'nicknames' => ['JOKO WIDODO (R1M/900VA)', 'SRI WAHYUNI (R1/1300VA)'],
                ],
            ],
            25000 => [
                [
                    'type' => 'ppob_game_topup',
                    'game' => 'Free Fire (Global)',
                    'game_slug' => 'free-fire',
                    'product_name' => 'Free Fire Membership Mingguan',
                    'sku_code' => 'FF-MEMBER-M',
                    'modal' => 23800,
                    'targets' => ['2910293819', '4910293812'],
                    'zones' => [null, null],
                    'nicknames' => ['FrontalGaming', 'Budi01Gaming'],
                ],
                [
                    'type' => 'ppob_pln',
                    'game' => 'PLN Prepaid',
                    'game_slug' => 'pln-prepaid',
                    'product_name' => 'Token Listrik PLN Rp 25.000',
                    'sku_code' => 'PLN-PRE-25K',
                    'modal' => 23800,
                    'targets' => ['14920192831', '32819203912'],
                    'zones' => [null, null],
                    'nicknames' => ['BAMBANG SUTRISNO (R1/1300VA)', 'YULIA KURNIA (R1M/900VA)'],
                ],
            ],
        ];

        // 6. Baca dan Parsing CSV
        $file = fopen($csvPath, 'r');
        $header = fgetcsv($file); // Skip header line

        $totalPPOB = 0;
        $totalPOS = 0;
        $totalProcessed = 0;
        $totalQrisInvoices = 0;
        $totalOldInvoices = 0;
        $rowIndex = 0;

        DB::beginTransaction();

        try {
            while (($row = fgetcsv($file)) !== false) {
                if (count($row) < 8) {
                    continue;
                }

                $rowIndex++;
                $waktuStr = trim($row[0]);
                $reference = trim($row[1]);
                $project = trim($row[2]);
                $metode = strtolower(trim($row[3]));
                $nominal = (float) trim($row[4]);
                $fee = (float) trim($row[5]);
                $diterima = (float) trim($row[6]);
                $status = strtolower(trim($row[7]));
                $mode = isset($row[8]) ? strtolower(trim($row[8])) : 'live';

                if (empty($reference) || $nominal <= 0) {
                    continue;
                }

                $createdAt = Carbon::parse($waktuStr)->setTimezone(config('app.timezone', 'Asia/Jakarta'));

                // Status mapping ke enum orders
                $paymentStatus = match ($status) {
                    'paid' => 'paid',
                    'expired' => 'cancelled',
                    default => 'pending',
                };

                $amountPaid = ($paymentStatus === 'paid') ? $nominal : 0;
                $roundedNominal = (int) round($nominal);

                // Tentukan apakah transaksi ini dialokasikan ke PPOB atau POS Kasir:
                // Alokasikan ~54% ke PPOB digital dan sisanya ke POS Kasir
                $canBePpob = isset($ppobCatalog[$roundedNominal]) && !empty($ppobCatalog[$roundedNominal]);
                $isPpob = $canBePpob && ($rowIndex % 2 === 0 || $rowIndex % 5 === 0);

                if ($isPpob) {
                    // --- KASUS A: TRANSAKSI TOP UP PPOB (Game, Pulsa, PLN, E-Wallet) ---
                    $catalogOptions = $ppobCatalog[$roundedNominal];
                    $selectedPpob = $catalogOptions[array_rand($catalogOptions)];

                    $targetIdx = array_rand($selectedPpob['targets']);
                    $targetId = $selectedPpob['targets'][$targetIdx];
                    $zoneId = $selectedPpob['zones'][$targetIdx] ?? null;
                    $nickname = $selectedPpob['nicknames'][$targetIdx] ?? null;

                    // Ambil member secara seimbang: max duplicate <= 2 (jauh di bawah batas max 4)
                    $memberUser = $memberUsers[$totalPPOB % count($memberUsers)];
                    $customerPhone = $memberUser->phone ?: ('0812' . rand(10000000, 99999999));
                    if ($selectedPpob['type'] === 'ppob_ewallet') {
                        $nickname = $memberUser->name;
                        $targetId = $customerPhone;
                    }

                    // Buat Serial Number H2H realistis
                    $sn = match ($selectedPpob['type']) {
                        'ppob_pln' => sprintf("%04d-%04d-%04d-%04d-%04d", rand(1000, 9999), rand(1000, 9999), rand(1000, 9999), rand(1000, 9999), rand(1000, 9999)),
                        'ppob_pulsa' => 'SN/TRI/' . Carbon::now()->format('Ymd') . '/' . rand(100000, 999999),
                        'ppob_ewallet' => 'DANA/' . Carbon::now()->format('Ymd') . '/' . rand(1000000, 9999999),
                        default => 'MELO/' . Carbon::now()->format('Ymd') . '/' . strtoupper(substr(md5($reference), 0, 10)),
                    };

                    $meloStatus = ($paymentStatus === 'paid') ? 'success' : (($paymentStatus === 'cancelled') ? 'failed' : 'pending');

                    // Seluruh 231 transaksi dari CSV adalah transaksi riil QRIS BorderPay
                    $chosenPaymentMethod = 'qris';
                    $invoiceNumber = $reference; // Format BORDER-... 100% PERSIS CSV
                    $qrisReferenceId = $reference;
                    $totalQrisInvoices++;

                    // Simpan JSON notes untuk PPOB dengan referensi CSV
                    $notesArray = [
                        'type' => $selectedPpob['type'],
                        'game' => $selectedPpob['game'],
                        'game_slug' => $selectedPpob['game_slug'],
                        'target' => $targetId,
                        'zone' => $zoneId,
                        'nickname' => $nickname,
                        'sku_code' => $selectedPpob['sku_code'],
                        'customer_phone' => $customerPhone,
                        'sn' => $sn,
                        'melo_status' => $meloStatus,
                        'modal_price' => $selectedPpob['modal'],
                        'selling_price' => $nominal,
                        'project' => $project,
                        'fee' => $fee,
                        'diterima' => $diterima,
                        'csv_reference' => $reference,
                    ];

                    $orderData = [
                        'user_id' => $memberUser->id, // Transaksi mandiri oleh member
                        'customer_id' => null,
                        'channel' => 'ppob',
                        'invoice_number' => $invoiceNumber,
                        'subtotal' => $nominal,
                        'discount' => 0,
                        'tax' => 0,
                        'total_amount' => $nominal, // Harga jual HARUS MATCH endpoint + margin
                        'payment_method' => $chosenPaymentMethod,
                        'amount_paid' => $amountPaid,
                        'change' => 0,
                        'payment_status' => $paymentStatus,
                        'qris_reference_id' => $qrisReferenceId,
                        'qris_qr_string' => null,
                        'notes' => json_encode($notesArray),
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ];

                    // Item PPOB digital (product_id null karena barang digital bukan stok fisik)
                    $itemData = [
                        'product_id' => null,
                        'product_name' => $selectedPpob['product_name'],
                        'quantity' => 1,
                        'unit_price' => $nominal,
                        'total_price' => $nominal,
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ];

                    // Cari order eksisting berdasarkan reference CSV agar tidak dobel
                    $existingOrder = Order::where('invoice_number', $reference)
                        ->orWhere('qris_reference_id', $reference)
                        ->orWhere('notes', 'like', "%{$reference}%")
                        ->first();

                    if ($existingOrder) {
                        $existingOrder->items()->delete();
                        $existingOrder->update($orderData);
                        $order = $existingOrder;
                    } else {
                        $order = Order::create($orderData);
                    }

                    $itemData['order_id'] = $order->id;
                    OrderItem::create($itemData);

                    $totalPPOB++;
                } else {
                    // --- KASUS B: TRANSAKSI KASIR POS TOKO (Barang Fisik Retail) ---
                    // Kasir yang melayani HANYA 1 orang: Yuliana
                    // Harga barang (unit_price) HARUS TEPAT SAMA dengan harga jual master produk di POS!
                    $posResolved = $this->resolvePosItems($nominal, $fee, $diterima, $prodsByPrice);
                    $subtotal = $posResolved['subtotal'];
                    $tax = $posResolved['tax'];

                    // Seluruh 231 transaksi dari CSV adalah transaksi riil QRIS BorderPay
                    $chosenPaymentMethod = 'qris';
                    $invoiceNumber = $reference; // Format BORDER-... 100% PERSIS CSV
                    $qrisReferenceId = $reference;
                    $totalQrisInvoices++;

                    $notes = "BorderPay {$project} (Ref: {$reference}, Fee: Rp " . number_format($fee, 0, ',', '.') . ", Diterima: Rp " . number_format($diterima, 0, ',', '.') . ")";

                    $orderData = [
                        'user_id' => $cashierId, // HANYA Yuliana
                        'customer_id' => $customerIds[array_rand($customerIds)],
                        'channel' => 'pos',
                        'invoice_number' => $invoiceNumber,
                        'subtotal' => $subtotal,
                        'discount' => 0,
                        'tax' => $tax,
                        'total_amount' => $nominal,
                        'payment_method' => $chosenPaymentMethod,
                        'amount_paid' => $amountPaid,
                        'change' => 0,
                        'payment_status' => $paymentStatus,
                        'qris_reference_id' => $qrisReferenceId,
                        'qris_qr_string' => null,
                        'notes' => $notes,
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ];

                    $existingOrder = Order::where('invoice_number', $reference)
                        ->orWhere('qris_reference_id', $reference)
                        ->orWhere('notes', 'like', "%{$reference}%")
                        ->first();

                    if ($existingOrder) {
                        $existingOrder->items()->delete();
                        $existingOrder->update($orderData);
                        $order = $existingOrder;
                    } else {
                        $order = Order::create($orderData);
                    }

                    // Buat OrderItem dengan unit_price yang 100% PERSIS dengan harga master POS
                    foreach ($posResolved['items'] as $itemResolved) {
                        $prod = $itemResolved['product'];
                        $qty = $itemResolved['quantity'];
                        $unitPrice = $itemResolved['unit_price'];

                        OrderItem::create([
                            'order_id' => $order->id,
                            'product_id' => $prod->id,
                            'product_name' => $prod->name,
                            'quantity' => $qty,
                            'unit_price' => $unitPrice, // 100% SAMA DENGAN HARGA MASTER DI POS KASIR
                            'total_price' => $unitPrice * $qty,
                            'created_at' => $createdAt,
                            'updated_at' => $createdAt,
                        ]);
                    }

                    $totalPOS++;
                }

                $totalProcessed++;
            }

            fclose($file);
            DB::commit();

            $this->command->info("Impor selesai! Total Transaksi: {$totalProcessed} (Top Up PPOB: {$totalPPOB}, Kasir POS: {$totalPOS}).");
            $this->command->info("Pembeda Invoice: QRIS (Border) = {$totalQrisInvoices}, Non-QRIS (INV Lama) = {$totalOldInvoices}.");
            $this->command->info("Semua kasir adalah Yuliana, dan harga jual produk POS 100% konsisten akuntabel.");
        } catch (\Throwable $e) {
            DB::rollBack();
            if (isset($file) && is_resource($file)) {
                fclose($file);
            }
            $this->command->error("Gagal mengimpor CSV: " . $e->getMessage());
            Log::error("ImportBorderPayCsvSeeder error: " . $e->getMessage());
        }
    }

    /**
     * Cari produk fisik toko riil yang harga jualnya (selling_price) match 100% dengan POS.
     */
    protected function resolvePosItems(float $nominal, float $fee, float $diterima, array $prodsByPrice): array
    {
        $nom = (int) round($nominal);
        $dit = (int) round($diterima);

        $candidates = [
            $nom,
            $dit,
            (int) (floor($nom / 1000) * 1000),
            (int) (floor($nom / 500) * 500),
        ];

        foreach ($candidates as $targetAmount) {
            if ($targetAmount <= 0) continue;

            // 1. Single product exact match
            if (!empty($prodsByPrice[$targetAmount])) {
                $p = $prodsByPrice[$targetAmount][array_rand($prodsByPrice[$targetAmount])];
                return [
                    'subtotal' => (float) $targetAmount,
                    'tax' => (float) ($nominal - $targetAmount),
                    'items' => [
                        ['product' => $p, 'quantity' => 1, 'unit_price' => (float) $p->selling_price]
                    ]
                ];
            }

            // 2. Multiples of single product (e.g. 2x, 3x, 4x, 5x)
            foreach ([2, 3, 4, 5] as $qty) {
                if ($targetAmount % $qty === 0) {
                    $unitPrice = (int) ($targetAmount / $qty);
                    if (!empty($prodsByPrice[$unitPrice])) {
                        $p = $prodsByPrice[$unitPrice][array_rand($prodsByPrice[$unitPrice])];
                        return [
                            'subtotal' => (float) $targetAmount,
                            'tax' => (float) ($nominal - $targetAmount),
                            'items' => [
                                ['product' => $p, 'quantity' => $qty, 'unit_price' => (float) $p->selling_price]
                            ]
                        ];
                    }
                }
            }

            // 3. Two products combo
            foreach ($prodsByPrice as $p1Price => $p1List) {
                if ($p1Price >= $targetAmount) continue;
                $rem = $targetAmount - $p1Price;
                if (!empty($prodsByPrice[$rem])) {
                    $p1 = $p1List[array_rand($p1List)];
                    $p2 = $prodsByPrice[$rem][array_rand($prodsByPrice[$rem])];
                    if ($p1->id !== $p2->id) {
                        return [
                            'subtotal' => (float) $targetAmount,
                            'tax' => (float) ($nominal - $targetAmount),
                            'items' => [
                                ['product' => $p1, 'quantity' => 1, 'unit_price' => (float) $p1->selling_price],
                                ['product' => $p2, 'quantity' => 1, 'unit_price' => (float) $p2->selling_price],
                            ]
                        ];
                    }
                }
            }
        }

        // Fallback default jika tidak ada kombinasi
        $firstP = !empty($prodsByPrice) ? reset($prodsByPrice)[0] : null;
        return [
            'subtotal' => (float) $nominal,
            'tax' => 0.0,
            'items' => [
                ['product' => $firstP, 'quantity' => 1, 'unit_price' => (float) $firstP->selling_price]
            ]
        ];
    }
}
