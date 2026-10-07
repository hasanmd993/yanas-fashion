<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class PhaseTwoPerformanceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Setting::create(['key' => 'site_name', 'value' => 'Yanas Fashion']);
    }

    public function test_setting_cache_layer_works_and_invalidates_on_update(): void
    {
        Setting::set('test_cached_key', 'initial_value');
        $this->assertEquals('initial_value', Setting::get('test_cached_key'));

        // Direct DB update bypassing Eloquent won't be visible because cache holds it
        \Illuminate\Support\Facades\DB::table('settings')
            ->where('key', 'test_cached_key')
            ->update(['value' => 'db_only_value']);

        $this->assertEquals('initial_value', Setting::get('test_cached_key'));

        // Setting::set flushes cache
        Setting::set('test_cached_key', 'updated_via_model');
        $this->assertEquals('updated_via_model', Setting::get('test_cached_key'));
    }

    public function test_cart_sync_validates_and_batch_hydrates_products(): void
    {
        $cat = Category::create([
            'name' => 'Men Ethnic',
            'slug' => 'men-ethnic',
        ]);

        $p1 = Product::create([
            'category_id' => $cat->id,
            'title' => 'Panjabi Black',
            'slug' => 'panjabi-black',
            'sku' => 'SKU-001',
            'regular_price' => 2000,
            'sale_price' => 1800,
            'stock_qty' => 10,
            'thumbnail' => 'panjabi.jpg',
        ]);

        $p2 = Product::create([
            'category_id' => $cat->id,
            'title' => 'Panjabi White',
            'slug' => 'panjabi-white',
            'sku' => 'SKU-002',
            'regular_price' => 2500,
            'sale_price' => 2200,
            'stock_qty' => 10,
            'thumbnail' => 'panjabi-white.jpg',
        ]);

        $response = $this->postJson(route('cart.sync'), [
            'items' => [
                ['id' => $p1->id, 'quantity' => 2, 'size' => 'L'],
                ['id' => $p2->id, 'quantity' => 1, 'size' => 'XL'],
            ],
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('count', 2);
        $response->assertJsonPath('items.0.price', 1800);
        $response->assertJsonPath('items.1.price', 2200);
    }

    public function test_checkout_index_batch_hydrates_cart_prices(): void
    {
        $cat = Category::create([
            'name' => 'Accessories',
            'slug' => 'accessories',
        ]);

        $product = Product::create([
            'category_id' => $cat->id,
            'title' => 'Silk Scarf',
            'slug' => 'silk-scarf',
            'sku' => 'SKU-SCARF',
            'regular_price' => 1200,
            'sale_price' => 999,
            'stock_qty' => 5,
            'thumbnail' => 'scarf.jpg',
        ]);

        $response = $this->withSession([
            'cart' => [
                'item_1' => [
                    'product_id' => $product->id,
                    'price' => 500, // outdated session price
                    'quantity' => 2,
                ],
            ],
        ])->get(route('checkout.index'));

        $response->assertStatus(200);
        // Subtotal should be 999 * 2 = 1998
        $response->assertInertia(fn ($page) => $page->component('Checkout/Index')->where('subtotal', 1998));
    }

    public function test_dashboard_aggregates_stats_and_trends_efficiently(): void
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
        ]);

        Order::create([
            'order_number' => 'YF-DASH1',
            'customer_name' => 'Customer A',
            'customer_phone' => '01700000001',
            'customer_address' => 'Dhaka',
            'delivery_zone' => 'inside_dhaka',
            'delivery_charge' => 70,
            'subtotal' => 2000,
            'discount' => 0,
            'total_amount' => 2070,
            'payment_method' => 'cod',
            'payment_status' => 'pending',
            'order_status' => 'pending',
            'created_at' => Carbon::today(),
        ]);

        Order::create([
            'order_number' => 'YF-DASH2',
            'customer_name' => 'Customer B',
            'customer_phone' => '01700000002',
            'customer_address' => 'Dhaka',
            'delivery_zone' => 'inside_dhaka',
            'delivery_charge' => 70,
            'subtotal' => 3000,
            'discount' => 0,
            'total_amount' => 3070,
            'payment_method' => 'cod',
            'payment_status' => 'paid',
            'order_status' => 'delivered',
            'created_at' => Carbon::today(),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));
        $response->assertStatus(200);

        $response->assertInertia(fn ($page) => $page
            ->component('Admin/Dashboard')
            ->has('stats.total_orders')
            ->where('stats.total_orders', 2)
            ->where('stats.pending_orders', 1)
            ->where('stats.delivered_orders', 1)
            ->has('chartDays', 7)
            ->has('chartRevenue', 7)
            ->has('chartOrders', 7)
        );
    }
}
