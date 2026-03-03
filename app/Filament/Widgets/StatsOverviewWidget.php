<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // Optimized Data Fetching for Today and Yesterday
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();


        // 1. Revenue & Transactions (Today vs Yesterday)
        $todayData = Order::whereDate('created_at', $today)
            ->paid()
            ->selectRaw('SUM(total_amount) as revenue, COUNT(*) as count')
            ->first();

        $yesterdayData = Order::whereDate('created_at', $yesterday)
            ->paid()
            ->selectRaw('SUM(total_amount) as revenue, COUNT(*) as count')
            ->first();

        $todaySales = $todayData->revenue ?? 0;
        $todayTransactions = $todayData->count ?? 0;

        $yesterdaySales = $yesterdayData->revenue ?? 0;
        $yesterdayTransactions = $yesterdayData->count ?? 0;

        $salesChange = $yesterdaySales > 0
            ? (($todaySales - $yesterdaySales) / $yesterdaySales) * 100
            : ($todaySales > 0 ? 100 : 0);

        // 2. Optimized Profit Calculation
        $calculateProfit = function ($date) {
            return DB::table('order_items')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->whereDate('orders.created_at', $date)
                ->where('orders.payment_status', 'paid')
                ->selectRaw('SUM((order_items.unit_price - products.purchase_price) * order_items.quantity) as total_profit')
                ->first()
                ->total_profit ?? 0;
        };

        $todayProfit = $calculateProfit($today);
        $yesterdayProfit = $calculateProfit($yesterday);

        $profitChange = $yesterdayProfit > 0
            ? (($todayProfit - $yesterdayProfit) / $yesterdayProfit) * 100
            : ($todayProfit > 0 ? 100 : 0);

        // 3. AOV (Average Order Value)
        // $todayAOV = $todayTransactions > 0 ? $todaySales / $todayTransactions : 0;
        // $yesterdayAOV = $yesterdayTransactions > 0 ? $yesterdaySales / $yesterdayTransactions : 0;
        // $aovChange = $yesterdayAOV > 0
        //     ? (($todayAOV - $yesterdayAOV) / $yesterdayAOV) * 100
        //     : ($todayAOV > 0 ? 100 : 0);

        // 4. Margin %
        $todayMargin = $todaySales > 0 ? ($todayProfit / $todaySales) * 100 : 0;

        // 5. Product Stats
        $activeProducts = Product::active()->count();
        $lowStockCount = Product::active()->lowStock()->count();
        $totalRevenue = Order::paid()->sum('total_amount');

        return [
            Stat::make('Penjualan Hari Ini', 'Rp ' . number_format($todaySales, 0, ',', '.'))
                ->description($salesChange >= 0 ? '+' . number_format($salesChange, 1) . '%' : number_format($salesChange, 1) . '%')
                ->descriptionIcon($salesChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($salesChange >= 0 ? 'success' : 'danger')
                ->chart([7, 3, 4, 5, 6, 3, 5, 8]),

            Stat::make('Estimasi Laba Hari Ini', 'Rp ' . number_format($todayProfit, 0, ',', '.'))
                ->description($profitChange >= 0 ? '+' . number_format($profitChange, 1) . '%' : number_format($profitChange, 1) . '%')
                ->descriptionIcon($profitChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($profitChange >= 0 ? 'success' : 'danger'),

            Stat::make('Total Omset', 'Rp ' . number_format($totalRevenue, 0, ',', '.'))
                ->description('Dari awal transaksi')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('primary'),

            Stat::make('Produk Aktif', $activeProducts)
                ->description('Produk yang tersedia')
                ->descriptionIcon('heroicon-m-archive-box')
                ->color('success'),

            // Stat::make('Rata-rata Pesanan (AOV)', 'Rp ' . number_format($todayAOV, 0, ',', '.'))
            //     ->description($aovChange >= 0 ? '+' . number_format($aovChange, 1) . '%' : number_format($aovChange, 1) . '%')
            //     ->descriptionIcon('heroicon-m-calculator')
            //     ->color($aovChange >= 0 ? 'success' : 'warning'),

            Stat::make('Transaksi Hari Ini', $todayTransactions)
                ->description('Kemarin: ' . $yesterdayTransactions)
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('info'),

            Stat::make('Stok Menipis', $lowStockCount)
                ->description($lowStockCount > 0 ? 'Perlu restock!' : 'Semua stok aman')
                ->descriptionIcon($lowStockCount > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-circle')
                ->color($lowStockCount > 0 ? 'warning' : 'success')
                ->url($lowStockCount > 0 ? route('filament.admin.resources.products.index', ['tableFilters[low_stock][value]' => 1]) : null),
        ];
    }
}
