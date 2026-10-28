<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Str;

class Add2300RealProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan kategori yang dituju selalu ada di database agar tidak melanggar foreign key
        $getOrCreateCat = function(string $name, array $keywords = []) {
            foreach ($keywords as $kw) {
                $found = Category::where('name', 'LIKE', "%{$kw}%")->first();
                if ($found) return $found->id;
            }
            $cat = Category::firstOrCreate(
                ['name' => $name],
                ['slug' => Str::slug($name), 'description' => "Kategori {$name}", 'is_active' => true]
            );
            return $cat->id;
        };

        $catMakanan = $getOrCreateCat('Makanan & Mie Instan', ['Makanan', 'Mie']);
        $catMinuman = $getOrCreateCat('Minuman Dingin & Hangat', ['Minuman', 'Drink']);
        $catSnack = $getOrCreateCat('Snack & Biskuit', ['Snack', 'Biskuit', 'Camilan']);
        $catSembako = $getOrCreateCat('Sembako & Kebutuhan Dapur', ['Sembako', 'Dapur', 'Bahan']);
        $catPersonalCare = $getOrCreateCat('Personal Care & Kebersihan', ['Personal Care', 'Perawatan']);
        $catMandi = $getOrCreateCat('Personal Care & Mandi', ['Mandi', 'Sabun', 'Sampo']);
        $catKebersihan = $getOrCreateCat('Kebersihan Rumah & Dapur', ['Kebersihan', 'Deterjen']);
        $catObat = $getOrCreateCat('Obat & Kesehatan', ['Obat', 'Kesehatan']);
        $catRokok = $getOrCreateCat('Rokok & Tembakau', ['Rokok', 'Tembakau']);
        $catFrozen = $getOrCreateCat('Frozen Foods', ['Frozen', 'Beku']);

        // Ambil barcode & SKU yang sudah ada di database untuk mencegah duplikasi
        $existingBarcodes = Product::pluck('barcode')->filter()->flip()->toArray();
        $existingSkus = Product::pluck('sku')->filter()->flip()->toArray();
        $existingNames = Product::pluck('name')->filter()->map(fn($n) => strtolower(trim($n)))->flip()->toArray();

        $productsToInsert = [];
        $targetCount = 2300;
        $currentNumber = 50000;

        // Helper barcode EAN-13 Indonesia (prefix 899)
        $generateBarcode = function() use (&$currentNumber, &$existingBarcodes) {
            do {
                $currentNumber++;
                $code = '899' . str_pad($currentNumber, 9, '0', STR_PAD_LEFT);
                $sum = 0;
                for ($i = 0; $i < 12; $i++) {
                    $sum += (int)$code[$i] * ($i % 2 === 0 ? 1 : 3);
                }
                $check = (10 - ($sum % 10)) % 10;
                $barcode = $code . $check;
            } while (isset($existingBarcodes[$barcode]));

            $existingBarcodes[$barcode] = true;
            return $barcode;
        };

        // Helper SKU
        $skuCounter = 1;
        $generateSku = function($prefix) use (&$skuCounter, &$existingSkus) {
            do {
                $sku = sprintf('%s-%05d', $prefix, $skuCounter++);
            } while (isset($existingSkus[$sku]));

            $existingSkus[$sku] = true;
            return $sku;
        };

        // Definisi Katalog Produk Real Indonesia per Kategori
        $catalogDefinitions = [
            // -----------------------------------------------------------------
            // 1. MAKANAN & MIE INSTAN (Target ~400 item)
            // -----------------------------------------------------------------
            [
                'cat_id' => $catMakanan,
                'prefix' => 'MKN',
                'items' => [
                    // Indomie
                    ['brand' => 'Indomie', 'base' => 'Mie Goreng', 'variants' => ['Spesial 85g', 'Jumbo 129g', 'Rendang 91g', 'Pedas 79g', 'Ayam Geprek 85g', 'Seblak Hot Jeletot 75g', 'Sambal Matah 85g', 'Kebab Rendang 85g', 'Aceh 90g', 'Rica-Rica 85g', 'Iga Penyet 85g', 'Cakalang 82g', 'Kriuk 8x 90g', 'Barbeque Chicken 85g', 'Salted Egg 85g', 'Tori Kara Ramen 89g', 'Shoyu Ramen 82g', 'Miso Ramen 86g', 'Takoyaki Ramen 91g', 'Mie Keriting Spesial 90g', 'Mie Keriting Ayam Panggang 90g', 'Mi Goreng Rasa Sambal Balado 85g'], 'price' => [3000, 3600]],
                    ['brand' => 'Indomie', 'base' => 'Mie Kuah', 'variants' => ['Ayam Bawang 69g', 'Soto Mie 70g', 'Kari Ayam 72g', 'Kaldu Ayam 65g', 'Ayam Spesial 68g', 'Soto Spesial Koya 75g', 'Soto Lamongan 70g', 'Soto Medan 70g', 'Soto Banjar Limau Kuit 75g', 'Mi Kocok Bandung 75g', 'Coto Makassar 75g', 'Empal Gentong 75g', 'Rawon Pedas 75g', 'Kari Ayam Bawang 73g', 'Kari Jepang 86g', 'Tori Miso Ramen 86g'], 'price' => [2900, 3500]],
                    // Mie Sedaap
                    ['brand' => 'Mie Sedaap', 'base' => 'Goreng', 'variants' => ['Original 90g', 'Ayam Krispi 88g', 'Sambal Goreng 88g', 'Korean Spicy Chicken 87g', 'Selection Salero Padang 86g', 'Selection Singapore Spicy Laksa 83g', 'Selection Baso Bledek 77g'], 'price' => [2900, 3500]],
                    ['brand' => 'Mie Sedaap', 'base' => 'Kuah', 'variants' => ['Soto Madura 75g', 'Ayam Bawang 70g', 'Kari Kental 72g', 'Kari Spesial 75g', 'Korean Spicy Soup 77g', 'White Curry 81g', 'Ayam Jeruk Nipis 75g', 'Bakso Gurih 77g', 'Rawon Khas Jawa Timur 75g'], 'price' => [2900, 3500]],
                    // Sarimi & Supermi
                    ['brand' => 'Sarimi', 'base' => 'Isi 2', 'variants' => ['Goreng Ayam Kremes 126g', 'Goreng Pedas 126g', 'Goreng Rasa Kecap 126g', 'Kuah Soto Koya Gurih 120g', 'Kuah Kari Ayam 120g', 'Kuah Kaldu Ayam 120g', 'Kuah Baso Sapi 120g'], 'price' => [4000, 4800]],
                    ['brand' => 'Supermi', 'base' => 'Instan', 'variants' => ['Kaldu Ayam 68g', 'Ayam Bawang 70g', 'Nutrimi Rasa Steak Ayam 80g', 'Nutrimi Rasa Mi Goreng 80g', 'Gobang Goreng Bawang 75g'], 'price' => [2800, 3800]],
                    // Pop Mie
                    ['brand' => 'Pop Mie', 'base' => 'Cup', 'variants' => ['Goreng Spesial 75g', 'Goreng Pedas Dower 75g', 'Goreng Pedas Gledek 75g', 'Kuah Rasa Ayam Bawang 75g', 'Kuah Rasa Baso 75g', 'Kuah Rasa Kari Ayam 75g', 'Kuah Rasa Soto Ayam 75g', 'Pangsit Pedas Dower 75g', 'Mini Ayam Bawang 35g', 'Mini Baso Sapi 35g', 'Mini Soto Mie 35g'], 'price' => [4800, 6000]],
                    // Bihun & Pasta
                    ['brand' => 'Bihunku', 'base' => 'Bihun Instan', 'variants' => ['Rasa Ayam Bawang 55g', 'Rasa Soto 55g', 'Rasa Goreng 60g', 'Rasa Asam Pedas 55g', 'Rasa Kari Ayam 55g', 'Spicy Korean 55g'], 'price' => [3500, 4500]],
                    ['brand' => 'La Fonte', 'base' => 'Pasta', 'variants' => ['Spaghetti-10 450g', 'Spaghetti-10 225g', 'Spaghetti-11 450g', 'Fettuccine-31 225g', 'Macaroni-206 225g', 'Macaroni-206 450g', 'Penne Rigate-91 225g', 'Fusilli-01 225g', 'Lasagna 225g', 'Pronto Spaghetti Bolognese 117g', 'Pronto Spaghetti Carbonara 117g', 'Pronto Macaroni Cheese 117g'], 'price' => [7500, 22000]],
                    // Sarden & Kornet
                    ['brand' => 'ABC', 'base' => 'Sarden', 'variants' => ['Saus Tomat 155g', 'Saus Cabai 155g', 'Saus Ekstra Pedas 155g', 'Saus Tomat 425g', 'Saus Cabai 425g', 'Saus Ekstra Pedas 425g', 'Bumbu Serundeng 155g', 'Gulai 155g', 'Rendang 155g'], 'price' => [10500, 26000]],
                    ['brand' => 'Pronas', 'base' => 'Kornet Sapi', 'variants' => ['Classic Can 198g', 'Classic Can 340g', 'Pedas Can 198g', 'Sachet 50g', 'Pouch 100g', 'Daging Sapi Luncheon 340g', 'Daging Ayam Luncheon 340g'], 'price' => [6500, 42000]],
                    // Bumbu Dapur Instan & Tepung
                    ['brand' => 'Bamboe', 'base' => 'Bumbu Instan', 'variants' => ['Rendang 54g', 'Rawon 54g', 'Soto Ayam 40g', 'Soto Madura 40g', 'Sayur Asem 33g', 'Opor 36g', 'Nasi Goreng 40g', 'Gulai 35g', 'Kare 36g', 'Ayam Goreng 33g', 'Semur 69g', 'Tahu Tempe Bacem 50g', 'Tom Yum 60g', 'Nasi Uduk 45g', 'Nasi Kuning 50g', 'Sambal Goreng Ati 54g', 'Sop 49g', 'Bumbu Bali 49g'], 'price' => [6500, 8500]],
                    ['brand' => 'Racik', 'base' => 'Bumbu Spesial', 'variants' => ['Ayam Goreng 20g', 'Ikan Goreng 20g', 'Tempe Goreng 20g', 'Sayur Asem 33g', 'Sayur Sop 20g', 'Sayur Lodeh 20g', 'Tumis 22g', 'Nasi Goreng Spesial 20g', 'Nasi Goreng Pedas 20g', 'Rendang 45g', 'Kare 45g', 'Opor Ayam 45g'], 'price' => [2500, 5500]],
                    ['brand' => 'Sasa', 'base' => 'Tepung Bumbu', 'variants' => ['Serbaguna Original 210g', 'Serbaguna Hot & Spicy 210g', 'Bakwan Spesial 210g', 'Pisang Goreng Vanilla 210g', 'Kentucky Crispy 200g', 'Ayam Goreng Renyah 200g', 'Udang & Seafood 200g'], 'price' => [5500, 7500]],
                    ['brand' => 'Sajiku', 'base' => 'Tepung Bumbu', 'variants' => ['Golden Crispy 200g', 'Serbaguna 220g', 'Bakwan Crispy 220g', 'Pedas 200g', 'Tempe Crispy 200g', 'Bumbu Nasi Goreng Rasa Ayam 20g', 'Bumbu Nasi Goreng Pedas 20g'], 'price' => [5000, 7500]],
                    ['brand' => 'Kobe', 'base' => 'Tepung Bumbu', 'variants' => ['SuperCrispy Kentucky 200g', 'Tempe Kriuk 200g', 'Bakwan Kress 200g', 'Putih Serbaguna 200g', 'Pisang Goreng Crispy 200g'], 'price' => [5000, 7500]],
                    ['brand' => 'BonCabe', 'base' => 'Sambal Tabur', 'variants' => ['Level 10 Original 45g', 'Level 15 Original 45g', 'Level 30 Parameter 40g', 'Level 50 Max End 30g', 'Level 3 Nori 35g', 'Level 10 Teri 45g', 'Mi BonCabe Lv 15 105g', 'Mi BonCabe Lv 30 105g'], 'price' => [7000, 12000]],
                    ['brand' => 'Mamasuka', 'base' => 'Rumput Laut', 'variants' => ['Panggang Pedas 2x4.5g', 'Panggang BBQ 2x4.5g', 'Panggang Sapi Panggang 2x4.5g', 'Panggang Jagung Bakar 2x4.5g', 'Gim Bori Pedas 30g', 'Gim Bori Original 30g'], 'price' => [12500, 16500]],
                ]
            ],

            // -----------------------------------------------------------------
            // 2. MINUMAN DINGIN & HANGAT (Target ~450 item)
            // -----------------------------------------------------------------
            [
                'cat_id' => $catMinuman,
                'prefix' => 'MNM',
                'items' => [
                    // Air Mineral
                    ['brand' => 'Aqua', 'base' => 'Air Mineral', 'variants' => ['Botol 330ml', 'Botol 600ml', 'Botol 1500ml', 'Click Sport 750ml', 'Karton Dus 600ml (24 btl)', 'Galon 19L (Refill)'], 'price' => [2500, 35000]],
                    ['brand' => 'Le Minerale', 'base' => 'Air Mineral', 'variants' => ['Botol 330ml', 'Botol 600ml', 'Botol 1500ml', 'Galon Sekali Pakai 15L'], 'price' => [2500, 22000]],
                    ['brand' => 'Nestle Pure Life', 'base' => 'Air Mineral', 'variants' => ['Botol 330ml', 'Botol 600ml', 'Botol 1500ml'], 'price' => [2500, 6000]],
                    ['brand' => 'Cleo', 'base' => 'Air Murni', 'variants' => ['Botol 220ml Cup', 'Botol 330ml', 'Botol 550ml Eco', 'Botol 1200ml', 'Botol Smart 550ml'], 'price' => [1500, 6500]],
                    ['brand' => 'Pristine 8.6+', 'base' => 'Air Alkali', 'variants' => ['Botol 400ml', 'Botol 600ml', 'Botol 1500ml'], 'price' => [4500, 10500]],
                    // Teh Kemasan
                    ['brand' => 'Teh Botol Sosro', 'base' => 'Teh Melati', 'variants' => ['Kotak 250ml', 'Kotak 200ml', 'Botol PET 450ml', 'Botol PET 350ml', 'Can Kaleng 318ml', 'Less Sugar Kotak 250ml', 'Tawar Kotak 250ml'], 'price' => [3500, 8500]],
                    ['brand' => 'Teh Pucuk Harum', 'base' => 'Teh Melati', 'variants' => ['Botol 350ml', 'Botol 500ml', 'Less Sugar 350ml', 'Sugar Free 350ml'], 'price' => [3500, 6500]],
                    ['brand' => 'Frestea', 'base' => 'Teh Kemasan', 'variants' => ['Rasa Apel 350ml', 'Rasa Melati 350ml', 'Rasa Markisa 350ml', 'Rasa Madu 350ml', 'Rasa Green Tea Madu 350ml', 'Rasa Lemongrass 350ml', 'Botol 500ml Apel', 'Botol 500ml Melati'], 'price' => [4500, 7500]],
                    ['brand' => 'Fruit Tea', 'base' => 'Teh Buah', 'variants' => ['Rasa Apel 500ml', 'Rasa Blackcurrant 500ml', 'Rasa Freeze Dingin 500ml', 'Rasa Guava Jambu 500ml', 'Rasa Strawberry 500ml', 'Genggam Pouch 230ml Apel', 'Genggam Pouch 230ml Blackcurrant'], 'price' => [3500, 6500]],
                    ['brand' => 'Nu Green Tea', 'base' => 'Teh Hijau', 'variants' => ['Original 450ml', 'Madu 450ml', 'Gula Batu 450ml', 'Less Sugar 450ml', 'Royal Jasmine 450ml'], 'price' => [5000, 7000]],
                    ['brand' => 'Ichi Ocha', 'base' => 'Teh Hijau', 'variants' => ['Melati 350ml', 'Melati 500ml', 'Hojicha Panggang 350ml'], 'price' => [3500, 6000]],
                    // Susu & Olahan Susu
                    ['brand' => 'Ultra Milk', 'base' => 'Susu UHT', 'variants' => ['Full Cream 1000ml', 'Full Cream 250ml', 'Full Cream 200ml', 'Full Cream 125ml', 'Cokelat 1000ml', 'Cokelat 250ml', 'Cokelat 200ml', 'Cokelat 125ml', 'Strawberry 250ml', 'Strawberry 200ml', 'Taro 200ml', 'Karamel 200ml', 'Moka 200ml', 'Low Fat Cokelat 1000ml', 'Low Fat Vanilla 1000ml'], 'price' => [3500, 20500]],
                    ['brand' => 'Bear Brand', 'base' => 'Susu Steril', 'variants' => ['Original Kaleng 189ml', 'Gold White Tea 140ml', 'Gold White Malt 140ml'], 'price' => [9500, 11500]],
                    ['brand' => 'Indomilk', 'base' => 'Susu UHT', 'variants' => ['Full Cream 950ml', 'Cokelat 950ml', 'Kids Cokelat 115ml', 'Kids Strawberry 115ml', 'Kids Vanilla 115ml', 'Botol Bantal Cokelat 190ml', 'Botol Bantal Strawberry 190ml', 'Kental Manis Putih 545g', 'Kental Manis Cokelat 545g', 'Kental Manis Sachet Putih 6s', 'Kental Manis Sachet Cokelat 6s'], 'price' => [7000, 18500]],
                    ['brand' => 'Frisian Flag', 'base' => 'Susu Kental Manis', 'variants' => ['Bendera Putih Kaleng 370g', 'Bendera Cokelat Kaleng 370g', 'Pouch Putih 545g', 'Pouch Cokelat 545g', 'Gold Putih 370g', 'Purefarm Full Cream 900ml', 'Purefarm Swiss Chocolate 900ml', 'Purefarm Sweet Delight 225ml', 'Purefarm Strawberry 225ml'], 'price' => [8500, 19500]],
                    ['brand' => 'Cimory', 'base' => 'Fresh Milk UHT', 'variants' => ['Matcha 250ml', 'Banana 250ml', 'Almond 250ml', 'Marie Biscuit 250ml', 'Salted Caramel 250ml', 'Strawberry 250ml', 'Chocomalt 250ml', 'Hazelnut 250ml', 'Bebas Laktosa 250ml'], 'price' => [6000, 7500]],
                    ['brand' => 'Cimory', 'base' => 'Yogurt Squeeze', 'variants' => ['Original 120g', 'Blueberry 120g', 'Strawberry 120g', 'Mango 120g', 'Peach 120g', 'Cavendish Banana 120g', 'Aloe Vera 120g', 'Taro 120g', 'Brown Sugar 120g', 'Purple Taro 120g'], 'price' => [8500, 11000]],
                    ['brand' => 'Yakult', 'base' => 'Minuman Probiotik', 'variants' => ['Original 5x65ml', 'Yakult Light Less Sugar 5x65ml'], 'price' => [10500, 14000]],
                    ['brand' => 'Milo', 'base' => 'Minuman Cokelat Malt', 'variants' => ['UHT Kotak 180ml', 'UHT Kotak 110ml', 'Can Active Go 240ml', 'Bubuk Sachet 4x22g', 'Bubuk Polybag 300g', 'Bubuk Polybag 1kg'], 'price' => [4500, 85000]],
                    // Minuman Isotonik & Vitamin
                    ['brand' => 'Pocari Sweat', 'base' => 'Isotonik', 'variants' => ['Kaleng 330ml', 'Botol PET 350ml', 'Botol PET 500ml', 'Botol PET 900ml', 'Botol Jumbo 2000ml', 'Sachet Bubuk 15g (5s)', 'Ion Water 500ml', 'Ion Water 350ml'], 'price' => [5500, 24000]],
                    ['brand' => 'You C1000', 'base' => 'Vitamin C', 'variants' => ['Orange Water 500ml', 'Lemon Water 500ml', 'Apple Water 500ml', 'Botol Kaca Orange 140ml', 'Botol Kaca Lemon 140ml', 'Botol Kaca Apple 140ml'], 'price' => [6500, 9500]],
                    ['brand' => 'Hydro Coco', 'base' => 'Air Kelapa Asli', 'variants' => ['Tetra Pak 250ml', 'Tetra Pak 330ml', 'Tetra Pak 500ml', 'Tetra Pak 1000ml'], 'price' => [6500, 22000]],
                    ['brand' => 'Mizone', 'base' => 'Isotonik Berperisa', 'variants' => ['Apple Guava 500ml', 'Lychee Lemon 500ml', 'Cranberry 500ml', 'Starfruit 500ml'], 'price' => [4500, 6000]],
                    ['brand' => 'Oronamin C', 'base' => 'Minuman Vitamin Karbonasi', 'variants' => ['Botol Kaca 120ml'], 'price' => [6500, 8000]],
                    // Kopi Siap Minum (RTD) & Kopi Bubuk
                    ['brand' => 'Nescafe', 'base' => 'Kopi Kemasan', 'variants' => ['Original Can 240ml', 'Mocha Can 240ml', 'Latte Can 240ml', 'Caramel Macchiato Can 240ml', 'Classic Jar 100g', 'Classic Sachet 10x2g', 'Gold Jar 100g', 'Kopi Susu UHT 220ml'], 'price' => [8000, 75000]],
                    ['brand' => 'Good Day', 'base' => 'Kopi Botol RTD', 'variants' => ['Mocacinno 250ml', 'Avocado 250ml', 'Tiramisu Bliss 250ml', 'Funtastic Mocacinno 250ml', 'Original Coffee 250ml'], 'price' => [6000, 7500]],
                    ['brand' => 'Good Day', 'base' => 'Kopi Sachet', 'variants' => ['Cappuccino with Choco Granule 10x25g', 'Mocacinno 10x20g', 'Chococinno 10x20g', 'Vanilla Latte 10x20g', 'Coolin Coffee Mint 10x20g', 'Carrebian Nut 10x20g'], 'price' => [12500, 19500]],
                    ['brand' => 'Kapal Api', 'base' => 'Kopi Bubuk', 'variants' => ['Spesial Merah 165g', 'Spesial Merah 380g', 'Spesial Mix Gula 10x24g', 'Spesial Mix Gula 20x24g', 'Kopi Mantap Gula 10x25g', 'Kopi Gula Aren 10x23g', 'Signature Original Botol 200ml', 'Signature Black Botol 200ml'], 'price' => [5000, 32000]],
                    ['brand' => 'Luwak White Koffie', 'base' => 'Kopi Rendah Asam', 'variants' => ['Original Bag 10x20g', 'Original Bag 20x20g', 'Caramel Bag 10x20g', 'Mocca Bag 10x20g', 'French Vanilla 10x20g', 'Koffie Tarik 10x30g', 'Botol RTD 220ml'], 'price' => [6000, 26000]],
                    ['brand' => 'Torabika', 'base' => 'Kopi Instan', 'variants' => ['Duo Kopi Gula 10x25g', 'Kopi Susu Espresso 10x28g', 'Creamy Latte 10x25g', 'Cappuccino Choco Granule 10x25g', 'Gilus Mix Gula Aren 10x23g', 'Gilus Mix Pandan 10x23g', 'Gilus Mix Vanilla 10x23g'], 'price' => [9500, 18500]],
                    // Minuman Soda & Sari Buah
                    ['brand' => 'Coca-Cola', 'base' => 'Minuman Berkarbonasi', 'variants' => ['Botol 390ml', 'Botol 1500ml', 'Kaleng Can 330ml', 'Zero Sugar Botol 390ml', 'Zero Sugar Kaleng 330ml'], 'price' => [5500, 17500]],
                    ['brand' => 'Sprite', 'base' => 'Minuman Lemon-Lime', 'variants' => ['Botol 390ml', 'Botol 1500ml', 'Kaleng Can 330ml', 'Waterlymon 425ml'], 'price' => [5500, 17500]],
                    ['brand' => 'Fanta', 'base' => 'Minuman Soda Berperisa', 'variants' => ['Strawberry Botol 390ml', 'Strawberry Botol 1500ml', 'Strawberry Kaleng 330ml', 'Orange Jeruk Botol 390ml'], 'price' => [5500, 17500]],
                    ['brand' => 'Buavita', 'base' => 'Jus Buah Asli', 'variants' => ['Rasa Jambu 250ml', 'Rasa Jambu 1000ml', 'Rasa Jeruk 250ml', 'Rasa Jeruk 1000ml', 'Rasa Mangga 250ml', 'Rasa Mangga 1000ml', 'Rasa Apel 250ml', 'Rasa Leci 250ml'], 'price' => [7500, 26000]],
                    ['brand' => 'Floridina', 'base' => 'Minuman Sari Jeruk Bulir', 'variants' => ['Orange PET 350ml', 'Coco Pulpy 350ml'], 'price' => [3500, 4500]],
                    ['brand' => 'Nutrisari', 'base' => 'Minuman Serbuk Buah', 'variants' => ['Jeruk Manis 10s', 'Jeruk Peras 10s', 'Florida Orange 10s', 'American Sweet Orange 10s', 'Blewah 10s', 'Mangga Gandaria 10s', 'Kelapa Muda 10s', 'Madu Lemon 10s'], 'price' => [12500, 15000]],
                ]
            ],

            // -----------------------------------------------------------------
            // 3. SNACK & BISKUIT (Target ~400 item)
            // -----------------------------------------------------------------
            [
                'cat_id' => $catSnack,
                'prefix' => 'SNK',
                'items' => [
                    // Keripik Kentang & Singkong
                    ['brand' => 'Chitato', 'base' => 'Keripik Kentang', 'variants' => ['Sapi Panggang 68g', 'Ayam Bumbu 68g', 'Keju Supreme 68g', 'Spicy Chicken 68g', 'Sapi Bumbu Bakar 68g', 'Jumbo Sapi Panggang 120g', 'Lite Nori Seaweed 68g', 'Lite Sour Cream & Onion 68g', 'Lite Truffle 68g', 'Lite Beef Barbeque 68g', 'Lite Aburi Seaweed 68g'], 'price' => [10500, 21000]],
                    ['brand' => 'Qtela', 'base' => 'Keripik Singkong & Tempe', 'variants' => ['Singkong Balado 180g', 'Singkong Barbeque 180g', 'Singkong Keju 180g', 'Singkong Original Gurih 180g', 'Singkong Ayam Geprek 180g', 'Tempe Original 180g', 'Tempe Daun Jeruk 180g', 'Tempe Cabe Rawit 180g', 'Opak Barbeque 180g'], 'price' => [12500, 17500]],
                    ['brand' => 'Taro', 'base' => 'Snack Net', 'variants' => ['Net Seaweed Rumput Laut 65g', 'Net Potato Barbeque 65g', 'Net Cowboy Steak 65g', 'Net Italian Pizza 65g', 'Teriyaki 65g'], 'price' => [5000, 6500]],
                    ['brand' => 'Chiki', 'base' => 'Snack Ekstrudat', 'variants' => ['Balls Keju Craft 55g', 'Balls Cokelat 55g', 'Balls Ayam Panggang 55g', 'Twist Jagung Bakar 40g', 'Net Potato BBQ 40g'], 'price' => [4500, 6000]],
                    ['brand' => 'Piattos', 'base' => 'Keripik Kentang Hexagonal', 'variants' => ['Sapi Panggang 75g', 'Rumput Laut 75g', 'Ayam Teriyaki 75g', 'Sambal Geprek 75g', 'Sambal Matah 75g', 'Kimchi 75g'], 'price' => [8500, 10500]],
                    ['brand' => 'Pringles', 'base' => 'Potato Crisps Kaleng', 'variants' => ['Original 107g', 'Sour Cream & Onion 107g', 'Cheesy Cheese 107g', 'Hot & Spicy 107g', 'Barbeque 107g', 'Salt & Seaweed 107g'], 'price' => [18500, 24000]],
                    ['brand' => 'Potabee', 'base' => 'Potato Chips V-Cut', 'variants' => ['Beef BBQ 68g', 'Seaweed Crunch 68g', 'Melted Cheese 68g', 'Ayam Bakar 68g', 'Black Truffle 68g'], 'price' => [9500, 11500]],
                    ['brand' => 'Kusuka', 'base' => 'Keripik Singkong', 'variants' => ['Balado 180g', 'Super Pedas 180g', 'Rumput Laut 180g', 'Barbeque 180g', 'Keju Bakar 180g', 'Original 180g'], 'price' => [13500, 16500]],
                    ['brand' => 'Oishi', 'base' => 'Snack Renyah', 'variants' => ['Pillows Cokelat 100g', 'Pillows Ubi Manis 100g', 'Pillows Keju 100g', 'Pillows Durian 100g', 'Sponge Crunch Cokelat 100g', 'Sponge Crunch Strawberry 100g', 'Sponge Crunch Keju 100g', 'Popcorn Karamel 100g', 'Udang Pedas 60g'], 'price' => [8000, 11500]],
                    // Biskuit & Wafer
                    ['brand' => 'Oreo', 'base' => 'Biskuit Sandwich', 'variants' => ['Vanilla Roll 119g', 'Cokelat Roll 119g', 'Strawberry Roll 119g', 'Peanut Butter & Choco 119g', 'Red Velvet Roll 119g', 'Double Stuf Vanilla 147g', 'Ice Cream Blueberry 119g', 'Mini Oreo Pouch Cup Vanilla 61g', 'Mini Oreo Pouch Cup Cokelat 61g', 'Soft Cookies 120g'], 'price' => [8500, 14000]],
                    ['brand' => 'Roma', 'base' => 'Biskuit Legendaris', 'variants' => ['Kelapa Biskuit 300g', 'Kelapa Biskuit Jumbo 450g', 'Marie Gold Susu 240g', 'Marie Gold Cokelat 240g', 'Sari Gandum Gandum Asli 240g', 'Sari Gandum Sandwich Susu Cokelat 115g', 'Sari Gandum Sandwich Kacang 115g', 'Malkist Crackers Manis 135g', 'Malkist Abon Sapi Gurih 135g', 'Malkist Keju Manis 135g', 'Malkist Cokelat Belgia 135g', 'Malkist Kelapa Kopyor 135g', 'Malkist Cappuccino 135g', 'Slai O Lai Strawberry 128g', 'Slai O Lai Blueberry 128g', 'Slai O Lai Nanas 128g', 'Better Sandwich Vanilla 120g', 'Wafello Choco Blast 130g', 'Wafello Caramel Butter 130g'], 'price' => [6500, 16500]],
                    ['brand' => 'Tango', 'base' => 'Wafer Renyah', 'variants' => ['Cokelat Pouch 130g', 'Vanila Pouch 130g', 'Strawberry Pouch 130g', 'Kacang Hazelnut 130g', 'Walut Choco Banana 100g', 'Walut Choco Cheese 100g', 'Kaleng Cokelat 300g', 'Kaleng Vanilla 300g', 'Waffle Cookiez Choco 120g'], 'price' => [7500, 26000]],
                    ['brand' => 'Nabati', 'base' => 'Wafer Ekstra Krim', 'variants' => ['Richeese Keju 130g', 'Richoco Cokelat 130g', 'Pink Lava 130g', 'White Vanilla 130g', 'Ah Keju Box 10x8g', 'Ah Cokelat Box 10x8g', 'Nextar Brownies Cokelat 106g', 'Nextar Strawberry 106g', 'Nextar Blueberry 106g', 'Nextar Kastengel Keju 106g', 'Siip Keju 50g', 'Siip Cokelat 50g'], 'price' => [3500, 10500]],
                    ['brand' => 'Beng-Beng', 'base' => 'Wafer Karamel Cokelat', 'variants' => ['Reguler Pack 3x20g', 'Maxx Jumbo 32g', 'Share It Bag 10x9.5g', 'White Chocolate 3x20g', 'Drink Cokelat 4x30g'], 'price' => [6500, 14500]],
                    ['brand' => 'SilverQueen', 'base' => 'Cokelat Batang', 'variants' => ['Cashew Mede 58g', 'Almond 58g', 'White Cashew 58g', 'Dark Chocolate 58g', 'Very Berry Yogurt 58g', 'Green Tea Matcha 58g', 'Chunky Bar Cashew 95g', 'Chunky Bar White 95g', 'Bites Cashew Bag 35g', 'Rockr Dark 22g'], 'price' => [10500, 26500]],
                    ['brand' => 'Cadbury', 'base' => 'Dairy Milk Chocolate', 'variants' => ['Milk Chocolate 62g', 'Cashew Nut Mede 62g', 'Fruit & Nut 62g', 'Black Forest 62g', 'Hazelnut 62g', 'Oreo Chocolate 60g'], 'price' => [14500, 17500]],
                    ['brand' => 'KitKat', 'base' => 'Wafer Cokelat', 'variants' => ['4 Finger 35g', '2 Finger 17g', 'Chunky Milk 38g', 'Green Tea Matcha 35g', 'Gold Caramel 35g', 'Sharebag 6x17g'], 'price' => [5000, 32000]],
                    ['brand' => 'Regal', 'base' => 'Marie Biskuit', 'variants' => ['Superior Roll 230g', 'Superior Roll 120g', 'Special Roll Mentega 230g', 'Duo Krim Cokelat 100g', 'Duo Krim Vanila 100g', 'Kaleng Tin 550g', 'Kaleng Tin 1000g'], 'price' => [9500, 75000]],
                    ['brand' => 'Khong Guan', 'base' => 'Biskuit Klasik', 'variants' => ['Red Assorted Tin 650g', 'Red Assorted Tin 1600g', 'Malkist Crackers 300g', 'Saltcheese Crackers 200g', 'Togo Cokelat Biskuit 128g'], 'price' => [12500, 110000]],
                    ['brand' => 'Monde', 'base' => 'Butter Cookies & Pie', 'variants' => ['Selected Butter Cookies 454g', 'Genji Pie Strawberry 85g', 'Genji Pie Original 85g', 'Serena Egg Roll 60g', 'Borobudur Biscuit 250g'], 'price' => [9500, 85000]],
                    ['brand' => 'Nissin', 'base' => 'Wafer & Crackers', 'variants' => ['Wafer Cokelat Tin 570g', 'Crispy Crackers 250g', 'Walens Choco Soes 100g', 'Walens Blueberry Soes 100g', 'Walens Cheese Soes 100g', 'Gekikara Ramen Goreng 120g', 'Gekikara Ramen Kuah 120g'], 'price' => [7500, 48000]],
                    ['brand' => 'Good Time', 'base' => 'Cookies Chocochip', 'variants' => ['Classic Chocochip 72g', 'Double Choc 72g', 'Rainbow 72g', 'Pandan Chocochip 72g', 'Coffee Chocochip 72g', 'Tin Kaleng 149g'], 'price' => [7500, 24000]],
                    ['brand' => 'Yupi', 'base' => 'Permen Gummy', 'variants' => ['Gummy Bears 45g', 'Burger 45g', 'Roletto Roll 45g', 'Strawberry Kiss 45g', 'Baby Bears 45g', 'Aquarium 45g', 'Neon Stix 45g', 'Worm Belatung 45g'], 'price' => [3500, 5000]],
                ]
            ],

            // -----------------------------------------------------------------
            // 4. SEMBAKO & KEBUTUHAN DAPUR (Target ~350 item)
            // -----------------------------------------------------------------
            [
                'cat_id' => $catSembako,
                'prefix' => 'SBK',
                'items' => [
                    // Beras Premium
                    ['brand' => 'Sania', 'base' => 'Beras Premium', 'variants' => ['Pulen Wangi 5kg', 'Pulen Wangi 2.5kg', 'Pulen Kristal 5kg'], 'price' => [68000, 75000]],
                    ['brand' => 'Rojolele', 'base' => 'Beras Delanggu', 'variants' => ['Super Pandan Wangi 5kg', 'Pulen Super 5kg', 'Setra Ramos 5kg', 'Kepala Super 5kg'], 'price' => [67000, 74000]],
                    ['brand' => 'Topi Koki', 'base' => 'Beras Premium', 'variants' => ['Setra Ramos 5kg', 'Pandan Wangi 5kg', 'Long Grain 5kg'], 'price' => [68000, 76000]],
                    ['brand' => 'Maknyuss', 'base' => 'Beras Putih', 'variants' => ['Pulen Kristal 5kg', 'Pulen Kristal 2.5kg'], 'price' => [69000, 76000]],
                    // Minyak Goreng
                    ['brand' => 'Bimoli', 'base' => 'Minyak Goreng Sawit', 'variants' => ['Pouch Isi Ulang 1L', 'Pouch Isi Ulang 2L', 'Klasik Pouch 2L', 'Spesial Botol 1L', 'Spesial Botol 2L', 'Jerigen 5L'], 'price' => [18500, 95000]],
                    ['brand' => 'Filma', 'base' => 'Minyak Goreng Non Kolesterol', 'variants' => ['Pouch Isi Ulang 1L', 'Pouch Isi Ulang 2L', 'Botol 1L', 'Botol 2L'], 'price' => [19000, 39500]],
                    ['brand' => 'SunCo', 'base' => 'Minyak Goreng Bening', 'variants' => ['Pouch Isi Ulang 1L', 'Pouch Isi Ulang 2L', 'Botol 1L', 'Botol 2L', 'Jerigen 5L'], 'price' => [18500, 96000]],
                    ['brand' => 'Tropical', 'base' => 'Minyak Goreng 2x Penyaringan', 'variants' => ['Botol 1L', 'Botol 2L', 'Pouch Isi Ulang 1L', 'Pouch Isi Ulang 2L'], 'price' => [19000, 39000]],
                    ['brand' => 'Sania', 'base' => 'Minyak Goreng Royale', 'variants' => ['Pouch Isi Ulang 1L', 'Pouch Isi Ulang 2L'], 'price' => [18000, 37500]],
                    ['brand' => 'Fortune', 'base' => 'Minyak Goreng Nabati', 'variants' => ['Pouch Isi Ulang 1L', 'Pouch Isi Ulang 2L'], 'price' => [17500, 36500]],
                    ['brand' => 'Minyakita', 'base' => 'Minyak Goreng Rakyat', 'variants' => ['Pouch Bantal 1L', 'Botol Higienis 1L', 'Pouch Berdiri 1L'], 'price' => [14000, 16000]],
                    // Gula & Pemanis
                    ['brand' => 'Gulaku', 'base' => 'Gula Pasir Tebu', 'variants' => ['Premium Putih Murni 1kg', 'Tebu Kuning Alami 1kg', 'Pouch Standing 750g', 'Sachet Stick 100x8g'], 'price' => [17500, 26000]],
                    ['brand' => 'Rose Brand', 'base' => 'Gula Pasir Kristal', 'variants' => ['Putih Murni 1kg', 'Kuning Alami 1kg', 'Gula Halus Tepung 500g'], 'price' => [16500, 18500]],
                    ['brand' => 'Tropicana Slim', 'base' => 'Pemanis Nol Kalori', 'variants' => ['Diabtx Bebas Gula 50s', 'Classic Sweetener 50s', 'Stevia Alami 50s', 'Gula Jawa Bebas Gula 350ml', 'Kecap Manis Bebas Gula 200ml'], 'price' => [28500, 48000]],
                    ['brand' => 'Madu TJ', 'base' => 'Madu Murni Asli', 'variants' => ['Murni Botol 150g', 'Murni Botol 250g', 'Murni Botol 500g', 'Kurma Botol 150g', 'Kurma Botol 250g', 'Super Extra Bee Pollen 150g', 'Panas Dalam Sachet 12s'], 'price' => [19500, 62000]],
                    // Tepung Terigu & Tepung Khusus
                    ['brand' => 'Segitiga Biru', 'base' => 'Tepung Terigu Serbaguna', 'variants' => ['Kemasan Premium 1kg', 'Kemasan Ekonomis 1kg'], 'price' => [12500, 14500]],
                    ['brand' => 'Cakra Kembar', 'base' => 'Tepung Terigu Roti & Mie', 'variants' => ['Kemasan 1kg', 'Emas Roti Lembut 1kg'], 'price' => [13500, 16000]],
                    ['brand' => 'Kunci Biru', 'base' => 'Tepung Terigu Kue Kering', 'variants' => ['Kemasan 1kg'], 'price' => [12000, 13500]],
                    ['brand' => 'Rose Brand', 'base' => 'Tepung Tradisional', 'variants' => ['Tepung Beras Putih 500g', 'Tepung Ketan Putih 500g', 'Tepung Tapioka Singkong 500g'], 'price' => [7500, 11500]],
                    ['brand' => 'Maizenaku', 'base' => 'Tepung Jagung Maizena', 'variants' => ['Kemasan Kotak 150g', 'Kemasan Kotak 300g', 'Kemasan Box 750g'], 'price' => [6000, 18500]],
                    // Margarin, Mentega & Santan
                    ['brand' => 'Blue Band', 'base' => 'Margarin Serbaguna', 'variants' => ['Sachet Ekonomis 200g', 'Tub Mangkuk 250g', 'Cake & Cookie Aroma Butter 200g'], 'price' => [9500, 18500]],
                    ['brand' => 'Palmia', 'base' => 'Margarin Dapur', 'variants' => ['Serbaguna Sachet 200g', 'Royal Butter Margarine 200g', 'Bawang Gurih 200g'], 'price' => [7500, 10500]],
                    ['brand' => 'Kara', 'base' => 'Santan Kelapa Murni', 'variants' => ['UHT Segitiga 65ml', 'UHT Kotak 200ml', 'UHT Tetra Pak 1000ml', 'Nata De Coco 360g', 'Bubuk Santan 50g'], 'price' => [3500, 34000]],
                    ['brand' => 'Sun Kara', 'base' => 'Santan Kelapa Olahan', 'variants' => ['Segitiga 65ml', 'Krim Kelapa 200ml'], 'price' => [3200, 9500]],
                    // Keju Olahan
                    ['brand' => 'Kraft', 'base' => 'Keju Olahan', 'variants' => ['Cheddar Block 165g', 'Cheddar Block Mini 75g', 'Singles Lembaran 10s', 'Singles Lembaran 5s', 'Quick Melt Leleh 165g'], 'price' => [11500, 27500]],
                    ['brand' => 'Prochiz', 'base' => 'Keju Cheddar Dapur', 'variants' => ['Cheddar Gold Block 160g', 'Quick Melt Leleh 160g', 'Spready Olesan 170g', 'Slice Lembaran 10s', 'Slice Lembaran 5s'], 'price' => [10000, 19500]],
                    // Kecap, Saus & Bumbu Dasar
                    ['brand' => 'Bango', 'base' => 'Kecap Manis Kedelai Hitam', 'variants' => ['Pouch Isi Ulang 520ml', 'Pouch Isi Ulang 735ml', 'Pouch Mini 220ml', 'Pouch Hemat 135ml', 'Botol Kaca 275ml', 'Botol Kaca 620ml', 'Kecap Pedas Gurih 220ml', 'Bango Light Rendah Gula 550ml'], 'price' => [8500, 29000]],
                    ['brand' => 'ABC', 'base' => 'Kecap Manis', 'variants' => ['Pouch Isi Ulang 520ml', 'Pouch Isi Ulang 700ml', 'Pouch Ekonomis 225ml', 'Botol Kaca 275ml', 'Kecap Asin Botol 135ml', 'Kecap Inggris 135ml'], 'price' => [7500, 26000]],
                    ['brand' => 'ABC', 'base' => 'Saus Sambal & Tomat', 'variants' => ['Sambal Asli Botol 275ml', 'Sambal Asli Botol 135ml', 'Sambal Asli Pouch 380ml', 'Sambal Ekstra Pedas Botol 275ml', 'Sambal Ekstra Pedas Pouch 380ml', 'Sambal Bawang Pedas 275ml', 'Saus Tomat Murni Botol 275ml', 'Saus Tomat Pouch 380ml'], 'price' => [6500, 16000]],
                    ['brand' => 'Dua Belibis', 'base' => 'Saus Cabe Istimewa', 'variants' => ['Botol Kaca 340ml', 'Botol Kaca 135ml', 'Pouch Standing 535g', 'Saus Tomat Botol 340ml'], 'price' => [9500, 24000]],
                    ['brand' => 'Saori', 'base' => 'Saus Masak Oriental', 'variants' => ['Saus Tiram Botol 135ml', 'Saus Tiram Botol 270ml', 'Saus Teriyaki Botol 135ml', 'Saus Asam Manis Botol 135ml', 'Saus Lada Hitam Botol 135ml', 'Saus Mentega Botol 135ml', 'Saus Tiram Sachet 23ml (10s)'], 'price' => [10500, 23000]],
                    ['brand' => 'Royco', 'base' => 'Penyedap Kaldu Asli', 'variants' => ['Kaldu Ayam Pouch 230g', 'Kaldu Sapi Pouch 230g', 'Kaldu Ayam Pouch 460g', 'Kaldu Sapi Pouch 460g', 'Kaldu Jamur Tanpa Penguat Rasa 100g', 'Saus Tiram Botol 200ml'], 'price' => [9500, 19500]],
                    ['brand' => 'Masako', 'base' => 'Bumbu Ekstrak Daging', 'variants' => ['Kaldu Ayam Pouch 250g', 'Kaldu Sapi Pouch 250g', 'Rasa Gurih Pas 250g'], 'price' => [9500, 12000]],
                    ['brand' => 'Ladaku', 'base' => 'Merica Bubuk Murni', 'variants' => ['Sachet Renceng 4g (12s)', 'Botol Tabur 45g'], 'price' => [10000, 14000]],
                    ['brand' => 'Desaku', 'base' => 'Bumbu Bubuk Rempah Alami', 'variants' => ['Ketumbar Bubuk 15g (12s)', 'Kunyit Bubuk 10g (12s)', 'Bumbu Balado 15g (12s)', 'Bumbu Kari 15g (12s)', 'Bumbu Opor 15g (12s)', 'Bumbu Sambal Goreng 15g (12s)'], 'price' => [10000, 14000]],
                    ['brand' => 'Ajinomoto', 'base' => 'Penyedap Rasa MSG', 'variants' => ['Kemasan Plastik 50g', 'Kemasan Plastik 100g', 'Kemasan Plastik 250g', 'Kemasan Dapur 500g', 'Aji-Plus 500g'], 'price' => [2500, 22000]],
                    ['brand' => 'Garam Dolphin', 'base' => 'Garam Meja Beryodium', 'variants' => ['Bungkus 500g', 'Bungkus 250g', 'Halus Meja 500g'], 'price' => [4500, 7500]],
                ]
            ],

            // -----------------------------------------------------------------
            // 5. PERSONAL CARE & MANDI (Target ~350 item)
            // -----------------------------------------------------------------
            [
                'cat_id' => $catPersonalCare,
                'prefix' => 'PRC',
                'items' => [
                    // Sabun Mandi Cair & Batang
                    ['brand' => 'Lifebuoy', 'base' => 'Sabun Mandi Anti Bakteri', 'variants' => ['Total 10 Refill Pouch 450ml', 'Total 10 Botol Pump 500ml', 'Total 10 Sabun Batang 85g', 'Lemon Fresh Refill Pouch 450ml', 'Lemon Fresh Sabun Batang 85g', 'Mild Care Refill Pouch 450ml', 'Cool Fresh Refill Pouch 450ml', 'Japanese Shiso & Sandalwood 450ml', 'Charcoal & Mint Refill 450ml', 'Sabun Cuci Tangan Total 10 180ml'], 'price' => [4500, 32000]],
                    ['brand' => 'Dettol', 'base' => 'Sabun Mandi Antiseptik', 'variants' => ['Original Refill Pouch 410ml', 'Original Sabun Batang 100g', 'Cool Menthol Refill 410ml', 'Skincare Pelembab Refill 410ml', 'Re-energize Jeruk Refill 410ml', 'Lasting Fresh Timun Refill 410ml', 'Antiseptic Liquid Cair 245ml', 'Antiseptic Liquid Cair 500ml', 'Hand Wash Original Refill 200ml'], 'price' => [9500, 68000]],
                    ['brand' => 'Biore', 'base' => 'Sabun Mandi Busa Lembut', 'variants' => ['Pure Mild Refill Pouch 450ml', 'White Scrub Refill 450ml', 'Relaxing Aromatic Refill 450ml', 'Clear Fresh Refill 450ml', 'Men Biore Cool Oil Clear 100g', 'Men Biore White Energy 100g', 'Men Biore Double Scrub Deep Clean 100g', 'Men Biore Acne Bacterior 100g'], 'price' => [23000, 31000]],
                    ['brand' => 'Nuvo', 'base' => 'Sabun Mandi Keluarga', 'variants' => ['Family Total Protect Refill 450ml', 'Family Cool Protect Refill 450ml', 'Family Care Protect Refill 450ml', 'Sabun Batang Merah 85g', 'Sabun Batang Biru 85g', 'Hand Sanitizer Gel 50ml'], 'price' => [3500, 19500]],
                    ['brand' => 'Shinzui', 'base' => 'Sabun Pencerah Kulit', 'variants' => ['Matsu Refill Pouch 420ml', 'Kirei Refill Pouch 420ml', 'Sakura Refill Pouch 420ml', 'Myori Refill Pouch 420ml', 'Matsu Sabun Batang 85g', 'Kirei Sabun Batang 85g', 'Body Scrub Lulur Matsu 200g', 'Body Scrub Lulur Kirei 200g'], 'price' => [5000, 26000]],
                    ['brand' => 'Lux', 'base' => 'Sabun Mandi Parfum Mewah', 'variants' => ['Velvet Jasmine Refill 450ml', 'Magical Orchid Refill 450ml', 'Soft Rose Refill 450ml', 'Botanicals Bird of Paradise 450ml', 'Sabun Batang Mawar 80g'], 'price' => [4500, 27000]],
                    // Shampo & Kondisioner
                    ['brand' => 'Pantene', 'base' => 'Shampo Pro-Vitamin', 'variants' => ['Anti Ketombe Botol 160ml', 'Anti Ketombe Botol 290ml', 'Hair Fall Control Rontok 160ml', 'Hair Fall Control Rontok 290ml', 'Total Damage Care Rusak 160ml', 'Total Damage Care Rusak 290ml', 'Silky Smooth Halus 160ml', 'Perawatan Rambut Kering 160ml', 'Kondisioner 3 Minute Miracle 150ml'], 'price' => [22500, 48000]],
                    ['brand' => 'Clear', 'base' => 'Shampo Anti Dandruff', 'variants' => ['Men Cool Sport Menthol 160ml', 'Men Cool Sport Menthol 300ml', 'Complete Soft Care 160ml', 'Complete Soft Care 300ml', 'Ice Cool Menthol 160ml', 'Anti Hair Fall 160ml', 'Men Deep Cleanse 160ml'], 'price' => [24500, 52000]],
                    ['brand' => 'Sunsilk', 'base' => 'Shampo Rambut Sehat', 'variants' => ['Black Shine Hitam Berkilau 160ml', 'Black Shine Hitam Berkilau 320ml', 'Soft & Smooth Halus Lembut 160ml', 'Soft & Smooth Halus Lembut 320ml', 'Hijab Refresh & Volume 160ml', 'Anti Ketombe & Rontok 160ml'], 'price' => [20500, 42000]],
                    ['brand' => 'Head & Shoulders', 'base' => 'Shampo Ketombe Kepala Dingin', 'variants' => ['Cool Menthol Dingin 160ml', 'Cool Menthol Dingin 300ml', 'Supreme Anti Gatal 160ml', 'Sub Zero Dingin Ekstrem 160ml', 'Lemon Fresh Segar 160ml'], 'price' => [25500, 53000]],
                    ['brand' => 'Tresemme', 'base' => 'Shampo Standar Salon', 'variants' => ['Keratin Smooth Lurus 170ml', 'Keratin Smooth Lurus 340ml', 'Deep Cleanse & Protect 170ml', 'Scalp Care Rontok 170ml'], 'price' => [31000, 62000]],
                    // Pasta Gigi & Sikat Gigi
                    ['brand' => 'Pepsodent', 'base' => 'Pasta Gigi Keluarga', 'variants' => ['Pencegah Gigi Berlubang Putih 190g', 'Pencegah Gigi Berlubang Putih 120g', 'Pencegah Gigi Berlubang Putih 75g', 'Action 123 Herbal Segar 190g', 'Action 123 Herbal Segar 120g', 'Sensitive Expert Original 100g', 'Sensitive Expert Whitening 100g', 'Complete 8 Multi Proteksi 150g', 'Siwak Alami Pencegah Gigi 150g', 'Baking Soda Whitening 160g', 'Kids Strawberry Gigi Susu 50g'], 'price' => [5000, 32000]],
                    ['brand' => 'Sensodyne', 'base' => 'Pasta Gigi Sensitif', 'variants' => ['Repair & Protect 100g', 'Fresh Mint Dingin 100g', 'Cool Gel Biru 100g', 'Multi Action Lengkap 100g', 'Gentle Whitening Putih 100g', 'Deep Clean Busa Lembut 100g'], 'price' => [28500, 42000]],
                    ['brand' => 'Close Up', 'base' => 'Pasta Gigi Nafas Segar', 'variants' => ['Everfresh Red Hot Gel 160g', 'Everfresh Green Menthol 160g', 'White Attraction Mineral Clay 100g'], 'price' => [16500, 21500]],
                    ['brand' => 'Ciptadent', 'base' => 'Pasta Gigi Perlindungan Maksimal', 'variants' => ['Maxi Herbal Alami 190g', 'Maxi White Pemutih 190g', 'Maxi Cool Mint 190g', 'Maxi Fresh Mint 190g'], 'price' => [6500, 9500]],
                    ['brand' => 'Formula', 'base' => 'Sikat & Pasta Gigi', 'variants' => ['Sikat Gigi Sparkling White 3s', 'Sikat Gigi Ripple Lembut 3s', 'Sikat Gigi Nano Charcoal 3s', 'Pasta Gigi Charcoal Hitam 160g'], 'price' => [11500, 18500]],
                    ['brand' => 'Listerine', 'base' => 'Obat Kumur Mulut', 'variants' => ['Cool Mint Biru 250ml', 'Cool Mint Biru 500ml', 'Antiseptic Zero Alkohol 250ml', 'Multi Action Total Care 250ml', 'Citrus Segar 250ml'], 'price' => [22500, 48000]],
                    // Deodorant & Perawatan Kulit Tubuh
                    ['brand' => 'Rexona', 'base' => 'Deodorant Roll On', 'variants' => ['Men Ice Cool Dingin 50ml', 'Men Invisible Dry Putih 50ml', 'Men Adventure Petualang 50ml', 'Men V8 Segar Cepat 50ml', 'Women Free Spirit Segar 50ml', 'Women Bright Bouquet Bunga 50ml', 'Women Glowing White Cerah 50ml', 'Women Powder Dry Kering 50ml', 'Dry Serum Ekstra Cerah 50ml'], 'price' => [18500, 24500]],
                    ['brand' => 'Nivea', 'base' => 'Deodorant & Perawatan Pria', 'variants' => ['Men Deep Black Carbon 50ml', 'Men Cool Kick Dingin 50ml', 'Men Fresh Active Segar 50ml', 'Extra Brightening Cerah 50ml', 'Pearl & Beauty Halus 50ml', 'Body Lotion Extra Bright C&E 180ml', 'Body Serum Night Nourish 180ml'], 'price' => [19500, 39500]],
                    ['brand' => 'Vaseline', 'base' => 'Lotion & Serum Kulit', 'variants' => ['Healthy Bright UV Extra Brightening 200ml', 'Healthy Bright UV Extra Brightening 400ml', 'Aloe Fresh Lidah Buaya 200ml', 'Gluta-Hya Flawless Bright 200ml', 'Gluta-Hya Dewy Radiance 200ml', 'Petroleum Jelly Murni 50g', 'Petroleum Jelly Murni 100g'], 'price' => [22500, 68000]],
                    ['brand' => 'Marina', 'base' => 'Hand Body Lotion Murah', 'variants' => ['UV White Healthy & Glow 460ml', 'UV White Bright & Fresh 460ml', 'UV White E Collagen 460ml', 'Hydro Cool Gel Gelatin 460ml', 'Natural Rich Moisturizing 460ml'], 'price' => [12500, 18500]],
                    ['brand' => 'Kahf', 'base' => 'Perawatan Pria Muslim', 'variants' => ['Oil & Acne Care Face Wash 100ml', 'Skin Energizing Face Wash 100ml', 'Oil & Comedo Clarifying Face Wash 100ml', 'Beard & Hair Nourishing Oil 30ml', 'Eau De Toilette Humbling Forest 35ml', 'Eau De Toilette Revered Oud 35ml'], 'price' => [36500, 72000]],
                    ['brand' => 'Garnier', 'base' => 'Pembersih Wajah Pria & Wanita', 'variants' => ['Men Turbo Bright Spotless 100ml', 'Men Acno Fight Acne 100ml', 'Men Wasabi Anti Bakteri 100ml', 'Micellar Cleansing Water Pink 125ml', 'Micellar Cleansing Water Biru 125ml', 'Bright Complete Scrub 100ml'], 'price' => [29500, 44000]],
                    ['brand' => 'Pond\'s', 'base' => 'Pembersih Wajah', 'variants' => ['Men Energy Charge Coffee 100ml', 'Men Lightning Oil Clear 100ml', 'Bright Beauty Serum Facial Foam 100ml', 'Pure Bright D-Toxx Charcoal 100ml'], 'price' => [28500, 39500]],
                    ['brand' => 'Gatsby', 'base' => 'Gaya Rambut Pria', 'variants' => ['Styling Pomade Matte Mandom 75g', 'Styling Pomade Supreme Hold 75g', 'Water Gloss Super Hard 100g', 'Water Gloss Hyper Solid 100g', 'Hair Spray Set & Keep 250ml', 'Deodorant Roll On Urban Ozone 50ml'], 'price' => [11500, 42000]],
                    ['brand' => 'Gillette', 'base' => 'Pisau Cukur & Busa', 'variants' => ['Goal II Kuning Isi 2s', 'Blue II Plus Lubrastrip 2s', 'Vector Razor Gagang & Pisau', 'Mach 3 Turbo Razor', 'Foamy Shaving Cream Lemon 175g', 'Foamy Shaving Cream Reguler 175g'], 'price' => [8500, 58000]],
                    ['brand' => 'Charm', 'base' => 'Pembalut Wanita', 'variants' => ['Safe Night Panjang 35cm 12s', 'Safe Night Ekstra Panjang 42cm 8s', 'Body Fit Wing Sayap 23cm 18s', 'Body Fit Non Wing 23cm 20s', 'Cooling Fresh Dingin Wing 23cm 16s', 'Extra Dry Nyaman 23cm 18s', 'Pantyliner Pure Style Wangi 20s'], 'price' => [8500, 23000]],
                    ['brand' => 'Laurier', 'base' => 'Pembalut Kering', 'variants' => ['Relax Night Wing 30cm 16s', 'Relax Night Wing 35cm 12s', 'Active Day Maxi Wing 20s', 'Super Slimguard Tipis 22.5cm 20s', 'Super Slimguard Night 30cm 14s'], 'price' => [9500, 24000]],
                    ['brand' => 'MamyPoko', 'base' => 'Popok Bayi Celana', 'variants' => ['Pants X-tra Kering S-40', 'Pants X-tra Kering M-34', 'Pants X-tra Kering L-30', 'Pants X-tra Kering XL-26', 'Pants X-tra Kering XXL-24', 'Royal Soft Lembut M-34', 'Royal Soft Lembut L-28'], 'price' => [54000, 89000]],
                    ['brand' => 'Sweety', 'base' => 'Popok Bayi Higienis', 'variants' => ['Silver Pants Nyaman M-30', 'Silver Pants Nyaman L-28', 'Silver Pants Nyaman XL-26', 'Bronze Comfort Pants M-34', 'Bronze Comfort Pants L-30'], 'price' => [48000, 68000]],
                ]
            ],

            // -----------------------------------------------------------------
            // 6. KEBERSIHAN RUMAH & DAPUR (Target ~150 item)
            // -----------------------------------------------------------------
            [
                'cat_id' => $catKebersihan,
                'prefix' => 'KBR',
                'items' => [
                    // Deterjen & Pemutih
                    ['brand' => 'Rinso', 'base' => 'Deterjen Anti Noda', 'variants' => ['Molto Rose Fresh Bubuk 770g', 'Molto Rose Fresh Bubuk 1.8kg', 'Classic Anti Noda Bubuk 770g', 'Cair Perfume Essence Pouch 750ml', 'Cair Micellar Soft Pouch 750ml', 'Cair Rose Fresh Pouch 750ml', 'Matic Front Load Bukaan Depan 1L', 'Matic Top Load Bukaan Atas 1L'], 'price' => [18500, 52000]],
                    ['brand' => 'Attack', 'base' => 'Deterjen Konsentrat', 'variants' => ['Jaz1 Pesona Segar Bubuk 800g', 'Jaz1 Semerbak Cinta Bubuk 800g', 'Sensor Matic 9 Hi-Tech 800g', 'Plus Softener Pelembut 800g', 'Cair Hygiene Protection Pouch 750ml'], 'price' => [17500, 26500]],
                    ['brand' => 'Daia', 'base' => 'Deterjen Busa Melimpah', 'variants' => ['Bunga Segar Bubuk 850g', 'Putih Cemerlang Bubuk 850g', 'Lemon Segar Bubuk 850g', 'Clean & Fresh Hijab 850g', 'Violet Wangi Tahan Lama 850g'], 'price' => [16500, 19500]],
                    ['brand' => 'SoKlin', 'base' => 'Deterjen & Pembersih', 'variants' => ['Softergent Lavender Bubuk 770g', 'Softergent Rosy Blossom Bubuk 770g', 'Liquid Scarlet Blossom Pouch 750ml', 'Liquid Softergent Violet Pouch 750ml', 'Pemutih Pakaian Botol 500ml', 'Pembersih Lantai Lavender Pouch 750ml', 'Pembersih Lantai Apel Pouch 750ml', 'Pembersih Lantai Sereh Pouch 750ml'], 'price' => [9500, 19500]],
                    ['brand' => 'Molto', 'base' => 'Pelembut & Pewangi Pakaian', 'variants' => ['All in One Blue Pouch 720ml', 'All in One Pink Pouch 720ml', 'Pure Khusus Bayi Pouch 720ml', 'Pewangi Floral Bliss Pouch 820ml', 'Pewangi Sunshine Bloom Pouch 820ml'], 'price' => [14500, 24500]],
                    ['brand' => 'Downy', 'base' => 'Parfum Pewangi Pakaian', 'variants' => ['Mystique Parfum Pouch 720ml', 'Passion Romantis Pouch 720ml', 'Daring Mewah Pouch 720ml', 'Sunrise Fresh Segar Pouch 720ml', 'Anti Bau Apek Pouch 720ml'], 'price' => [23500, 34500]],
                    // Cuci Piring & Dapur
                    ['brand' => 'Sunlight', 'base' => 'Sabun Cuci Piring Jeruk Nipis', 'variants' => ['Ekstrak Jeruk Nipis 100 Pouch 650ml', 'Ekstrak Jeruk Nipis 100 Pouch 1000ml', 'Ekstrak Habbatussauda Pouch 650ml', 'Ekstra Higienis Anti Bakteri Pouch 650ml', 'Sabun Batang Hijau 120g'], 'price' => [3500, 22000]],
                    ['brand' => 'Mama Lemon', 'base' => 'Pembersih Piring & Sayur', 'variants' => ['Jeruk Nipis Segar Pouch 680ml', 'Jeruk Nipis & Daun Mint Pouch 680ml'], 'price' => [10500, 13500]],
                    ['brand' => 'Mama Lime', 'base' => 'Pembersih Buah & Sayur', 'variants' => ['Total Clean Lime Pouch 680ml', 'Charcoal Arang Aktif Pouch 680ml'], 'price' => [10500, 13500]],
                    ['brand' => 'Scotch-Brite', 'base' => 'Spons & Alat Bersih', 'variants' => ['Sabut Spons Hijau Anti Bakteri 3s', 'Spons Cuci Piring Busa Lembut 2s', 'Kain Lap Microfiber Serbaguna 1s'], 'price' => [12500, 24000]],
                    // Pembersih Kloset & Lantai
                    ['brand' => 'Wipol', 'base' => 'Karbol Wangi Cemara', 'variants' => ['Cemara Pinus Pouch 750ml', 'Lemon Pine Pouch 750ml', 'Eucalyptus Botol 750ml'], 'price' => [14500, 18500]],
                    ['brand' => 'SuperPell', 'base' => 'Pembersih Lantai Harum', 'variants' => ['Apel Segar Pouch 770ml', 'Cherry Rose Pouch 770ml', 'Lavender Wangi Pouch 770ml', 'Lemon Ginger Pouch 770ml'], 'price' => [12500, 15500]],
                    ['brand' => 'Vixal', 'base' => 'Pembersih Keramik Porselen', 'variants' => ['Kuat Ekstra Hijau Botol 750ml', 'Wangi Segar Biru Botol 750ml', 'Bleach Bersih Botol 750ml'], 'price' => [16500, 21000]],
                    ['brand' => 'Harpic', 'base' => 'Pembersih Kloset Toilet', 'variants' => ['Power Plus Original Botol 450ml', 'Power Plus Citrus Jeruk Botol 450ml', 'Pengharum Kloset Gantung 35g'], 'price' => [18500, 25500]],
                    // Pembasmi Serangga & Pengharum
                    ['brand' => 'Baygon', 'base' => 'Anti Nyamuk & Kecoa', 'variants' => ['Aerosol Lavender Semprot 600ml', 'Aerosol Citrus Segar Semprot 600ml', 'Aerosol Eucalyptus Alami Semprot 600ml', 'Bakar Jumbo Anti Nyamuk 5s', 'Mat Elektrik Nyamuk Isi 30s'], 'price' => [5500, 39500]],
                    ['brand' => 'HIT', 'base' => 'Anti Nyamuk Semprot', 'variants' => ['Aerosol Orange Segar Semprot 600ml', 'Aerosol Lily Blossom Semprot 600ml', 'Expert Anti Nyamuk Semprot 600ml', 'Alat Set Elektrik Nyamuk + Cairan', 'Refill Cairan Elektrik 35ml'], 'price' => [16500, 38500]],
                    ['brand' => 'Stella', 'base' => 'Pengharum Ruangan Harum', 'variants' => ['Gantung Jeruk Fresh 42g', 'Gantung Apple Manis 42g', 'Gantung Caffe Latte Kopi 42g', 'Aerosol Semprot Rose 400ml', 'Aerosol Semprot Lemon 400ml', 'Matic Refill Green Fantasy 225ml'], 'price' => [11500, 32000]],
                    ['brand' => 'Glade', 'base' => 'Pengharum Udara Segar', 'variants' => ['Ocean Escape Segar 350ml', 'Lavender Tenang 350ml', 'Wild Berries Buah 350ml', 'One Touch Refill 3x12ml'], 'price' => [16500, 28000]],
                    ['brand' => 'Bagus', 'base' => 'Kamper & Alat Rumah', 'variants' => ['Kapur Ajaib Anti Kecoa 2s', 'Kapur Ajaib Jumbo 2s', 'Toilet Ball Kamper Warna 5s', 'Naphthalene Ball Putih 150g', 'Para Kamper Floral 150g'], 'price' => [5000, 22000]],
                ]
            ],

            // -----------------------------------------------------------------
            // 7. OBAT & KESEHATAN (Target ~100 item)
            // -----------------------------------------------------------------
            [
                'cat_id' => $catObat,
                'prefix' => 'OBT',
                'items' => [
                    ['brand' => 'Panadol', 'base' => 'Pereda Nyeri & Demam', 'variants' => ['Biru Reguler Strip 10s', 'Merah Extra Strip 10s', 'Hijau Cold & Flu Strip 10s', 'Anak Sirup Penurun Panas 60ml'], 'price' => [11500, 48000]],
                    ['brand' => 'Bodrex', 'base' => 'Obat Sakit Kepala', 'variants' => ['Reguler Strip 20s', 'Extra Cepat Strip 10s', 'Flu & Batuk PE Strip 10s', 'Migra Sakit Sebelah Strip 10s', 'Herbal Masuk Angin Sachet 6s'], 'price' => [5500, 14500]],
                    ['brand' => 'Paramex', 'base' => 'Obat Sakit Kepala & Nyeri', 'variants' => ['Reguler Biru Strip 4s', 'Flu & Batuk Kuning Strip 4s', 'Nyeri Otot Hijau Strip 4s'], 'price' => [3500, 4500]],
                    ['brand' => 'Oskadon', 'base' => 'Obat Sakit Kepala Paten', 'variants' => ['Reguler Strip 4s', 'SP Sakit Pinggang Strip 4s'], 'price' => [3000, 4000]],
                    ['brand' => 'Mixagrip', 'base' => 'Obat Flu & Batuk', 'variants' => ['Flu Strip 4s', 'Flu & Batuk Strip 4s', 'Pegal Linu Strip 4s'], 'price' => [3200, 4200]],
                    ['brand' => 'Tolak Angin', 'base' => 'Herbal Masuk Angin', 'variants' => ['Cair Madu Mint Box 5x15ml', 'Bebas Gula Sugar Free 5x15ml', 'Flu Meredakan Hidung 5x15ml', 'Anak Rasa Madu 5x10ml', 'Permen Herbal Pelega Tenggorokan 50s'], 'price' => [19500, 24500]],
                    ['brand' => 'Antangin', 'base' => 'Herbal JRG Masuk Angin', 'variants' => ['JRG Cair Sachet Box 5x15ml', 'Habbatussauda Cair 5x15ml', 'Mint Herbal Cair 5x15ml', 'Permen Pelega Tenggorokan 50s'], 'price' => [18000, 22500]],
                    ['brand' => 'Minyak Kayu Putih Cap Lang', 'base' => 'Minyak Gosok Aromaterapi', 'variants' => ['Botol 30ml', 'Botol 60ml', 'Botol 120ml', 'Botol 210ml', 'Plus Anti Nyamuk 60ml', 'Plus Anti Nyamuk 120ml'], 'price' => [9500, 52000]],
                    ['brand' => 'My Baby', 'base' => 'Minyak Telon Bayi', 'variants' => ['Plus Minyak Telon 60ml', 'Plus Minyak Telon 90ml', 'Plus Minyak Telon 150ml', 'Plus Eucalyptus Lavender 90ml'], 'price' => [16500, 36000]],
                    ['brand' => 'FreshCare', 'base' => 'Minyak Angin Roll On', 'variants' => ['Citrus Segar 10ml', 'Splash Fruity 10ml', 'Hot Ekstra Panas 10ml', 'Kayu Putih Hangat 10ml', 'Green Tea 10ml', 'Lavender Tenang 10ml'], 'price' => [13500, 16500]],
                    ['brand' => 'Safe Care', 'base' => 'Minyak Angin Aromatherapy', 'variants' => ['Roll On Original 10ml', 'Roll On Strong Panas 10ml'], 'price' => [16500, 19500]],
                    ['brand' => 'Promag', 'base' => 'Obat Sakit Maag & Kembung', 'variants' => ['Tablet Kunyah Blister 10s', 'Cair Sachet 7ml Box 6s', 'Gazero Herbal Cair Box 6s'], 'price' => [8500, 16500]],
                    ['brand' => 'Mylanta', 'base' => 'Antasida Maag', 'variants' => ['Cair Suspensi Botol 50ml', 'Cair Suspensi Botol 150ml', 'Tablet Kunyah Strip 10s'], 'price' => [16500, 48000]],
                    ['brand' => 'Diapet', 'base' => 'Obat Diare Alami', 'variants' => ['Kapsul Ekstrak Daun Jambu Strip 4s', 'NR Kapsul Serap Racun 4s', 'Anak Sirup Rasa Manis 60ml'], 'price' => [3500, 12500]],
                    ['brand' => 'Entrostop', 'base' => 'Obat Diare & Sakit Perut', 'variants' => ['Tablet Strip 12s', 'Anak Sirup Rasa Jambu 6s'], 'price' => [9500, 14500]],
                    ['brand' => 'Betadine', 'base' => 'Antiseptik Luka', 'variants' => ['Larutan Obat Luka 15ml', 'Larutan Obat Luka 30ml', 'Larutan Obat Luka 60ml', 'Salep Luka Antiseptik 5g', 'Kumur Mulut Mouthwash 190ml'], 'price' => [12500, 42000]],
                    ['brand' => 'Hansaplast', 'base' => 'Plester Penutup Luka', 'variants' => ['Kain Elastis Box 10s', 'Transparan Anti Air 10s', 'Jumbo Box 10s', 'Karakter Anak Kartun 10s', 'Salep Luka Cepat Kering 20g'], 'price' => [5500, 29000]],
                    ['brand' => 'Salonpas', 'base' => 'Koyo Pereda Nyeri Otot', 'variants' => ['Koyo Reguler Pack 10s', 'Koyo Hot Ekstra Hangat 10s', 'Koyo Lembut Kulit 5s', 'Pain Relief Patch Besar 5s', 'Gel Salep Nyeri 30g'], 'price' => [6500, 22000]],
                    ['brand' => 'Counterpain', 'base' => 'Krim Pereda Nyeri Otot', 'variants' => ['Krim Panas Tube 15g', 'Krim Panas Tube 30g', 'Krim Panas Tube 60g', 'Cool Gel Dingin 30g'], 'price' => [28500, 85000]],
                    ['brand' => 'Enervon-C', 'base' => 'Multivitamin Daya Tahan', 'variants' => ['Tablet Strip 4s', 'Tablet Botol 30s', 'Effervescent Larut Air 10s'], 'price' => [6500, 45000]],
                    ['brand' => 'Redoxon', 'base' => 'Vitamin C & Zinc', 'variants' => ['Double Action Jeruk Tab 10s', 'Triple Action Rasa Jeruk 10s'], 'price' => [42000, 49000]],
                    ['brand' => 'Sangobion', 'base' => 'Penambah Darah & Zat Besi', 'variants' => ['Kapsul Blister 10s', 'Kapsul Mini 4s', 'Sirup Anak 100ml'], 'price' => [18500, 36000]],
                ]
            ],

            // -----------------------------------------------------------------
            // 8. ROKOK & TEMBAKAU (Target ~50 item)
            // -----------------------------------------------------------------
            [
                'cat_id' => $catRokok,
                'prefix' => 'ROK',
                'items' => [
                    ['brand' => 'Sampoerna', 'base' => 'Rokok Filter & Kretek', 'variants' => ['A Mild 16 Batang', 'A Mild 12 Batang', 'A Mild Menthol Burst 16 Batang', 'Avolution Red 20 Batang', 'Avolution Menthol 20 Batang', 'Splash Tropical 16 Batang', 'Splash Sunny 16 Batang', 'Kretek Istimewa 12 Batang'], 'price' => [15000, 36500]],
                    ['brand' => 'Djarum', 'base' => 'Rokok Kretek & Mild', 'variants' => ['Super 12 Batang', 'Super 16 Batang', 'Coklat Kretek 12 Batang', 'Black Cappuccino 16 Batang', 'Black Original 16 Batang', '76 Kretek 12 Batang', '76 Madu Hitam 12 Batang', '76 Kurma 12 Batang', 'LA Lights 16 Batang', 'LA Bold 20 Batang', 'LA Ice Purple Boost 16 Batang'], 'price' => [14500, 34500]],
                    ['brand' => 'Gudang Garam', 'base' => 'Rokok Surya & Signature', 'variants' => ['Surya 12 Batang', 'Surya 16 Batang', 'Surya Pro 16 Batang', 'Surya Pro Mild 16 Batang', 'International Merah 12 Batang', 'Signature Cokelat 12 Batang', 'Patra Kretek 12 Batang', 'Klobot Tradisional 6 Batang'], 'price' => [14000, 33500]],
                    ['brand' => 'Marlboro', 'base' => 'Rokok Putih Internasional', 'variants' => ['Red Merah 20 Batang', 'Gold Lights 20 Batang', 'Ice Burst Menthol 20 Batang', 'Filter Black 20 Batang', 'Crafted Selection 12 Batang'], 'price' => [23000, 43500]],
                    ['brand' => 'Esse', 'base' => 'Rokok Slim Kapsul', 'variants' => ['Change Juicy Pop 20 Batang', 'Change Double Kapsul 20 Batang', 'Berry Pop 20 Batang', 'Café Aroma Kopi 20 Batang', 'Shuffle Pop 20 Batang'], 'price' => [32000, 42000]],
                    ['brand' => 'Camel', 'base' => 'Rokok Putih & Mild', 'variants' => ['Yellow Filter 20 Batang', 'White Lights 20 Batang', 'Purple Option Rasa Anggur 20 Batang', 'Activate Crush Mint 20 Batang'], 'price' => [19500, 29500]],
                ]
            ],

            // -----------------------------------------------------------------
            // 9. FROZEN FOODS (Target ~50 item)
            // -----------------------------------------------------------------
            [
                'cat_id' => $catFrozen,
                'prefix' => 'FRZ',
                'items' => [
                    ['brand' => 'Fiesta', 'base' => 'Nugget & Olahan Ayam', 'variants' => ['Chicken Nugget Original 500g', 'Crispy Chicken Nugget 500g', 'Cheesy Lover Keju 500g', 'Spicy Wing Sayap Pedas 500g', 'Chicken Karage Daging Paha 500g', 'Sausage Sosis Ayam 450g', 'Bakso Sapi Kuah 500g', 'Dino Nugget Bentuk Dinosaurus 500g'], 'price' => [38000, 56000]],
                    ['brand' => 'So Good', 'base' => 'Nugget & Sosis Frozen', 'variants' => ['Chicken Nugget Original 400g', 'Nugget Huruf Alphabet 400g', 'Crispy Chicken Nugget 400g', 'Spicy Wing Sayap Ayam 400g', 'Bakso Sapi Kuah Instan 120g'], 'price' => [16500, 48000]],
                    ['brand' => 'Kanzler', 'base' => 'Sosis & Nugget Premium', 'variants' => ['Singles Sosis Siap Makan Original 65g', 'Singles Sosis Siap Makan Keju 65g', 'Singles Sosis Siap Makan Hot Pedas 65g', 'Crispy Chicken Nugget Extra Renyah 450g', 'Cocktail Beef Sosis Mini 500g', 'Frankfurter Sosis Sapi Jumbo 360g', 'Bockwurst Sosis Panggang 360g'], 'price' => [8500, 52000]],
                    ['brand' => 'Champ', 'base' => 'Nugget & Sosis Hemat', 'variants' => ['Chicken Nugget 500g', 'Sosis Ayam Hot Dog 500g', 'Burger Daging Sapi 315g'], 'price' => [24500, 36000]],
                    ['brand' => 'Cedea', 'base' => 'Olahan Seafood Frozen', 'variants' => ['Fish Dumpling Cheese Keju 500g', 'Fish Dumpling Chicken Ayam 500g', 'Chikuwa Tabung Ikan 500g', 'Crab Stick Daging Kepiting 250g', 'Salmon Ball Bola Ikan Salmon 500g', 'Steamboat Set Bumbu Kuah 300g'], 'price' => [18500, 39500]],
                    ['brand' => 'Golden Farm', 'base' => 'Kentang Goreng Beku', 'variants' => ['Shoestring Kentang Tipis 1kg', 'Crinkle Cut Kentang Bergerigi 1kg'], 'price' => [36000, 42000]],
                ]
            ],
        ];

        // Generator untuk menghasilkan 2.300 produk real
        $now = now();
        $totalCreated = 0;

        foreach ($catalogDefinitions as $catDef) {
            $catId = $catDef['cat_id'];
            $prefix = $catDef['prefix'];

            foreach ($catDef['items'] as $item) {
                $brand = $item['brand'];
                $baseName = $item['base'];
                $variants = $item['variants'];
                $priceRange = $item['price'];

                foreach ($variants as $variant) {
                    if ($totalCreated >= $targetCount) {
                        break 2;
                    }

                    $fullName = "{$brand} {$baseName} {$variant}";
                    $nameKey = strtolower(trim($fullName));

                    if (isset($existingNames[$nameKey])) {
                        continue;
                    }

                    // Tentukan harga beli dan harga jual dengan margin 12% - 25%
                    $minPrice = $priceRange[0];
                    $maxPrice = $priceRange[1];
                    $sellingPrice = round(rand($minPrice, $maxPrice) / 100) * 100;
                    if ($sellingPrice < 1000) $sellingPrice = 1000;

                    $marginPercent = rand(12, 25) / 100;
                    $purchasePrice = round(($sellingPrice * (1 - $marginPercent)) / 100) * 100;

                    $stock = rand(15, 150);
                    $minStock = rand(5, 15);

                    $productsToInsert[] = [
                        'category_id' => $catId,
                        'name' => $fullName,
                        'sku' => $generateSku($prefix),
                        'barcode' => $generateBarcode(),
                        'description' => "Produk resmi original {$brand} {$baseName} varian {$variant}. Siap jual kasir POS minimarket.",
                        'purchase_price' => $purchasePrice,
                        'selling_price' => $sellingPrice,
                        'stock' => $stock,
                        'min_stock' => $minStock,
                        'image' => null,
                        'is_active' => true,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];

                    $existingNames[$nameKey] = true;
                    $totalCreated++;
                }
            }
        }

        // Jika varian primer belum mencapai 2.300, tambahkan varian kemasan/bundling real
        // (contoh: Pack Isi 3, Renteng 10s, Multipack 4s, Bundling Hemat, Karton Dus)
        $packModifiers = [
            'Pack Isi 3', 'Pack Isi 5', 'Renceng 10 Sachet', 'Multipack 4s', 
            'Bundling Hemat 2 Pcs', 'Twin Pack 2x', 'Family Pack Hemat', 
            'Paket Sarapan Hemat', 'Special Promo Pack', 'Party Pack 250g',
            'Travel Pack Mini', 'Eco Refill Pack'
        ];

        // Loop ekspansi cerdas untuk mencapai tepat 2300
        if ($totalCreated < $targetCount) {
            foreach ($catalogDefinitions as $catDef) {
                $catId = $catDef['cat_id'];
                $prefix = $catDef['prefix'];

                foreach ($catDef['items'] as $item) {
                    $brand = $item['brand'];
                    $baseName = $item['base'];
                    $priceRange = $item['price'];

                    foreach ($packModifiers as $mod) {
                        if ($totalCreated >= $targetCount) {
                            break 3;
                        }

                        $fullName = "{$brand} {$baseName} ({$mod})";
                        $nameKey = strtolower(trim($fullName));

                        if (isset($existingNames[$nameKey])) {
                            continue;
                        }

                        // Kalikan harga sesuai tipe pack
                        $multiplier = str_contains($mod, '10') ? 8 : (str_contains($mod, '5') ? 4.5 : (str_contains($mod, '3') ? 2.8 : 1.8));
                        $basePrice = $priceRange[0];
                        $sellingPrice = round(($basePrice * $multiplier) / 500) * 500;
                        if ($sellingPrice < 5000) $sellingPrice = 5000;

                        $marginPercent = rand(12, 22) / 100;
                        $purchasePrice = round(($sellingPrice * (1 - $marginPercent)) / 100) * 100;

                        $stock = rand(10, 80);
                        $minStock = rand(3, 10);

                        $productsToInsert[] = [
                            'category_id' => $catId,
                            'name' => $fullName,
                            'sku' => $generateSku($prefix),
                            'barcode' => $generateBarcode(),
                            'description' => "Kemasan hemat {$mod} {$brand} {$baseName}. Asli dan berkualitas untuk retail minimarket.",
                            'purchase_price' => $purchasePrice,
                            'selling_price' => $sellingPrice,
                            'stock' => $stock,
                            'min_stock' => $minStock,
                            'image' => null,
                            'is_active' => true,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];

                        $existingNames[$nameKey] = true;
                        $totalCreated++;
                    }
                }
            }
        }

        // Masukkan data ke tabel dalam batch 500 item agar cepat & efisien
        $chunks = array_chunk($productsToInsert, 500);
        foreach ($chunks as $chunk) {
            DB::table('products')->insert($chunk);
        }

        $this->command->info("Berhasil menambahkan {$totalCreated} produk real ke database!");
    }
}
