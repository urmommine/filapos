<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PeakHoursWidget extends ChartWidget
{
    protected static ?string $heading = 'Jam Ramai Transaksi';

    protected static ?int $sort = 7;

    public static function canView(): bool
    {
        return false;
    }

    protected int|string|array $columnSpan = 'half';

    protected function getData(): array
    {
        // Optimized query to get count per hour for the last 30 days
        $data = Order::paid()
            ->whereDate('created_at', '>=', Carbon::now()->subDays(30))
            ->selectRaw('HOUR(created_at) as hour, COUNT(*) as count')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->pluck('count', 'hour');

        $labels = [];
        $chartData = [];

        for ($i = 0; $i < 24; $i++) {
            $labels[] = str_pad($i, 2, '0', STR_PAD_LEFT) . ':00';
            $chartData[] = $data->get($i) ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Transaksi',
                    'data' => $chartData,
                    'backgroundColor' => 'rgba(132, 204, 22, 0.5)',
                    'borderColor' => '#84cc16',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
