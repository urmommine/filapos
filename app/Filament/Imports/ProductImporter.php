<?php

namespace App\Filament\Imports;

use App\Models\Category;
use App\Models\Product;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class ProductImporter extends Importer
{
    protected static ?string $model = Product::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->label('Nama Produk')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('sku')
                ->label('SKU')
                ->requiredMapping()
                ->rules(['required', 'max:50']),
            ImportColumn::make('barcode')
                ->label('Barcode')
                ->rules(['nullable', 'max:50']),
            ImportColumn::make('category')
                ->label('Kategori')
                ->relationship(resolveUsing: function (string $state): ?Category {
                    return Category::query()
                        ->where('name', $state)
                        ->first() ?? Category::create([
                                    'name' => $state,
                                    'is_active' => true,
                                ]);
                }),
            ImportColumn::make('description')
                ->label('Deskripsi')
                ->rules(['nullable']),
            ImportColumn::make('purchase_price')
                ->label('Harga Beli')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'numeric', 'min:0']),
            ImportColumn::make('selling_price')
                ->label('Harga Jual')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'numeric', 'min:0']),
            ImportColumn::make('stock')
                ->label('Stok')
                ->numeric()
                ->rules(['nullable', 'integer', 'min:0']),
            ImportColumn::make('min_stock')
                ->label('Stok Minimum')
                ->numeric()
                ->rules(['nullable', 'integer', 'min:0']),
            ImportColumn::make('is_active')
                ->label('Status Aktif')
                ->boolean()
                ->rules(['nullable', 'boolean']),
        ];
    }

    public function resolveRecord(): ?Product
    {
        // Update existing product by SKU, or create new
        return Product::firstOrNew([
            'sku' => $this->data['sku'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Import produk selesai. ' . number_format($import->successful_rows) . ' baris berhasil diimport.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' baris gagal.';
        }

        return $body;
    }
}
