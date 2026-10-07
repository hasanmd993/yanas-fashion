<?php

namespace Tests\Feature;

use App\Jobs\SendOrderSmsJob;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use App\Models\Setting;
use App\Models\Slider;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class PhaseThreeArchitectureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Setting::create(['key' => 'site_name', 'value' => 'Yanas Fashion']);
    }

    public function test_checkout_dispatches_queued_sms_job(): void
    {
        Queue::fake();

        $category = Category::create([
            'name' => 'Women Silk',
            'slug' => 'women-silk',
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'title' => 'Silk Saree Blue',
            'slug' => 'silk-saree-blue',
            'sku' => 'SLK-001',
            'regular_price' => 2500,
            'sale_price' => 2200,
            'stock_qty' => 15,
            'thumbnail' => 'saree.jpg',
        ]);

        $cart = [
            $product->id => [
                'id' => $product->id,
                'title' => $product->title,
                'price' => 2200,
                'quantity' => 1,
                'image' => 'saree.jpg',
            ]
        ];

        $response = $this->withSession(['cart' => $cart])
            ->post('/checkout', [
                'customer_name' => 'Ayesha Rahman',
                'customer_phone' => '01712345678',
                'customer_email' => 'ayesha@example.com',
                'customer_address' => 'House 5, Road 2, Dhanmondi, Dhaka',
                'delivery_zone' => 'inside_dhaka',
                'payment_method' => 'cod',
            ]);

        $response->assertStatus(302);

        Queue::assertPushed(SendOrderSmsJob::class, function ($job) {
            return $job->event === 'order_placed'
                && $job->order->customer_phone === '01712345678';
        });
    }

    public function test_order_shipped_dispatches_queued_sms_job(): void
    {
        Queue::fake();

        $admin = User::factory()->create();

        $order = Order::create([
            'order_number' => 'ORD-TEST-001',
            'customer_name' => 'Karim Ali',
            'customer_phone' => '01812345678',
            'customer_address' => 'Chittagong GEC',
            'delivery_zone' => 'outside_dhaka',
            'payment_method' => 'cod',
            'order_status' => 'pending',
            'subtotal' => 1500,
            'delivery_charge' => 130,
            'total_amount' => 1630,
        ]);

        $response = $this->actingAs($admin)
            ->post("/admin/orders/{$order->id}/status", [
                'order_status' => 'shipped',
                'payment_status' => 'pending',
                'admin_notes' => 'Dispatched via courier',
            ]);

        $response->assertStatus(302);

        Queue::assertPushed(SendOrderSmsJob::class, function ($job) use ($order) {
            return $job->event === 'order_shipped'
                && $job->order->id === $order->id;
        });
    }

    public function test_admin_legacy_create_and_edit_redirect_to_index(): void
    {
        $admin = User::factory()->create();

        $cat = Category::create([
            'name' => 'Test Cat',
            'slug' => 'test-cat',
        ]);

        $slider = Slider::create([
            'title' => 'Test Slide',
            'image' => 'assets/hero.jpg',
        ]);

        $coupon = Coupon::create([
            'code' => 'TESTCODE',
            'type' => 'fixed',
            'value' => 50,
        ]);

        // Categories
        $this->actingAs($admin)->get('/admin/categories/create')
            ->assertRedirect(route('admin.categories.index'));
        $this->actingAs($admin)->get("/admin/categories/{$cat->id}/edit")
            ->assertRedirect(route('admin.categories.index'));

        // Sliders
        $this->actingAs($admin)->get('/admin/sliders/create')
            ->assertRedirect(route('admin.sliders.index'));
        $this->actingAs($admin)->get("/admin/sliders/{$slider->id}/edit")
            ->assertRedirect(route('admin.sliders.index'));

        // Coupons
        $this->actingAs($admin)->get('/admin/coupons/create')
            ->assertRedirect(route('admin.coupons.index'));
        $this->actingAs($admin)->get("/admin/coupons/{$coupon->id}/edit")
            ->assertRedirect(route('admin.coupons.index'));
    }

    public function test_settings_whitelisting_and_ssrf_protection(): void
    {
        $admin = User::factory()->create();

        // 1. Unwhitelisted keys must be ignored
        $response = $this->actingAs($admin)->post('/admin/settings', [
            'site_name' => 'Whitelisted Store',
            'inside_dhaka_charge' => 80,
            'unauthorized_backdoor_key' => 'evil_value',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertEquals('Whitelisted Store', Setting::get('site_name'));
        $this->assertEquals(80, Setting::get('inside_dhaka_charge'));
        $this->assertNull(Setting::get('unauthorized_backdoor_key'));

        // 2. Loopback / Internal IP in sms_generic_url must fail validation (SSRF defense)
        $responseSsrf = $this->actingAs($admin)->post('/admin/settings', [
            'sms_generic_url' => 'http://127.0.0.1:8000/internal-api',
        ]);

        $responseSsrf->assertSessionHasErrors('sms_generic_url');

        $responseSsrfHost = $this->actingAs($admin)->post('/admin/settings', [
            'sms_generic_url' => 'http://localhost/admin/metadata',
        ]);

        $responseSsrfHost->assertSessionHasErrors('sms_generic_url');
    }

    public function test_product_review_sanitizes_html_and_computes_verified_buyer(): void
    {
        $cat = Category::create([
            'name' => 'Accessories',
            'slug' => 'accessories',
        ]);

        $product = Product::create([
            'category_id' => $cat->id,
            'title' => 'Leather Bag',
            'slug' => 'leather-bag',
            'sku' => 'BAG-001',
            'regular_price' => 1500,
            'stock_qty' => 10,
            'thumbnail' => 'bag.jpg',
        ]);

        // Reviewer who has NOT purchased the product
        $this->post("/product/{$product->id}/review", [
            'name' => '<b>Hacker</b>',
            'email' => 'stranger@example.com',
            'rating' => 5,
            'comment' => '<script>alert("xss")</script>Loved this bag!',
        ])->assertStatus(302);

        $unverifiedReview = Review::where('product_id', $product->id)->where('email', 'stranger@example.com')->first();
        $this->assertNotNull($unverifiedReview);
        $this->assertEquals('Hacker', $unverifiedReview->name);
        $this->assertEquals('Loved this bag!', $unverifiedReview->comment);
        $this->assertFalse((bool) $unverifiedReview->is_verified_buyer);

        // Now create a delivered order for buyer@example.com containing this product
        $order = Order::create([
            'order_number' => 'ORD-BUYER-001',
            'customer_name' => 'Genuine Buyer',
            'customer_phone' => '01799887766',
            'customer_email' => 'buyer@example.com',
            'customer_address' => 'Gulshan 2, Dhaka',
            'delivery_zone' => 'inside_dhaka',
            'payment_method' => 'cod',
            'order_status' => 'delivered',
            'subtotal' => 1500,
            'delivery_charge' => 70,
            'total_amount' => 1570,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->title,
            'unit_price' => 1500,
            'quantity' => 1,
            'total_price' => 1500,
        ]);

        // Genuine buyer posts a review
        $this->post("/product/{$product->id}/review", [
            'name' => 'Genuine Buyer',
            'email' => 'buyer@example.com',
            'rating' => 5,
            'comment' => 'Received yesterday, top notch leather quality.',
        ])->assertStatus(302);

        $verifiedReview = Review::where('product_id', $product->id)->where('email', 'buyer@example.com')->first();
        $this->assertNotNull($verifiedReview);
        $this->assertTrue((bool) $verifiedReview->is_verified_buyer);
    }
}
