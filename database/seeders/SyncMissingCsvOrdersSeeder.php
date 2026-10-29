<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\WalletTransaction;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SyncMissingCsvOrdersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvPath = database_path('seeders/data/borderpay_transactions.csv');
        if (!file_exists($csvPath)) {
            $this->command->error("CSV file tidak ditemukan di {$csvPath}");
            return;
        }

        $file = fopen($csvPath, 'r');
        $header = fgetcsv($file);
        $header[0] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $header[0]);
        $header = array_map('trim', $header);

        $csvRows = [];
        while (($row = fgetcsv($file)) !== false) {
            if (empty($row) || count($row) < 5) continue;
            $csvRows[] = array_combine($header, $row);
        }
        fclose($file);

        DB::beginTransaction();

        try {
            // Ambil semua invoice yang sudah ada di orders
            $existingInvoices = Order::pluck('invoice_number')->flip()->toArray();
            $existingQrisRefs = Order::pluck('qris_reference_id')->filter()->flip()->toArray();

            $syncedCount = 0;
            $defaultUser = User::where('role', 'user')->first() ?? User::first();

            foreach ($csvRows as $r) {
                $ref = trim($r['Reference']);
                $status = strtolower(trim($r['Status']));
                $nominal = (float) $r['Nominal'];
                $fee = (float) ($r['Fee'] ?? 0);
                $diterima = (float) ($r['Diterima'] ?? 0);
                $waktuStr = $r['Waktu'] ?? '';
                $createdAt = !empty($waktuStr) ? Carbon::parse($waktuStr)->setTimezone('Asia/Jakarta') : now();

                // Hanya proses yang berstatus PAID
                if ($status !== 'paid') {
                    continue;
                }

                // Jika sudah ada di orders, lewati
                if (isset($existingInvoices[$ref]) || isset($existingQrisRefs[$ref])) {
                    continue;
                }

                // Cari apakah ada transaksi dompet yang sudah memakai ref ini
                $walletTx = WalletTransaction::where('reference_id', $ref)->first();

                // Tentukan user pemilik
                $user = $walletTx?->user ?? $defaultUser;

                // Jika belum ada di wallet_transactions (kasus BORDER-1793094612735-LU8RN), buat juga di wallet_transactions
                if (!$walletTx) {
                    $depositAmount = ($diterima > 0) ? $diterima : $nominal;
                    $openingBalance = (float) $user->balance;
                    $closingBalance = $openingBalance + $depositAmount;

                    $walletTx = WalletTransaction::create([
                        'user_id' => $user->id,
                        'order_id' => null,
                        'type' => 'deposit',
                        'amount' => $depositAmount,
                        'opening_balance' => $openingBalance,
                        'closing_balance' => $closingBalance,
                        'status' => 'completed',
                        'payment_method' => 'qris',
                        'qris_qr_string' => null,
                        'qris_expires_at' => $createdAt->copy()->addMinutes(15),
                        'description' => "Pengisian Saldo via QRIS BorderPay - {$ref}",
                        'metadata' => [
                            'nominal_transfer' => $nominal,
                            'fee' => $fee,
                            'diterima' => $diterima,
                            'project' => $r['Project'] ?? 'filashops',
                            'provider' => 'BorderPay',
                        ],
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ]);

                    $user->balance = $closingBalance;
                    $user->save();
                }

                // Buat Order sebagai Top Up Saldo
                $notesData = [
                    'type' => 'deposit',
                    'game' => 'Top Up Saldo',
                    'game_slug' => 'topup-saldo',
                    'target' => 'Akun Member ' . $user->name,
                    'zone' => 'ID ' . $user->id,
                    'nickname' => $user->name,
                    'customer_phone' => $user->phone ?: '08123456789',
                    'sn' => 'TOPUP/' . $createdAt->format('Ymd') . '/' . strtoupper(substr(md5($ref), 0, 10)),
                    'melo_status' => 'success',
                    'fee' => $fee,
                    'diterima' => $diterima,
                    'csv_reference' => $ref,
                ];

                $order = Order::create([
                    'user_id' => $user->id,
                    'customer_id' => null,
                    'channel' => 'ppob',
                    'invoice_number' => $ref,
                    'subtotal' => $nominal,
                    'discount' => 0,
                    'tax' => 0,
                    'total_amount' => $nominal,
                    'payment_method' => 'qris',
                    'amount_paid' => $nominal,
                    'change' => 0,
                    'payment_status' => 'paid',
                    'qris_reference_id' => $ref,
                    'notes' => json_encode($notesData),
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);

                // Buat Order Item
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => null,
                    'product_name' => 'Top Up Saldo Akun Rp ' . number_format($nominal, 0, ',', '.'),
                    'quantity' => 1,
                    'unit_price' => $nominal,
                    'total_price' => $nominal,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);

                // Kaitkan order_id ke WalletTransaction
                $walletTx->update(['order_id' => $order->id]);

                // Update array pencarian agar tidak dobel
                $existingInvoices[$ref] = true;
                $existingQrisRefs[$ref] = true;

                $syncedCount++;
            }

            DB::commit();

            $this->command->info("Selesai! Berhasil menyinkronkan {$syncedCount} invoice CSV ke tabel orders sebagai Top Up Saldo.");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error("Gagal sinkronisasi: " . $e->getMessage());
            throw $e;
        }
    }
}
