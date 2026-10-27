<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Str;

class DummyProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Baju Muslim' => [
                ['name' => 'Rabbani Kerudung Instan', 'buy' => 45000, 'sell' => 65000],
                ['name' => 'Zoya Tunik Muslimah', 'buy' => 120000, 'sell' => 185000],
                ['name' => 'Elzatta Hijab Segi Empat', 'buy' => 35000, 'sell' => 55000],
                ['name' => 'Dian Pelangi Gamis Exclusive', 'buy' => 250000, 'sell' => 450000],
                ['name' => 'Shafira Dress Premium', 'buy' => 300000, 'sell' => 500000],
                ['name' => 'Keke Busana Muslim Anak', 'buy' => 85000, 'sell' => 125000],
                ['name' => 'Mezora Baju Koko', 'buy' => 110000, 'sell' => 160000],
                ['name' => 'Vanilla Hijab Scarf', 'buy' => 95000, 'sell' => 140000],
                ['name' => 'Ria Miranda Tunik', 'buy' => 220000, 'sell' => 380000],
                ['name' => 'Kami Idea Dress', 'buy' => 280000, 'sell' => 420000],
            ],
            'Frozen Foods' => [
                ['name' => 'Fiesta Chicken Nugget 500g', 'buy' => 38000, 'sell' => 45000],
                ['name' => 'So Good Spicy Chicken', 'buy' => 42000, 'sell' => 50000],
                ['name' => 'Champ Sosis Ayam', 'buy' => 18000, 'sell' => 22000],
                ['name' => 'Kanzler Sosis Singles', 'buy' => 7000, 'sell' => 9000],
                ['name' => 'Belfoods Chicken Stick', 'buy' => 32000, 'sell' => 38000],
                ['name' => 'Bernardi Bakso Sapi', 'buy' => 55000, 'sell' => 65000],
                ['name' => 'Cedea Fish Roll', 'buy' => 25000, 'sell' => 30000],
                ['name' => 'Golden Farm French Fries', 'buy' => 28000, 'sell' => 35000],
                ['name' => 'Sunny Gold Chicken Karage', 'buy' => 45000, 'sell' => 55000],
                ['name' => 'Fiesta Spicy Wing', 'buy' => 48000, 'sell' => 58000],
            ],
            'Skin Care' => [
                ['name' => 'Wardah UV Shield Aqua', 'buy' => 28000, 'sell' => 35000],
                ['name' => 'Somethinc Niacinamide Serum', 'buy' => 90000, 'sell' => 115000],
                ['name' => 'Avoskin Perfect Hydrating', 'buy' => 120000, 'sell' => 145000],
                ['name' => 'Skintific 5X Ceramide', 'buy' => 135000, 'sell' => 165000],
                ['name' => 'Emina Sun Battle', 'buy' => 22000, 'sell' => 29000],
                ['name' => 'Scarlett Whitening Body Lotion', 'buy' => 55000, 'sell' => 75000],
                ['name' => 'COSRX Snail Mucin', 'buy' => 180000, 'sell' => 220000],
                ['name' => 'Bioaqua Sheet Mask', 'buy' => 3000, 'sell' => 5000],
                ['name' => 'N\'PURE Cica Clear Pad', 'buy' => 95000, 'sell' => 129000],
                ['name' => 'Whitelab Brightening Serum', 'buy' => 65000, 'sell' => 85000],
            ],
            'Stationary' => [
                ['name' => 'Buku Tulis Sinar Dunia 58', 'buy' => 2500, 'sell' => 4000],
                ['name' => 'Pulpen Kenko Gel Pen', 'buy' => 2000, 'sell' => 3500],
                ['name' => 'Pensil Faber-Castell 2B', 'buy' => 1500, 'sell' => 3000],
                ['name' => 'Stabilo Boss Original', 'buy' => 8000, 'sell' => 12000],
                ['name' => 'Penghapus Joyko', 'buy' => 500, 'sell' => 1500],
                ['name' => 'Penggaris Butterfly 30cm', 'buy' => 1500, 'sell' => 3000],
                ['name' => 'Spidol Snowman Board', 'buy' => 6000, 'sell' => 8500],
                ['name' => 'Kertas HVS PaperOne A4', 'buy' => 42000, 'sell' => 55000],
                ['name' => 'Map Clear Folder Bantex', 'buy' => 12000, 'sell' => 18000],
                ['name' => 'Sticky Notes Post-it', 'buy' => 5000, 'sell' => 8000],
            ],
            'Herbal' => [
                ['name' => 'Tolak Angin Sido Muncul', 'buy' => 2500, 'sell' => 4000],
                ['name' => 'Antangin JRG', 'buy' => 2500, 'sell' => 4000],
                ['name' => 'Madu TJ Murni 250g', 'buy' => 18000, 'sell' => 25000],
                ['name' => 'Sari Kurma Sahira', 'buy' => 22000, 'sell' => 35000],
                ['name' => 'Habbatussauda Cap Kurma Ajwa', 'buy' => 35000, 'sell' => 50000],
                ['name' => 'Jamu Pegal Linu Sido Muncul', 'buy' => 1500, 'sell' => 2500],
                ['name' => 'Teh Daun Jati Cina', 'buy' => 12000, 'sell' => 20000],
                ['name' => 'Minyak Kutus Kutus', 'buy' => 170000, 'sell' => 230000],
                ['name' => 'Sari Jahe Merah AMH', 'buy' => 10000, 'sell' => 15000],
                ['name' => 'Kapsul Daun Kelor', 'buy' => 25000, 'sell' => 40000],
            ]
        ];

        foreach ($categories as $categoryName => $products) {
            $category = Category::firstOrCreate([
                'name' => $categoryName
            ], [
                'slug' => Str::slug($categoryName),
                'description' => 'Produk ' . $categoryName,
                'is_active' => true,
            ]);

            foreach ($products as $index => $prod) {
                // Generate a consistent dummy SKU and barcode
                $sku = strtoupper(Str::slug($categoryName)) . '-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT);
                $barcode = '899' . rand(100000000, 999999999);

                Product::updateOrCreate(
                    ['sku' => $sku],
                    [
                        'category_id' => $category->id,
                        'name' => $prod['name'],
                        'barcode' => $barcode,
                        'description' => 'Produk ' . $prod['name'] . ' original berkualitas.',
                        'purchase_price' => $prod['buy'],
                        'selling_price' => $prod['sell'],
                        'stock' => rand(20, 100),
                        'min_stock' => 5,
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
