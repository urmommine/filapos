<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\StoreSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin User
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@pos.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Create Kasir User
        User::create([
            'name' => 'Kasir 1',
            'email' => 'kasir@pos.com',
            'password' => Hash::make('password'),
            'role' => 'kasir',
        ]);

        // Create Categories
        $categories = [
            ['name' => 'Minuman', 'slug' => 'minuman', 'description' => 'Berbagai jenis minuman'],
            ['name' => 'Makanan', 'slug' => 'makanan', 'description' => 'Berbagai jenis makanan'],
            ['name' => 'Snack', 'slug' => 'snack', 'description' => 'Camilan dan snack'],
            ['name' => 'Rokok', 'slug' => 'rokok', 'description' => 'Produk rokok'],
            ['name' => 'Lainnya', 'slug' => 'lainnya', 'description' => 'Produk lainnya'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        // Create Sample Products
        $products = [
            // Minuman
            ['category_id' => 1, 'name' => 'Kopi Arabica', 'sku' => 'MNM-001', 'barcode' => '8991001001', 'purchase_price' => 15000, 'selling_price' => 25000, 'stock' => 50, 'min_stock' => 10],
            ['category_id' => 1, 'name' => 'Es Teh Manis', 'sku' => 'MNM-002', 'barcode' => '8991001002', 'purchase_price' => 3000, 'selling_price' => 8000, 'stock' => 100, 'min_stock' => 20],
            ['category_id' => 1, 'name' => 'Susu Segar', 'sku' => 'MNM-003', 'barcode' => '8991001003', 'purchase_price' => 5000, 'selling_price' => 10000, 'stock' => 30, 'min_stock' => 10],
            ['category_id' => 1, 'name' => 'Jus Jeruk', 'sku' => 'MNM-004', 'barcode' => '8991001004', 'purchase_price' => 8000, 'selling_price' => 15000, 'stock' => 25, 'min_stock' => 5],
            ['category_id' => 1, 'name' => 'Air Mineral 600ml', 'sku' => 'MNM-005', 'barcode' => '8991001005', 'purchase_price' => 2000, 'selling_price' => 4000, 'stock' => 200, 'min_stock' => 50],
            
            // Makanan
            ['category_id' => 2, 'name' => 'Nasi Goreng', 'sku' => 'MKN-001', 'barcode' => '8992001001', 'purchase_price' => 12000, 'selling_price' => 20000, 'stock' => 0, 'min_stock' => 5],
            ['category_id' => 2, 'name' => 'Mie Goreng', 'sku' => 'MKN-002', 'barcode' => '8992001002', 'purchase_price' => 10000, 'selling_price' => 18000, 'stock' => 40, 'min_stock' => 10],
            ['category_id' => 2, 'name' => 'Roti Bakar', 'sku' => 'MKN-003', 'barcode' => '8992001003', 'purchase_price' => 8000, 'selling_price' => 15000, 'stock' => 20, 'min_stock' => 5],
            ['category_id' => 2, 'name' => 'Ayam Goreng', 'sku' => 'MKN-004', 'barcode' => '8992001004', 'purchase_price' => 15000, 'selling_price' => 25000, 'stock' => 15, 'min_stock' => 5],
            
            // Snack
            ['category_id' => 3, 'name' => 'Keripik Kentang', 'sku' => 'SNK-001', 'barcode' => '8993001001', 'purchase_price' => 5000, 'selling_price' => 10000, 'stock' => 60, 'min_stock' => 15],
            ['category_id' => 3, 'name' => 'Cokelat Bar', 'sku' => 'SNK-002', 'barcode' => '8993001002', 'purchase_price' => 8000, 'selling_price' => 12000, 'stock' => 3, 'min_stock' => 10], // Low stock
            ['category_id' => 3, 'name' => 'Kacang Goreng', 'sku' => 'SNK-003', 'barcode' => '8993001003', 'purchase_price' => 4000, 'selling_price' => 8000, 'stock' => 45, 'min_stock' => 10],
            ['category_id' => 3, 'name' => 'Biskuit', 'sku' => 'SNK-004', 'barcode' => '8993001004', 'purchase_price' => 6000, 'selling_price' => 10000, 'stock' => 2, 'min_stock' => 10], // Low stock
            
            // Rokok
            ['category_id' => 4, 'name' => 'Rokok Filter A', 'sku' => 'RKK-001', 'barcode' => '8994001001', 'purchase_price' => 20000, 'selling_price' => 25000, 'stock' => 100, 'min_stock' => 20],
            ['category_id' => 4, 'name' => 'Rokok Filter B', 'sku' => 'RKK-002', 'barcode' => '8994001002', 'purchase_price' => 18000, 'selling_price' => 23000, 'stock' => 80, 'min_stock' => 20],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }

        // Store Settings
        StoreSetting::set(StoreSetting::STORE_NAME, 'Antigravity Store');
        StoreSetting::set(StoreSetting::STORE_ADDRESS, 'Jl. Contoh No. 123, Kota');
        StoreSetting::set(StoreSetting::STORE_PHONE, '0812-3456-789');
        StoreSetting::set(StoreSetting::STORE_EMAIL, 'info@antigravity.com');
        StoreSetting::set(StoreSetting::TAX_PERCENTAGE, '0'); // 0% tax by default
        StoreSetting::set(StoreSetting::RECEIPT_FOOTER, 'Terima Kasih Sudah Berbelanja!');
        StoreSetting::set(StoreSetting::PRINTER_TYPE, 'usb');
    }
}
