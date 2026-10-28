<?php

namespace App\Models;

use App\Models\Traits\PreventTamuModification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory, PreventTamuModification;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
    ];

    /**
     * Relasi ke pesanan yang dibuat oleh pelanggan ini.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
