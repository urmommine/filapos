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

        // 8 Katalog produk PPOB nyata dengan nominal kecil-menengah (Total: Rp 161.000)
        $ppobProducts = [
            [
                'name' => 'Pulsa Telkomsel Rp 10.000',
                'game' => 'Telkomsel',
                'slug' => 'telkomsel',
                'sku' => 'telkomsel-10k',
                'nominal' => 10000,
                'type' => 'pulsa',
            ],
            [
                'name' => 'Token Listrik PLN Rp 20.000',
                'game' => 'PLN Prepaid',
                'slug' => 'pln-prepaid',
                'sku' => 'pln-20k',
                'nominal' => 20000,
                'type' => 'pln',
            ],
            [
                'name' => 'Top Up Mobile Legends - 28 Diamonds',
                'game' => 'Mobile Legends',
                'slug' => 'mobile-legends',
                'sku' => 'mlbb-28dm',
                'nominal' => 10000,
                'type' => 'game',
            ],
            [
                'name' => 'Top Up Free Fire - 70 Diamonds',
                'game' => 'Free Fire',
                'slug' => 'free-fire',
                'sku' => 'ff-70dm',
                'nominal' => 10000,
                'type' => 'game',
            ],
            [
                'name' => 'Pulsa Indosat Ooredoo Rp 25.000',
                'game' => 'Indosat',
                'slug' => 'indosat',
                'sku' => 'indosat-25k',
                'nominal' => 25000,
                'type' => 'pulsa',
            ],
            [
                'name' => 'Paket Data Tri Happy 2GB 7 Hari',
                'game' => 'Tri (3)',
                'slug' => 'tri',
                'sku' => 'tri-happy-2gb',
                'nominal' => 15000,
                'type' => 'data',
            ],
            [
                'name' => 'Token Listrik PLN Rp 50.000',
                'game' => 'PLN Prepaid',
                'slug' => 'pln-prepaid',
                'sku' => 'pln-50k',
                'nominal' => 50000,
                'type' => 'pln',
            ],
            [
                'name' => 'Top Up Mobile Legends - 86 Diamonds',
                'game' => 'Mobile Legends',
                'slug' => 'mobile-legends',
                'sku' => 'mlbb-86dm',
                'nominal' => 21000,
                'type' => 'game',
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

        // Target: Total POS ~Rp 340.000 + Total PPOB Rp 161.000 = Total Grand ~Rp 501.000 (< Rp 1.000.000)
        $targetPosBudget = 340000;
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
                        'notes' => 'Transaksi POS Kasir Tunai',
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
            $this->command->info("- PPOB Saldo: {$ppobCount} transaksi (Total: Rp 161.000)");
            $this->command->info(">>> GRAND TOTAL PENJUALAN: Rp " . number_format($grandTotalSales, 0, ',', '.') . " <<<");
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->command->error("Gagal menambahkan seeder transaksi: " . $e->getMessage());
            throw $e;
        }
    }
}
