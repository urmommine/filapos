<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'customer_id',
        'channel',
        'invoice_number',
        'subtotal',
        'discount',
        'tax',
        'total_amount',
        'payment_method',
        'amount_paid',
        'change',
        'payment_status',
        'qris_reference_id',
        'qris_qr_string',
        'qris_expires_at',
        'notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'change' => 'decimal:2',
        'qris_expires_at' => 'datetime',
    ];

    /**
     * Boot method untuk generate nomor invoice secara otomatis.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->invoice_number)) {
                $order->invoice_number = self::generateInvoiceNumber($order->payment_method ?? 'cash');
            }
        });
    }

    /**
     * Generate nomor invoice.
     * Format BORDER-... hanya untuk pembayaran QRIS.
     * Untuk selain QRIS (Cash, Saldo Akun, Transfer), gunakan format invoice lama: INV-YYYYMMDD-XXXX.
     */
    public static function generateInvoiceNumber(?string $paymentMethod = 'cash'): string
    {
        if ($paymentMethod === 'qris') {
            $milliseconds = (string) round(microtime(true) * 1000);
            $random = str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);

            return 'BORDER-' . $milliseconds . '-' . $random;
        }

        // Format invoice lama POS: INV-YYYYMMDD-XXXX
        $dateStr = now()->format('Ymd');
        $random = str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT);

        return 'INV-' . $dateStr . '-' . $random;
    }

    /**
     * Relasi ke user (kasir) yang memproses pesanan ini.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Alias untuk relasi user (kasir).
     */
    public function kasir(): BelongsTo
    {
        return $this->user();
    }

    /**
     * Relasi ke pelanggan untuk pesanan ini.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Relasi ke item pesanan.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Cek apakah pesanan sudah dibayar.
     */
    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    /**
     * Tandai pesanan sebagai sudah dibayar.
     */
    public function markAsPaid(): void
    {
        $this->update(['payment_status' => 'paid']);
    }

    /**
     * Hitung total keuntungan dari pesanan ini.
     */
    public function calculateProfit(): float
    {
        return $this->items->sum(function ($item) {
            $product = $item->product;
            if ($product) {
                return ($item->unit_price - $product->purchase_price) * $item->quantity;
            }
            return 0;
        });
    }

    /**
     * Scope untuk mendapatkan pesanan hari ini.
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', Carbon::today());
    }

    /**
     * Scope untuk mendapatkan pesanan minggu ini.
     */
    public function scopeThisWeek($query)
    {
        return $query->whereBetween('created_at', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ]);
    }

    /**
     * Scope untuk mendapatkan pesanan bulan ini.
     */
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year);
    }

    /**
     * Scope untuk mendapatkan pesanan yang sudah dibayar saja.
     */
    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }
}
