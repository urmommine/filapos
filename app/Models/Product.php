<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;
use App\Models\User;
use Illuminate\Support\HtmlString;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'sku',
        'barcode',
        'description',
        'purchase_price',
        'selling_price',
        'stock',
        'min_stock',
        'image',
        'is_active',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'stock' => 'integer',
        'min_stock' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Booted method untuk mendaftarkan event listener.
     */
    protected static function booted()
    {
        static::updated(function ($product) {
            // Hanya kirim notifikasi jika stok berubah dan menjadi menipis
            // Dan hanya kirim jika sebelumnya stok TIDAK menipis untuk menghindari spam
            if ($product->wasChanged('stock')) {
                $wasLow = $product->getOriginal('stock') <= $product->getOriginal('min_stock');
                $isLow = $product->stock <= $product->min_stock;

                if ($isLow && !$wasLow) {
                    $product->notifyLowStock();
                }
            }
        });
    }

    /**
     * Relasi ke kategori produk ini.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relasi ke item pesanan yang berisi produk ini.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Cek apakah stok menipis (di bawah atau sama dengan stok minimum).
     */
    public function isLowStock(): bool
    {
        return $this->stock <= $this->min_stock;
    }

    /**
     * Cek apakah stok produk habis.
     */
    public function isOutOfStock(): bool
    {
        return $this->stock <= 0;
    }

    /**
     * Hitung margin keuntungan.
     */
    public function getProfitAttribute(): float
    {
        return $this->selling_price - $this->purchase_price;
    }

    /**
     * Hitung persentase keuntungan.
     */
    public function getProfitPercentageAttribute(): float
    {
        if ($this->purchase_price <= 0) {
            return 0;
        }
        return (($this->selling_price - $this->purchase_price) / $this->purchase_price) * 100;
    }

    /**
     * Kurangi stok setelah penjualan.
     */
    public function reduceStock(int $quantity): bool
    {
        if ($this->stock >= $quantity) {
            $this->decrement('stock', $quantity);
            return true;
        }
        return false;
    }

    /**
     * Tambah stok (restock).
     */
    public function addStock(int $quantity): void
    {
        $this->increment('stock', $quantity);
    }

    /**
     * Scope untuk mendapatkan produk yang aktif saja.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope untuk mendapatkan produk dengan stok menipis.
     */
    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock', '<=', 'min_stock');
    }

    /**
     * Scope untuk mencari produk berdasarkan barcode atau SKU yang tepat.
     */
    public function scopeByCode($query, string $code)
    {
        return $query->where('barcode', $code)
            ->orWhere('sku', $code);
    }

    /**
     * Scope untuk mencari produk berdasarkan nama, SKU, atau barcode.
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('sku', 'like', "%{$search}%")
                ->orWhere('barcode', 'like', "%{$search}%");
        });
    }

    /**
     * Kirim notifikasi stok menipis ke semua admin.
     */
    public function notifyLowStock(): void
    {
        Notification::make()
            ->title('Peringatan: Stok Menipis')
            ->body(new HtmlString("Produk <b>{$this->name}</b> saat ini memiliki stok <b>{$this->stock}</b> (Batas minimum: {$this->min_stock})."))
            ->warning()
            ->icon('heroicon-o-exclamation-triangle')
            ->actions([
                Action::make('view')
                    ->label('Lihat Produk')
                    ->url(\App\Filament\Resources\ProductResource::getUrl('edit', ['record' => $this->id])),
            ])
            ->sendToDatabase(User::whereIn('role', ['admin', 'superadmin'])->get());
    }
}
