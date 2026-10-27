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
        'invoice_number',
        'subtotal',
        'discount',
        'tax',
        'total_amount',
        'payment_method',
        'amount_paid',
        'change',
        'payment_status',
        'notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'change' => 'decimal:2',
    ];

    /**
     * Boot method untuk generate nomor invoice secara otomatis.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->invoice_number)) {
                $order->invoice_number = self::generateInvoiceNumber();
            }
        });
    }

    /**
     * Generate nomor invoice yang unik.
     * Format: INV-YYYYMMDD-XXXX
     */
    public static function generateInvoiceNumber(): string
    {
        $today = Carbon::today();
        $prefix = 'INV-' . $today->format('Ymd') . '-';

        // Get the last order of today
        $lastOrder = self::where('invoice_number', 'like', $prefix . '%')
            ->orderBy('invoice_number', 'desc')
            ->first();

        if ($lastOrder) {
            // Extract the number and increment
            $lastNumber = (int) substr($lastOrder->invoice_number, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
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
