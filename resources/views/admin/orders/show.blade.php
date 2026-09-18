@extends('admin.layouts.master')

@section('title', "Order #{$order->order_number} Details")

@section('content')

    <!-- Header -->
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-800 dark:text-white">
                Order #{{ $order->order_number }}
            </h1>
            <p class="text-xs text-gray-500 dark:text-gray-400">Placed on {{ $order->created_at->format('M d, Y h:i A') }}</p>
        </div>
        <div class="flex flex-wrap items-center gap-2 sm:gap-3 admin-actions-wrap">
            <a href="{{ route('admin.orders.index') }}" class="btn inline-flex items-center gap-2 rounded-lg bg-gray-700 px-3.5 py-2 text-xs font-bold text-white shadow-md hover:bg-gray-800 transition-all">
                <i class="fa-solid fa-arrow-left"></i> Back to Orders
            </a>
             <a href="{{ route('admin.orders.stream', $order->id) }}" target="_blank" class="btn inline-flex items-center gap-2 rounded-lg bg-primary px-3.5 py-2 text-xs font-bold text-white shadow-md hover:bg-primary-hover transition-all" title="Download PDF File">
                <i class="fa-solid fa-file-arrow-down"></i> Download PDF
            </a>
            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $order->customer_phone) }}?text=Hello%20{{ urlencode($order->customer_name) }},%20regarding%20your%20Yanas%20Fashion%20order%20%23{{ $order->order_number }}..." target="_blank" 
               class="btn inline-flex items-center gap-2 rounded-lg bg-[#25D366] px-3.5 py-2 text-xs font-bold text-white shadow-md hover:bg-[#1eb956] transition-all">
                <i class="fa-brands fa-whatsapp text-sm"></i> WhatsApp
            </a>
        </div>
    </div>

    <!-- 2 Column Layout -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        
        <!-- Left: Order Items & Breakdown (2 Cols) -->
        <div class="lg:col-span-2 space-y-6">
            
            <div class="panel">
                <h3 class="text-base font-bold text-gray-800 dark:text-white border-b border-gray-100 dark:border-[#192a43] pb-3 mb-4">
                    Ordered Items
                </h3>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs admin-card-table">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-[#192a43] text-gray-400 font-bold uppercase text-[10px]">
                                <th class="py-3 px-3">Item</th>
                                <th class="py-3 px-3">Size</th>
                                <th class="py-3 px-3">Unit Price</th>
                                <th class="py-3 px-3 text-center">Qty</th>
                                <th class="py-3 px-3 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-[#192a43]">
                            @foreach($order->items as $item)
                                <tr>
                                    <td class="py-3.5 px-3" data-label="Item">
                                        <div class="flex items-center gap-3">
                                            @if($item->product_thumbnail)
                                                <img src="{{ asset($item->product_thumbnail) }}" alt="{{ $item->product_name }}" class="h-11 w-11 rounded-lg object-cover">
                                            @endif
                                            <div>
                                                <span class="font-bold text-gray-800 dark:text-white block">{{ $item->product_name }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-3 font-semibold" data-label="Size">{{ $item->size ?? 'Standard' }}</td>
                                    <td class="py-3.5 px-3" data-label="Unit Price">৳{{ number_format($item->unit_price) }}</td>
                                    <td class="py-3.5 px-3 text-center font-bold" data-label="Qty">{{ $item->quantity }}</td>
                                    <td class="py-3.5 px-3 text-right font-black text-gray-800 dark:text-white" data-label="Total">৳{{ number_format($item->total_price) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="border-t-2 border-dashed border-gray-200 dark:border-[#192a43]">
                            <tr>
                                <td colspan="4" class="py-2.5 px-3 text-right text-gray-500 font-semibold">Subtotal:</td>
                                <td class="py-2.5 px-3 text-right font-bold">৳{{ number_format($order->subtotal) }}</td>
                            </tr>
                            <tr>
                                <td colspan="4" class="py-2 px-3 text-right text-gray-500 font-semibold">Delivery Charge ({{ $order->zone_label }}):</td>
                                <td class="py-2 px-3 text-right font-bold">৳{{ number_format($order->delivery_charge) }}</td>
                            </tr>
                            @if($order->discount > 0)
                                <tr class="text-success">
                                    <td colspan="4" class="py-2 px-3 text-right font-semibold">Coupon Discount:</td>
                                    <td class="py-2 px-3 text-right font-bold">-৳{{ number_format($order->discount) }}</td>
                                </tr>
                            @endif
                            <tr class="text-base font-black text-primary dark:text-primary-light border-t border-gray-200 dark:border-[#192a43]">
                                <td colspan="4" class="py-3 px-3 text-right">Grand Total:</td>
                                <td class="py-3 px-3 text-right">৳{{ number_format($order->total_amount) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            @if($order->customer_note)
                <div class="panel">
                    <span class="text-[11px] uppercase font-bold text-gray-400">Customer Delivery Instructions:</span>
                    <p class="mt-1 text-xs text-gray-700 dark:text-gray-300 font-semibold">{{ $order->customer_note }}</p>
                </div>
            @endif

        </div>

        <!-- Right: Status Updater & Customer Details (1 Col) -->
        <div class="space-y-6">
            
            <!-- Update Status Card -->
            <div class="panel">
                <h3 class="text-base font-bold text-gray-800 dark:text-white border-b border-gray-100 dark:border-[#192a43] pb-3 mb-4">
                    Update Order Status
                </h3>

                <form action="{{ route('admin.orders.update_status', $order->id) }}" method="POST" class="space-y-4 text-xs">
                    @csrf

                    <div>
                        <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">Fulfillment Status</label>
                        <select name="order_status" class="w-full rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] px-3.5 py-2 text-xs font-bold focus:border-primary focus:outline-none dark:text-white">
<option value="pending" {{ $order->order_status == 'pending' ? 'selected' : '' }}>🟡 Pending</option>
                            <option value="processing" {{ $order->order_status == 'processing' ? 'selected' : '' }}>🔵 Processing</option>
                            <option value="shipped" {{ $order->order_status == 'shipped' ? 'selected' : '' }}>🟣 In Transit</option>
                            <option value="delivered" {{ $order->order_status == 'delivered' ? 'selected' : '' }}>🟢 Delivered</option>
                            <option value="cancelled" {{ $order->order_status == 'cancelled' ? 'selected' : '' }}>🔴 Cancelled</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">Payment Status</label>
                        <select name="payment_status" class="w-full rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] px-3.5 py-2 text-xs font-bold focus:border-primary focus:outline-none dark:text-white">
<option value="pending" {{ $order->payment_status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="paid" {{ $order->payment_status == 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="failed" {{ $order->payment_status == 'failed' ? 'selected' : '' }}>Failed</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">Admin / Courier Notes</label>
                        <textarea name="admin_notes" rows="2" placeholder="e.g. Steadfast Consignment ID: 90218..." 
                                  class="w-full rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] p-3 text-xs font-semibold focus:border-primary focus:outline-none dark:text-white">{{ $order->admin_notes }}</textarea>
                    </div>

                    <button type="submit" class="w-full rounded-lg bg-primary py-2.5 text-xs font-bold text-white shadow-md hover:bg-primary-hover transition-all">
                        Save Status Changes
                    </button>
                </form>
            </div>

            <!-- Customer Details Card -->
            <div class="panel">
                <h3 class="text-base font-bold text-gray-800 dark:text-white border-b border-gray-100 dark:border-[#192a43] pb-3 mb-4">
                    Customer Information
                </h3>

                <div class="space-y-3 text-xs">
                    <div>
                        <span class="text-gray-400 block text-[10px] uppercase font-bold">Full Name:</span>
                        <span class="font-bold text-gray-800 dark:text-white">{{ $order->customer_name }}</span>
                    </div>

                    <div>
                        <span class="text-gray-400 block text-[10px] uppercase font-bold">Phone:</span>
                        <a href="tel:{{ $order->customer_phone }}" class="font-bold text-primary dark:text-primary-light hover:underline font-mono">
                            {{ $order->customer_phone }}
                        </a>
                    </div>

                    <div>
                        <span class="text-gray-400 block text-[10px] uppercase font-bold">Delivery Zone:</span>
                        <span class="font-semibold">{{ $order->zone_label }}</span>
                    </div>

                    <div>
                        <span class="text-gray-400 block text-[10px] uppercase font-bold">Full Address:</span>
                        <p class="font-semibold text-gray-700 dark:text-gray-300 leading-relaxed">{{ $order->customer_address }}</p>
                    </div>

                    <div>
                        <span class="text-gray-400 block text-[10px] uppercase font-bold">Payment Method:</span>
                        <span class="font-bold uppercase text-secondary">{{ $order->payment_method }} (Cash On Delivery)</span>
                    </div>
                </div>
            </div>

        </div>

    </div>

@endsection

