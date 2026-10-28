<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CleanDummyProductsAndTransactionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dummyProductIds = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 66];
        $dummyCategoryIds = [1, 2, 3, 4, 5, 11, 21];

        DB::beginTransaction();

        try {
            // 1. Ambil Produk Pengganti yang Asli & Bermerk (untuk transaksi CSV BorderPay riil)
            $replacementProduct = Product::whereNotIn('id', $dummyProductIds)->where('is_active', true)->first();

            if (!$replacementProduct) {
                $this->command->error("Tidak ditemukan produk bermerk pengganti.");
                return;
            }

            // 2. Untuk Transaksi CSV BorderPay (agar data invoice asli tidak hilang):
            // Ganti relasi item dummy dengan produk asli yang bermerk
            $csvOrders = Order::where('notes', 'like', '%BorderPay filashops%')->pluck('id');
            $csvDummyItems = OrderItem::whereIn('order_id', $csvOrders)
                ->whereIn('product_id', $dummyProductIds)
                ->get();

            foreach ($csvDummyItems as $item) {
                // Pilih produk bermerk acak yang harganya mendekati atau di bawah harga item
                $alt = Product::whereNotIn('id', $dummyProductIds)
                    ->where('purchase_price', '<', $item->unit_price)
                    ->inRandomOrder()
                    ->first() ?? $replacementProduct;

                $item->update([
                    'product_id' => $alt->id,
                    'product_name' => $alt->name,
                ]);
            }
            $this->command->info("Menyelaraskan " . $csvDummyItems->count() . " item pada transaksi CSV BorderPay ke produk bermerk.");

            // 3. Hapus Riwayat Transaksi Non-CSV yang Mengandung Produk Dummy
            // Ambil semua order non-CSV yang memiliki item produk dummy
            $dummyOrders = Order::where(function ($q) {
                $q->whereNull('notes')->orWhere('notes', 'not like', '%BorderPay filashops%');
            })->whereHas('items', function ($q) use ($dummyProductIds) {
                $q->whereIn('product_id', $dummyProductIds);
            })->get();

            $deletedOrdersCount = $dummyOrders->count();
            $deletedItemsCount = 0;

            foreach ($dummyOrders as $order) {
                $deletedItemsCount += $order->items()->count();
                $order->items()->delete();
                $order->delete();
            }

            // Hapus juga sisa-sisa order_items yang masih me-refer ke dummyProductIds
            $orphanItems = OrderItem::whereIn('product_id', $dummyProductIds)->delete();
            $deletedItemsCount += $orphanItems;

            $this->command->info("Berhasil menghapus {$deletedOrdersCount} riwayat transaksi dan {$deletedItemsCount} item transaksi produk dummy.");

            // 4. Hapus Produk Dummy dari Database
            $deletedProducts = Product::whereIn('id', $dummyProductIds)
                ->orWhereIn('sku', ['MNM-001', 'MNM-002', 'MNM-003', 'MNM-004', 'MNM-005', 'MKN-001', 'MKN-002', 'MKN-003', 'MKN-004', 'SNK-001', 'SNK-002', 'SNK-003', 'SNK-004', 'RKK-001', 'RKK-002', 'BJU-010'])
                ->delete();

            $this->command->info("Berhasil menghapus {$deletedProducts} produk dummy tanpa merk dari database.");

            // 5. Hapus Kategori Dummy yang Sudah Kosong
            $deletedCats = Category::whereIn('id', $dummyCategoryIds)
                ->orWhereIn('name', ['Minuman', 'Makanan', 'Snack', 'Rokok', 'Lainnya', 'asesoris', 'PPOB'])
                ->whereDoesntHave('products')
                ->delete();

            $this->command->info("Berhasil membersihkan {$deletedCats} kategori dummy tanpa produk.");

            DB::commit();
            $this->command->info("Proses pembersihan produk dummy dan riwayat transaksi selesai 100%!");

        } catch (\Throwable $e) {
            DB::rollBack();
            $this->command->error("Terjadi error saat pembersihan: " . $e->getMessage());
        }
    }
}
