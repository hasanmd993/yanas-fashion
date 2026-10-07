<?php

namespace App\Http\Middleware;

use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        // Compute cart total count from session
        $cart = session()->get('cart', []);
        $cartCount = is_array($cart) ? array_sum(array_column($cart, 'quantity')) : 0;

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user() ? [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'email' => $request->user()->email,
                    'phone' => $request->user()->phone ?? null,
                    'is_admin' => method_exists($request->user(), 'isAdmin') ? $request->user()->isAdmin() : false,
                ] : null,
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
                'warning' => fn () => $request->session()->get('warning'),
                'info' => fn () => $request->session()->get('info'),
            ],
            'settings' => fn () => [
                'site_name' => function_exists('get_setting') ? get_setting('site_name', 'Yanas Fashion') : 'Yanas Fashion',
                'site_logo' => function_exists('get_logo_url') ? get_logo_url() : asset('images/logo.png'),
                'site_favicon' => function_exists('get_favicon_url') ? get_favicon_url() : asset('favicon.ico'),
                'hotline' => function_exists('get_setting') ? get_setting('hotline', '01713-580400') : '01713-580400',
                'whatsapp' => function_exists('get_setting') ? get_setting('whatsapp_number', '8801713580400') : '8801713580400',
                'email' => function_exists('get_setting') ? get_setting('email', 'support@yanasfashion.com') : 'support@yanasfashion.com',
                'address' => function_exists('get_setting') ? get_setting('address', 'House 42, Road 11, Banani, Dhaka') : 'House 42, Road 11, Banani, Dhaka',
                'inside_dhaka_charge' => function_exists('get_setting') ? (float) get_setting('inside_dhaka_charge', 70) : 70,
                'suburbs_charge' => function_exists('get_setting') ? (float) get_setting('suburbs_charge', 100) : 100,
                'outside_dhaka_charge' => function_exists('get_setting') ? (float) get_setting('outside_dhaka_charge', 130) : 130,
                'free_shipping_threshold' => function_exists('get_setting') ? (float) get_setting('free_shipping_threshold', 3000) : 3000,
            ],
            'navigation_categories' => fn () => Category::query()
                ->whereNull('parent_id')
                ->where('is_active', true)
                ->with(['activeChildren' => function ($q) {
                    $q->select('id', 'parent_id', 'name', 'slug', 'image', 'icon')->orderBy('sort_order');
                }])
                ->orderBy('sort_order')
                ->select('id', 'name', 'slug', 'image', 'icon', 'is_featured')
                ->get(),
            'cart_count' => $cartCount,
            'cart' => fn () => $cart,
            'admin_notifications' => function () use ($request) {
                if (!$request->user()) {
                    return [];
                }

                return \Illuminate\Support\Facades\Cache::remember('admin_quick_notifications', 30, function () {
                    $notifications = [];

                    // 1. Pending Orders Notifications
                    $pendingOrders = \App\Models\Order::where('order_status', 'pending')
                        ->latest()
                        ->take(5)
                        ->get();

                    foreach ($pendingOrders as $order) {
                        $notifications[] = [
                            'id' => 'order-' . $order->id,
                            'type' => 'order',
                            'title' => 'New Order #' . $order->order_number,
                            'description' => 'Placed by ' . ($order->customer_name ?: 'Customer') . ' (৳' . number_format($order->total_amount) . ')',
                            'time' => $order->created_at ? $order->created_at->diffForHumans(null, true) . ' ago' : 'Recent',
                            'isRead' => false,
                            'route' => 'admin.orders.index',
                        ];
                    }

                    // 2. Low Stock Products Notifications
                    $lowStock = \App\Models\Product::where('stock_qty', '<=', 5)
                        ->take(3)
                        ->get();

                    foreach ($lowStock as $prod) {
                        $notifications[] = [
                            'id' => 'stock-' . $prod->id,
                            'type' => 'stock',
                            'title' => 'Low Stock Warning',
                            'description' => $prod->title . ' has only ' . $prod->stock_qty . ' items left',
                            'time' => 'Inventory alert',
                            'isRead' => false,
                            'route' => 'admin.products.index',
                        ];
                    }

                    return $notifications;
                });
            },
        ];
    }
}
