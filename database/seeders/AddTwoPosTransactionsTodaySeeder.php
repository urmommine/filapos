<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddTwoPosTransactionsTodaySeeder extends Seeder
{
    public function run(): void
    {
        // 1. Cari Kasir Yuliana
        $kasir = User::where('name', 'like', '%Yuliana%')->first();
        if (!$kasir) {
            $kasir = User::where('role', 'kasir')->first() ?? User::first();
        }

        echo "Menggunakan Kasir: {$kasir->name} (ID: {$kasir->id})\n";

        // 2. Ambil produk real inventory bernilai > 20.000
        $p1 = Product::where('name', 'like', '%Gajah Duduk%')->where('stock', '>', 5)->first()
            ?? Product::where('selling_price', '>=', 25000)->where('stock', '>', 5)->first();

        $p2 = Product::where('name', 'like', '%Downy%')->where('stock', '>', 5)->first()
            ?? Product::where('id', '!=', $p1->id)->where('selling_price', '>=', 20000)->where('stock', '>', 5)->first();

        if (!$p1 || !$p2) {
            echo "Produk real inventory tidak mencukupi.\n";
            return;
        }

        DB::beginTransaction();
        try {
            // Waktu transaksi hari ini 29 Oktober 2026
            $time1 = Carbon::parse('2026-10-29 09:45:15');
            $time2 = Carbon::parse('2026-10-29 09:48:30');

            // ----------------------------------------------------
            // TRANSAKSI 1: Produk 1 (Gajah Duduk / Real Inventory > 20rb)
            // ----------------------------------------------------
            $subtotal1 = (float) $p1->selling_price;
            $paid1 = ceil($subtotal1 / 50000) * 50000;
            if ($paid1 <= $subtotal1) {
                $paid1 = $subtotal1 + 10000;
            }
            $change1 = $paid1 - $subtotal1;
            $inv1 = 'INV-20261029-' . str_pad((string) random_int(1000, 9999), 4, '0', STR_PAD_LEFT);

            $order1 = Order::create([
                'user_id' => $kasir->id,
                'customer_id' => null,
                'channel' => 'pos',
                'invoice_number' => $inv1,
                'subtotal' => $subtotal1,
                'discount' => 0,
                'tax' => 0,
                'total_amount' => $subtotal1,
                'payment_method' => 'cash',
                'amount_paid' => $paid1,
                'change' => $change1,
                'payment_status' => 'paid',
                'created_at' => $time1,
                'updated_at' => $time1,
            ]);

            OrderItem::create([
                'order_id' => $order1->id,
                'product_id' => $p1->id,
                'product_name' => $p1->name,
                'quantity' => 1,
                'unit_price' => $subtotal1,
                'total_price' => $subtotal1,
                'created_at' => $time1,
                'updated_at' => $time1,
            ]);

            $p1->reduceStock(1);

            // Pastikan timestamp ter-update dengan tanggal 29 Oktober 2026
            $order1->timestamps = false;
            $order1->created_at = $time1;
            $order1->updated_at = $time1;
            $order1->save();

            // ----------------------------------------------------
            // TRANSAKSI 2: Produk 2 (Downy / Real Inventory > 20rb)
            // ----------------------------------------------------
            $subtotal2 = (float) $p2->selling_price;
            $paid2 = ceil($subtotal2 / 50000) * 50000;
            if ($paid2 <= $subtotal2) {
                $paid2 = $subtotal2 + 10000;
            }
            $change2 = $paid2 - $subtotal2;
            $inv2 = 'INV-20261029-' . str_pad((string) random_int(1000, 9999), 4, '0', STR_PAD_LEFT);

            $order2 = Order::create([
                'user_id' => $kasir->id,
                'customer_id' => null,
                'channel' => 'pos',
                'invoice_number' => $inv2,
                'subtotal' => $subtotal2,
                'discount' => 0,
                'tax' => 0,
                'total_amount' => $subtotal2,
                'payment_method' => 'cash',
                'amount_paid' => $paid2,
                'change' => $change2,
                'payment_status' => 'paid',
                'created_at' => $time2,
                'updated_at' => $time2,
            ]);

            OrderItem::create([
                'order_id' => $order2->id,
                'product_id' => $p2->id,
                'product_name' => $p2->name,
                'quantity' => 1,
                'unit_price' => $subtotal2,
                'total_price' => $subtotal2,
                'created_at' => $time2,
                'updated_at' => $time2,
            ]);

            $p2->reduceStock(1);

            $order2->timestamps = false;
            $order2->created_at = $time2;
            $order2->updated_at = $time2;
            $order2->save();

            DB::commit();

            echo "Berhasil menambahkan 2 transaksi POS tanggal 29 Oktober 2026:\n";
            echo "1. Invoice: {$order1->invoice_number} | Produk: {$p1->name} (1x) | Total: Rp " . number_format($subtotal1, 0, ',', '.') . " | Bayar (Cash): Rp " . number_format($paid1, 0, ',', '.') . " | Kembali: Rp " . number_format($change1, 0, ',', '.') . " | Waktu: {$time1->toDateTimeString()} | Kasir: {$kasir->name}\n";
            echo "2. Invoice: {$order2->invoice_number} | Produk: {$p2->name} (1x) | Total: Rp " . number_format($subtotal2, 0, ',', '.') . " | Bayar (Cash): Rp " . number_format($paid2, 0, ',', '.') . " | Kembali: Rp " . number_format($change2, 0, ',', '.') . " | Waktu: {$time2->toDateTimeString()} | Kasir: {$kasir->name}\n";

        } catch (\Throwable $e) {
            DB::rollBack();
            echo "Error: " . $e->getMessage() . "\n";
            throw $e;
        }
    }
}
