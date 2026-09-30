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
        $today = Carbon::today();

        $stats = [
            'total_revenue' => Order::where('order_status', '!=', 'cancelled')->sum('total_amount'),
            'today_sales' => Order::whereDate('created_at', $today)->where('order_status', '!=', 'cancelled')->sum('total_amount'),
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('order_status', 'pending')->count(),
            'processing_orders' => Order::where('order_status', 'processing')->count(),
            'delivered_orders' => Order::where('order_status', 'delivered')->count(),
            'total_products' => Product::count(),
            'low_stock_products' => Product::where('stock_qty', '<=', 5)->count(),
        ];

        $recentOrders = Order::with('items')->latest()->take(8)->get();
        $topProducts = Product::orderBy('reviews_count', 'desc')->take(5)->get();

        // Calculate dynamic 7-day sales and order trends
        $chartDays = [];
        $chartRevenue = [];
        $chartOrders = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dayLabel = $i === 0 ? 'Today' : $date->format('D');
            $dayRevenue = Order::whereDate('created_at', $date)
                ->where('order_status', '!=', 'cancelled')
                ->sum('total_amount');
            $dayOrdersCount = Order::whereDate('created_at', $date)
                ->where('order_status', '!=', 'cancelled')
                ->count();

            $chartDays[] = $dayLabel;
            $chartRevenue[] = (float) $dayRevenue;
            $chartOrders[] = (int) $dayOrdersCount;
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

