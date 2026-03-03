<?php

namespace App\Filament\Widgets;

use App\Models\OrderItem;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\DB;

class BestSellersWidget extends BaseWidget
{
    protected static ?string $heading = '5 Produk Terlaris';

    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 'half';

    protected static ?string $maxHeight = '300px';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                OrderItem::query()
                    ->select(
                        'product_id as id',
                        'product_id',
                        'product_name',
                        DB::raw('SUM(quantity) as total_qty'),
                        DB::raw('SUM(total_price) as total_revenue')
                    )
                    ->groupBy('product_id', 'product_name')
                    ->orderByDesc('total_qty')
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('product_name')
                    ->label('Produk'),
                Tables\Columns\TextColumn::make('total_qty')
                    ->label('Terjual')
                    ->numeric()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('total_revenue')
                    ->label('Pendapatan')
                    ->money('IDR')
                    ->badge()
                    ->color('success'),
            ])
            ->paginated(false);
    }
}
