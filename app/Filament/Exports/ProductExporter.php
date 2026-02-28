<?php

namespace App\Filament\Exports;

use App\Models\Product;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ProductExporter extends Exporter
{
    protected static ?string $model = Product::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name')
                ->label('Nama Produk'),
            ExportColumn::make('sku')
                ->label('SKU'),
            ExportColumn::make('barcode')
                ->label('Barcode'),
            ExportColumn::make('category.name')
                ->label('Kategori'),
            ExportColumn::make('description')
                ->label('Deskripsi'),
            ExportColumn::make('purchase_price')
                ->label('Harga Beli'),
            ExportColumn::make('selling_price')
                ->label('Harga Jual'),
            ExportColumn::make('stock')
                ->label('Stok'),
            ExportColumn::make('min_stock')
                ->label('Stok Minimum'),
            ExportColumn::make('is_active')
                ->label('Status Aktif')
                ->formatStateUsing(fn(bool $state): string => $state ? 'Ya' : 'Tidak'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Export produk selesai. ' . number_format($export->successful_rows) . ' baris berhasil diexport.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' baris gagal.';
        }

        return $body;
    }
}
