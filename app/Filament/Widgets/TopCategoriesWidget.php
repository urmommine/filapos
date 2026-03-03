<?php

namespace App\Filament\Widgets;

use App\Models\OrderItem;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class TopCategoriesWidget extends ChartWidget
{
    protected static ?string $heading = 'Performa per Kategori';

    protected static ?int $sort = 6;

    public static function canView(): bool
    {
        return false;
    }

    protected int|string|array $columnSpan = 'half';

    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $data = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('categories.name', DB::raw('SUM(order_items.total_price) as total'))
            ->groupBy('categories.name')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Pendapatan',
                    'data' => $data->pluck('total')->toArray(),
                    'backgroundColor' => [
                        '#84cc16', // Lime 500
                        '#10b981', // Emerald 500
                        '#06b6d4', // Cyan 500
                        '#3b82f6', // Blue 500
                        '#f59e0b', // Amber 500
                    ],
                ],
            ],
            'labels' => $data->pluck('name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
