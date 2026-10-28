<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Models\WalletTransaction;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddOctober29TransactionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 24 Transaksi untuk tanggal 29 Oktober 2026:
     * - 16 POS Cash (66.67% ~ 67%)
     * - 8 PPOB Saldo (33.33% ~ 33%)
     * Grand total transaksi keseluruhan di kisaran ~Rp 500.000 (di bawah Rp 1.000.000).
     * Harga dan kalkulasi match 100% dengan database dan stok ter-update.
     */
    public function run(): void
    {
        $this->command->info("Menambahkan 24 data transaksi tanggal 29 Oktober 2026 (Grand total ~500rb, max < 1jt)...");

        $cashier = User::where('role', 'kasir')->first() ?? User::where('email', 'yuliana@pos.com')->first();
        $cashierId = $cashier ? $cashier->id : 2;

        $members = User::where('role', 'user')->get();
        if ($members->isEmpty()) {
            $this->command->error("Tidak ada user dengan role 'user'!");
            return;
        }

        // Ambil produk ritel/harian dengan harga terjangkau (Rp 2.500 s.d. Rp 25.000)
        $cheapProducts = Product::where('is_active', true)
            ->where('selling_price', '>=', 2500)
            ->where('selling_price', '<=', 25000)
            ->where('stock', '>=', 10)
            ->get();

        if ($cheapProducts->isEmpty()) {
            $this->command->error("Tidak ada produk aktif dengan harga murah dan stok cukup!");
            return;
        }

        $customerIds = Customer::pluck('id')->toArray();

        // 8 Katalog produk PPOB nyata diambil langsung dari endpoint Melostore H2H (Harga Modal + Margin Resmi Sistem)
        $ppobProducts = [
            [
                'name' => 'Top Up Mobile Legends - 33 Diamonds',
                'game' => 'Mobile Legends',
                'slug' => 'mobile-legends',
                'sku' => 'mlid33dd-s12a',
                'nominal' => 11200, // Modal: Rp 10.636 + Margin 5% = Rp 11.200
                'type' => 'game',
            ],
            [
                'name' => 'Top Up Free Fire - 110 Diamonds',
                'game' => 'Free Fire',
                'slug' => 'free-fire',
                'sku' => 'ffgl110da-s111',
                'nominal' => 15600, // Modal: Rp 14.836 + Margin 5% = Rp 15.600
                'type' => 'game',
            ],
            [
                'name' => 'Token Listrik PLN 20.000',
                'game' => 'PLN Prepaid',
                'slug' => 'pln-prepaid',
                'sku' => 'pp20kpo-s9',
                'nominal' => 22500, // Modal: Rp 21.365 + Margin 5% = Rp 22.500
                'type' => 'pln',
            ],
            [
                'name' => 'Pulsa Telkomsel - Pulsa 8.000',
                'game' => 'Telkomsel',
                'slug' => 'telkomsel-12',
                'sku' => 't1kmp8ko-s41',
                'nominal' => 10200, // Modal: Rp 9.665 + Margin 5% = Rp 10.200
                'type' => 'pulsa',
            ],
            [
                'name' => 'Pulsa Indosat - Pulsa 10.000',
                'game' => 'Indosat',
                'slug' => 'indosat-12',
                'sku' => 'isatidp10ka-s12',
                'nominal' => 12000, // Modal: Rp 11.386 + Margin 5% = Rp 12.000
                'type' => 'pulsa',
            ],
            [
                'name' => 'Top Up Free Fire - Weekly Lite Diamonds',
                'game' => 'Free Fire',
                'slug' => 'free-fire',
                'sku' => 'ffglwld-s111',
                'nominal' => 7500, // Modal: Rp 7.103 + Margin 5% = Rp 7.500
                'type' => 'game',
            ],
            [
                'name' => 'Token Listrik PLN 10.000',
                'game' => 'PLN Prepaid',
                'slug' => 'pln-prepaid',
                'sku' => 'ppprs10ko-s9',
                'nominal' => 12000, // Modal: Rp 11.415 + Margin 5% = Rp 12.000
                'type' => 'pln',
            ],
            [
                'name' => 'Top Up Saldo DANA - Tier Basic',
                'game' => 'DANA',
                'slug' => 'dana',
                'sku' => 'dana-2',
                'nominal' => 10500, // Modal: Rp 10.000 + Margin 5% = Rp 10.500
                'type' => 'ewallet',
            ],
        ];

        // Hapus data tanggal 2026-10-29 jika sudah ada agar seeder bersifat idempoten
        $existingOrders = Order::whereDate('created_at', '2026-10-29')->get();
        foreach ($existingOrders as $oldOrder) {
            OrderItem::where('order_id', $oldOrder->id)->delete();
            WalletTransaction::where('order_id', $oldOrder->id)->delete();
            $oldOrder->delete();
        }

        // Distribusi Tepat: 16 POS (66.67% ~ 67%), 8 PPOB (33.33% ~ 33%) = 24 Transaksi
        $sequence = [
            'pos', 'pos', 'ppob',
            'pos', 'pos', 'ppob',
            'pos', 'pos', 'ppob',
            'pos', 'pos', 'ppob',
            'pos', 'pos', 'ppob',
            'pos', 'pos', 'ppob',
            'pos', 'pos', 'ppob',
            'pos', 'pos', 'ppob',
        ];

        // 24 Timestamp acak antara 08:00 s.d. 21:45 WIB
        $dateStr = '2026-10-29';
        $carbonDate = Carbon::parse($dateStr);

        $secondOffsets = [];
        for ($i = 0; $i < 24; $i++) {
            $secondOffsets[] = rand(8 * 3600, 21 * 3600 + 45 * 60);
        }
        sort($secondOffsets);

        // Generate 24 invoice random 4-digit unik
        $randomCodes = [];
        while (count($randomCodes) < 24) {
            $code = str_pad((string) rand(1000, 9999), 4, '0', STR_PAD_LEFT);
            $randomCodes[$code] = $code;
        }
        $randomCodes = array_values($randomCodes);

        // Target: Total POS ~Rp 395.000 + Total PPOB Rp 101.500 = Total Grand ~Rp 496.500 (< Rp 1.000.000)
        $targetPosBudget = 395000;
        $runningPosSum = 0;

        DB::beginTransaction();

        try {
            $posCount = 0;
            $ppobCount = 0;
            $ppobIndex = 0;
            $grandTotalSales = 0;

            for ($i = 0; $i < 24; $i++) {
                $type = $sequence[$i];
                $timeOffset = $secondOffsets[$i];
                $orderTime = $carbonDate->copy()->startOfDay()->addSeconds($timeOffset);
                $timeStr = $orderTime->toDateTimeString();
                $randomCode = $randomCodes[$i];
                $invoiceNumber = 'INV-20261029-' . $randomCode;

                if ($type === 'pos') {
                    // ==========================================
                    // 1. TRANSAKSI POS (CASH ONLY)
                    // ==========================================
                    $posCount++;

                    // Customer: 20% probabilitas terhubung ke customer terdaftar
                    $customerId = null;
                    if (!empty($customerIds) && rand(1, 100) <= 20) {
                        $customerId = $customerIds[array_rand($customerIds)];
                    }

                    // Hitung target alokasi transaksi POS ini agar rata-rata nominal tetap proporsional (~21.000 / tx)
                    $remainingPosTx = 16 - ($posCount - 1);
                    $remainingBudget = max(15000 * $remainingPosTx, $targetPosBudget - $runningPosSum);
                    $targetThisTx = $remainingBudget / $remainingPosTx;

                    // Keranjang: 1 s.d. 3 item murah
                    $numItems = rand(1, 3);
                    $selectedProducts = $cheapProducts->random(min($numItems, $cheapProducts->count()));

                    $orderItemsData = [];
                    $subtotal = 0;

                    foreach ($selectedProducts as $prod) {
                        $qty = rand(1, 2);
                        $unitPrice = (float) $prod->selling_price;
                        $lineTotal = round($unitPrice * $qty, 2);

                        // Batasi agar tidak melampaui target transaksi per order jika sudah ada item
                        if ($subtotal > 0 && ($subtotal + $lineTotal) > ($targetThisTx * 1.25)) {
                            $qty = 1;
                            $lineTotal = round($unitPrice * $qty, 2);
                            if ($subtotal > 0 && ($subtotal + $lineTotal) > ($targetThisTx * 1.35)) {
                                continue;
                            }
                        }

                        $subtotal += $lineTotal;

                        $orderItemsData[] = [
                            'product' => $prod,
                            'product_id' => $prod->id,
                            'product_name' => $prod->name,
                            'quantity' => $qty,
                            'unit_price' => $unitPrice,
                            'total_price' => $lineTotal,
                        ];

                        if ($subtotal >= $targetThisTx * 0.9) {
                            break;
                        }
                    }

                    // Fallback jika tidak ada item terpilih
                    if (empty($orderItemsData)) {
                        $fallbackProd = $cheapProducts->where('selling_price', '<=', 15000)->random();
                        $unitPrice = (float) $fallbackProd->selling_price;
                        $subtotal = $unitPrice;
                        $orderItemsData[] = [
                            'product' => $fallbackProd,
                            'product_id' => $fallbackProd->id,
                            'product_name' => $fallbackProd->name,
                            'quantity' => 1,
                            'unit_price' => $unitPrice,
                            'total_price' => $unitPrice,
                        ];
                    }

                    $totalAmount = $subtotal;
                    $runningPosSum += $totalAmount;
                    $grandTotalSales += $totalAmount;

                    // Logika uang tunai yang realistis (uang pas atau pecahan 10k, 20k, 50k)
                    $denominations = [10000, 20000, 50000];
                    $amountPaid = $totalAmount;

                    if (rand(1, 100) <= 65) {
                        foreach ($denominations as $denom) {
                            $multiple = ceil($totalAmount / $denom) * $denom;
                            if ($multiple >= $totalAmount) {
                                $amountPaid = (float) $multiple;
                                break;
                            }
                        }
                    }

                    if ($amountPaid < $totalAmount) {
                        $amountPaid = ceil($totalAmount / 10000) * 10000;
                    }

                    $change = round($amountPaid - $totalAmount, 2);

                    $order = Order::create([
                        'user_id' => $cashierId,
                        'customer_id' => $customerId,
                        'channel' => 'pos',
                        'invoice_number' => $invoiceNumber,
                        'subtotal' => $subtotal,
                        'discount' => 0,
                        'tax' => 0,
                        'total_amount' => $totalAmount,
                        'payment_method' => 'cash',
                        'amount_paid' => $amountPaid,
                        'change' => $change,
                        'payment_status' => 'paid',
                        'qris_reference_id' => null,
                        'qris_qr_string' => null,
                        'notes' => null,
                        'created_at' => $timeStr,
                        'updated_at' => $timeStr,
                    ]);

                    foreach ($orderItemsData as $item) {
                        OrderItem::create([
                            'order_id' => $order->id,
                            'product_id' => $item['product_id'],
                            'product_name' => $item['product_name'],
                            'quantity' => $item['quantity'],
                            'unit_price' => $item['unit_price'],
                            'total_price' => $item['total_price'],
                            'created_at' => $timeStr,
                            'updated_at' => $timeStr,
                        ]);

                        // Kurangi stok produk secara presisi
                        $item['product']->decrement('stock', $item['quantity']);
                    }
                } else {
                    // ==========================================
                    // 2. TRANSAKSI PPOB (PEMBAYARAN SALDO)
                    // ==========================================
                    $ppobCount++;
                    $prod = $ppobProducts[$ppobIndex % count($ppobProducts)];
                    $ppobIndex++;

                    $member = $members[$ppobCount % $members->count()];
                    $nominal = (float) $prod['nominal'];
                    $grandTotalSales += $nominal;

                    // Pastikan saldo member mencukupi sebelum transaksi
                    $currentBalance = (float) $member->balance;
                    if ($currentBalance < $nominal) {
                        $member->balance = $currentBalance + 100000;
                        $member->save();
                        $currentBalance = (float) $member->balance;
                    }

                    $newBalance = round($currentBalance - $nominal, 2);
                    $member->balance = $newBalance;
                    $member->save();

                    $playerId = (string) rand(10000000, 99999999);
                    $zoneId = (string) rand(2000, 2999);
                    $sn = 'MELO/20261029/' . strtoupper(substr(md5($invoiceNumber), 0, 10));

                    $notesData = [
                        'type' => 'ppob_' . $prod['type'],
                        'provider' => 'melostore_h2h',
                        'game' => $prod['game'],
                        'game_slug' => $prod['slug'],
                        'sku_code' => $prod['sku'],
                        'target' => $playerId,
                        'zone' => $prod['type'] === 'game' ? $zoneId : null,
                        'nickname' => $member->name,
                        'customer_phone' => $member->phone ?: '0812' . rand(10000000, 99999999),
                        'sn' => $sn,
                        'melo_status' => 'success',
                        'payment_source' => 'member_wallet_balance',
                    ];

                    $order = Order::create([
                        'user_id' => $member->id,
                        'customer_id' => null,
                        'channel' => 'ppob',
                        'invoice_number' => $invoiceNumber,
                        'subtotal' => $nominal,
                        'discount' => 0,
                        'tax' => 0,
                        'total_amount' => $nominal,
                        'payment_method' => 'balance',
                        'amount_paid' => $nominal,
                        'change' => 0,
                        'payment_status' => 'paid',
                        'qris_reference_id' => null,
                        'qris_qr_string' => null,
                        'notes' => json_encode($notesData),
                        'created_at' => $timeStr,
                        'updated_at' => $timeStr,
                    ]);

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => null,
                        'product_name' => $prod['name'],
                        'quantity' => 1,
                        'unit_price' => $nominal,
                        'total_price' => $nominal,
                        'created_at' => $timeStr,
                        'updated_at' => $timeStr,
                    ]);

                    WalletTransaction::create([
                        'user_id' => $member->id,
                        'reference_id' => $invoiceNumber,
                        'order_id' => $order->id,
                        'type' => 'payment',
                        'amount' => $nominal,
                        'opening_balance' => $currentBalance,
                        'closing_balance' => $newBalance,
                        'status' => 'completed',
                        'payment_method' => 'balance',
                        'description' => "Pembayaran Order PPOB #{$invoiceNumber}",
                        'metadata' => [
                            'invoice_number' => $invoiceNumber,
                            'product_name' => $prod['name'],
                            'nominal' => $nominal,
                        ],
                        'created_at' => $timeStr,
                        'updated_at' => $timeStr,
                    ]);
                }
            }

            DB::commit();

            $this->command->info("BERHASIL! Menambahkan 24 Transaksi untuk 29 Oktober 2026:");
            $this->command->info("- POS Cash: {$posCount} transaksi (Total: Rp " . number_format($runningPosSum, 0, ',', '.') . ")");
            $this->command->info("- PPOB Saldo: {$ppobCount} transaksi (Total: Rp " . number_format($grandTotalSales - $runningPosSum, 0, ',', '.') . ")");
            $this->command->info(">>> GRAND TOTAL PENJUALAN: Rp " . number_format($grandTotalSales, 0, ',', '.') . " <<<");
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->command->error("Gagal menambahkan seeder transaksi: " . $e->getMessage());
            throw $e;
        }
    }
}
