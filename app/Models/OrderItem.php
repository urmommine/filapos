<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'quantity',
        'unit_price',
        'total_price',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    /**
     * Relasi ke pesanan induk.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Relasi ke produk (bisa null jika produk dihapus).
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Hitung total harga secara otomatis sebelum disimpan.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($item) {
            $item->total_price = $item->unit_price * $item->quantity;
        });

        static::updating(function ($item) {
            $item->total_price = $item->unit_price * $item->quantity;
        });
    }
}
