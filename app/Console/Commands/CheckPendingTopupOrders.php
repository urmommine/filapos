<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\BorderPayService;
use App\Services\MeloStoreService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckPendingTopupOrders extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'ppob:check-pending';

    /**
     * The console command description.
     */
    protected $description = 'Cek status pembayaran QRIS pending ke BorderPay dan proses top up Melostore jika sudah lunas (Anti-gagal tanpa webhook)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (!BorderPayService::isConfigured()) {
            $this->warn('BorderPay API belum dikonfigurasi. Lewati pengecekan.');
            return Command::SUCCESS;
        }

        // Ambil transaksi top up PPOB pending yang dibuat dalam 25 menit terakhir
        $pendingOrders = Order::where('payment_status', 'pending')
            ->where('payment_method', 'qris')
            ->whereNotNull('qris_reference_id')
            ->where('notes', 'like', '%ppob_game_topup%')
            ->where('created_at', '>=', now()->subMinutes(25))
            ->get();

        // 1. Ambil transaksi Deposit Saldo QRIS pending
        $pendingDeposits = \App\Models\WalletTransaction::where('type', 'deposit')
            ->where('status', 'pending')
            ->whereNotNull('reference_id')
            ->where('created_at', '>=', now()->subMinutes(25))
            ->get();

        foreach ($pendingDeposits as $deposit) {
            $statusResult = BorderPayService::checkPaymentStatus($deposit->reference_id);
            if ($statusResult['success'] && $statusResult['status'] === 'paid') {
                $this->info("Deposit {$deposit->reference_id} LUNAS! Menambahkan saldo user...");
                \App\Services\WalletService::confirmDeposit($deposit);
            } elseif ($statusResult['status'] === 'expired' || ($deposit->qris_expires_at && now()->greaterThan($deposit->qris_expires_at))) {
                BorderPayService::cancelPayment($deposit->reference_id);
                $deposit->update(['status' => 'expired']);
            }
        }

        // 2. Ambil transaksi Order PPOB pending
        if ($pendingOrders->isEmpty() && $pendingDeposits->isEmpty()) {
            $this->info('Tidak ada order/deposit pending yang perlu diverifikasi.');
            return Command::SUCCESS;
        }

        $this->info("Menemukan {$pendingOrders->count()} order pending. Memeriksa ke BorderPay...");

        foreach ($pendingOrders as $order) {
            $invoice = $order->qris_reference_id;
            $this->line("Memeriksa invoice: {$invoice}...");

            $expiresAt = $order->qris_expires_at 
                ? Carbon::parse($order->qris_expires_at) 
                : $order->created_at->addMinutes(15);

            $statusResult = BorderPayService::checkPaymentStatus($invoice);

            if ($statusResult['success'] && $statusResult['status'] === 'paid') {
                $this->info("Invoice {$invoice} TELAH DIBAYAR! Memproses order ke Melostore H2H...");

                $order->update([
                    'payment_status' => 'paid',
                    'amount_paid' => $order->total_amount,
                ]);

                // Eksekusi Pemesanan ke Melostore H2H
                $notes = json_decode($order->notes ?? '{}', true) ?: [];
                $sku = $notes['sku_code'] ?? '';
                $target = $notes['target'] ?? '';
                $zone = $notes['zone'] ?? null;

                if (!empty($sku) && !empty($target)) {
                    $meloResult = MeloStoreService::createTransaction(
                        skuCode: $sku,
                        target: $target,
                        zone: !empty($zone) ? $zone : null,
                        buyerTrxId: $order->invoice_number,
                        additionalData: ['email' => 'buyer@topup.com']
                    );

                    if ($meloResult['success']) {
                        $notes['melostore_trx_id'] = $meloResult['trx_id'] ?? null;
                        $notes['melostore_sn'] = $meloResult['sn'] ?? 'PROCESSING';
                        $order->update(['notes' => json_encode($notes)]);
                        $this->info("Top up sukses untuk invoice {$invoice}! SN: " . ($meloResult['sn'] ?? 'PROCESSING'));
                    } else {
                        $notes['melo_error'] = $meloResult['message'] ?? 'Provider gagal memproses';
                        $order->update(['notes' => json_encode($notes)]);
                        $this->warn("Provider gagal memproses {$invoice}: " . ($meloResult['message'] ?? 'Error'));
                    }
                }

            } elseif ($statusResult['status'] === 'expired' || now()->greaterThan($expiresAt)) {
                $this->warn("Invoice {$invoice} kadaluwarsa (> 15 menit). Membatalkan...");
                BorderPayService::cancelPayment($invoice);
                $order->update(['payment_status' => 'expired']);
            } else {
                $this->line("Invoice {$invoice} masih menunggu pembayaran.");
            }
        }

        return Command::SUCCESS;
    }
}
