<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class TriggerLowStockAlertSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil 80 produk aktif yang stoknya masih di atas min_stock
        $products = Product::where('is_active', true)
            ->whereColumn('stock', '>', 'min_stock')
            ->take(80)
            ->get();

        $updated = 0;
        foreach ($products as $product) {
            $minStock = $product->min_stock > 0 ? $product->min_stock : 5;
            // Kurangi stok menjadi 1 s/d 3 pcs (di bawah batas minimum)
            $newStock = rand(1, min(3, $minStock));

            $product->stock = $newStock;
            $product->save(); // Memicu event booted -> updated -> notifyLowStock()
            $updated++;
        }

        $this->command->info("Berhasil mengurangi stok pada {$updated} produk menjadi low stock.");

        // Proses antrean notifikasi jika antrean menggunakan database
        try {
            Artisan::call('queue:work', ['--stop-when-empty' => true]);
            $this->command->info("Antrean notifikasi low stock berhasil diproses.");
        } catch (\Throwable $e) {
            // Abaikan jika tidak ada queue worker
        }
    }
}
