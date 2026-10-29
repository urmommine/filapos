<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AddBusanaMuslimProductsAndTransactionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::beginTransaction();

        try {
            // 1. Ambil atau Buat Kategori Busana Muslim
            $category = Category::where('name', 'like', '%Muslim%')->first();
            if (!$category) {
                $category = Category::create([
                    'name' => 'Baju Muslim',
                    'slug' => 'baju-muslim',
                    'description' => 'Pakaian dan busana muslim pria, wanita, gamis, koko, hijab, sarung, dan mukena',
                    'is_active' => true,
                ]);
            }

            // 2. Daftar 110 Produk Nyata Busana Muslim
            $productsList = [
                // Rabbani (15 produk)
                ['name' => 'Rabbani Kerudung Instan Innova LX Putih', 'buy' => 48000, 'sell' => 68000, 'stock' => 45],
                ['name' => 'Rabbani Kerudung Instan Tetra Hemy Navy', 'buy' => 52000, 'sell' => 74000, 'stock' => 40],
                ['name' => 'Rabbani Kerudung Sekolah Great Innova Coklat', 'buy' => 46000, 'sell' => 65000, 'stock' => 50],
                ['name' => 'Rabbani Kerudung Kasturi Bordir Mocca', 'buy' => 58000, 'sell' => 82000, 'stock' => 35],
                ['name' => 'Rabbani Bergo Anisa Serut Belakang Hitam', 'buy' => 42000, 'sell' => 59000, 'stock' => 48],
                ['name' => 'Rabbani Baju Koko Amjad Lengan Panjang Putih', 'buy' => 145000, 'sell' => 195000, 'stock' => 30],
                ['name' => 'Rabbani Baju Koko Kemko Zayyan Sage Green', 'buy' => 155000, 'sell' => 215000, 'stock' => 28],
                ['name' => 'Rabbani Kemko Lengan Pendek Rayyan Grey', 'buy' => 135000, 'sell' => 185000, 'stock' => 32],
                ['name' => 'Rabbani Gamis Dresslim Kasturi Maroon', 'buy' => 220000, 'sell' => 310000, 'stock' => 25],
                ['name' => 'Rabbani Gamis Dresslim Rawdah Dusty Pink', 'buy' => 240000, 'sell' => 340000, 'stock' => 22],
                ['name' => 'Rabbani Gamis Dresslim Zahira Emerald', 'buy' => 250000, 'sell' => 350000, 'stock' => 20],
                ['name' => 'Rabbani Ciput Rajut Bandana 2 Warna', 'buy' => 15000, 'sell' => 25000, 'stock' => 60],
                ['name' => 'Rabbani Manset Tangan Handshock Renda Hitam', 'buy' => 18000, 'sell' => 28000, 'stock' => 55],
                ['name' => 'Rabbani Peci Rajut Haji Putih Polos', 'buy' => 20000, 'sell' => 32000, 'stock' => 50],
                ['name' => 'Rabbani Sarung Tenun Jacquard Hitam Corak', 'buy' => 95000, 'sell' => 135000, 'stock' => 35],

                // Zoya (12 produk)
                ['name' => 'Zoya Scarf Segi Empat Kaila Melika Nude', 'buy' => 45000, 'sell' => 69000, 'stock' => 45],
                ['name' => 'Zoya Kerudung Segi Empat Voal Blush', 'buy' => 48000, 'sell' => 72000, 'stock' => 40],
                ['name' => 'Zoya Bergo Zahra Jersey Instan Hitam', 'buy' => 50000, 'sell' => 75000, 'stock' => 42],
                ['name' => 'Zoya Tunik Nayla Floral Motif Mint', 'buy' => 140000, 'sell' => 199000, 'stock' => 28],
                ['name' => 'Zoya Tunik Casual Denim Lisna Biru Muda', 'buy' => 155000, 'sell' => 220000, 'stock' => 25],
                ['name' => 'Zoya Gamis Marwah Crinkle Sage Green', 'buy' => 220000, 'sell' => 315000, 'stock' => 22],
                ['name' => 'Zoya Dress Bellisima Maxi Rose', 'buy' => 250000, 'sell' => 360000, 'stock' => 20],
                ['name' => 'Zoya Mukena Silk Premium Khadijah Lavender', 'buy' => 260000, 'sell' => 375000, 'stock' => 18],
                ['name' => 'Zoya Baju Koko Pria Faizan Lengan Panjang Navy', 'buy' => 160000, 'sell' => 225000, 'stock' => 26],
                ['name' => 'Zoya Koko Casual Arkan Lengan Pendek Putih', 'buy' => 140000, 'sell' => 195000, 'stock' => 30],
                ['name' => 'Zoya Ciput Anti Pusing Ninja Hitam', 'buy' => 22000, 'sell' => 35000, 'stock' => 50],
                ['name' => 'Zoya Bros Dagu Mutiara Crystal Silver', 'buy' => 25000, 'sell' => 40000, 'stock' => 45],

                // Elzatta (12 produk)
                ['name' => 'Elzatta Scarf Kaila Lazia Segi Empat Biru', 'buy' => 42000, 'sell' => 62000, 'stock' => 45],
                ['name' => 'Elzatta Scarf Segi Empat Polos Keisha Soft Pink', 'buy' => 38000, 'sell' => 55000, 'stock' => 50],
                ['name' => 'Elzatta Bergo Zaria Sahila Instan Coksu', 'buy' => 46000, 'sell' => 66000, 'stock' => 40],
                ['name' => 'Elzatta Bergo Casual Zaria M Hitam', 'buy' => 39000, 'sell' => 58000, 'stock' => 48],
                ['name' => 'Elzatta Tunik Nelida A-Line Motif Mocca', 'buy' => 135000, 'sell' => 189000, 'stock' => 30],
                ['name' => 'Elzatta Tunik Valda Polos Mustard', 'buy' => 125000, 'sell' => 175000, 'stock' => 32],
                ['name' => 'Elzatta Gamis Gasna Rayon Motif Emerald', 'buy' => 195000, 'sell' => 275000, 'stock' => 24],
                ['name' => 'Elzatta Gamis Alysa Maxi Pleats Abu-abu', 'buy' => 215000, 'sell' => 299000, 'stock' => 22],
                ['name' => 'Elzatta Koko Pria Harun Lengan Panjang Maroon', 'buy' => 150000, 'sell' => 210000, 'stock' => 28],
                ['name' => 'Elzatta Koko Lengan Pendek Ghani Putih', 'buy' => 130000, 'sell' => 185000, 'stock' => 30],
                ['name' => 'Elzatta Mukena Travelling Rayon Parasut Tosca', 'buy' => 110000, 'sell' => 155000, 'stock' => 35],
                ['name' => 'Elzatta Inner Rajut Bandana Cokelat', 'buy' => 18000, 'sell' => 28000, 'stock' => 55],

                // Buttonscarves (10 produk)
                ['name' => 'Buttonscarves The Royale Voile Square Black', 'buy' => 260000, 'sell' => 375000, 'stock' => 20],
                ['name' => 'Buttonscarves The British Series Scarf London', 'buy' => 265000, 'sell' => 385000, 'stock' => 18],
                ['name' => 'Buttonscarves The Monogram Scarf Biscuit Beige', 'buy' => 270000, 'sell' => 390000, 'stock' => 20],
                ['name' => 'Buttonscarves Everyday Voile Square Soft Coral', 'buy' => 195000, 'sell' => 285000, 'stock' => 25],
                ['name' => 'Buttonscarves Amalfi Scarf Silk Voile Sage', 'buy' => 265000, 'sell' => 385000, 'stock' => 18],
                ['name' => 'Buttonscarves Inner Hijab Cross Ciput Nude', 'buy' => 65000, 'sell' => 95000, 'stock' => 40],
                ['name' => 'Buttonscarves Everyday Inner Silk Cap Black', 'buy' => 75000, 'sell' => 110000, 'stock' => 35],
                ['name' => 'Buttonscarves Brooch Signature Gold Pin', 'buy' => 18000, 'sell' => 260000, 'stock' => 25],
                ['name' => 'Buttonscarves Prayer Robe Satin Silk Burgundy', 'buy' => 275000, 'sell' => 395000, 'stock' => 15],
                ['name' => 'Buttonscarves Scrunchie Velvet Hair Tie Khaki', 'buy' => 35000, 'sell' => 55000, 'stock' => 45],

                // Vanilla Hijab (8 produk)
                ['name' => 'Vanilla Hijab Signature Square Scarf Terracotta', 'buy' => 130000, 'sell' => 185000, 'stock' => 35],
                ['name' => 'Vanilla Hijab Daisy Voal Printed Scarf Sky Blue', 'buy' => 125000, 'sell' => 175000, 'stock' => 35],
                ['name' => 'Vanilla Hijab Jasmine Floral Tunic Dusty Lilac', 'buy' => 175000, 'sell' => 245000, 'stock' => 28],
                ['name' => 'Vanilla Hijab Blossom Rayon Dress Sage', 'buy' => 220000, 'sell' => 310000, 'stock' => 24],
                ['name' => 'Vanilla Hijab Calma Pleated Skirt Mocca', 'buy' => 145000, 'sell' => 205000, 'stock' => 30],
                ['name' => 'Vanilla Hijab Basic Voile Square Hijab Olive', 'buy' => 65000, 'sell' => 95000, 'stock' => 45],
                ['name' => 'Vanilla Hijab Mukena Travelling Crinkle Chiffon', 'buy' => 185000, 'sell' => 265000, 'stock' => 25],
                ['name' => 'Vanilla Hijab Ciput Rajut Anti Geser Hitam', 'buy' => 22000, 'sell' => 35000, 'stock' => 50],

                // Ria Miranda (8 produk)
                ['name' => 'Ria Miranda Minang Heritage Scarf Navy Blue', 'buy' => 245000, 'sell' => 350000, 'stock' => 22],
                ['name' => 'Ria Miranda Dania Tunic Asimetris Pastel Peach', 'buy' => 230000, 'sell' => 335000, 'stock' => 20],
                ['name' => 'Ria Miranda Meira Dress A-Line Salju Grey', 'buy' => 265000, 'sell' => 385000, 'stock' => 18],
                ['name' => 'Ria Miranda Haru Knit Outerwear Lilac', 'buy' => 220000, 'sell' => 315000, 'stock' => 22],
                ['name' => 'Ria Miranda Orchid Scarf Satin Voile Mint', 'buy' => 210000, 'sell' => 299000, 'stock' => 25],
                ['name' => 'Ria Miranda Rayana Dress Harian Dusty Rose', 'buy' => 255000, 'sell' => 365000, 'stock' => 20],
                ['name' => 'Ria Miranda Aksesoris Bros Signature RM Gold', 'buy' => 155000, 'sell' => 225000, 'stock' => 28],
                ['name' => 'Ria Miranda Mukena Silk Mini Pouch Floral', 'buy' => 270000, 'sell' => 390000, 'stock' => 16],

                // Kami Idea / KAMI (7 produk)
                ['name' => 'Kami Idea Danica Printed Scarf Olive Green', 'buy' => 215000, 'sell' => 310000, 'stock' => 25],
                ['name' => 'Kami Idea Sola Long Tunic Terakota', 'buy' => 235000, 'sell' => 340000, 'stock' => 20],
                ['name' => 'Kami Idea Aira Casual Maxi Dress Sand Beige', 'buy' => 260000, 'sell' => 375000, 'stock' => 18],
                ['name' => 'Kami Idea Hani Linen Shirt Sage', 'buy' => 195000, 'sell' => 275000, 'stock' => 24],
                ['name' => 'Kami Idea Mara Cullote Pants Hitam', 'buy' => 185000, 'sell' => 265000, 'stock' => 25],
                ['name' => 'Kami Idea Plain Voile Hijab Square Mocca', 'buy' => 85000, 'sell' => 125000, 'stock' => 38],
                ['name' => 'Kami Idea Prayer Mat Sajadah Lipat Velvet', 'buy' => 95000, 'sell' => 140000, 'stock' => 30],

                // Dian Pelangi & Shafira (8 produk)
                ['name' => 'Dian Pelangi Scarf Silk Etnik Pelangi Cerah', 'buy' => 225000, 'sell' => 320000, 'stock' => 22],
                ['name' => 'Dian Pelangi Gamis Jumputan Tie Dye Royal Blue', 'buy' => 265000, 'sell' => 380000, 'stock' => 18],
                ['name' => 'Dian Pelangi Outer Tenun Songket Eksklusif Hitam', 'buy' => 255000, 'sell' => 365000, 'stock' => 20],
                ['name' => 'Dian Pelangi Tunik Katun Batik Kombinasi Merah', 'buy' => 190000, 'sell' => 270000, 'stock' => 24],
                ['name' => 'Shafira Koko Pria Bordir Minimalis Putih', 'buy' => 210000, 'sell' => 299000, 'stock' => 22],
                ['name' => 'Shafira Gamis Royal Linen Dress Navy', 'buy' => 270000, 'sell' => 390000, 'stock' => 16],
                ['name' => 'Shafira Tunik Katun Eksklusif Maroon', 'buy' => 225000, 'sell' => 320000, 'stock' => 20],
                ['name' => 'Shafira Hijab Voal Square Swarovski Crystal', 'buy' => 145000, 'sell' => 210000, 'stock' => 28],

                // Sarung & Busana Pria Tradisional (16 produk)
                ['name' => 'Sarung BHS Classic Bronze Motif TKB Hijau', 'buy' => 260000, 'sell' => 375000, 'stock' => 20],
                ['name' => 'Sarung BHS Classic Motif SKT Dam Coklat', 'buy' => 270000, 'sell' => 390000, 'stock' => 18],
                ['name' => 'Sarung Atlas Idaman 550 Dobby Coklat Tua', 'buy' => 78000, 'sell' => 110000, 'stock' => 45],
                ['name' => 'Sarung Atlas Super 770 Songket Jacquard Hitam', 'buy' => 145000, 'sell' => 205000, 'stock' => 30],
                ['name' => 'Sarung Atlas Favorit 500 Polos Warna Hijau Botol', 'buy' => 65000, 'sell' => 92000, 'stock' => 42],
                ['name' => 'Sarung Wadimor Padang Motif Tradisional Merah', 'buy' => 68000, 'sell' => 95000, 'stock' => 45],
                ['name' => 'Sarung Wadimor Relief Bordir Corak Biru', 'buy' => 75000, 'sell' => 105000, 'stock' => 40],
                ['name' => 'Sarung Wadimor Grand Master Hitam Polos Tumpal', 'buy' => 82000, 'sell' => 115000, 'stock' => 38],
                ['name' => 'Sarung Wadimor Primer Citra Jacquard Tosca', 'buy' => 95000, 'sell' => 135000, 'stock' => 35],
                ['name' => 'Sarung Gajah Duduk Asia Kembang Warna Warni', 'buy' => 62000, 'sell' => 88000, 'stock' => 45],
                ['name' => 'Sarung Gajah Duduk Mika Songket Biru Tua', 'buy' => 80000, 'sell' => 112000, 'stock' => 38],
                ['name' => 'Sarung Gajah Duduk Signature Jacquard Maroon', 'buy' => 120000, 'sell' => 170000, 'stock' => 32],
                ['name' => 'Koko Pria Al-Isra Katun Madinah Lengan Panjang Putih', 'buy' => 95000, 'sell' => 135000, 'stock' => 35],
                ['name' => 'Koko Pria Al-Isra Lengan Pendek Model Kurta Abu', 'buy' => 85000, 'sell' => 120000, 'stock' => 35],
                ['name' => 'Jubah Gamis Pria Al-Haramain Katun Toyobo Hitam', 'buy' => 175000, 'sell' => 250000, 'stock' => 25],
                ['name' => 'Songkok Peci Hitam Nasional Presiden AC Super', 'buy' => 45000, 'sell' => 68000, 'stock' => 50],

                // Mukena & Perlengkapan Sholat (12 produk)
                ['name' => 'Mukena Siti Khadijah Signature Putih Bordir', 'buy' => 260000, 'sell' => 375000, 'stock' => 20],
                ['name' => 'Mukena Siti Khadijah Modish Katun Poplin Nude', 'buy' => 245000, 'sell' => 350000, 'stock' => 22],
                ['name' => 'Mukena Silk 2in1 Lasercut Renda Mewah Lavender', 'buy' => 175000, 'sell' => 250000, 'stock' => 28],
                ['name' => 'Mukena Dewasa Katun Rayon Renda Travelling Navy', 'buy' => 95000, 'sell' => 135000, 'stock' => 35],
                ['name' => 'Mukena Bali Jumbo Bahan Rayon Dingin Floral Pink', 'buy' => 80000, 'sell' => 115000, 'stock' => 40],
                ['name' => 'Mukena Terusan Bordir Katun Paris Putih Tulang', 'buy' => 140000, 'sell' => 199000, 'stock' => 30],
                ['name' => 'Mukena Mini Parasut Korea Ultralight Sage', 'buy' => 75000, 'sell' => 110000, 'stock' => 40],
                ['name' => 'Mukena Dewasa Crinkle Airflow Polos Mocca', 'buy' => 115000, 'sell' => 165000, 'stock' => 35],
                ['name' => 'Sajadah Bulu Rasfur Busa Empuk Tebal Hitam', 'buy' => 85000, 'sell' => 125000, 'stock' => 35],
                ['name' => 'Sajadah Turkey Tenun Jacquard Emas', 'buy' => 65000, 'sell' => 95000, 'stock' => 40],
                ['name' => 'Tasbih Digital Tally Counter LED Finger Ring', 'buy' => 10000, 'sell' => 18000, 'stock' => 65],
                ['name' => 'Al-Qur\'an Terjemahan Tajwid Warna Ukuran A5', 'buy' => 65000, 'sell' => 95000, 'stock' => 40],

                // Gamis, Abaya, & Aksesoris Harian Populer (10 produk)
                ['name' => 'Abaya Dubai Hitam Polos Bahan Ceruty Babydoll', 'buy' => 195000, 'sell' => 280000, 'stock' => 24],
                ['name' => 'Abaya Turki Bordir Renda Mewah Jetblack', 'buy' => 235000, 'sell' => 340000, 'stock' => 20],
                ['name' => 'Gamis Crinkle Airflow Busui Friendly Terakota', 'buy' => 105000, 'sell' => 150000, 'stock' => 35],
                ['name' => 'Gamis Rayon Viscose Motif Bunga Salur Navy', 'buy' => 90000, 'sell' => 130000, 'stock' => 38],
                ['name' => 'Gamis Kaftan Payet Bordir Pesta Mint Green', 'buy' => 210000, 'sell' => 305000, 'stock' => 22],
                ['name' => 'Pashmina Ceruty Baby Doll Polos Hitam', 'buy' => 24000, 'sell' => 38000, 'stock' => 60],
                ['name' => 'Pashmina Plisket Lidi Premium Coksu', 'buy' => 28000, 'sell' => 45000, 'stock' => 55],
                ['name' => 'Pashmina Silk Cradle Shimmer Rose Gold', 'buy' => 42000, 'sell' => 65000, 'stock' => 48],
                ['name' => 'Ciput Inner Ninja Kaos Spandek Rayon Putih', 'buy' => 15000, 'sell' => 25000, 'stock' => 60],
                ['name' => 'Manset Baju Gamis Spandek Dingin Hitam', 'buy' => 25000, 'sell' => 40000, 'stock' => 50],
            ];

            // Potong tepat 110 produk
            $productsList = array_slice($productsList, 0, 110);

            $createdProducts = [];
            $skuIndex = 1;
            $barcodeBase = 899881100001;

            foreach ($productsList as $item) {
                $sku = 'BSM-' . str_pad($skuIndex, 3, '0', STR_PAD_LEFT);
                $barcode = (string) ($barcodeBase + $skuIndex);

                $product = Product::updateOrCreate(
                    ['sku' => $sku],
                    [
                        'category_id' => $category->id,
                        'name' => $item['name'],
                        'barcode' => $barcode,
                        'description' => $item['name'] . ' - Bahan premium, nyaman dan adem.',
                        'purchase_price' => $item['buy'],
                        'selling_price' => $item['sell'],
                        'stock' => $item['stock'],
                        'min_stock' => 5,
                        'is_active' => true,
                    ]
                );

                $createdProducts[] = $product;
                $skuIndex++;
            }

            $this->command->info("Berhasil membuat " . count($createdProducts) . " data produk busana muslim nyata.");

            // 3. Generate Riwayat Transaksi (1 Juli 2026 s/d 26 Oktober 2026)
            // Cukup 1 transaksi setiap 1-2 hari (~75-80 transaksi)
            $startDate = Carbon::create(2026, 7, 1, 9, 30, 0);
            $endDate = Carbon::create(2026, 10, 26, 20, 0, 0);

            $currentDate = $startDate->copy();
            $transactionCount = 0;
            $seqCounter = 101;

            while ($currentDate->lessThanOrEqualTo($endDate)) {
                // Tentukan jam transaksi toko retail (antara 09:30 - 20:30)
                $hour = rand(9, 20);
                $minute = rand(5, 55);
                $second = rand(10, 50);
                $trxDateTime = $currentDate->copy()->setTime($hour, $minute, $second);

                // Format invoice lama POS: INV-YYYYMMDD-XXXX
                $invoiceNumber = 'INV-' . $trxDateTime->format('Ymd') . '-' . str_pad($seqCounter, 4, '0', STR_PAD_LEFT);
                $seqCounter++;

                // Pilih produk yang dibeli khusus busana muslim (Maksimal total transaksi 400rb)
                // Aturan: "barang yang dibeli di riwayat tidak harus banyak 1 aja kalau mahal"
                $scenario = rand(1, 100);
                $cartItems = [];
                $subtotal = 0;

                if ($scenario <= 75) {
                    // 75% transaksi beli 1 jenis barang
                    $prod = $createdProducts[array_rand($createdProducts)];
                    $sellPrice = (float) $prod->selling_price;
                    
                    if ($sellPrice > 200000) {
                        $qty = 1; // Barang mahal wajib 1 pcs
                    } elseif ($sellPrice > 100000) {
                        $qty = 1;
                    } else {
                        // Barang murah (misal hijab/pashmina/ciput): 1 atau 2 pcs
                        $qty = rand(1, 2);
                    }

                    $totalItem = $sellPrice * $qty;
                    if ($totalItem > 400000) {
                        $qty = 1;
                        $totalItem = $sellPrice;
                    }

                    $cartItems[] = [
                        'product' => $prod,
                        'qty' => $qty,
                        'unit_price' => $sellPrice,
                        'total_price' => $totalItem,
                    ];
                    $subtotal = $totalItem;
                } else {
                    // 25% transaksi kombinasi 2 jenis barang (misal 1 baju/sarung + 1 pashmina/ciput)
                    // Cari 1 item sedang/mahal dan 1 item murah agar total <= 400.000
                    $moderateProducts = array_filter($createdProducts, fn($p) => $p->selling_price >= 100000 && $p->selling_price <= 280000);
                    $cheapProducts = array_filter($createdProducts, fn($p) => $p->selling_price < 100000);

                    $prod1 = !empty($moderateProducts) ? $moderateProducts[array_rand($moderateProducts)] : $createdProducts[0];
                    $prod2 = !empty($cheapProducts) ? $cheapProducts[array_rand($cheapProducts)] : $createdProducts[1];

                    $item1Total = (float) $prod1->selling_price * 1;
                    $item2Total = (float) $prod2->selling_price * 1;

                    if ($item1Total + $item2Total <= 400000) {
                        $cartItems[] = [
                            'product' => $prod1,
                            'qty' => 1,
                            'unit_price' => (float) $prod1->selling_price,
                            'total_price' => $item1Total,
                        ];
                        $cartItems[] = [
                            'product' => $prod2,
                            'qty' => 1,
                            'unit_price' => (float) $prod2->selling_price,
                            'total_price' => $item2Total,
                        ];
                        $subtotal = $item1Total + $item2Total;
                    } else {
                        // Fallback 1 item saja
                        $cartItems[] = [
                            'product' => $prod1,
                            'qty' => 1,
                            'unit_price' => (float) $prod1->selling_price,
                            'total_price' => $item1Total,
                        ];
                        $subtotal = $item1Total;
                    }
                }

                // Metode pembayaran: Cash (~60%) atau QRIS (~40%)
                $isCash = rand(1, 100) <= 60;
                $paymentMethod = $isCash ? 'cash' : 'qris';

                if ($isCash) {
                    // Dibulatkan ke nominal uang tunai terdekat (50rb atau 100rb terdekat)
                    if ($subtotal <= 100000) {
                        $amountPaid = ceil($subtotal / 50000) * 50000;
                    } elseif ($subtotal <= 200000) {
                        $amountPaid = ceil($subtotal / 50000) * 50000;
                    } else {
                        $amountPaid = ceil($subtotal / 50000) * 50000;
                    }
                    if ($amountPaid < $subtotal) {
                        $amountPaid = $subtotal;
                    }
                    $change = $amountPaid - $subtotal;
                } else {
                    $amountPaid = $subtotal;
                    $change = 0;
                }

                // Buat Order
                $order = Order::create([
                    'user_id' => 2, // Kasir POS Yuliana
                    'customer_id' => null, // Pelanggan Umum walk-in
                    'channel' => 'pos',
                    'invoice_number' => $invoiceNumber,
                    'subtotal' => $subtotal,
                    'discount' => 0,
                    'tax' => 0,
                    'total_amount' => $subtotal,
                    'payment_method' => $paymentMethod,
                    'amount_paid' => $amountPaid,
                    'change' => $change,
                    'payment_status' => 'paid',
                    'notes' => null,
                    'created_at' => $trxDateTime,
                    'updated_at' => $trxDateTime,
                ]);

                // Simpan Order Items dan kurangi stok
                foreach ($cartItems as $c) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $c['product']->id,
                        'product_name' => $c['product']->name,
                        'quantity' => $c['qty'],
                        'unit_price' => $c['unit_price'],
                        'total_price' => $c['total_price'],
                        'created_at' => $trxDateTime,
                        'updated_at' => $trxDateTime,
                    ]);

                    // Kurangi stok produk secara langsung
                    $c['product']->decrement('stock', $c['qty']);
                }

                $transactionCount++;

                // Maju 1 sampai 2 hari (sesuai preferensi 1 transaksi tiap 1-2 hari)
                $currentDate->addDays(rand(1, 2));
            }

            DB::commit();

            $this->command->info("Selesai! Berhasil membuat {$transactionCount} transaksi riwayat busana muslim dari 1 Juli 2026 hingga 26 Oktober 2026.");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error("Gagal menambahkan data: " . $e->getMessage());
            throw $e;
        }
    }
}
