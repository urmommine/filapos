<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WalletTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'reference_id',
        'order_id',
        'type',
        'amount',
        'opening_balance',
        'closing_balance',
        'status',
        'payment_method',
        'qris_qr_string',
        'qris_expires_at',
        'description',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'opening_balance' => 'decimal:2',
        'closing_balance' => 'decimal:2',
        'qris_expires_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function isDeposit(): bool
    {
        return $this->type === 'deposit';
    }

    public function isPayment(): bool
    {
        return $this->type === 'payment';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }
}
