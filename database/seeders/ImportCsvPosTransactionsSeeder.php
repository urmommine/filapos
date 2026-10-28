<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ImportCsvPosTransactionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvPath = database_path('seeders/data/borderpay_transactions.csv');
        if (!file_exists($csvPath)) {
            $csvPath = 'C:/Users/Administrator/.gemini/antigravity-ide/brain/97be1f04-8538-4c38-8de0-54d5072b421e/.user_uploaded/media_1793162187021.csv';
        }

        if (!file_exists($csvPath)) {
            $this->command->error("File CSV tidak ditemukan: {$csvPath}");
            return;
        }

        $this->command->info("1. Mempersiapkan Master Data Kasir Yuliana dan Pelanggan POS...");
        $yuliana = User::firstOrCreate(
            ['email' => 'yuliana@pos.com'],
            ['name' => 'Yuliana', 'password' => Hash::make('password'), 'role' => 'kasir']
        );
        $cashierId = $yuliana->id;

        $customers = Customer::pluck('id')->toArray();

        // 2. Load Produk Fisik Berdasarkan SKU
        $allProducts = Product::where('is_active', true)->get()->keyBy('sku');

        // Definisi variasi keranjang belanja untuk setiap nominal POS (1 item, 2 item, 3 item, dst.)
        $basketTemplates = [
            10000 => [
                // Variasi 1: 2 barang sama (5.000 x 2)
                [
                    ['sku' => 'PRC-LFB-001', 'qty' => 2], // Lifebuoy Sabun 5.000 x 2
                ],
                // Variasi 2: 2 barang berbeda (5.000 x 1 + 5.000 x 1)
                [
                    ['sku' => 'PRC-LFB-001', 'qty' => 1],
                    ['sku' => 'MKN-SRM-001', 'qty' => 1], // Sarimi Isi 2
                ],
                // Variasi 3: 2 barang beda harga (6.000 + 4.000)
                [
                    ['sku' => 'MKN-POP-001', 'qty' => 1], // Pop Mie 6.000
                    ['sku' => 'HERBAL-001', 'qty' => 1],  // Tolak Angin 4.000
                ],
                // Variasi 4: 2 barang beda harga (6.000 + 4.000)
                [
                    ['sku' => 'SNK-TRO-001', 'qty' => 1],   // Taro Net 6.000
                    ['sku' => 'STATIONARY-001', 'qty' => 1],// Buku Tulis 4.000
                ],
                // Variasi 5: 3 barang (3.500 x 2 + 3.000)
                [
                    ['sku' => 'MKN-IDM-001', 'qty' => 2], // Indomie Goreng 3.500 x 2 = 7.000
                    ['sku' => 'SNK-BBG-001', 'qty' => 1], // Beng-Beng 3.000
                ],
                // Variasi 6: 2 barang (7.000 + 3.000)
                [
                    ['sku' => 'MNM-PND-001', 'qty' => 1], // Cincau Panda 7.000
                    ['sku' => 'SNK-BBG-001', 'qty' => 1], // Beng-Beng 3.000
                ],
                // Variasi 7: 1 barang tunggal (10.000)
                [
                    ['sku' => 'SNK-PCK-001', 'qty' => 1], // Pocky 10.000
                ],
                // Variasi 8: 1 barang tunggal (10.000)
                [
                    ['sku' => 'SNK-GRD-001', 'qty' => 1], // Kacang Garudafood 10.000
                ],
            ],
            5000 => [
                // Variasi 1: 2 barang sama (2.500 x 2)
                [
                    ['sku' => 'HERBAL-006', 'qty' => 2], // Jamu Pegal Linu 2.500 x 2
                ],
                // Variasi 2: 1 barang tunggal (5.000)
                [
                    ['sku' => 'PRC-LFB-001', 'qty' => 1], // Lifebuoy Sabun 5.000
                ],
                // Variasi 3: 1 barang tunggal (5.000)
                [
                    ['sku' => 'MKN-SRM-001', 'qty' => 1], // Sarimi Isi 2 5.000
                ],
                // Variasi 4: 1 barang tunggal (5.000)
                [
                    ['sku' => 'SKIN-CARE-008', 'qty' => 1], // Bioaqua Sheet Mask 5.000
                ],
            ],
            20000 => [
                // Variasi 1: 2 barang (10.000 x 2)
                [
                    ['sku' => 'SNK-PCK-001', 'qty' => 2], // Pocky 10.000 x 2
                ],
                // Variasi 2: 3 barang (10.000 + 5.000 x 2)
                [
                    ['sku' => 'SNK-GRD-001', 'qty' => 1], // Kacang 10.000
                    ['sku' => 'PRC-LFB-001', 'qty' => 2], // Lifebuoy 5.000 x 2
                ],
                // Variasi 3: 2 barang combo (12.500 + 7.500)
                [
                    ['sku' => 'SNK-CHT-001', 'qty' => 1], // Chitato 12.500
                    ['sku' => 'MNM-ULT-001', 'qty' => 1], // Ultra Milk 7.500
                ],
                // Variasi 4: 1 barang tunggal (20.000)
                [
                    ['sku' => 'HSE-WPL-001', 'qty' => 1], // Wipol Karbol 20.000
                ],
            ],
            25000 => [
                // Variasi 1: 3 barang (10.000 x 2 + 5.000)
                [
                    ['sku' => 'SNK-PCK-001', 'qty' => 2], // Pocky 10.000 x 2
                    ['sku' => 'MKN-SRM-001', 'qty' => 1], // Sarimi 5.000
                ],
                // Variasi 2: 2 barang sama (12.500 x 2)
                [
                    ['sku' => 'SNK-CHT-001', 'qty' => 2], // Chitato 12.500 x 2
                ],
                // Variasi 3: 1 barang tunggal (25.000)
                [
                    ['sku' => 'HERBAL-003', 'qty' => 1], // Madu TJ Murni 25.000
                ],
            ],
            6000 => [
                // Variasi 1: 2 barang sama (3.000 x 2)
                [
                    ['sku' => 'SNK-BBG-001', 'qty' => 2], // Beng-Beng 3.000 x 2
                ],
                // Variasi 2: 1 barang (6.000)
                [
                    ['sku' => 'MKN-POP-001', 'qty' => 1], // Pop Mie 6.000
                ],
                // Variasi 3: 1 barang (6.000)
                [
                    ['sku' => 'SNK-TRO-001', 'qty' => 1], // Taro Net 6.000
                ],
                // Variasi 4: 1 barang (6.000)
                [
                    ['sku' => 'SNK-CHK-001', 'qty' => 1], // Chiki Balls 6.000
                ],
            ],
            5500 => [
                [
                    ['sku' => 'MNM-ICH-001', 'qty' => 1], // Ichitan 5.500
                ],
                [
                    ['sku' => 'MKN-MSK-001', 'qty' => 1], // Mamasuka 5.500
                ],
            ],
            7000 => [
                // Variasi 1: 2 barang sama (3.500 x 2)
                [
                    ['sku' => 'MKN-IDM-001', 'qty' => 2], // Indomie 3.500 x 2
                ],
                // Variasi 2: 1 barang (7.000)
                [
                    ['sku' => 'MNM-PND-001', 'qty' => 1], // Cincau Panda 7.000
                ],
            ],
            8000 => [
                // Variasi 1: 2 barang sama (4.000 x 2)
                [
                    ['sku' => 'STATIONARY-001', 'qty' => 2], // Buku Tulis 4.000 x 2
                ],
                // Variasi 2: 2 barang sama (4.000 x 2)
                [
                    ['sku' => 'HERBAL-001', 'qty' => 2], // Tolak Angin 4.000 x 2
                ],
                // Variasi 3: 1 barang (8.000)
                [
                    ['sku' => 'MNM-HYD-001', 'qty' => 1], // Hydro Coco 8.000
                ],
                // Variasi 4: 1 barang (8.000)
                [
                    ['sku' => 'SNK-MLK-001', 'qty' => 1], // Roma Malkist 8.000
                ],
            ],
            8500 => [
                // Variasi 1: 2 barang combo (5.000 + 3.500)
                [
                    ['sku' => 'PRC-LFB-001', 'qty' => 1], // Lifebuoy 5.000
                    ['sku' => 'MKN-IDM-001', 'qty' => 1], // Indomie 3.500
                ],
                // Variasi 2: 1 barang (8.500)
                [
                    ['sku' => 'MNM-PCR-001', 'qty' => 1], // Pocari Sweat 8.500
                ],
                // Variasi 3: 1 barang (8.500)
                [
                    ['sku' => 'MKN-LMN-001', 'qty' => 1], // Lemonilo 8.500
                ],
            ],
        ];

        // 3. Baca CSV dan Ambil Baris yang Cocok untuk POS
        $f = fopen($csvPath, 'r');
        $header = fgetcsv($f);
        $header[0] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $header[0]);

        $csvRows = [];
        while (($row = fgetcsv($f)) !== false) {
            $csvRows[] = array_combine($header, $row);
        }
        fclose($f);

        // Batasi kuota alokasi POS per status & nominal agar seimbang dengan PPOB:
        // Total POS: 75 Paid + 20 Cancelled (Expired) = 95 Transaksi
        $posTargetQuotas = [
            'paid' => [
                10000 => 25,
                5000  => 40,
                6000  => 2,
                5500  => 2,
                7000  => 1,
                8000  => 1,
                8500  => 2,
                20000 => 1,
                25000 => 1,
            ],
            'expired' => [
                10000 => 7,
                5000  => 12,
                25000 => 1,
            ],
        ];

        $posCounts = [
            'paid' => array_fill_keys(array_keys($posTargetQuotas['paid']), 0),
            'expired' => array_fill_keys(array_keys($posTargetQuotas['expired']), 0),
        ];
        $selectedPosRows = [];

        foreach ($csvRows as $r) {
            $st = strtolower(trim($r['Status']));
            if (!in_array($st, ['paid', 'expired'])) {
                continue;
            }

            $nom = (int) $r['Nominal'];
            if (isset($posTargetQuotas[$st][$nom]) && $posCounts[$st][$nom] < $posTargetQuotas[$st][$nom]) {
                $selectedPosRows[] = $r;
                $posCounts[$st][$nom]++;
            }
        }

        $this->command->info("2. Mengimpor " . count($selectedPosRows) . " Transaksi POS dari CSV (Paid & Cancelled/Expired)...");

        DB::beginTransaction();
        try {
            $orderCount = 0;
            $itemCount = 0;
            $paidCount = 0;
            $cancelledCount = 0;

            foreach ($selectedPosRows as $row) {
                $reference = trim($row['Reference']);
                $nominal = (float) trim($row['Nominal']);
                $waktuStr = trim($row['Waktu']);
                $rawStatus = strtolower(trim($row['Status']));

                // Map status CSV ke enum database orders: paid -> paid, expired -> cancelled
                $paymentStatus = ($rawStatus === 'paid') ? 'paid' : 'cancelled';
                $amountPaid = ($paymentStatus === 'paid') ? $nominal : 0;

                // Parse waktu persis sesuai CSV
                $createdAt = Carbon::parse($waktuStr)->setTimezone('Asia/Jakarta');
                $timeStr = $createdAt->toDateTimeString();

                // Customer walk-in 80%, terdaftar 20%
                $customerId = null;
                if (!empty($customers) && rand(1, 100) <= 20) {
                    $customerId = $customers[array_rand($customers)];
                }

                // Pilih variasi keranjang untuk nominal ini secara bergiliran agar variasinya kaya
                $nom = (int) $nominal;
                $templates = $basketTemplates[$nom];
                $selectedTemplate = $templates[$orderCount % count($templates)];

                // Hitung total items & pastikan subtotal 100% sama dengan nominal CSV
                $orderItemsData = [];
                $calculatedSubtotal = 0;

                foreach ($selectedTemplate as $itemSpec) {
                    $prod = $allProducts->get($itemSpec['sku']);
                    if (!$prod) {
                        throw new \Exception("Produk dengan SKU {$itemSpec['sku']} tidak ditemukan!");
                    }

                    $qty = $itemSpec['qty'];
                    $unitPrice = (float) $prod->selling_price;
                    $lineTotal = $unitPrice * $qty;
                    $calculatedSubtotal += $lineTotal;

                    $orderItemsData[] = [
                        'product_id'   => $prod->id,
                        'product_name' => $prod->name,
                        'quantity'     => $qty,
                        'unit_price'   => $unitPrice,
                        'total_price'  => $lineTotal,
                        'created_at'   => $timeStr,
                        'updated_at'   => $timeStr,
                    ];
                }

                if ($calculatedSubtotal !== (float)$nominal) {
                    throw new \Exception("Mismatch harga keranjang ({$calculatedSubtotal}) vs Nominal CSV ({$nominal}) untuk ref {$reference}!");
                }

                // Cek apakah order dengan referensi ini sudah ada di database
                $existingOrder = Order::where('invoice_number', $reference)
                    ->orWhere('qris_reference_id', $reference)
                    ->first();

                $notes = ($paymentStatus === 'paid')
                    ? "QRIS POS Borderpay: {$reference}"
                    : "QRIS POS Borderpay (Batal/Expired): {$reference}";

                $orderData = [
                    'user_id'           => $cashierId, // 100% Yuliana
                    'customer_id'       => $customerId,
                    'channel'           => 'pos',
                    'invoice_number'    => $reference, // Nomor Invoice QRIS dari CSV
                    'subtotal'          => $calculatedSubtotal,
                    'discount'          => 0,
                    'tax'               => 0,
                    'total_amount'      => $nominal,
                    'payment_method'    => 'qris',
                    'amount_paid'       => $amountPaid,
                    'change'            => 0,
                    'payment_status'    => $paymentStatus,
                    'qris_reference_id' => $reference,
                    'qris_qr_string'    => null,
                    'notes'             => $notes,
                    'created_at'        => $timeStr,
                    'updated_at'        => $timeStr,
                ];

                if ($existingOrder) {
                    $existingOrder->items()->delete();
                    $existingOrder->update($orderData);
                    $order = $existingOrder;
                } else {
                    $order = Order::create($orderData);
                }

                foreach ($orderItemsData as $itemRow) {
                    $itemRow['order_id'] = $order->id;
                    OrderItem::create($itemRow);
                    $itemCount++;
                }

                if ($paymentStatus === 'paid') {
                    $paidCount++;
                } else {
                    $cancelledCount++;
                }

                $orderCount++;
            }

            DB::commit();

            $this->command->info("Selesai! Berhasil mengimpor {$orderCount} transaksi POS QRIS dari CSV (Paid: {$paidCount}, Cancelled/Expired: {$cancelledCount}).");
            $this->command->info("Total Items: {$itemCount} item barang.");
            $this->command->info("Kasir: 100% Yuliana.");
            $this->command->info("Invoice ID: 100% persis kolom Reference CSV.");
            $this->command->info("Waktu: 100% persis kolom Waktu CSV.");
            $this->command->info("Distribusi Keranjang: Campuran 1 produk, 2 produk (5000x2, 6000+4000), hingga 3 produk.");
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->command->error("Terjadi error: " . $e->getMessage());
        }
    }
}
