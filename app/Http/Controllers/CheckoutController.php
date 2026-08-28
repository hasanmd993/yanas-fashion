<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        // Direct Buy-Now single product support or full cart
        $cart = session()->get('cart', []);

        // If direct product checkout requested
        if ($request->filled('buy_now')) {
            $product = Product::findOrFail($request->buy_now);
            $size = $request->size ?? ($product->sizes ? $product->sizes[0] : null);
            $cart = [
                'direct' => [
                    'key' => 'direct',
                    'product_id' => $product->id,
                    'title' => $product->title,
                    'title_bn' => $product->title_bn,
                    'slug' => $product->slug,
                    'price' => (float) ($product->sale_price ?? $product->regular_price),
                    'regular_price' => (float) $product->regular_price,
                    'thumbnail' => $product->thumbnail,
                    'size' => $size,
                    'quantity' => (int) ($request->quantity ?? 1),
                ]
            ];
        }

        if (empty($cart)) {
            return redirect()->route('shop.index')->with('warning', 'আপনার কার্ট খালি! অনুগ্রহ করে আগে পণ্য নির্বাচন করুন।');
        }

        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        $insideDhaka = (float) Setting::get('inside_dhaka_charge', 70);
        $suburbs = (float) Setting::get('suburbs_charge', 100);
        $outsideDhaka = (float) Setting::get('outside_dhaka_charge', 130);
        $freeShippingThreshold = (float) Setting::get('free_shipping_threshold', 3000);

        $appliedCoupon = session()->get('coupon');
        $discount = 0;
        if ($appliedCoupon) {
            $couponModel = Coupon::where('code', $appliedCoupon['code'])->first();
            if ($couponModel && $couponModel->isValidFor($subtotal)) {
                $discount = $couponModel->calculateDiscount($subtotal);
            } else {
                session()->forget('coupon');
                $appliedCoupon = null;
            }
        }

        return view('checkout.index', compact(
            'cart',
            'subtotal',
            'insideDhaka',
            'suburbs',
            'outsideDhaka',
            'freeShippingThreshold',
            'appliedCoupon',
            'discount'
        ));
    }

    public function applyCoupon(Request $request)
    {
        $request->validate(['code' => 'required|string']);
        $code = strtoupper(trim($request->code));

        $coupon = Coupon::where('code', $code)->first();

        if (!$coupon) {
            return response()->json(['success' => false, 'message' => 'অকার্যকর কুপন কোড (Invalid coupon code)'], 422);
        }

        $cart = session()->get('cart', []);
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        if (!$coupon->isValidFor($subtotal)) {
            $msg = $coupon->min_order > 0
                ? "এই কুপন ব্যবহার করতে ন্যূনতম ৳{$coupon->min_order} এর অর্ডার প্রয়োজন।"
                : "কুপনটির মেয়াদ শেষ হয়ে গেছে।";
            return response()->json(['success' => false, 'message' => $msg], 422);
        }

        $discount = $coupon->calculateDiscount($subtotal);

        session()->put('coupon', [
            'code' => $coupon->code,
            'type' => $coupon->type,
            'value' => $coupon->value,
            'discount' => $discount,
        ]);

        return response()->json([
            'success' => true,
            'message' => "কুপন প্রয়োগ করা হয়েছে! আপনি ৳{$discount} ছাড় পেয়েছেন।",
            'discount' => $discount,
            'coupon' => $coupon->code,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:100',
            'customer_phone' => [
                'required',
                'string',
                'regex:/^(?:\+88|88)?(01[3-9]\d{8})$/'
            ],
            'customer_address' => 'required|string|min:6|max:500',
            'delivery_zone' => 'required|in:inside_dhaka,dhaka_suburbs,outside_dhaka',
            'payment_method' => 'required|in:cod,bkash,nagad',
            'customer_note' => 'nullable|string|max:300',
        ], [
            'customer_name.required' => 'আপনার সম্পূর্ণ নাম লিখুন',
            'customer_phone.required' => 'সঠিক মোবাইল নম্বর দিন (যেমন: 01712345678)',
            'customer_phone.regex' => '১১ ডিজিটের সঠিক বাংলাদেশি মোবাইল নম্বর দিন (01XXXXXXXXX)',
            'customer_address.required' => 'আপনার সম্পূর্ণ ডেলিভারি ঠিকানা লিখুন',
            'delivery_zone.required' => 'ডেলিভারি এরিয়া নির্বাচন করুন',
        ]);

        // Clean phone number
        $phone = preg_replace('/[^0-9]/', '', $request->customer_phone);
        if (str_starts_with($phone, '880')) {
            $phone = '0' . substr($phone, 3);
        }

        // Direct product order or cart order
        $cart = [];
        if ($request->filled('buy_now_product_id')) {
            $product = Product::findOrFail($request->buy_now_product_id);
            $cart[] = [
                'product_id' => $product->id,
                'title' => $product->title,
                'thumbnail' => $product->thumbnail,
                'size' => $request->buy_now_size ?? ($product->sizes ? $product->sizes[0] : null),
                'price' => (float) ($product->sale_price ?? $product->regular_price),
                'quantity' => (int) ($request->buy_now_quantity ?? 1),
            ];
        } else {
            $sessionCart = session()->get('cart', []);
            if (empty($sessionCart)) {
                return redirect()->route('shop.index')->with('error', 'আপনার কার্ট খালি!');
            }
            $cart = array_values($sessionCart);
        }

        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        // Delivery fee calculation
        $deliveryCharge = match($request->delivery_zone) {
            'inside_dhaka' => (float) Setting::get('inside_dhaka_charge', 70),
            'dhaka_suburbs' => (float) Setting::get('suburbs_charge', 100),
            'outside_dhaka' => (float) Setting::get('outside_dhaka_charge', 130),
        };

        // Free shipping check
        $freeShippingThreshold = (float) Setting::get('free_shipping_threshold', 3000);
        if ($subtotal >= $freeShippingThreshold && $request->delivery_zone === 'inside_dhaka') {
            $deliveryCharge = 0.00;
        }

        // Coupon calculation
        $discount = 0;
        $couponSession = session()->get('coupon');
        if ($couponSession) {
            $couponModel = Coupon::where('code', $couponSession['code'])->first();
            if ($couponModel && $couponModel->isValidFor($subtotal)) {
                $discount = $couponModel->calculateDiscount($subtotal);
            }
        }

        $totalAmount = max(0, $subtotal + $deliveryCharge - $discount);

        $order = DB::transaction(function () use ($request, $phone, $deliveryCharge, $subtotal, $discount, $totalAmount, $cart) {
            $order = Order::create([
                'customer_name' => $request->customer_name,
                'customer_phone' => $phone,
                'customer_address' => $request->customer_address,
                'customer_note' => $request->customer_note,
                'delivery_zone' => $request->delivery_zone,
                'delivery_charge' => $deliveryCharge,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'total_amount' => $totalAmount,
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending',
                'order_status' => 'pending',
            ]);

            foreach ($cart as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'] ?? null,
                    'product_name' => $item['title'],
                    'product_thumbnail' => $item['thumbnail'] ?? null,
                    'size' => $item['size'] ?? null,
                    'unit_price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'total_price' => $item['price'] * $item['quantity'],
                ]);
            }

            return $order;
        });

        // Clear Cart and Coupon sessions
        session()->forget('cart');
        session()->forget('coupon');

        return redirect()->route('checkout.success', $order->order_number);
    }

    public function success($order_number)
    {
        $order = Order::with('items')->where('order_number', $order_number)->firstOrFail();
        $whatsappNumber = Setting::get('whatsapp_number', '8801713580400');

        // Pre-formatted WhatsApp message for customer confirmation
        $itemsText = "";
        foreach ($order->items as $item) {
            $itemsText .= "• {$item->product_name} (" . ($item->size ?? 'Standard') . ") x {$item->quantity} = ৳" . number_format($item->total_price) . "\n";
        }

        $waMessage = urlencode(
            "🛍️ *Yana's Fashion - New Order Confirmation*\n" .
            "Order ID: *#{$order->order_number}*\n" .
            "Name: {$order->customer_name}\n" .
            "Phone: {$order->customer_phone}\n" .
            "Address: {$order->customer_address}\n\n" .
            "📦 *Items:*\n" . $itemsText . "\n" .
            "Subtotal: ৳" . number_format($order->subtotal) . "\n" .
            "Delivery: ৳" . number_format($order->delivery_charge) . "\n" .
            "Total: *৳" . number_format($order->total_amount) . "* (COD)\n\n" .
            "Please confirm my order. Thank you!"
        );

        $whatsappUrl = "https://wa.me/{$whatsappNumber}?text={$waMessage}";

        return view('checkout.success', compact('order', 'whatsappUrl'));
    }
}
