<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LowPerformingProductsWidget extends BaseWidget
{
    protected static ?string $heading = 'Produk Kurang Laku (30 Hari)';

    protected static ?int $sort = 8;

    protected int|string|array $columnSpan = 'half';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Product::query()
                    ->with('category')
                    ->active()
                    ->whereNotExists(function ($query) {
                        $query->select(DB::raw(1))
                            ->from('order_items')
                            ->join('orders', 'order_items.order_id', '=', 'orders.id')
                            ->whereColumn('order_items.product_id', 'products.id')
                            ->where('orders.payment_status', 'paid')
                            ->where('orders.created_at', '>=', Carbon::now()->subDays(30));
                    })
                    ->orderBy('stock', 'desc')
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Produk'),
                Tables\Columns\TextColumn::make('stock')
                    ->label('Stok Saat Ini')
                    ->numeric()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Kategori')
                    ->badge(),
            ])
            ->paginated(false)
            ->emptyStateHeading('Semua produk laku terjual!');
    }
}
