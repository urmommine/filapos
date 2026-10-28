<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RealProductAndSalesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'yuliana@pos.com'],
            ['name' => 'Yuliana', 'password' => bcrypt('password'), 'role' => 'kasir']
        );

        $yuliana = User::where('email', 'yuliana@pos.com')->first();
        $cashierIds = [$yuliana->id];

        // 2. Data Pelanggan Nyata
        $customerList = [
            ['name' => 'Agus Santoso', 'phone' => '081234567890', 'email' => 'agus.santoso@gmail.com', 'address' => 'Jl. Merdeka No. 12, Jakarta'],
            ['name' => 'Siti Nurhaliza', 'phone' => '081398765432', 'email' => 'siti.nur@gmail.com', 'address' => 'Jl. Kenanga No. 5, Bandung'],
            ['name' => 'Budi Pratama', 'phone' => '082155566778', 'email' => 'budi.pratama@yahoo.com', 'address' => 'Jl. Diponegoro No. 88, Surabaya'],
            ['name' => 'Dewi Sartika', 'phone' => '085611223344', 'email' => 'dewi.sartika@gmail.com', 'address' => 'Jl. Melati No. 34, Yogyakarta'],
            ['name' => 'Rian Hidayat', 'phone' => '087899887766', 'email' => 'rian.h@gmail.com', 'address' => 'Jl. Sudirman No. 101, Semarang'],
        ];

        $customerIds = [null]; // null = Pelanggan Umum (walk-in)
        foreach ($customerList as $c) {
            $customer = Customer::firstOrCreate(['phone' => $c['phone']], $c);
            $customerIds[] = $customer->id;
        }

        // 3. Kategori & Real Produk Minimarket / Retail Indonesia
        $catalog = [
            'Makanan & Mie Instan' => [
                'desc' => 'Mie instan, pasta, dan makanan cepat saji favorit',
                'products' => [
                    ['name' => 'Indomie Goreng Spesial 85g', 'sku' => 'MKN-IDM-001', 'barcode' => '8998866200213', 'buy' => 2800, 'sell' => 3500, 'stock' => 120, 'min_stock' => 20],
                    ['name' => 'Indomie Kuah Ayam Bawang 69g', 'sku' => 'MKN-IDM-002', 'barcode' => '8998866200114', 'buy' => 2700, 'sell' => 3500, 'stock' => 95, 'min_stock' => 20],
                    ['name' => 'Mie Sedaap Goreng 90g', 'sku' => 'MKN-SDP-001', 'barcode' => '8993888800101', 'buy' => 2800, 'sell' => 3500, 'stock' => 80, 'min_stock' => 15],
                    ['name' => 'Pop Mie Rasa Ayam Bawang 75g', 'sku' => 'MKN-POP-001', 'barcode' => '8998866200503', 'buy' => 4500, 'sell' => 6000, 'stock' => 45, 'min_stock' => 10],
                    ['name' => 'Samyang Hot Chicken Ramen 140g', 'sku' => 'MKN-SMY-001', 'barcode' => '8801073110502', 'buy' => 16500, 'sell' => 22000, 'stock' => 25, 'min_stock' => 5],
                    ['name' => 'Pronas Kornet Daging Sapi 198g', 'sku' => 'MKN-PRN-001', 'barcode' => '8992745110111', 'buy' => 21000, 'sell' => 26500, 'stock' => 18, 'min_stock' => 5],
                ],
            ],
            'Minuman Dingin & Hangat' => [
                'desc' => 'Air mineral, susu kemasan, jus, teh, dan kopi siap minum',
                'products' => [
                    ['name' => 'Le Minerale 600ml', 'sku' => 'MNM-LEM-001', 'barcode' => '8996001600269', 'buy' => 2500, 'sell' => 4000, 'stock' => 150, 'min_stock' => 30],
                    ['name' => 'Aqua Air Mineral 600ml', 'sku' => 'MNM-AQU-001', 'barcode' => '8992771001018', 'buy' => 2700, 'sell' => 4000, 'stock' => 140, 'min_stock' => 30],
                    ['name' => 'Teh Pucuk Harum 350ml', 'sku' => 'MNM-PCH-001', 'barcode' => '8996001410141', 'buy' => 3200, 'sell' => 4500, 'stock' => 85, 'min_stock' => 15],
                    ['name' => 'Ultra Milk Cokelat 250ml', 'sku' => 'MNM-ULT-001', 'barcode' => '8992753110118', 'buy' => 5500, 'sell' => 7500, 'stock' => 60, 'min_stock' => 10],
                    ['name' => 'Pocari Sweat Can 330ml', 'sku' => 'MNM-PCR-001', 'barcode' => '8992741910010', 'buy' => 6500, 'sell' => 8500, 'stock' => 40, 'min_stock' => 10],
                    ['name' => 'Kopi Kenangan Mantancino 220ml', 'sku' => 'MNM-KPK-001', 'barcode' => '8997232230018', 'buy' => 8500, 'sell' => 11000, 'stock' => 35, 'min_stock' => 8],
                    ['name' => 'Coca-Cola Pet 390ml', 'sku' => 'MNM-CCL-001', 'barcode' => '8992761001019', 'buy' => 4800, 'sell' => 6500, 'stock' => 50, 'min_stock' => 10],
                ],
            ],
            'Snack & Biskuit' => [
                'desc' => 'Keripik, biskuit, cokelat, dan camilan renyah',
                'products' => [
                    ['name' => 'Chitato Sapi Panggang 68g', 'sku' => 'SNK-CHT-001', 'barcode' => '8991001710110', 'buy' => 9500, 'sell' => 12500, 'stock' => 40, 'min_stock' => 10],
                    ['name' => 'Silverqueen Chunky Bar Cashew 95g', 'sku' => 'SNK-SLV-001', 'barcode' => '8991002102143', 'buy' => 21000, 'sell' => 26500, 'stock' => 4, 'min_stock' => 8], // Low stock alert
                    ['name' => 'Pocky Chocolate 47g', 'sku' => 'SNK-PCK-001', 'barcode' => '8998009010112', 'buy' => 7500, 'sell' => 10000, 'stock' => 50, 'min_stock' => 10],
                    ['name' => 'Oreo Vanilla Roll 133g', 'sku' => 'SNK-ORO-001', 'barcode' => '8992760120100', 'buy' => 8000, 'sell' => 10500, 'stock' => 45, 'min_stock' => 10],
                    ['name' => 'Beng-Beng Regular 20g', 'sku' => 'SNK-BBG-001', 'barcode' => '8996001301012', 'buy' => 2000, 'sell' => 3000, 'stock' => 100, 'min_stock' => 25],
                    ['name' => 'Taro Net Seaweed 65g', 'sku' => 'SNK-TRO-001', 'barcode' => '8992775210119', 'buy' => 4200, 'sell' => 6000, 'stock' => 30, 'min_stock' => 10],
                ],
            ],
            'Sembako & Kebutuhan Dapur' => [
                'desc' => 'Beras, minyak goreng, gula, dan bumbu dapur lengkap',
                'products' => [
                    ['name' => 'Bimoli Minyak Goreng Pouch 2L', 'sku' => 'SBK-BML-001', 'barcode' => '8991001100011', 'buy' => 34000, 'sell' => 39500, 'stock' => 35, 'min_stock' => 10],
                    ['name' => 'Gulaku Gula Pasir Premium 1kg', 'sku' => 'SBK-GLK-001', 'barcode' => '8998888100010', 'buy' => 16000, 'sell' => 18500, 'stock' => 50, 'min_stock' => 10],
                    ['name' => 'Kecap Manis Bango Refill 520ml', 'sku' => 'SBK-BNG-001', 'barcode' => '8999999052011', 'buy' => 20500, 'sell' => 24500, 'stock' => 28, 'min_stock' => 5],
                    ['name' => 'Beras Setra Ramos Super 5kg', 'sku' => 'SBK-BRS-001', 'barcode' => '8997012340019', 'buy' => 67000, 'sell' => 74500, 'stock' => 20, 'min_stock' => 5],
                    ['name' => 'Saus Sambal Extra Pedas ABC 335ml', 'sku' => 'SBK-ABC-001', 'barcode' => '8992742010111', 'buy' => 13500, 'sell' => 16500, 'stock' => 30, 'min_stock' => 8],
                    ['name' => 'Royco Rasa Ayam Sachet 230g', 'sku' => 'SBK-RYC-001', 'barcode' => '8999999030019', 'buy' => 9500, 'sell' => 12000, 'stock' => 40, 'min_stock' => 10],
                ],
            ],
            'Personal Care & Kebersihan' => [
                'desc' => 'Perawatan tubuh, mandi, deterjen, dan pembersih rumah',
                'products' => [
                    ['name' => 'Lifebuoy Sabun Mandi Total 10 85g', 'sku' => 'PRC-LFB-001', 'barcode' => '8999999010011', 'buy' => 3800, 'sell' => 5000, 'stock' => 70, 'min_stock' => 15],
                    ['name' => 'Pepsodent Pencegah Gigi Berlubang 190g', 'sku' => 'PRC-PEP-001', 'barcode' => '8999999020010', 'buy' => 12500, 'sell' => 16000, 'stock' => 35, 'min_stock' => 10],
                    ['name' => 'Pantene Shampoo Anti Dandruff 160ml', 'sku' => 'PRC-PNT-001', 'barcode' => '8992828010118', 'buy' => 22000, 'sell' => 27500, 'stock' => 20, 'min_stock' => 5],
                    ['name' => 'Rinso Molto Deterjen Bubuk 770g', 'sku' => 'PRC-RNS-001', 'barcode' => '8999999040018', 'buy' => 19500, 'sell' => 23500, 'stock' => 25, 'min_stock' => 5],
                    ['name' => 'Mama Lemon Jeruk Nipis 680ml', 'sku' => 'PRC-MML-001', 'barcode' => '8998866100018', 'buy' => 11000, 'sell' => 14000, 'stock' => 3, 'min_stock' => 10], // Low stock alert
                ],
            ],
            'Rokok & Tembakau' => [
                'desc' => 'Produk rokok filter dan kretek resmi',
                'products' => [
                    ['name' => 'Sampoerna A Mild 16 Batang', 'sku' => 'RKK-SMP-001', 'barcode' => '8992689001019', 'buy' => 31000, 'sell' => 35000, 'stock' => 60, 'min_stock' => 15],
                    ['name' => 'Gudang Garam Surya 16 Batang', 'sku' => 'RKK-GGS-001', 'barcode' => '8992693001012', 'buy' => 32500, 'sell' => 36500, 'stock' => 55, 'min_stock' => 15],
                    ['name' => 'Marlboro Red 20 Batang', 'sku' => 'RKK-MLB-001', 'barcode' => '8992689002016', 'buy' => 39000, 'sell' => 44000, 'stock' => 40, 'min_stock' => 10],
                ],
            ],
        ];

        $createdProducts = [];

        foreach ($catalog as $catName => $catData) {
            $category = Category::updateOrCreate(
                ['name' => $catName],
                [
                    'slug' => Str::slug($catName),
                    'description' => $catData['desc'],
                    'is_active' => true,
                ]
            );

            foreach ($catData['products'] as $p) {
                $product = Product::updateOrCreate(
                    ['sku' => $p['sku']],
                    [
                        'category_id' => $category->id,
                        'name' => $p['name'],
                        'barcode' => $p['barcode'],
                        'description' => $p['name'] . ' original dan bersegel resmi.',
                        'purchase_price' => $p['buy'],
                        'selling_price' => $p['sell'],
                        'stock' => $p['stock'],
                        'min_stock' => $p['min_stock'],
                        'is_active' => true,
                    ]
                );
                $createdProducts[] = $product;
            }
        }

        // 4. Seed Statistik Penjualan Sejak Tanggal 25 Oktober 2026
        // Tanggal: 25, 26, 27, 28 (Hari ini)
        $datesSchedule = [
            '2026-10-25' => ['target_orders' => 22, 'hour_start' => 8, 'hour_end' => 21],
            '2026-10-26' => ['target_orders' => 26, 'hour_start' => 8, 'hour_end' => 21],
            '2026-10-27' => ['target_orders' => 30, 'hour_start' => 8, 'hour_end' => 22],
            '2026-10-28' => ['target_orders' => 18, 'hour_start' => 8, 'hour_end' => 11], // Hari ini s.d jam saat ini
        ];

        $orderSequence = 1;

        // Load nominals dari CSV BorderPay
        $csvNominals = [5000, 5000, 5000, 10000, 5000, 10000, 8500, 25000, 6000, 5500, 20000];
        $csvPath = database_path('seeders/data/borderpay_transactions.csv');
        if (file_exists($csvPath) && ($f = fopen($csvPath, 'r')) !== false) {
            fgetcsv($f);
            $csvNominals = [];
            while (($row = fgetcsv($f)) !== false) {
                if (isset($row[4]) && (float)$row[4] > 0) {
                    $csvNominals[] = (float)$row[4];
                }
            }
            fclose($f);
        }

        foreach ($datesSchedule as $dateStr => $schedule) {
            $numOrders = $schedule['target_orders'];
            $carbonDate = Carbon::parse($dateStr);

            for ($i = 0; $i < $numOrders; $i++) {
                // Buat jam acak dalam rentang
                $hour = rand($schedule['hour_start'], $schedule['hour_end']);
                $minute = rand(0, 59);
                $second = rand(0, 59);
                $orderTime = $carbonDate->copy()->setHour($hour)->setMinute($minute)->setSecond($second);

                // Tentukan metode bayar: 50% Cash, 35% QRIS (BorderPay), 15% Transfer
                $methodRand = rand(1, 100);
                $isBorderEra = $carbonDate->gte(Carbon::parse('2026-10-27'));

                if ($isBorderEra) {
                    $timestampMs = $orderTime->timestamp * 1000 + rand(100, 999);
                    $invoiceNumber = 'BORDER-' . $timestampMs . '-' . rand(1000, 9999);
                } else {
                    $invoiceNumber = 'INV-' . $orderTime->format('Ymd') . '-' . str_pad($orderSequence, 4, '0', STR_PAD_LEFT);
                }

                if ($methodRand <= 50) {
                    $method = 'cash';
                    $qrisRef = null;
                    $notes = null;
                } elseif ($methodRand <= 85) {
                    $method = 'qris';
                    $qrisRef = $isBorderEra ? $invoiceNumber : null;
                    $notes = $isBorderEra ? 'Pembayaran QRIS BorderPay Sukses' : 'Pembayaran QRIS';
                } else {
                    $method = 'transfer';
                    $qrisRef = null;
                    $notes = null;
                }
                $orderSequence++;

                $nominal = $csvNominals[array_rand($csvNominals)];
                $subtotal = $nominal;
                $discount = 0;
                $totalAmount = $nominal;

                // Nominal bayar & kembalian
                if ($method === 'cash') {
                    $amountPaid = ceil($totalAmount / 5000) * 5000;
                    if ($amountPaid < $totalAmount) $amountPaid = $totalAmount;
                    $change = $amountPaid - $totalAmount;
                } else {
                    $amountPaid = $totalAmount;
                    $change = 0;
                }

                $order = Order::create([
                    'user_id' => $cashierIds[array_rand($cashierIds)],
                    'customer_id' => $customerIds[array_rand($customerIds)],
                    'invoice_number' => $invoiceNumber,
                    'subtotal' => $subtotal,
                    'discount' => $discount,
                    'tax' => 0,
                    'total_amount' => $totalAmount,
                    'payment_method' => $method,
                    'amount_paid' => $amountPaid,
                    'change' => $change,
                    'payment_status' => 'paid',
                    'qris_reference_id' => $qrisRef,
                    'notes' => $notes,
                    'created_at' => $orderTime,
                    'updated_at' => $orderTime,
                ]);

                if ($nominal >= 20000) {
                    $part1 = round($nominal * 0.55);
                    $part2 = $nominal - $part1;
                    $p1 = collect($createdProducts)->random();
                    $p2 = collect($createdProducts)->where('id', '!=', $p1->id)->random();

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $p1->id,
                        'product_name' => $p1->name,
                        'quantity' => 1,
                        'unit_price' => $part1,
                        'total_price' => $part1,
                        'created_at' => $orderTime,
                        'updated_at' => $orderTime,
                    ]);

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $p2->id,
                        'product_name' => $p2->name,
                        'quantity' => 1,
                        'unit_price' => $part2,
                        'total_price' => $part2,
                        'created_at' => $orderTime,
                        'updated_at' => $orderTime,
                    ]);
                } else {
                    $p = collect($createdProducts)->random();
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $p->id,
                        'product_name' => $p->name,
                        'quantity' => 1,
                        'unit_price' => $nominal,
                        'total_price' => $nominal,
                        'created_at' => $orderTime,
                        'updated_at' => $orderTime,
                    ]);
                }
            }
        }
    }
}
