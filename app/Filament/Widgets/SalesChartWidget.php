<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SalesChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Grafik Penjualan & Laba';

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';
    protected static ?string $maxHeight = '300px';

    public ?string $filter = '7';

    protected function getFilters(): ?array
    {
        return [
            '7' => '7 Hari Terakhir',
            '30' => '30 Hari Terakhir',
            '90' => '90 Hari Terakhir',
        ];
    }

    protected function getData(): array
    {
        $activeFilter = $this->filter;
        $days = intval($activeFilter);

        $labels = [];
        $revenueData = [];
        $profitData = [];

        // 1. Fetch Revenue Data in one query
        $revenueQuery = Order::whereDate('created_at', '>=', Carbon::today()->subDays($days - 1))
            ->paid()
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as total')
            ->groupBy('date')
            ->get()
            ->pluck('total', 'date');

        // 2. Fetch Profit Data in one query
        $profitQuery = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->whereDate('orders.created_at', '>=', Carbon::today()->subDays($days - 1))
            ->where('orders.payment_status', 'paid')
            ->selectRaw('DATE(orders.created_at) as date, SUM((order_items.unit_price - products.purchase_price) * order_items.quantity) as total_profit')
            ->groupBy('date')
            ->get()
            ->pluck('total_profit', 'date');

        // Fill in the gaps
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i)->format('Y-m-d');
            $labels[] = Carbon::parse($date)->format('d M');

            $revenueData[] = $revenueQuery->get($date) ?? 0;
            $profitData[] = $profitQuery->get($date) ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Penjualan (Rp)',
                    'data' => $revenueData,
                    'fill' => 'start',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'tension' => 0.3,
                ],
                [
                    'label' => 'Laba Estimasi (Rp)',
                    'data' => $profitData,
                    'fill' => 'start',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'borderColor' => 'rgb(16, 185, 129)',
                    'tension' => 0.3,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'callback' => "function(value) { 
                            if (value >= 1000000) return 'Rp ' + (value / 1000000).toFixed(1) + 'jt';
                            if (value >= 1000) return 'Rp ' + (value / 1000).toFixed(0) + 'rb';
                            return 'Rp ' + value; 
                        }",
                    ],
                ],
            ],
        ];
    }
}
