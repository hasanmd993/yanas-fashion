@extends('layouts.app')

@section('title', "Order Placed Successfully (#{$order->order_number}) - Yanas Fashion")

@section('content')

    <section class="container" style="padding: 48px 16px 80px; max-width: 780px;">
        
        <!-- Success Card -->
        <div style="background: var(--white); border: 1px solid var(--line); border-radius: var(--radius-lg); padding: 36px 24px; text-align: center; box-shadow: var(--shadow-md); margin-bottom: 28px;">
            
            <!-- Check Icon -->
            <div style="width: 76px; height: 76px; background: #e8f7ed; color: var(--success); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 2.5rem; margin-bottom: 16px;">
                <i class="fa-solid fa-circle-check"></i>
            </div>

            <h1 style="font-size: 2rem; color: var(--dark); margin-bottom: 8px;">
                Thank you! Your order has been received successfully.
            </h1>
            
            <p style="color: var(--text-muted); font-size: 1rem; margin-bottom: 20px;">
                Our customer care team will contact you shortly to confirm your order.
            </p>

            <div style="display: inline-block; background: var(--bg-light); border: 1.5px dashed var(--primary); border-radius: var(--radius-sm); padding: 10px 24px; font-size: 1.15rem; font-weight: 800; color: var(--primary); margin-bottom: 24px;">
                Order No: #{{ $order->order_number }}
            </div>

            <!-- WhatsApp Direct Confirmation Button -->
            <div style="margin-bottom: 24px;">
                <a href="{{ $whatsappUrl }}" target="_blank" class="btn btn-whatsapp" style="font-size: 1rem; padding: 12px 24px;">
                    <i class="fa-brands fa-whatsapp"></i> Confirm via WhatsApp
                </a>
            </div>

            <!-- Invoice Details Table -->
            <div style="text-align: left; background: var(--bg-light); border: 1px solid var(--line); border-radius: var(--radius-md); padding: 20px; margin-top: 24px;">
                <h3 style="font-size: 1.2rem; margin-bottom: 14px; border-bottom: 1px solid var(--line); padding-bottom: 8px;">
                    Invoice Breakdown
                </h3>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; font-size: 0.88rem; margin-bottom: 16px;">
                    <div><strong>Name:</strong> {{ $order->customer_name }}</div>
                    <div><strong>Phone:</strong> {{ $order->customer_phone }}</div>
                    <div><strong>Payment:</strong> {{ strtoupper($order->payment_method) }} (Cash On Delivery)</div>
                    <div><strong>Delivery Zone:</strong> {{ $order->zone_label }}</div>
                    <div style="grid-column: span 2;"><strong>Address:</strong> {{ $order->customer_address }}</div>
                </div>

                <!-- Products Table -->
                <table style="width: 100%; border-collapse: collapse; font-size: 0.88rem; margin-top: 12px;">
                    <thead>
                        <tr style="background: #eee; text-align: left;">
                            <th style="padding: 8px 10px;">Product</th>
                            <th style="padding: 8px 10px;">Size</th>
                            <th style="padding: 8px 10px; text-align: center;">Qty</th>
                            <th style="padding: 8px 10px; text-align: right;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                            <tr style="border-bottom: 1px solid var(--line);">
                                <td style="padding: 10px;">
                                    <strong>{{ $item->product_name }}</strong>
                                </td>
                                <td style="padding: 10px;">{{ $item->size ?? 'N/A' }}</td>
                                <td style="padding: 10px; text-align: center;">{{ $item->quantity }}</td>
                                <td style="padding: 10px; text-align: right; font-weight: 700;">৳{{ number_format($item->total_price) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" style="padding: 8px 10px; text-align: right; color: var(--text-muted);">Subtotal:</td>
                            <td style="padding: 8px 10px; text-align: right; font-weight: 700;">৳{{ number_format($order->subtotal) }}</td>
                        </tr>
                        <tr>
                            <td colspan="3" style="padding: 8px 10px; text-align: right; color: var(--text-muted);">Delivery:</td>
                            <td style="padding: 8px 10px; text-align: right; font-weight: 700;">৳{{ number_format($order->delivery_charge) }}</td>
                        </tr>
                        @if($order->discount > 0)
                            <tr style="color: var(--success);">
                                <td colspan="3" style="padding: 8px 10px; text-align: right;">Discount:</td>
                                <td style="padding: 8px 10px; text-align: right; font-weight: 700;">-৳{{ number_format($order->discount) }}</td>
                            </tr>
                        @endif
                        <tr style="font-size: 1.15rem; font-weight: 800; color: var(--primary); border-top: 2px dashed var(--line);">
                            <td colspan="3" style="padding: 12px 10px; text-align: right;">Total:</td>
                            <td style="padding: 12px 10px; text-align: right;">৳{{ number_format($order->total_amount) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Action Buttons -->
            <div style="display: flex; justify-content: center; gap: 14px; margin-top: 28px; flex-wrap: wrap;">
                <a href="{{ route('order.invoice', $order->order_number) }}" class="btn btn-outline btn-sm" style="border-color:var(--primary); color:var(--primary);">
                    <i class="fa-solid fa-file-pdf"></i> PDF Invoice Download
                </a>
                <button type="button" onclick="window.print()" class="btn btn-outline btn-sm">
                    <i class="fa-solid fa-print"></i> Print
                </button>
                <a href="{{ route('tracking.index') }}" class="btn btn-primary btn-sm">
                    <i class="fa-solid fa-truck-fast"></i> Track Order
                </a>
                <a href="{{ route('shop.index') }}" class="btn btn-sm" style="background:var(--bg-light); border:1px solid var(--line);">
                    Continue Shopping
                </a>
            </div>

        </div>

    </section>

@endsection
