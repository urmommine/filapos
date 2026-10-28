<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HistoricalMonthlySalesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $allProducts = Product::where('is_active', true)->get();
        if ($allProducts->isEmpty()) {
            $allProducts = Product::all();
        }

        $largeProducts = $allProducts->filter(fn($p) => $p->selling_price >= 100000)->values();
        $mediumProducts = $allProducts->filter(fn($p) => $p->selling_price >= 20000 && $p->selling_price < 100000)->values();
        $smallProducts = $allProducts->filter(fn($p) => $p->selling_price < 20000)->values();

        $cashiers = User::where('role', 'kasir')->get();
        $cashierIds = $cashiers->isNotEmpty() ? $cashiers->pluck('id')->toArray() : [2];

        $customers = Customer::all();
        $customerIds = $customers->isNotEmpty() ? $customers->pluck('id')->toArray() : [null];
        $customerIds[] = null; // Walk-in

        // Jadwal transaksi bulanan untuk mengisi gap Mei s/d September 2026 (+ awal Oktober)
        $monthSchedules = [
            // Mei 2026 (lanjutan setelah tgl 5)
            ['year' => 2026, 'month' => 5, 'days' => [8, 12, 15, 19, 22, 26, 29, 31]],
            // Juni 2026
            ['year' => 2026, 'month' => 6, 'days' => [2, 5, 7, 9, 12, 14, 16, 19, 21, 23, 26, 28, 30]],
            // Juli 2026
            ['year' => 2026, 'month' => 7, 'days' => [3, 5, 8, 10, 12, 15, 17, 19, 22, 24, 26, 29, 31]],
            // Agustus 2026
            ['year' => 2026, 'month' => 8, 'days' => [2, 4, 7, 9, 11, 14, 16, 18, 21, 23, 25, 28, 30]],
            // September 2026
            ['year' => 2026, 'month' => 9, 'days' => [2, 4, 6, 9, 11, 13, 16, 18, 20, 23, 25, 27, 30]],
            // Awal s/d Pertengahan Oktober 2026 (sebelum tgl 21)
            ['year' => 2026, 'month' => 10, 'days' => [2, 4, 6, 8, 11, 13, 15, 17, 19, 20]],
        ];

        $totalInserted = 0;

        DB::beginTransaction();
        try {
            foreach ($monthSchedules as $sched) {
                $year = $sched['year'];
                $month = $sched['month'];

                foreach ($sched['days'] as $day) {
                    // 1 - 2 transaksi per hari terpilih
                    $ordersInDay = rand(1, 2);

                    for ($seq = 1; $seq <= $ordersInDay; $seq++) {
                        $hour = rand(8, 20);
                        $minute = rand(0, 59);
                        $second = rand(0, 59);
                        $orderTime = Carbon::create($year, $month, $day, $hour, $minute, $second);
                        $dateStr = $orderTime->format('Ymd');

                        $invoiceNumber = 'INV-' . $dateStr . '-' . str_pad((string)$seq, 4, '0', STR_PAD_LEFT);

                        // Cek apakah invoice ini sudah pernah ada agar tidak duplikat
                        if (Order::where('invoice_number', $invoiceNumber)->exists()) {
                            continue;
                        }

                        // ~35% transaksi agak besar (ratusan ribu), ~65% transaksi biasa/sedang
                        $isLarge = (rand(1, 100) <= 35);
                        $subtotal = 0;
                        $itemsData = [];

                        if ($isLarge && $largeProducts->isNotEmpty()) {
                            // 1 produk besar atau 1 produk besar + 1 medium
                            if (rand(1, 2) === 1) {
                                $p = $largeProducts->random();
                                $subtotal += $p->selling_price;
                                $itemsData[] = [
                                    'product_id' => $p->id,
                                    'product_name' => $p->name,
                                    'quantity' => 1,
                                    'unit_price' => $p->selling_price,
                                    'total_price' => $p->selling_price,
                                ];
                            } else {
                                $p1 = $largeProducts->random();
                                $p2 = $mediumProducts->isNotEmpty() ? $mediumProducts->random() : $smallProducts->random();
                                $subtotal += ($p1->selling_price + $p2->selling_price);
                                $itemsData[] = [
                                    'product_id' => $p1->id,
                                    'product_name' => $p1->name,
                                    'quantity' => 1,
                                    'unit_price' => $p1->selling_price,
                                    'total_price' => $p1->selling_price,
                                ];
                                $itemsData[] = [
                                    'product_id' => $p2->id,
                                    'product_name' => $p2->name,
                                    'quantity' => 1,
                                    'unit_price' => $p2->selling_price,
                                    'total_price' => $p2->selling_price,
                                ];
                            }
                        } else {
                            // Transaksi reguler (1 - 3 produk kecil/sedang)
                            $num = rand(1, 3);
                            $picked = $smallProducts->random($num);
                            foreach ($picked as $p) {
                                $q = rand(1, 2);
                                $line = $p->selling_price * $q;
                                $subtotal += $line;
                                $itemsData[] = [
                                    'product_id' => $p->id,
                                    'product_name' => $p->name,
                                    'quantity' => $q,
                                    'unit_price' => $p->selling_price,
                                    'total_price' => $line,
                                ];
                            }
                        }

                        $methodRand = rand(1, 100);
                        if ($methodRand <= 55) {
                            $method = 'cash';
                            $amountPaid = ceil($subtotal / 10000) * 10000;
                            if ($amountPaid < $subtotal) $amountPaid = $subtotal;
                            $change = $amountPaid - $subtotal;
                            $notes = null;
                        } elseif ($methodRand <= 80) {
                            $method = 'transfer';
                            $amountPaid = $subtotal;
                            $change = 0;
                            $notes = null;
                        } else {
                            $method = 'qris';
                            $amountPaid = $subtotal;
                            $change = 0;
                            $notes = 'Pembayaran QRIS';
                        }

                        $order = Order::create([
                            'user_id' => $cashierIds[array_rand($cashierIds)],
                            'customer_id' => $customerIds[array_rand($customerIds)],
                            'invoice_number' => $invoiceNumber,
                            'subtotal' => $subtotal,
                            'discount' => 0,
                            'tax' => 0,
                            'total_amount' => $subtotal,
                            'payment_method' => $method,
                            'amount_paid' => $amountPaid,
                            'change' => $change,
                            'payment_status' => 'paid',
                            'qris_reference_id' => null,
                            'notes' => $notes,
                        ]);

                        // Set timestamp historis persis
                        DB::table('orders')->where('id', $order->id)->update([
                            'created_at' => $orderTime,
                            'updated_at' => $orderTime,
                        ]);

                        foreach ($itemsData as $it) {
                            $it['order_id'] = $order->id;
                            $it['created_at'] = $orderTime;
                            $it['updated_at'] = $orderTime;
                            OrderItem::create($it);
                        }

                        $totalInserted++;
                    }
                }
            }

            DB::commit();
            $this->command->info("Berhasil menambahkan {$totalInserted} transaksi historis bulanan (Mei - September 2026).");
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->command->error("Gagal menambahkan transaksi: " . $e->getMessage());
        }
    }
}
