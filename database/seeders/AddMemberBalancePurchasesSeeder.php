<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\WalletTransaction;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddMemberBalancePurchasesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info("Menambahkan transaksi PPOB pembayaran via Saldo (INV- Lama)...");

        // Ambil member yang memiliki saldo cukup (>= 5.000)
        $members = User::where('role', 'user')->where('balance', '>=', 50000)->get();
        if ($members->isEmpty()) {
            $members = User::where('role', 'user')->get();
        }

        if ($members->isEmpty()) {
            $this->command->error("Tidak ada user member!");
            return;
        }

        // Katalog produk digital untuk pembelian via saldo
        $products = [
            [
                'nominal' => 10000,
                'name' => 'Top Up Mobile Legends - 28 Diamonds',
                'game' => 'Mobile Legends',
                'slug' => 'mobile-legends',
                'type' => 'game',
            ],
            [
                'nominal' => 20000,
                'name' => 'Token Listrik PLN Rp 20.000',
                'game' => 'PLN Prepaid',
                'slug' => 'pln-prepaid',
                'type' => 'pln',
            ],
            [
                'nominal' => 25000,
                'name' => 'Pulsa Telkomsel Rp 25.000',
                'game' => 'Telkomsel',
                'slug' => 'telkomsel-12',
                'type' => 'pulsa',
            ],
            [
                'nominal' => 5000,
                'name' => 'Top Up Mobile Legends - 12 Diamonds',
                'game' => 'Mobile Legends',
                'slug' => 'mobile-legends',
                'type' => 'game',
            ],
            [
                'nominal' => 10000,
                'name' => 'Top Up Free Fire - 70 Diamonds',
                'game' => 'Free Fire',
                'slug' => 'free-fire',
                'type' => 'game',
            ],
            [
                'nominal' => 15000,
                'name' => 'Paket Data Tri Happy 2GB 7hr',
                'game' => 'Tri (3)',
                'slug' => '3-4',
                'type' => 'data',
            ],
            [
                'nominal' => 50000,
                'name' => 'Token Listrik PLN Rp 50.000',
                'game' => 'PLN Prepaid',
                'slug' => 'pln-prepaid',
                'type' => 'pln',
            ],
            [
                'nominal' => 25000,
                'name' => 'Top Up Free Fire - 140 Diamonds',
                'game' => 'Free Fire',
                'slug' => 'free-fire',
                'type' => 'game',
            ],
        ];

        DB::beginTransaction();

        try {
            $totalAdded = 0;
            $days = ['20261027', '20261028'];

            // Tambahkan 26 transaksi pembayaran via Saldo (14 di 27 Okt, 12 di 28 Okt)
            for ($i = 0; $i < 26; $i++) {
                $dayStr = $days[$i % 2];
                $year = substr($dayStr, 0, 4);
                $month = substr($dayStr, 4, 2);
                $day = substr($dayStr, 6, 2);

                $hour = rand(8, 22);
                $minute = rand(5, 55);
                $second = rand(10, 50);
                $createdAt = Carbon::create($year, $month, $day, $hour, $minute, $second);
                $timeStr = $createdAt->toDateTimeString();

                // Format Invoice Lama Kasir/Sistem: INV-YYYYMMDD-XXXX (4 angka random acak)
                $randomCode = (string) rand(1000, 9999);
                $invoiceNumber = "INV-{$dayStr}-{$randomCode}";

                // Pilih member & produk
                $member = $members[$i % $members->count()];
                $prod = $products[$i % count($products)];
                $nominal = (float) $prod['nominal'];

                // Pastikan saldo member mencukupi
                $currentBalance = (float) $member->balance;
                if ($currentBalance < $nominal) {
                    $member->balance = $currentBalance + 100000;
                    $member->save();
                    $currentBalance = (float) $member->balance;
                }

                $newBalance = $currentBalance - $nominal;
                $member->balance = $newBalance;
                $member->save();

                $playerId = (string) rand(10000000, 99999999);
                $zoneId = (string) rand(2000, 2999);
                $sn = 'MELO/' . $dayStr . '/' . strtoupper(substr(md5($invoiceNumber), 0, 10));

                $notesData = [
                    'type' => $prod['type'],
                    'game' => $prod['game'],
                    'game_slug' => $prod['slug'],
                    'target' => $playerId,
                    'zone' => $zoneId,
                    'nickname' => $member->name,
                    'customer_phone' => $member->phone ?: '08129876543',
                    'sn' => $sn,
                    'melo_status' => 'success',
                    'payment_source' => 'member_wallet_balance',
                ];

                // 1. Buat Order PPOB via Saldo
                $order = Order::create([
                    'user_id' => $member->id,
                    'customer_id' => null,
                    'channel' => 'ppob',
                    'invoice_number' => $invoiceNumber, // Format LAMA: INV-YYYYMMDD-XXXX
                    'subtotal' => $nominal,
                    'discount' => 0,
                    'tax' => 0,
                    'total_amount' => $nominal,
                    'payment_method' => 'balance', // Pembayaran VIA SALDO
                    'amount_paid' => $nominal,
                    'change' => 0,
                    'payment_status' => 'paid',
                    'qris_reference_id' => null, // Bukan QRIS Border!
                    'qris_qr_string' => null,
                    'notes' => json_encode($notesData),
                    'created_at' => $timeStr,
                    'updated_at' => $timeStr,
                ]);

                // 2. Buat Order Item
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => null,
                    'product_name' => $prod['name'],
                    'quantity' => 1,
                    'unit_price' => $nominal,
                    'total_price' => $nominal,
                    'created_at' => $timeStr,
                    'updated_at' => $timeStr,
                ]);

                // 3. Buat Catatan Mutasi Wallet Transaction (Type: payment)
                WalletTransaction::create([
                    'user_id' => $member->id,
                    'reference_id' => $invoiceNumber,
                    'order_id' => $order->id,
                    'type' => 'payment',
                    'amount' => $nominal,
                    'opening_balance' => $currentBalance,
                    'closing_balance' => $newBalance,
                    'status' => 'completed',
                    'payment_method' => 'balance',
                    'description' => "Pembayaran Order PPOB #{$invoiceNumber}",
                    'metadata' => [
                        'invoice_number' => $invoiceNumber,
                        'product_name' => $prod['name'],
                        'nominal' => $nominal,
                    ],
                    'created_at' => $timeStr,
                    'updated_at' => $timeStr,
                ]);

                $totalAdded++;
            }

            DB::commit();

            $this->command->info("Selesai! Berhasil menambahkan {$totalAdded} transaksi PPOB via Saldo Akun Member.");
            $this->command->info("Format Invoice: 100% menggunakan format lama INV-YYYYMMDD-XXXX.");
            $this->command->info("Payment Method: 100% 'balance'.");
            $this->command->info("Mutasi Saldo: Terpotong dan tercatat di WalletTransaction (type: payment).");
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->command->error("Terjadi error: " . $e->getMessage() . " on line " . $e->getLine());
        }
    }
}
