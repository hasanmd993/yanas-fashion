<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $todayStart = Carbon::today()->startOfDay();

        $statusCounts = Order::selectRaw('order_status, count(*) as count')
            ->groupBy('order_status')
            ->pluck('count', 'order_status')
            ->toArray();

        $stats = [
            'total_revenue' => (float) Order::where('order_status', '!=', 'cancelled')->sum('total_amount'),
            'today_sales' => (float) Order::where('created_at', '>=', $todayStart)->where('order_status', '!=', 'cancelled')->sum('total_amount'),
            'total_orders' => array_sum($statusCounts),
            'pending_orders' => (int) ($statusCounts['pending'] ?? 0),
            'processing_orders' => (int) ($statusCounts['processing'] ?? 0),
            'delivered_orders' => (int) ($statusCounts['delivered'] ?? 0),
            'total_products' => Product::count(),
            'low_stock_products' => Product::where('stock_qty', '<=', 5)->count(),
        ];

        $recentOrders = Order::with('items')->latest()->take(8)->get();
        $topProducts = Product::orderBy('reviews_count', 'desc')->take(5)->get();

        // Calculate dynamic 7-day sales and order trends in a single aggregation query
        $chartDays = [];
        $chartRevenue = [];
        $chartOrders = [];

        $trendStart = Carbon::today()->subDays(6)->startOfDay();
        $trendRecords = Order::where('created_at', '>=', $trendStart)
            ->where('order_status', '!=', 'cancelled')
            ->selectRaw('DATE(created_at) as order_date, SUM(total_amount) as revenue, COUNT(*) as orders_count')
            ->groupBy('order_date')
            ->get()
            ->keyBy(function ($row) {
                return Carbon::parse($row->order_date)->format('Y-m-d');
            });

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dateKey = $date->format('Y-m-d');
            $dayLabel = $i === 0 ? 'Today' : $date->format('D');
            $record = $trendRecords->get($dateKey);

            $chartDays[] = $dayLabel;
            $chartRevenue[] = (float) ($record?->revenue ?? 0);
            $chartOrders[] = (int) ($record?->orders_count ?? 0);
        }

        return \Inertia\Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'recentOrders' => $recentOrders,
            'topProducts' => $topProducts,
            'chartDays' => $chartDays,
            'chartRevenue' => $chartRevenue,
            'chartOrders' => $chartOrders,
        ]);
    }
}

