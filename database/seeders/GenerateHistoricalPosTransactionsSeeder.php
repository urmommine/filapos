<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class GenerateHistoricalPosTransactionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info("1. Membersihkan riwayat transaksi lama (orders & order_items)...");

        // Kosongkan tabel transaksi lama tanpa menghapus data master (produk, kategori, user, pelanggan)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('order_items')->truncate();
        DB::table('orders')->truncate();
        DB::table('wallet_transactions')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info("2. Memastikan Kasir Toko adalah Yuliana (ID 2)...");
        $yuliana = User::firstOrCreate(
            ['email' => 'yuliana@pos.com'],
            ['name' => 'Yuliana', 'password' => Hash::make('password'), 'role' => 'kasir']
        );
        $cashierId = $yuliana->id;

        // Ambil data master produk fisik toko (100% harga asli inventory)
        $allProducts = Product::where('is_active', true)->get();
        if ($allProducts->isEmpty()) {
            $this->command->error("Tidak ada produk aktif di tabel products!");
            return;
        }

        // Ambil pelanggan untuk variasi walk-in vs pelanggan terdaftar
        $customerIds = Customer::pluck('id')->toArray();
        $prodsBySku = $allProducts->keyBy('sku');

        // Nominal bersih/bulat yang persis ada di CSV beserta produk riilnya
        $csvMatchProducts = [
            5000 => [
                ['sku' => 'PRC-LFB-001', 'qty' => 1],
                ['sku' => 'SKIN-CARE-008', 'qty' => 1],
                ['sku' => 'MKN-SRM-001', 'qty' => 1],
            ],
            10000 => [
                ['sku' => 'SNK-PCK-001', 'qty' => 1],
                ['sku' => 'SNK-GRD-001', 'qty' => 1],
                ['sku' => 'PRC-LFB-001', 'qty' => 2],
            ],
            20000 => [
                ['sku' => 'HERBAL-007', 'qty' => 1],
                ['sku' => 'HSE-WPL-001', 'qty' => 1],
                ['sku' => 'SNK-PCK-001', 'qty' => 2],
            ],
            25000 => [
                ['sku' => 'HERBAL-003', 'qty' => 1],
            ],
            6000 => [
                ['sku' => 'MKN-POP-001', 'qty' => 1],
                ['sku' => 'SNK-TRO-001', 'qty' => 1],
                ['sku' => 'SNK-CHK-001', 'qty' => 1],
            ],
            5500 => [
                ['sku' => 'MNM-ICH-001', 'qty' => 1],
                ['sku' => 'MKN-MSK-001', 'qty' => 1],
            ],
            8500 => [
                ['sku' => 'MNM-PCR-001', 'qty' => 1],
                ['sku' => 'MNM-NBR-001', 'qty' => 1],
                ['sku' => 'MKN-LMN-001', 'qty' => 1],
                ['sku' => 'SNK-TNG-001', 'qty' => 1],
            ],
            8000 => [
                ['sku' => 'MNM-HYD-001', 'qty' => 1],
                ['sku' => 'SNK-MLK-001', 'qty' => 1],
                ['sku' => 'PRC-DTL-001', 'qty' => 1],
            ],
            7000 => [
                ['sku' => 'MNM-PND-001', 'qty' => 1],
            ],
            2000 => [
                ['sku' => 'MNM-KPL-001', 'qty' => 1],
            ],
            34500 => [
                ['sku' => 'SNK-KHG-001', 'qty' => 1],
            ],
        ];

        $this->command->info("3. Men-generate ~5.000 transaksi POS realistis (1 Juli 2026 s/d 26 Oktober 2026)...");

        $startDate = Carbon::create(2026, 7, 1);
        $endDate = Carbon::create(2026, 10, 26); // STRICT: Sampai 26 Oktober saja! 27 Oktober jangan diisi.

        $totalDays = $startDate->diffInDays($endDate) + 1;
        $currentDate = $startDate->copy();

        $totalOrdersCreated = 0;
        $totalItemsCreated = 0;

        // Siapkan penampung batch insert agar proses sangat cepat
        $orderBatch = [];
        $itemsBatch = [];
        $orderIdCounter = 1;

        while ($currentDate->lte($endDate)) {
            $dateStr = $currentDate->format('Ymd');
            $dayOfWeek = $currentDate->dayOfWeek; // 0 = Sun, 6 = Sat, 5 = Fri

            // Variasi transaksi harian yang natural:
            if ($dayOfWeek === Carbon::SATURDAY || $dayOfWeek === Carbon::SUNDAY) {
                $dailyTxCount = rand(50, 68);
            } elseif ($dayOfWeek === Carbon::FRIDAY) {
                $dailyTxCount = rand(42, 56);
            } else {
                $dailyTxCount = rand(34, 46);
            }

            // Generate detik acak dalam jam buka toko (07:30 - 22:00) dan urutkan waktu agar kronologis
            $secondOffsets = [];
            for ($i = 0; $i < $dailyTxCount; $i++) {
                $secondOffsets[] = rand(7 * 3600 + 1800, 22 * 3600);
            }
            sort($secondOffsets);

            // Generate kode 4 angka belakang yang acak & unik (tidak berpola 0001, 0002)
            $dayRandomCodes = [];
            while (count($dayRandomCodes) < $dailyTxCount) {
                $code = (string) rand(1000, 9999);
                $dayRandomCodes[$code] = $code;
            }
            $dayRandomCodes = array_values($dayRandomCodes);

            for ($seq = 1; $seq <= $dailyTxCount; $seq++) {
                $orderId = $orderIdCounter++;
                $secondOffset = $secondOffsets[$seq - 1];

                $orderTime = $currentDate->copy()->startOfDay()->addSeconds($secondOffset);
                $timeStr = $orderTime->toDateTimeString();

                // Format invoice standar kasir POS: INV-YYYYMMDD-XXXX (4 angka acak)
                $randomCode = $dayRandomCodes[$seq - 1];
                $invoiceNumber = 'INV-' . $dateStr . '-' . $randomCode;

                // Pelanggan: 75% Walk-in (null), 25% Terdaftar
                $customerId = null;
                if (!empty($customerIds) && rand(1, 100) <= 25) {
                    $customerId = $customerIds[array_rand($customerIds)];
                }

                $subtotal = 0;
                $orderItemsForThisOrder = [];

                // KHUSUS NOMINAL Rp 2.000: HANYA DIBUAT TEPAT 2 TRANSAKSI di seluruh riwayat!
                // 1 di bulan Juli (Cash) dan 1 di bulan September (QRIS)
                $isSpecialTwoThousand = ($dateStr === '20260715' && $seq === 1) || ($dateStr === '20260920' && $seq === 1);

                // Sekitar 25% transaksi adalah pembelian produk yang nominalnya SAMA PERSIS dengan nominal CSV (dan BUKAN ganjil)
                $isCsvNominalMatch = ($seq % 4 === 0);

                if ($isSpecialTwoThousand) {
                    $paymentMethod = ($dateStr === '20260715') ? 'cash' : 'qris';
                    $targetProd = $prodsBySku->get('MNM-KPL-001'); // Kopi Kapal Api Rp 2.000
                    if ($targetProd) {
                        $qty = 1;
                        $unitPrice = (float) $targetProd->selling_price;
                        $lineTotal = $unitPrice * $qty;
                        $subtotal = $lineTotal;

                        $orderItemsForThisOrder[] = [
                            'order_id' => $orderId,
                            'product_id' => $targetProd->id,
                            'product_name' => $targetProd->name,
                            'quantity' => $qty,
                            'unit_price' => $unitPrice,
                            'total_price' => $lineTotal,
                            'created_at' => $timeStr,
                            'updated_at' => $timeStr,
                        ];
                    }
                } elseif ($isCsvNominalMatch) {
                    // Metode pembayaran: Berimbang antara Cash (50%) dan QRIS (50%)
                    $paymentMethod = (rand(1, 100) <= 50) ? 'cash' : 'qris';

                    // Nominal bersih CSV TANPA 2.000 (karena Rp 2.000 sudah dibatasi tepat 2 saja)
                    $possibleNominals = [5000, 10000, 20000, 25000, 6000, 5500, 8500, 8000, 7000, 34500];
                    $targetNominal = $possibleNominals[array_rand($possibleNominals)];
                    $options = $csvMatchProducts[$targetNominal];
                    $selectedOption = $options[array_rand($options)];

                    $targetProd = $prodsBySku->get($selectedOption['sku']);
                    if ($targetProd) {
                        $qty = $selectedOption['qty'];
                        $unitPrice = (float) $targetProd->selling_price;
                        $lineTotal = $unitPrice * $qty;
                        $subtotal = $lineTotal;

                        $orderItemsForThisOrder[] = [
                            'order_id' => $orderId,
                            'product_id' => $targetProd->id,
                            'product_name' => $targetProd->name,
                            'quantity' => $qty,
                            'unit_price' => $unitPrice,
                            'total_price' => $lineTotal,
                            'created_at' => $timeStr,
                            'updated_at' => $timeStr,
                        ];
                    }
                } else {
                    // Metode pembayaran standar: Cash (67%), QRIS (28%), Transfer (5%)
                    $methodRoll = rand(1, 100);
                    if ($methodRoll <= 67) {
                        $paymentMethod = 'cash';
                    } elseif ($methodRoll <= 95) {
                        $paymentMethod = 'qris';
                    } else {
                        $paymentMethod = 'transfer';
                    }

                    // Tipe keranjang belanja beragam (Ringan, Sedang, Besar, Bulanan)
                    $basketRoll = rand(1, 100);
                    if ($basketRoll <= 50) {
                        $distinctCount = rand(1, 2);
                    } elseif ($basketRoll <= 85) {
                        $distinctCount = rand(2, 4);
                    } elseif ($basketRoll <= 96) {
                        $distinctCount = rand(3, 6);
                    } else {
                        $distinctCount = rand(4, 8);
                    }

                    // Kecualikan produk 2000 agar tidak menghasilkan transaksi 2000 tambahan
                    $non2000Products = $allProducts->where('sku', '!=', 'MNM-KPL-001');
                    $pickedProducts = $non2000Products->random(min($distinctCount, $non2000Products->count()));

                    foreach ($pickedProducts as $prod) {
                        $unitPrice = (float) $prod->selling_price;
                        if ($unitPrice <= 0) continue;

                        if ($unitPrice >= 300000) {
                            $qty = 1;
                        } elseif ($unitPrice >= 100000) {
                            $qty = rand(1, 2);
                        } elseif ($unitPrice >= 35000) {
                            $qty = rand(1, 3);
                        } else {
                            $qty = rand(1, 5);
                        }

                        if ($subtotal > 0 && ($subtotal + ($unitPrice * $qty)) > 1500000) {
                            $remainingBudget = 1500000 - $subtotal;
                            $possibleQty = (int) floor($remainingBudget / $unitPrice);
                            if ($possibleQty >= 1) {
                                $qty = $possibleQty;
                            } else {
                                continue;
                            }
                        }

                        $lineTotal = $unitPrice * $qty;
                        $subtotal += $lineTotal;

                        $orderItemsForThisOrder[] = [
                            'order_id' => $orderId,
                            'product_id' => $prod->id,
                            'product_name' => $prod->name,
                            'quantity' => $qty,
                            'unit_price' => $unitPrice,
                            'total_price' => $lineTotal,
                            'created_at' => $timeStr,
                            'updated_at' => $timeStr,
                        ];
                    }
                }

                // Fallback aman jika keranjang kosong
                if (empty($orderItemsForThisOrder)) {
                    $fallbackProd = $allProducts->where('selling_price', '<=', 50000)->random();
                    $unitPrice = (float) $fallbackProd->selling_price;
                    $qty = 1;
                    $lineTotal = $unitPrice * $qty;
                    $subtotal = $lineTotal;

                    $orderItemsForThisOrder[] = [
                        'order_id' => $orderId,
                        'product_id' => $fallbackProd->id,
                        'product_name' => $fallbackProd->name,
                        'quantity' => $qty,
                        'unit_price' => $unitPrice,
                        'total_price' => $lineTotal,
                        'created_at' => $timeStr,
                        'updated_at' => $timeStr,
                    ];
                }

                foreach ($orderItemsForThisOrder as $itemRow) {
                    $itemsBatch[] = $itemRow;
                    $totalItemsCreated++;
                }

                $discount = 0;
                $tax = 0;
                $totalAmount = $subtotal;

                // Hitung amount_paid & kembalian realistis
                if ($paymentMethod === 'cash') {
                    if ($totalAmount <= 10000) {
                        $possibleBills = [10000, 20000, 50000];
                    } elseif ($totalAmount <= 20000) {
                        $possibleBills = [20000, 50000];
                    } elseif ($totalAmount <= 50000) {
                        $possibleBills = [50000, 100000];
                    } elseif ($totalAmount <= 100000) {
                        $possibleBills = [100000];
                    } elseif ($totalAmount <= 500000) {
                        $next50 = (ceil($totalAmount / 50000) * 50000);
                        $next100 = (ceil($totalAmount / 100000) * 100000);
                        $possibleBills = [$next50, $next100];
                    } else {
                        // Di atas 500rb sampai 1.5jt, pembulatan ke kelipatan 100.000 terdekat
                        $next100 = (ceil($totalAmount / 100000) * 100000);
                        $possibleBills = [$next100];
                    }
                    $validBills = array_filter($possibleBills, fn($b) => $b >= $totalAmount);
                    $amountPaid = !empty($validBills) ? $validBills[array_rand($validBills)] : $totalAmount;
                    $change = $amountPaid - $totalAmount;
                } else {
                    $amountPaid = $totalAmount;
                    $change = 0;
                }

                $orderBatch[] = [
                    'id' => $orderId,
                    'user_id' => $cashierId, // Selalu Yuliana
                    'customer_id' => $customerId,
                    'channel' => 'pos',
                    'invoice_number' => $invoiceNumber,
                    'subtotal' => $subtotal,
                    'discount' => $discount,
                    'tax' => $tax,
                    'total_amount' => $totalAmount,
                    'payment_method' => $paymentMethod,
                    'amount_paid' => $amountPaid,
                    'change' => $change,
                    'payment_status' => 'paid',
                    'qris_reference_id' => null, // Sebelum era Border
                    'qris_qr_string' => null,
                    'qris_expires_at' => null,
                    'notes' => null,
                    'created_at' => $timeStr,
                    'updated_at' => $timeStr,
                ];
                $totalOrdersCreated++;

                // Lakukan chunk insert jika batch order sudah mencapai 500
                if (count($orderBatch) >= 500) {
                    DB::table('orders')->insert($orderBatch);
                    $orderBatch = [];
                    if (!empty($itemsBatch)) {
                        foreach (array_chunk($itemsBatch, 1000) as $chunk) {
                            DB::table('order_items')->insert($chunk);
                        }
                        $itemsBatch = [];
                    }
                }
            }

            $currentDate->addDay();
        }

        // Insert sisa batch terakhir
        if (!empty($orderBatch)) {
            DB::table('orders')->insert($orderBatch);
        }
        if (!empty($itemsBatch)) {
            foreach (array_chunk($itemsBatch, 1000) as $chunk) {
                DB::table('order_items')->insert($chunk);
            }
        }

        $this->command->info("Selesai!");
        $this->command->info("Total Transaksi POS Dibuat: {$totalOrdersCreated}");
        $this->command->info("Total Order Items Dibuat: {$totalItemsCreated}");
        $this->command->info("Periode: 1 Juli 2026 s/d 26 Oktober 2026 (Tanggal 27 Oktober bersih).");
        $this->command->info("Format Invoice: Seluruhnya INV-YYYYMMDD-XXXX.");
        $this->command->info("Kasir: 100% Yuliana.");
        $this->command->info("Harga: 100% sesuai harga jual asli master inventory (banyak item & qty beragam).");
    }
}
