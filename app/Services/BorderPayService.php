<?php

namespace App\Services;

use App\Models\StoreSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BorderPayService
{
    /**
     * Ambil API Key BorderPay (Prioritas: StoreSetting DB -> config/borderpay.php).
     */
    public static function getApiKey(): string
    {
        $dbKey = StoreSetting::get(StoreSetting::BORDERPAY_API_KEY);
        if (!empty($dbKey)) {
            return trim($dbKey);
        }

        return trim(config('borderpay.api_key', ''));
    }

    /**
     * Ambil Base URL BorderPay API v1.
     */
    public static function getBaseUrl(): string
    {
        return rtrim(config('borderpay.base_url', 'https://borderpay.id/api/v1'), '/');
    }

    /**
     * Cek apakah integrasi BorderPay aktif dan memiliki API Key.
     */
    public static function isConfigured(): bool
    {
        $isActive = StoreSetting::get(StoreSetting::BORDERPAY_ACTIVE, '1');
        if ($isActive === '0' || $isActive === false) {
            return false;
        }

        return !empty(self::getApiKey());
    }

    /**
     * Generate format Invoice khusus BorderPay.
     * Format: BORDER-{timestamp_in_ms}-{random_4_digits}
     * Contoh: BORDER-1793161063843-7091
     */
    public static function generateInvoiceNumber(): string
    {
        $timestampMs = (int) round(microtime(true) * 1000);
        $random = str_pad((string) random_int(1000, 9999), 4, '0', STR_PAD_LEFT);

        return "BORDER-{$timestampMs}-{$random}";
    }

    /**
     * Inisialisasi transaksi QRIS ke BorderPay.
     * POST /api/v1/payments
     */
    public static function createQrisPayment(
        float|int $amount,
        ?string $referenceId = null,
        ?string $returnUrl = null,
        ?string $customerName = null,
        ?string $customerEmail = null,
        ?string $customerPhone = null
    ): array
    {
        $apiKey = self::getApiKey();

        if (empty($apiKey)) {
            return [
                'success' => false,
                'message' => 'API Key BorderPay belum dikonfigurasi. Silakan isi di Pengaturan Toko atau .env',
            ];
        }

        $intAmount = (int) round($amount);
        if ($intAmount < 1000) {
            return [
                'success' => false,
                'message' => 'Nominal transaksi QRIS minimal Rp1.000',
            ];
        }

        $refId = $referenceId ?? self::generateInvoiceNumber();

        $payload = [
            'amount' => $intAmount,
            'method' => 'qris',
            'reference_id' => $refId,
        ];

        if (!empty($returnUrl)) {
            $payload['return_url'] = $returnUrl;
        }

        try {
            $response = Http::withToken($apiKey)
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->timeout(config('borderpay.timeout', 15))
                ->post(self::getBaseUrl() . '/payments', $payload);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'reference_id' => $data['reference_id'] ?? $refId,
                    'status' => $data['status'] ?? 'pending',
                    'amount' => $data['amount'] ?? $intAmount,
                    'customer_pays' => $data['customer_pays'] ?? $intAmount,
                    'fee' => $data['fee'] ?? 0,
                    'pay_url' => $data['pay_url'] ?? null,
                    'qr_string' => $data['qr_string'] ?? '',
                    'expires_at' => $data['expires_at'] ?? null,
                    'raw' => $data,
                ];
            }

            $errorData = $response->json();
            $errorMessage = 'Gagal membuat pembayaran QRIS (' . $response->status() . ')';

            if (isset($errorData['message']) && is_string($errorData['message'])) {
                $errorMessage = $errorData['message'];
            } elseif (isset($errorData['error'])) {
                if (is_string($errorData['error'])) {
                    $errorMessage = $errorData['error'];
                } elseif (is_array($errorData['error'])) {
                    $errorMessage = $errorData['error']['message'] ?? $errorData['error']['code'] ?? json_encode($errorData['error']);
                }
            } elseif (isset($errorData['message']) && is_array($errorData['message'])) {
                $errorMessage = implode(', ', array_map(fn($v) => is_array($v) ? implode(', ', $v) : (string)$v, $errorData['message']));
            }

            Log::error('BorderPay createQrisPayment failed', [
                'status' => $response->status(),
                'payload' => $payload,
                'response' => $errorData,
            ]);

            return [
                'success' => false,
                'status_code' => $response->status(),
                'message' => (string) $errorMessage,
                'error_code' => is_array($errorData['error'] ?? null) ? ($errorData['error']['code'] ?? null) : ($errorData['code'] ?? null),
            ];
        } catch (\Throwable $e) {
            Log::error('BorderPay createQrisPayment exception: ' . $e->getMessage(), [
                'payload' => $payload,
            ]);

            return [
                'success' => false,
                'message' => 'Koneksi ke BorderPay gagal: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Cek status pembayaran terkini.
     * GET /api/v1/payments/{reference_id}
     */
    public static function checkPaymentStatus(string $referenceId): array
    {
        $apiKey = self::getApiKey();

        if (empty($apiKey)) {
            return [
                'success' => false,
                'message' => 'API Key BorderPay belum dikonfigurasi.',
            ];
        }

        try {
            $response = Http::withToken($apiKey)
                ->withHeaders([
                    'Accept' => 'application/json',
                ])
                ->timeout(config('borderpay.timeout', 10))
                ->get(self::getBaseUrl() . '/payments/' . urlencode($referenceId));

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'reference_id' => $data['reference_id'] ?? $referenceId,
                    'status' => strtolower($data['status'] ?? 'pending'),
                    'is_paid' => strtolower($data['status'] ?? '') === 'paid',
                    'amount' => $data['amount'] ?? 0,
                    'order_amount' => $data['order_amount'] ?? 0,
                    'customer_pays' => $data['customer_pays'] ?? 0,
                    'fee' => $data['fee'] ?? 0,
                    'paid_at' => $data['paid_at'] ?? null,
                    'raw' => $data,
                ];
            }

            return [
                'success' => false,
                'status_code' => $response->status(),
                'message' => 'Status pembayaran tidak ditemukan atau error server.',
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => 'Gagal memeriksa status: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Batalkan pembayaran QRIS yang sedang pending.
     * POST /api/v1/payments/{reference_id}/cancel
     */
    public static function cancelPayment(string $referenceId): array
    {
        $apiKey = self::getApiKey();

        if (empty($apiKey)) {
            return [
                'success' => false,
                'message' => 'API Key BorderPay belum dikonfigurasi.',
            ];
        }

        try {
            $response = Http::withToken($apiKey)
                ->withHeaders([
                    'Accept' => 'application/json',
                ])
                ->timeout(config('borderpay.timeout', 10))
                ->post(self::getBaseUrl() . '/payments/' . urlencode($referenceId) . '/cancel');

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'reference_id' => $data['reference_id'] ?? $referenceId,
                    'status' => $data['status'] ?? 'expired',
                ];
            }

            return [
                'success' => false,
                'message' => 'Pembatalan gagal (' . $response->status() . ')',
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => 'Gagal membatalkan transaksi: ' . $e->getMessage(),
            ];
        }
    }
}
