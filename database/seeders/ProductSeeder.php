<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [1, 2, 3, 4]; // Assumes categories 1-4 exist from DatabaseSeeder
        $names = ['Super', 'Extra', 'Mega', 'Ultra', 'Premium', 'Classic', 'Deluxe', 'Lite', 'Pro', 'Max'];
        $items = ['Coffee', 'Tea', 'Cake', 'Bread', 'Chip', 'Soda', 'Water', 'Juice', 'Milk', 'Candy'];

        for ($i = 0; $i < 20; $i++) {
            $name = $names[array_rand($names)] . ' ' . $items[array_rand($items)] . ' ' . rand(1, 100);
            
            Product::create([
                'category_id' => $categories[array_rand($categories)],
                'name' => $name,
                'sku' => 'TEST-' . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                'barcode' => '899' . rand(1000000, 9999999),
                'purchase_price' => rand(50, 200) * 100,
                'selling_price' => rand(250, 500) * 100,
                'stock' => rand(10, 100),
                'min_stock' => 10,
                'is_active' => true,
            ]);
        }
    }
}
