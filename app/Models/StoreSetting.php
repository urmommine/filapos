<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class StoreSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * Ambil nilai pengaturan berdasarkan key.
     */
    public static function get(string $key, $default = null)
    {
        return Cache::remember("store_setting_{$key}", 3600, function () use ($key, $default) {
            $setting = self::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Simpan atau perbarui nilai pengaturan.
     */
    public static function set(string $key, $value): void
    {
        self::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );

        Cache::forget("store_setting_{$key}");
    }

    /**
     * Ambil semua pengaturan dalam bentuk pasangan key-value.
     */
    public static function getAllSettings(): array
    {
        return self::all()->pluck('value', 'key')->toArray();
    }

    /**
     * Key pengaturan default.
     */
    public const STORE_NAME = 'store_name';
    public const STORE_ADDRESS = 'store_address';
    public const STORE_PHONE = 'store_phone';
    public const STORE_EMAIL = 'store_email';
    public const TAX_PERCENTAGE = 'tax_percentage';
    public const PRINTER_NAME = 'printer_name';
    public const PRINTER_TYPE = 'printer_type'; // usb, jaringan, bluetooth
    public const PRINTER_IP = 'printer_ip';
    public const RECEIPT_FOOTER = 'receipt_footer';
}
