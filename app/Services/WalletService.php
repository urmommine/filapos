<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use App\Models\WalletTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WalletService
{
    /**
     * Membuat permintaan Deposit Saldo melalui QRIS BorderPay.
     */
    public static function createDepositQris(User $user, float $amount): array
    {
        $intAmount = (int) round($amount);
        if ($intAmount < 1000) {
            return [
                'success' => false,
                'message' => 'Nominal pengisian saldo minimal Rp1.000',
            ];
        }

        $referenceId = 'DEP-' . date('YmdHis') . '-' . strtoupper(substr(uniqid(), -4));

        // Request QRIS ke BorderPay
        $paymentResult = BorderPayService::createQrisPayment(
            amount: $intAmount,
            referenceId: $referenceId
        );

        if (!$paymentResult['success']) {
            $msg = $paymentResult['message'] ?? 'Gagal membuat QRIS deposit BorderPay';
            return [
                'success' => false,
                'message' => is_array($msg) ? ($msg['message'] ?? json_encode($msg)) : (string) $msg,
            ];
        }

        $expiresAt = !empty($paymentResult['expires_at'])
            ? Carbon::parse($paymentResult['expires_at'])
            : now()->addMinutes(15);

        // Catat mutasi deposit berstatus pending
        $walletTx = WalletTransaction::create([
            'user_id' => $user->id,
            'reference_id' => $referenceId,
            'order_id' => null,
            'type' => 'deposit',
            'amount' => $intAmount,
            'opening_balance' => (float) $user->balance,
            'closing_balance' => (float) $user->balance, // Belum berubah sampai lunas
            'status' => 'pending',
            'payment_method' => 'qris',
            'qris_qr_string' => $paymentResult['qr_string'],
            'qris_expires_at' => $expiresAt,
            'description' => "Pengisian Saldo via QRIS BorderPay - {$referenceId}",
            'metadata' => [
                'pay_url' => $paymentResult['pay_url'] ?? null,
                'fee' => $paymentResult['fee'] ?? 0,
            ],
        ]);

        return [
            'success' => true,
            'transaction_id' => $walletTx->id,
            'reference_id' => $referenceId,
            'qr_string' => $paymentResult['qr_string'],
            'amount' => $intAmount,
            'formatted_amount' => 'Rp ' . number_format($intAmount, 0, ',', '.'),
            'expires_at' => $expiresAt->toISOString(),
            'time_left' => 900,
        ];
    }

    /**
     * Konfirmasi pembayaran deposit dan tambahkan saldo akun secara aman (Pessimistic Locking).
     */
    public static function confirmDeposit(WalletTransaction $walletTx): bool
    {
        if ($walletTx->status === 'completed') {
            return true;
        }

        try {
            DB::beginTransaction();

            $user = User::where('id', $walletTx->user_id)->lockForUpdate()->first();
            if (!$user) {
                DB::rollBack();
                return false;
            }

            $opening = (float) $user->balance;
            $closing = $opening + (float) $walletTx->amount;

            // Tambah saldo user
            $user->update(['balance' => $closing]);

            // Update transaksi deposit
            $walletTx->update([
                'status' => 'completed',
                'opening_balance' => $opening,
                'closing_balance' => $closing,
            ]);

            DB::commit();
            Log::info("Deposit sukses ID {$walletTx->id} untuk User {$user->id}: +Rp {$walletTx->amount}. Saldo baru: Rp {$closing}");
            return true;
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("confirmDeposit failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Pembayaran Order Produk PPOB menggunakan Saldo Akun User.
     */
    public static function payOrderWithBalance(User $user, Order $order, float $amount): array
    {
        try {
            DB::beginTransaction();

            $lockedUser = User::where('id', $user->id)->lockForUpdate()->first();
            if (!$lockedUser) {
                DB::rollBack();
                return ['success' => false, 'message' => 'Pengguna tidak ditemukan.'];
            }

            $currentBalance = (float) $lockedUser->balance;
            if ($currentBalance < $amount) {
                DB::rollBack();
                return [
                    'success' => false,
                    'message' => 'Saldo Anda tidak mencukupi (Tersedia: Rp ' . number_format($currentBalance, 0, ',', '.') . '). Silakan isi saldo terlebih dahulu.',
                ];
            }

            $newBalance = $currentBalance - $amount;

            // 1. Kurangi Saldo User
            $lockedUser->update(['balance' => $newBalance]);

            // 2. Catat Mutasi Pengeluaran Saldo
            WalletTransaction::create([
                'user_id' => $lockedUser->id,
                'reference_id' => $order->invoice_number,
                'order_id' => $order->id,
                'type' => 'payment',
                'amount' => $amount,
                'opening_balance' => $currentBalance,
                'closing_balance' => $newBalance,
                'status' => 'completed',
                'payment_method' => 'balance',
                'description' => "Pembayaran Order PPOB #{$order->invoice_number}",
                'metadata' => [
                    'invoice_number' => $order->invoice_number,
                ],
            ]);

            // 3. Update Status Order menjadi Paid
            $order->update([
                'customer_id' => $lockedUser->id,
                'payment_method' => 'balance',
                'payment_status' => 'paid',
                'amount_paid' => $amount,
                'change' => 0,
            ]);

            DB::commit();

            return [
                'success' => true,
                'new_balance' => $newBalance,
                'formatted_new_balance' => 'Rp ' . number_format($newBalance, 0, ',', '.'),
            ];
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("payOrderWithBalance error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Gagal memproses pembayaran saldo: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Refund saldo jika provider Melostore H2H gagal mengirimkan produk.
     */
    public static function refundOrderBalance(User $user, Order $order, float $amount, string $reason = 'Provider busy'): bool
    {
        try {
            DB::beginTransaction();

            $lockedUser = User::where('id', $user->id)->lockForUpdate()->first();
            if (!$lockedUser) {
                DB::rollBack();
                return false;
            }

            $currentBalance = (float) $lockedUser->balance;
            $newBalance = $currentBalance + $amount;

            $lockedUser->update(['balance' => $newBalance]);

            WalletTransaction::create([
                'user_id' => $lockedUser->id,
                'reference_id' => 'REF-' . $order->invoice_number,
                'order_id' => $order->id,
                'type' => 'refund',
                'amount' => $amount,
                'opening_balance' => $currentBalance,
                'closing_balance' => $newBalance,
                'status' => 'completed',
                'payment_method' => 'balance',
                'description' => "Pengembalian Saldo: {$reason} (#{$order->invoice_number})",
                'metadata' => [
                    'order_id' => $order->id,
                    'reason' => $reason,
                ],
            ]);

            DB::commit();
            return true;
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("refundOrderBalance failed: " . $e->getMessage());
            return false;
        }
    }
}
