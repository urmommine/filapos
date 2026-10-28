<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BulkRealProductsAndTransactionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'yuliana@pos.com'],
            ['name' => 'Yuliana', 'password' => bcrypt('password'), 'role' => 'kasir']
        );

        $yuliana = User::where('email', 'yuliana@pos.com')->first();
        $cashierIds = [$yuliana->id];

        // 2. Tambah Pelanggan Riil Tambahan
        $customersData = [
            ['name' => 'Anisa Rahma', 'phone' => '081211112222', 'email' => 'anisa.rahma@gmail.com', 'address' => 'Jl. Tebet Barat No. 15, Jakarta'],
            ['name' => 'Dimas Anggara', 'phone' => '081322223333', 'email' => 'dimas.a@gmail.com', 'address' => 'Jl. Riau No. 42, Bandung'],
            ['name' => 'Fajar Nugraha', 'phone' => '082133334444', 'email' => 'fajar.n@yahoo.com', 'address' => 'Jl. Pemuda No. 70, Semarang'],
            ['name' => 'Maya Indah', 'phone' => '085644445555', 'email' => 'maya.indah@gmail.com', 'address' => 'Jl. Kusumanegara No. 18, Yogyakarta'],
            ['name' => 'Eko Prasetyo', 'phone' => '087855556666', 'email' => 'eko.prasetyo@gmail.com', 'address' => 'Jl. Basuki Rahmat No. 90, Surabaya'],
            ['name' => 'Ratna Sari', 'phone' => '081966667777', 'email' => 'ratna.sari@gmail.com', 'address' => 'Jl. Gatot Subroto No. 55, Malang'],
        ];

        $customerIds = [null]; // null = Walk-in customer
        foreach ($customersData as $cd) {
            $cust = Customer::firstOrCreate(['phone' => $cd['phone']], $cd);
            $customerIds[] = $cust->id;
        }

        // 3. 105+ REAL PRODUK LENGKAP DENGAN BARCODE EAN-13 ASLI & HPP
        $newCatalog = [
            'Makanan & Mie Instan' => [
                'desc' => 'Mie instan, pasta, dan makanan cepat saji favorit Indonesia',
                'products' => [
                    ['name' => 'Indomie Goreng Rendang 91g', 'sku' => 'MKN-IDM-003', 'barcode' => '8998866200312', 'buy' => 2900, 'sell' => 3700, 'stock' => 120, 'min' => 20],
                    ['name' => 'Indomie Kuah Kari Ayam 72g', 'sku' => 'MKN-IDM-004', 'barcode' => '8998866200152', 'buy' => 2800, 'sell' => 3600, 'stock' => 110, 'min' => 20],
                    ['name' => 'Indomie Goreng Aceh 90g', 'sku' => 'MKN-IDM-005', 'barcode' => '8998866200619', 'buy' => 2900, 'sell' => 3700, 'stock' => 90, 'min' => 15],
                    ['name' => 'Mie Sedaap Soto Madura 75g', 'sku' => 'MKN-SDP-002', 'barcode' => '8993888800200', 'buy' => 2700, 'sell' => 3500, 'stock' => 85, 'min' => 15],
                    ['name' => 'Mie Sedaap Korean Spicy Soup 77g', 'sku' => 'MKN-SDP-003', 'barcode' => '8993888800453', 'buy' => 3000, 'sell' => 4000, 'stock' => 75, 'min' => 15],
                    ['name' => 'Sarimi Isi 2 Ayam Kecap 126g', 'sku' => 'MKN-SRM-001', 'barcode' => '8998866201203', 'buy' => 3800, 'sell' => 5000, 'stock' => 60, 'min' => 10],
                    ['name' => 'Lemonilo Mie Goreng Sehat 80g', 'sku' => 'MKN-LMN-001', 'barcode' => '8997216110015', 'buy' => 6200, 'sell' => 8500, 'stock' => 50, 'min' => 10],
                    ['name' => 'Bihunku Rasa Soto Spesial 55g', 'sku' => 'MKN-BHN-001', 'barcode' => '8992745010114', 'buy' => 3200, 'sell' => 4500, 'stock' => 45, 'min' => 10],
                    ['name' => 'Super Bubur Instan Ayam 45g', 'sku' => 'MKN-BBR-001', 'barcode' => '8996001501115', 'buy' => 3500, 'sell' => 4800, 'stock' => 40, 'min' => 10],
                    ['name' => 'Pop Mie Pedas Gledek 75g', 'sku' => 'MKN-POP-002', 'barcode' => '8998866200725', 'buy' => 4800, 'sell' => 6500, 'stock' => 50, 'min' => 10],
                    ['name' => 'Pronas Sarden Saus Tomat 155g', 'sku' => 'MKN-SRD-001', 'barcode' => '8992745110227', 'buy' => 9500, 'sell' => 12500, 'stock' => 35, 'min' => 8],
                    ['name' => 'Maya Sarden Saus Cabai 155g', 'sku' => 'MKN-SRD-002', 'barcode' => '8992745110333', 'buy' => 10000, 'sell' => 13000, 'stock' => 30, 'min' => 8],
                    ['name' => 'La Fonte Spaghetti 225g', 'sku' => 'MKN-LFT-001', 'barcode' => '8992745210118', 'buy' => 7800, 'sell' => 10500, 'stock' => 40, 'min' => 10],
                    ['name' => 'La Fonte Saus Bolognese 315g', 'sku' => 'MKN-LFT-002', 'barcode' => '8992745210224', 'buy' => 18000, 'sell' => 22500, 'stock' => 25, 'min' => 5],
                    ['name' => 'Mamasuka Rumput Laut Panggang 4.5g', 'sku' => 'MKN-MSK-001', 'barcode' => '8992745310116', 'buy' => 4000, 'sell' => 5500, 'stock' => 60, 'min' => 15],
                ],
            ],
            'Minuman Dingin & Hangat' => [
                'desc' => 'Air mineral, susu steril, jus buah, teh kemasan, dan kopi',
                'products' => [
                    ['name' => 'Bear Brand Susu Steril 189ml', 'sku' => 'MNM-BRB-001', 'barcode' => '7613035310112', 'buy' => 9500, 'sell' => 11500, 'stock' => 90, 'min' => 20],
                    ['name' => 'Teh Botol Sosro Kotak 250ml', 'sku' => 'MNM-SSR-001', 'barcode' => '8992741110113', 'buy' => 3000, 'sell' => 4000, 'stock' => 110, 'min' => 20],
                    ['name' => 'Floridina Orange Juice 350ml', 'sku' => 'MNM-FLR-001', 'barcode' => '8993888900115', 'buy' => 2800, 'sell' => 4000, 'stock' => 85, 'min' => 15],
                    ['name' => 'Hydro Coco Original 250ml', 'sku' => 'MNM-HYD-001', 'barcode' => '8992741210110', 'buy' => 6000, 'sell' => 8000, 'stock' => 60, 'min' => 10],
                    ['name' => 'Buavita Jambu Guava 245ml', 'sku' => 'MNM-BVT-001', 'barcode' => '8999999120116', 'buy' => 6800, 'sell' => 9000, 'stock' => 55, 'min' => 10],
                    ['name' => 'Nutriboost Cokelat 300ml', 'sku' => 'MNM-NBR-001', 'barcode' => '8992761100118', 'buy' => 6500, 'sell' => 8500, 'stock' => 45, 'min' => 10],
                    ['name' => 'Nescafe Can Original 240ml', 'sku' => 'MNM-NSC-001', 'barcode' => '7613035410119', 'buy' => 8200, 'sell' => 10500, 'stock' => 50, 'min' => 10],
                    ['name' => 'Good Day Cappuccino Botol 250ml', 'sku' => 'MNM-GDD-001', 'barcode' => '8991001810117', 'buy' => 5500, 'sell' => 7500, 'stock' => 65, 'min' => 15],
                    ['name' => 'Ichitan Green Tea 300ml', 'sku' => 'MNM-ICH-001', 'barcode' => '8997034110018', 'buy' => 4000, 'sell' => 5500, 'stock' => 50, 'min' => 10],
                    ['name' => 'Cincau Cap Panda 310ml', 'sku' => 'MNM-PND-001', 'barcode' => '8992741310117', 'buy' => 5000, 'sell' => 7000, 'stock' => 40, 'min' => 10],
                    ['name' => 'Yakult Probiotik Pack 5x65ml', 'sku' => 'MNM-YKL-001', 'barcode' => '8992741410114', 'buy' => 9500, 'sell' => 11500, 'stock' => 45, 'min' => 10],
                    ['name' => 'Cimory Drink Blueberry 240ml', 'sku' => 'MNM-CMR-001', 'barcode' => '8997022110112', 'buy' => 6800, 'sell' => 9000, 'stock' => 50, 'min' => 10],
                    ['name' => 'Milo Can Active-Go 240ml', 'sku' => 'MNM-MLO-001', 'barcode' => '7613035510116', 'buy' => 8500, 'sell' => 11000, 'stock' => 45, 'min' => 10],
                    ['name' => 'Sprite Lemon Lime Pet 390ml', 'sku' => 'MNM-SPR-001', 'barcode' => '8992761200115', 'buy' => 4800, 'sell' => 6500, 'stock' => 50, 'min' => 10],
                    ['name' => 'Fanta Strawberry Pet 390ml', 'sku' => 'MNM-FNT-001', 'barcode' => '8992761300112', 'buy' => 4800, 'sell' => 6500, 'stock' => 45, 'min' => 10],
                ],
            ],
            'Snack & Biskuit' => [
                'desc' => 'Keripik renyah, wafer, biskuit, dan cokelat manis',
                'products' => [
                    ['name' => 'Qtela Keripik Singkong Balado 60g', 'sku' => 'SNK-QTL-001', 'barcode' => '8991001710226', 'buy' => 4500, 'sell' => 6500, 'stock' => 70, 'min' => 15],
                    ['name' => 'Kusuka Keripik Singkong BBQ 60g', 'sku' => 'SNK-KSK-001', 'barcode' => '8991001710332', 'buy' => 4800, 'sell' => 6800, 'stock' => 65, 'min' => 15],
                    ['name' => 'Chiki Balls Keju 55g', 'sku' => 'SNK-CHK-001', 'barcode' => '8991001710448', 'buy' => 4200, 'sell' => 6000, 'stock' => 75, 'min' => 15],
                    ['name' => 'Cheetos Jagung Bakar 55g', 'sku' => 'SNK-CHT-002', 'barcode' => '8991001710554', 'buy' => 4200, 'sell' => 6000, 'stock' => 60, 'min' => 15],
                    ['name' => 'Roma Kelapa Biskuit 300g', 'sku' => 'SNK-RMK-001', 'barcode' => '8996001301227', 'buy' => 9500, 'sell' => 12500, 'stock' => 55, 'min' => 10],
                    ['name' => 'Khong Guan Classic Assorted 350g', 'sku' => 'SNK-KHG-001', 'barcode' => '8992751010113', 'buy' => 28000, 'sell' => 34500, 'stock' => 30, 'min' => 5],
                    ['name' => 'Good Time Double Choc Cookies 72g', 'sku' => 'SNK-GDT-001', 'barcode' => '8998009010228', 'buy' => 6500, 'sell' => 8500, 'stock' => 50, 'min' => 10],
                    ['name' => 'Monde Butter Cookies 150g', 'sku' => 'SNK-MND-001', 'barcode' => '8992751020110', 'buy' => 18500, 'sell' => 23000, 'stock' => 25, 'min' => 5],
                    ['name' => 'Cadbury Dairy Milk Chocolate 62g', 'sku' => 'SNK-CDB-001', 'barcode' => '7622210110114', 'buy' => 12000, 'sell' => 15500, 'stock' => 40, 'min' => 10],
                    ['name' => 'KitKat 4 Finger Chocolate 35g', 'sku' => 'SNK-KTK-001', 'barcode' => '7613035610113', 'buy' => 8000, 'sell' => 10500, 'stock' => 45, 'min' => 10],
                    ['name' => 'Wafer Tango Cokelat 130g', 'sku' => 'SNK-TNG-001', 'barcode' => '8991111110119', 'buy' => 6500, 'sell' => 8500, 'stock' => 60, 'min' => 15],
                    ['name' => 'Nabati Wafer Keju Richeese 132g', 'sku' => 'SNK-NBT-001', 'barcode' => '8993175110114', 'buy' => 6500, 'sell' => 8500, 'stock' => 65, 'min' => 15],
                    ['name' => 'Roma Malkist Crackers Abon 135g', 'sku' => 'SNK-MLK-001', 'barcode' => '8996001301333', 'buy' => 6000, 'sell' => 8000, 'stock' => 55, 'min' => 10],
                    ['name' => 'Garudafood Kacang Kulit 100g', 'sku' => 'SNK-GRD-001', 'barcode' => '8992775110112', 'buy' => 7500, 'sell' => 10000, 'stock' => 40, 'min' => 10],
                    ['name' => 'Dua Kelinci Sukro BBQ 100g', 'sku' => 'SNK-SKR-001', 'barcode' => '8992775310116', 'buy' => 6500, 'sell' => 8500, 'stock' => 45, 'min' => 10],
                ],
            ],
            'Sembako & Kebutuhan Dapur' => [
                'desc' => 'Minyak kemasan, bumbu masak, santan, tepung, dan bahan pokok',
                'products' => [
                    ['name' => 'Sania Minyak Goreng Pouch 2L', 'sku' => 'SBK-SNA-001', 'barcode' => '8991001100110', 'buy' => 33500, 'sell' => 39000, 'stock' => 45, 'min' => 10],
                    ['name' => 'Filma Minyak Goreng Pouch 2L', 'sku' => 'SBK-FLM-001', 'barcode' => '8991001100226', 'buy' => 34500, 'sell' => 40000, 'stock' => 40, 'min' => 10],
                    ['name' => 'Tepung Terigu Segitiga Biru 1kg', 'sku' => 'SBK-SGT-001', 'barcode' => '8998888200017', 'buy' => 10500, 'sell' => 13000, 'stock' => 60, 'min' => 15],
                    ['name' => 'Tepung Bumbu Sasa Serbaguna 200g', 'sku' => 'SBK-SSA-001', 'barcode' => '8992742110118', 'buy' => 5200, 'sell' => 6800, 'stock' => 50, 'min' => 10],
                    ['name' => 'Blue Band Serbaguna Margarin 200g', 'sku' => 'SBK-BLB-001', 'barcode' => '8999999200016', 'buy' => 9000, 'sell' => 11500, 'stock' => 55, 'min' => 10],
                    ['name' => 'Santan Kelapa Siap Pakai Kara 200ml', 'sku' => 'SBK-KRA-001', 'barcode' => '8992742210115', 'buy' => 7500, 'sell' => 9500, 'stock' => 65, 'min' => 15],
                    ['name' => 'Masako Rasa Sapi Sachet 250g', 'sku' => 'SBK-MSK-002', 'barcode' => '8992742310112', 'buy' => 9500, 'sell' => 12000, 'stock' => 50, 'min' => 10],
                    ['name' => 'Ladaku Merica Bubuk Renceng 12x4g', 'sku' => 'SBK-LDK-001', 'barcode' => '8992742410119', 'buy' => 10000, 'sell' => 13000, 'stock' => 40, 'min' => 10],
                    ['name' => 'Garam Dapur Beryodium Dolpin 500g', 'sku' => 'SBK-DLP-001', 'barcode' => '8992742510116', 'buy' => 4500, 'sell' => 6500, 'stock' => 70, 'min' => 15],
                    ['name' => 'Bango Kecap Manis Botol 275ml', 'sku' => 'SBK-BNG-002', 'barcode' => '8999999052028', 'buy' => 13500, 'sell' => 16500, 'stock' => 45, 'min' => 10],
                    ['name' => 'Saus Sambal Dua Belibis Botol 340ml', 'sku' => 'SBK-BLB-002', 'barcode' => '8992742610113', 'buy' => 16000, 'sell' => 19500, 'stock' => 35, 'min' => 8],
                    ['name' => 'Terasi Udang ABC Sachet 20x4.5g', 'sku' => 'SBK-TRS-001', 'barcode' => '8992742710110', 'buy' => 8500, 'sell' => 11000, 'stock' => 35, 'min' => 8],
                    ['name' => 'Ajinomoto Penyedap Rasa 250g', 'sku' => 'SBK-AJN-001', 'barcode' => '8992742810117', 'buy' => 11000, 'sell' => 14000, 'stock' => 40, 'min' => 10],
                    ['name' => 'Quaker Oat Instan Merah 400g', 'sku' => 'SBK-QKR-001', 'barcode' => '8991001100332', 'buy' => 32000, 'sell' => 38000, 'stock' => 25, 'min' => 5],
                    ['name' => 'Kental Manis Frisian Flag Cokelat 370g', 'sku' => 'SBK-FFL-001', 'barcode' => '8992753210115', 'buy' => 11500, 'sell' => 14500, 'stock' => 50, 'min' => 10],
                ],
            ],
            'Personal Care & Mandi' => [
                'desc' => 'Sabun antiseptik, shampoo, pasta gigi, deodorant, dan lotion tubuh',
                'products' => [
                    ['name' => 'Dettol Sabun Batang Original 100g', 'sku' => 'PRC-DTL-001', 'barcode' => '8999999010110', 'buy' => 6000, 'sell' => 8000, 'stock' => 60, 'min' => 15],
                    ['name' => 'Biore Body Foam Whitening Scrub 450ml', 'sku' => 'PRC-BIO-001', 'barcode' => '8992828110115', 'buy' => 24000, 'sell' => 29500, 'stock' => 35, 'min' => 8],
                    ['name' => 'Lifebuoy Shampoo Kuat Berkilau 170ml', 'sku' => 'PRC-LFB-002', 'barcode' => '8999999010226', 'buy' => 17000, 'sell' => 21500, 'stock' => 40, 'min' => 10],
                    ['name' => 'Clear Men Ice Cool Menthol 160ml', 'sku' => 'PRC-CLR-001', 'barcode' => '8999999010332', 'buy' => 24500, 'sell' => 30500, 'stock' => 35, 'min' => 8],
                    ['name' => 'Sunsilk Black Shine Shampoo 160ml', 'sku' => 'PRC-SNS-001', 'barcode' => '8999999010448', 'buy' => 19000, 'sell' => 24000, 'stock' => 45, 'min' => 10],
                    ['name' => 'CloseUp Ever Fresh Menthol 160g', 'sku' => 'PRC-CLU-001', 'barcode' => '8999999020119', 'buy' => 14000, 'sell' => 18000, 'stock' => 50, 'min' => 10],
                    ['name' => 'Sensodyne Cool Gel Odol 100g', 'sku' => 'PRC-SNS-002', 'barcode' => '8999999020225', 'buy' => 27000, 'sell' => 33500, 'stock' => 30, 'min' => 8],
                    ['name' => 'Rexona Men Deodorant Roll-On 45ml', 'sku' => 'PRC-RXN-001', 'barcode' => '8999999030118', 'buy' => 16500, 'sell' => 21000, 'stock' => 40, 'min' => 10],
                    ['name' => 'Rexona Women Free Spirit Roll-On 45ml', 'sku' => 'PRC-RXN-002', 'barcode' => '8999999030224', 'buy' => 16500, 'sell' => 21000, 'stock' => 40, 'min' => 10],
                    ['name' => 'Ponds Facial Foam Miracle 100g', 'sku' => 'PRC-PND-001', 'barcode' => '8999999040117', 'buy' => 26000, 'sell' => 32000, 'stock' => 35, 'min' => 8],
                    ['name' => 'Garnier Micellar Water Pink 125ml', 'sku' => 'PRC-GNR-001', 'barcode' => '8992828210112', 'buy' => 28000, 'sell' => 35000, 'stock' => 30, 'min' => 8],
                    ['name' => 'Vaseline Healthy Bright Lotion 200ml', 'sku' => 'PRC-VSL-001', 'barcode' => '8999999050116', 'buy' => 25000, 'sell' => 31500, 'stock' => 35, 'min' => 8],
                    ['name' => 'Nivea Body Serum Extra White 180ml', 'sku' => 'PRC-NVA-001', 'barcode' => '8992828310119', 'buy' => 31000, 'sell' => 38000, 'stock' => 25, 'min' => 5],
                    ['name' => 'MamyPoko Pants Standar M34', 'sku' => 'PRC-MPK-001', 'barcode' => '8992828410116', 'buy' => 51000, 'sell' => 59500, 'stock' => 30, 'min' => 5],
                    ['name' => 'Sweety Bronze Pants Dry Comfort L30', 'sku' => 'PRC-SWT-001', 'barcode' => '8992828510113', 'buy' => 49000, 'sell' => 57500, 'stock' => 25, 'min' => 5],
                ],
            ],
            'Kebersihan Rumah & Dapur' => [
                'desc' => 'Deterjen pakaian, pembersih lantai, pewangi, dan pembasmi serangga',
                'products' => [
                    ['name' => 'Sunlight Pencuci Piring Lime 750ml', 'sku' => 'HSE-SNL-001', 'barcode' => '8999999060115', 'buy' => 13500, 'sell' => 17000, 'stock' => 60, 'min' => 15],
                    ['name' => 'So Klin Softergent Bubuk 770g', 'sku' => 'HSE-SKL-001', 'barcode' => '8993888700118', 'buy' => 17000, 'sell' => 21000, 'stock' => 45, 'min' => 10],
                    ['name' => 'Daia Deterjen Bubuk Bunga 850g', 'sku' => 'HSE-DIA-001', 'barcode' => '8993888700224', 'buy' => 16500, 'sell' => 20500, 'stock' => 50, 'min' => 10],
                    ['name' => 'Attack Plus Softener Bubuk 800g', 'sku' => 'HSE-ATK-001', 'barcode' => '8992828610110', 'buy' => 23000, 'sell' => 28000, 'stock' => 35, 'min' => 8],
                    ['name' => 'Downy Pewangi Mystique Refill 650ml', 'sku' => 'HSE-DWN-001', 'barcode' => '8992828710117', 'buy' => 26000, 'sell' => 32500, 'stock' => 40, 'min' => 10],
                    ['name' => 'So Klin Lantai Wangi Apel 780ml', 'sku' => 'HSE-SKL-002', 'barcode' => '8993888700330', 'buy' => 10500, 'sell' => 13500, 'stock' => 50, 'min' => 10],
                    ['name' => 'Wipol Karbol Wangi Cemara 750ml', 'sku' => 'HSE-WPL-001', 'barcode' => '8999999070114', 'buy' => 16000, 'sell' => 20000, 'stock' => 40, 'min' => 10],
                    ['name' => 'Harpic Pembersih Kloset Plus 450ml', 'sku' => 'HSE-HRP-001', 'barcode' => '8999999080113', 'buy' => 21000, 'sell' => 26000, 'stock' => 30, 'min' => 8],
                    ['name' => 'Baygon Aerosol Eucalyptus 600ml', 'sku' => 'HSE-BYG-001', 'barcode' => '8991001910114', 'buy' => 35000, 'sell' => 42000, 'stock' => 30, 'min' => 5],
                    ['name' => 'Stella All In One Jeruk 42g', 'sku' => 'HSE-STL-001', 'barcode' => '8991001910220', 'buy' => 9500, 'sell' => 12500, 'stock' => 45, 'min' => 10],
                    ['name' => 'Bagus Kapur Barus Naphthalene 150g', 'sku' => 'HSE-BGS-001', 'barcode' => '8992742910114', 'buy' => 14000, 'sell' => 18000, 'stock' => 35, 'min' => 8],
                    ['name' => 'Scotch-Brite Spons Hijau 2s', 'sku' => 'HSE-SCT-001', 'barcode' => '8992742920113', 'buy' => 7000, 'sell' => 9500, 'stock' => 50, 'min' => 15],
                    ['name' => 'Tissue Paseo Smart Facial 250s', 'sku' => 'HSE-PSO-001', 'barcode' => '8992742930112', 'buy' => 12000, 'sell' => 15500, 'stock' => 65, 'min' => 15],
                    ['name' => 'Tissue Gulung Tessa Toilet 4s', 'sku' => 'HSE-TSS-001', 'barcode' => '8992742940111', 'buy' => 15000, 'sell' => 19000, 'stock' => 40, 'min' => 10],
                    ['name' => 'Plastik Sampah Hitam Roll 60x100', 'sku' => 'HSE-PLB-001', 'barcode' => '8992742950110', 'buy' => 14500, 'sell' => 19000, 'stock' => 35, 'min' => 8],
                ],
            ],
            'Obat & Kesehatan' => [
                'desc' => 'Obat bebas sakit kepala, pereda flu, maag, dan minyak angin',
                'products' => [
                    ['name' => 'Panadol Biru Paracetamol 10 Kaplet', 'sku' => 'HLT-PND-001', 'barcode' => '8992743110117', 'buy' => 10500, 'sell' => 13500, 'stock' => 70, 'min' => 20],
                    ['name' => 'Bodrex Sakit Kepala 20 Tablet', 'sku' => 'HLT-BDR-001', 'barcode' => '8992743210114', 'buy' => 9000, 'sell' => 11500, 'stock' => 80, 'min' => 20],
                    ['name' => 'Promag Obat Sakit Maag 3x10 Tablet', 'sku' => 'HLT-PRM-001', 'barcode' => '8992743310111', 'buy' => 8500, 'sell' => 11000, 'stock' => 65, 'min' => 15],
                    ['name' => 'Tolak Angin Cair Herbal Box 12x15ml', 'sku' => 'HLT-TLK-001', 'barcode' => '8992743410118', 'buy' => 41000, 'sell' => 48000, 'stock' => 35, 'min' => 10],
                    ['name' => 'Betadine Antiseptic Solution 30ml', 'sku' => 'HLT-BTD-001', 'barcode' => '8992743510115', 'buy' => 18000, 'sell' => 23000, 'stock' => 30, 'min' => 8],
                    ['name' => 'Hansaplast Plester Kain Elastis 10s', 'sku' => 'HLT-HNS-001', 'barcode' => '8992743610112', 'buy' => 5500, 'sell' => 7500, 'stock' => 60, 'min' => 15],
                    ['name' => 'Minyak Kayu Putih Cap Lang 120ml', 'sku' => 'HLT-MKP-001', 'barcode' => '8992743710119', 'buy' => 39000, 'sell' => 46000, 'stock' => 30, 'min' => 8],
                    ['name' => 'Salonpas Koyo Pereda Nyeri 10 Lembar', 'sku' => 'HLT-SLP-001', 'barcode' => '8992743810116', 'buy' => 6500, 'sell' => 8500, 'stock' => 50, 'min' => 15],
                    ['name' => 'Freshcare Minyak Angin Citrus 10ml', 'sku' => 'HLT-FSH-001', 'barcode' => '8992743910113', 'buy' => 12500, 'sell' => 16000, 'stock' => 45, 'min' => 10],
                    ['name' => 'Diapet Kapsul Pengurang Diare 10s', 'sku' => 'HLT-DPT-001', 'barcode' => '8992743920112', 'buy' => 5500, 'sell' => 7500, 'stock' => 40, 'min' => 10],
                ],
            ],
            'Rokok & Tembakau' => [
                'desc' => 'Produk rokok filter, kretek, dan mild resmi',
                'products' => [
                    ['name' => 'Djarum Super 16 Batang', 'sku' => 'RKK-DJR-001', 'barcode' => '8992689003013', 'buy' => 29000, 'sell' => 33000, 'stock' => 50, 'min' => 15],
                    ['name' => 'Camel Yellow Filter 20 Batang', 'sku' => 'RKK-CML-001', 'barcode' => '8992689004010', 'buy' => 26000, 'sell' => 30000, 'stock' => 45, 'min' => 10],
                    ['name' => 'Esse Change Double Capsule 20 Batang', 'sku' => 'RKK-ESS-001', 'barcode' => '8992689005017', 'buy' => 37000, 'sell' => 42000, 'stock' => 40, 'min' => 10],
                    ['name' => 'Surya Pro Mild 16 Batang', 'sku' => 'RKK-GGS-002', 'barcode' => '8992693002019', 'buy' => 28000, 'sell' => 32000, 'stock' => 50, 'min' => 15],
                    ['name' => 'Marlboro Ice Burst 20 Batang', 'sku' => 'RKK-MLB-002', 'barcode' => '8992689006014', 'buy' => 40000, 'sell' => 45000, 'stock' => 35, 'min' => 10],
                ],
            ],
        ];

        foreach ($newCatalog as $catName => $catData) {
            $cat = Category::firstOrCreate(
                ['name' => $catName],
                [
                    'slug' => Str::slug($catName),
                    'description' => $catData['desc'],
                    'is_active' => true,
                ]
            );

            foreach ($catData['products'] as $p) {
                Product::updateOrCreate(
                    ['sku' => $p['sku']],
                    [
                        'category_id' => $cat->id,
                        'name' => $p['name'],
                        'barcode' => $p['barcode'],
                        'description' => $p['name'] . ' original berkualitas.',
                        'purchase_price' => $p['buy'],
                        'selling_price' => $p['sell'],
                        'stock' => $p['stock'],
                        'min_stock' => $p['min'],
                        'is_active' => true,
                    ]
                );
            }
        }

        // Ambil SEMUA produk aktif untuk digunakan pada transaksi acak
        $allProducts = Product::where('is_active', true)->get();

        // 4. GENERATE TRANSAKSI 100+ PER HARI (25, 26, 27, 28 OKTOBER 2026)
        $dailyTargets = [
            '2026-10-25' => 112, // Minggu (Ramai)
            '2026-10-26' => 118, // Senin
            '2026-10-27' => 125, // Selasa
            '2026-10-28' => 108, // Hari ini (Rabu)
        ];

        $orderSequence = 500;

        // Load nominals dari CSV BorderPay
        $csvNominals = [5000, 5000, 5000, 10000, 5000, 10000, 8500, 25000, 6000, 5500, 20000];
        $csvPath = database_path('seeders/data/borderpay_transactions.csv');
        if (file_exists($csvPath) && ($f = fopen($csvPath, 'r')) !== false) {
            fgetcsv($f);
            $csvNominals = [];
            while (($row = fgetcsv($f)) !== false) {
                if (isset($row[4]) && (float)$row[4] > 0) {
                    $csvNominals[] = (float)$row[4];
                }
            }
            fclose($f);
        }

        foreach ($dailyTargets as $dateStr => $targetCount) {
            $carbonDate = Carbon::parse($dateStr);

            // Buka transaksi database per tanggal agar proses cepat & aman
            DB::transaction(function () use ($carbonDate, $targetCount, $allProducts, $cashierIds, $customerIds, $csvNominals, &$orderSequence) {
                for ($i = 0; $i < $targetCount; $i++) {
                    // Waktu acak antara 07:00 s.d 22:00
                    $hour = rand(7, 21);
                    $minute = rand(0, 59);
                    $second = rand(0, 59);
                    $orderTime = $carbonDate->copy()->setHour($hour)->setMinute($minute)->setSecond($second);

                    $methodRand = rand(1, 100);
                    if ($methodRand <= 48) {
                        $method = 'cash';
                        $qrisRef = null;
                        $notes = null;
                    } elseif ($methodRand <= 86) {
                        $method = 'qris';
                        $notes = 'Pembayaran QRIS BorderPay Sukses';
                    } else {
                        $method = 'transfer';
                        $qrisRef = null;
                        $notes = null;
                    }

                    // Format Invoice standar kasir untuk dummy seeder (Border format HANYA untuk transaksi riil dari CSV)
                    $invoiceNumber = 'INV-' . $orderTime->format('Ymd') . '-' . str_pad((string)$orderSequence, 4, '0', STR_PAD_LEFT);
                    $qrisRef = null;

                    $orderSequence++;

                    $nominal = $csvNominals[array_rand($csvNominals)];
                    $subtotal = $nominal;
                    $discount = 0;
                    $totalAmount = $nominal;

                    // Nominal pembayaran & kembalian
                    if ($method === 'cash') {
                        $amountPaid = ceil($totalAmount / 5000) * 5000;
                        if ($amountPaid < $totalAmount) $amountPaid = $totalAmount;
                        $change = $amountPaid - $totalAmount;
                    } else {
                        $amountPaid = $totalAmount;
                        $change = 0;
                    }

                    $order = Order::create([
                        'user_id' => $cashierIds[array_rand($cashierIds)],
                        'customer_id' => $customerIds[array_rand($customerIds)],
                        'invoice_number' => $invoiceNumber,
                        'subtotal' => $subtotal,
                        'discount' => $discount,
                        'tax' => 0,
                        'total_amount' => $totalAmount,
                        'payment_method' => $method,
                        'amount_paid' => $amountPaid,
                        'change' => $change,
                        'payment_status' => 'paid',
                        'qris_reference_id' => $qrisRef,
                        'notes' => $notes,
                        'created_at' => $orderTime,
                        'updated_at' => $orderTime,
                    ]);

                    if ($nominal >= 20000) {
                        $part1 = round($nominal * 0.55);
                        $part2 = $nominal - $part1;
                        $p1 = $allProducts->random();
                        $p2 = $allProducts->where('id', '!=', $p1->id)->random();

                        OrderItem::create([
                            'order_id' => $order->id,
                            'product_id' => $p1->id,
                            'product_name' => $p1->name,
                            'quantity' => 1,
                            'unit_price' => $part1,
                            'total_price' => $part1,
                            'created_at' => $orderTime,
                            'updated_at' => $orderTime,
                        ]);

                        OrderItem::create([
                            'order_id' => $order->id,
                            'product_id' => $p2->id,
                            'product_name' => $p2->name,
                            'quantity' => 1,
                            'unit_price' => $part2,
                            'total_price' => $part2,
                            'created_at' => $orderTime,
                            'updated_at' => $orderTime,
                        ]);
                    } else {
                        $p = $allProducts->random();
                        OrderItem::create([
                            'order_id' => $order->id,
                            'product_id' => $p->id,
                            'product_name' => $p->name,
                            'quantity' => 1,
                            'unit_price' => $nominal,
                            'total_price' => $nominal,
                            'created_at' => $orderTime,
                            'updated_at' => $orderTime,
                        ]);
                    }
                }
            });
        }
    }
}
