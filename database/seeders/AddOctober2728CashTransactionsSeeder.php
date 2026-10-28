<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddOctober2728CashTransactionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info("Menambahkan 24 transaksi POS Cash Only untuk 27-28 Oktober 2026...");

        $cashier = User::where('role', 'kasir')->first() ?? User::where('email', 'yuliana@pos.com')->first();
        $cashierId = $cashier ? $cashier->id : 2;

        $allProducts = Product::where('is_active', true)->get();
        $customerIds = Customer::pluck('id')->toArray();

        // 24 transaksi dibagi ke 27 dan 28 Oktober:
        // 27 Okt: 14 transaksi, 28 Okt: 10 transaksi
        $distribution = [
            '2026-10-27' => 14,
            '2026-10-28' => 10,
        ];

        // Dapatkan order ID terakhir di database
        $nextOrderId = (int) DB::table('orders')->max('id') + 1;

        $ordersToInsert = [];
        $itemsToInsert = [];

        // Dari 24 transaksi, mayoritas (18 tx / 75%) bernominal kecil (< 65rb), 6 tx bernominal sedang (70rb - 380rb, max 400rb)
        $txProfiles = [
            // 27 Oktober (14 tx): 10 kecil, 4 sedang
            '2026-10-27' => ['small', 'small', 'medium', 'small', 'small', 'small', 'medium', 'small', 'small', 'medium', 'small', 'small', 'small', 'medium'],
            // 28 Oktober (10 tx): 8 kecil, 2 sedang
            '2026-10-28' => ['small', 'small', 'small', 'medium', 'small', 'small', 'small', 'medium', 'small', 'small'],
        ];

        $totalNewOrders = 0;
        $totalNewItems = 0;

        foreach ($distribution as $dateStr => $txCount) {
            $carbonDate = Carbon::parse($dateStr);
            $profiles = $txProfiles[$dateStr];

            // Waktu acak antara 08:00 s.d. 21:30 dan diurutkan kronologis
            $secondOffsets = [];
            for ($i = 0; $i < $txCount; $i++) {
                $secondOffsets[] = rand(8 * 3600, 21 * 3600 + 1800);
            }
            sort($secondOffsets);

            // Generate kode 4 angka belakang yang acak & unik (tidak berpola 0001, 0002)
            $dayRandomCodes = [];
            while (count($dayRandomCodes) < $txCount) {
                $code = (string) rand(1000, 9999);
                $dayRandomCodes[$code] = $code;
            }
            $dayRandomCodes = array_values($dayRandomCodes);

            for ($seq = 1; $seq <= $txCount; $seq++) {
                $orderId = $nextOrderId++;
                $timeOffset = $secondOffsets[$seq - 1];
                $orderTime = $carbonDate->copy()->startOfDay()->addSeconds($timeOffset);
                $timeStr = $orderTime->toDateTimeString();

                $randomCode = $dayRandomCodes[$seq - 1];
                $invoiceNumber = 'INV-' . $carbonDate->format('Ymd') . '-' . $randomCode;
                $profile = $profiles[$seq - 1];

                $customerId = null;
                if (!empty($customerIds) && rand(1, 100) <= 20) {
                    $customerId = $customerIds[array_rand($customerIds)];
                }

                $orderItems = [];
                $subtotal = 0;

                if ($profile === 'small') {
                    // NOMINAL KECIL: Belanja 1 s.d. 2 item murah (Rp 3.500 - Rp 60.000)
                    $cheapProducts = $allProducts->where('selling_price', '<=', 35000);
                    $itemCount = rand(1, 2);
                    $picked = $cheapProducts->random(min($itemCount, $cheapProducts->count()));

                    foreach ($picked as $prod) {
                        $qty = rand(1, 3);
                        $unitPrice = (float) $prod->selling_price;
                        $lineTotal = $unitPrice * $qty;

                        // Jaga agar total kecil tetap di bawah 65.000
                        if ($subtotal > 0 && ($subtotal + $lineTotal) > 65000) {
                            $qty = 1;
                            $lineTotal = $unitPrice * $qty;
                        }

                        $subtotal += $lineTotal;
                        $orderItems[] = [
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
                } else {
                    // NOMINAL SEDANG: Belanja 2 s.d. 4 item (Maksimal ketat Rp 400.000)
                    $midProducts = $allProducts->where('selling_price', '<=', 150000);
                    $itemCount = rand(2, 4);
                    $picked = $midProducts->random(min($itemCount, $midProducts->count()));

                    foreach ($picked as $prod) {
                        $unitPrice = (float) $prod->selling_price;
                        $qty = ($unitPrice >= 100000) ? 1 : rand(1, 3);

                        // Hard cap: Tidak boleh lebih dari Rp 400.000
                        if ($subtotal > 0 && ($subtotal + ($unitPrice * $qty)) > 400000) {
                            $remaining = 400000 - $subtotal;
                            $possibleQty = (int) floor($remaining / $unitPrice);
                            if ($possibleQty >= 1) {
                                $qty = $possibleQty;
                            } else {
                                continue;
                            }
                        }

                        $lineTotal = $unitPrice * $qty;
                        $subtotal += $lineTotal;
                        $orderItems[] = [
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

                // Fallback jika kosong
                if (empty($orderItems)) {
                    $prod = $allProducts->where('selling_price', '<=', 15000)->random();
                    $unitPrice = (float) $prod->selling_price;
                    $qty = 1;
                    $lineTotal = $unitPrice * $qty;
                    $subtotal = $lineTotal;
                    $orderItems[] = [
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

                $totalAmount = $subtotal;

                // PAYMENT CASH ONLY: Uang pecahan tunai
                if ($totalAmount <= 10000) {
                    $possibleBills = [10000, 20000, 50000];
                } elseif ($totalAmount <= 20000) {
                    $possibleBills = [20000, 50000];
                } elseif ($totalAmount <= 50000) {
                    $possibleBills = [50000, 100000];
                } elseif ($totalAmount <= 100000) {
                    $possibleBills = [100000];
                } elseif ($totalAmount <= 200000) {
                    $possibleBills = [200000, 250000];
                } else {
                    $next50 = (ceil($totalAmount / 50000) * 50000);
                    $next100 = (ceil($totalAmount / 100000) * 100000);
                    $possibleBills = [$next50, $next100];
                }
                $validBills = array_filter($possibleBills, fn($b) => $b >= $totalAmount);
                $amountPaid = !empty($validBills) ? $validBills[array_rand($validBills)] : $totalAmount;
                $change = $amountPaid - $totalAmount;

                $ordersToInsert[] = [
                    'id' => $orderId,
                    'user_id' => $cashierId,
                    'customer_id' => $customerId,
                    'channel' => 'pos',
                    'invoice_number' => $invoiceNumber,
                    'subtotal' => $subtotal,
                    'discount' => 0,
                    'tax' => 0,
                    'total_amount' => $totalAmount,
                    'payment_method' => 'cash', // PAYMENT CASH ONLY
                    'amount_paid' => $amountPaid,
                    'change' => $change,
                    'payment_status' => 'paid',
                    'qris_reference_id' => null,
                    'qris_qr_string' => null,
                    'qris_expires_at' => null,
                    'notes' => null,
                    'created_at' => $timeStr,
                    'updated_at' => $timeStr,
                ];

                foreach ($orderItems as $it) {
                    $itemsToInsert[] = $it;
                    $totalNewItems++;
                }

                $totalNewOrders++;
            }
        }

        // Insert ke database
        DB::table('orders')->insert($ordersToInsert);
        DB::table('order_items')->insert($itemsToInsert);

        $this->command->info("Selesai menambahkan {$totalNewOrders} transaksi baru ({$totalNewItems} items).");
    }
}
