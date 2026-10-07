<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class PhaseOneSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Setting::create(['key' => 'site_name', 'value' => 'Yanas Fashion']);
    }

    public function test_unauthenticated_invoice_download_without_signature_or_session_is_forbidden(): void
    {
        $order = Order::create([
            'order_number' => 'YF-TEST999',
            'customer_name' => 'Test Customer',
            'customer_phone' => '01712345678',
            'customer_address' => 'Dhaka, Bangladesh',
            'delivery_zone' => 'inside_dhaka',
            'delivery_charge' => 70,
            'subtotal' => 1500,
            'discount' => 0,
            'total_amount' => 1570,
            'payment_method' => 'cod',
            'payment_status' => 'pending',
            'order_status' => 'pending',
        ]);

        // Unauthenticated random request should be 403 Forbidden
        $response = $this->get(route('order.invoice', $order->order_number));
        $response->assertStatus(403);
    }

    public function test_invoice_download_succeeds_with_valid_signed_url(): void
    {
        $order = Order::create([
            'order_number' => 'YF-SIGNED123',
            'customer_name' => 'Signed Customer',
            'customer_phone' => '01712345678',
            'customer_address' => 'Dhaka, Bangladesh',
            'delivery_zone' => 'inside_dhaka',
            'delivery_charge' => 70,
            'subtotal' => 1500,
            'discount' => 0,
            'total_amount' => 1570,
            'payment_method' => 'cod',
            'payment_status' => 'pending',
            'order_status' => 'pending',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_name' => 'Silk Panjabi',
            'unit_price' => 1500,
            'quantity' => 1,
            'total_price' => 1500,
        ]);

        $signedUrl = URL::temporarySignedRoute('order.invoice', now()->addHour(), ['order_number' => $order->order_number]);

        $response = $this->get($signedUrl);
        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('content-type'));
    }

    public function test_order_tracking_rejects_arbitrary_id_and_requires_exact_phone(): void
    {
        $order = Order::create([
            'order_number' => 'YF-TRACK123',
            'customer_name' => 'Track Customer',
            'customer_phone' => '01712345678',
            'customer_address' => 'Banani, Dhaka',
            'delivery_zone' => 'inside_dhaka',
            'delivery_charge' => 70,
            'subtotal' => 1000,
            'discount' => 0,
            'total_amount' => 1070,
            'payment_method' => 'cod',
            'payment_status' => 'pending',
            'order_status' => 'pending',
        ]);

        // Trying to query by internal ID should fail validation or return not found
        $response = $this->post(route('tracking.track'), [
            'order_number' => (string)$order->id,
            'phone' => '01712345678',
        ]);
        $response->assertSessionHas('error');

        // Trying with partial phone wildcard should fail validation (must be 11-digit regex)
        $response = $this->post(route('tracking.track'), [
            'order_number' => 'YF-TRACK123',
            'phone' => '01',
        ]);
        $response->assertSessionHasErrors('phone');

        // Valid order number and full exact phone matches successfully
        $response = $this->post(route('tracking.track'), [
            'order_number' => 'YF-TRACK123',
            'phone' => '01712345678',
        ]);
        $response->assertStatus(200);
    }

    public function test_order_item_accessors_return_product_name_and_total_price(): void
    {
        $item = new OrderItem([
            'product_name' => 'Royal Sherwani',
            'unit_price' => 3500,
            'quantity' => 2,
            'total_price' => 7000,
        ]);

        $this->assertEquals('Royal Sherwani', $item->product_title);
        $this->assertEquals(7000.0, $item->subtotal);
    }

    public function test_order_generates_unique_date_prefixed_order_number(): void
    {
        $order = Order::create([
            'customer_name' => 'Auto Number Customer',
            'customer_phone' => '01712345678',
            'customer_address' => 'Mirpur, Dhaka',
            'delivery_zone' => 'inside_dhaka',
            'delivery_charge' => 70,
            'subtotal' => 2000,
            'discount' => 0,
            'total_amount' => 2070,
            'payment_method' => 'cod',
            'payment_status' => 'pending',
            'order_status' => 'pending',
        ]);

        $this->assertNotNull($order->order_number);
        $this->assertStringStartsWith('YF-' . date('ymd'), $order->order_number);
    }
}
