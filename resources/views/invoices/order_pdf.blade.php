<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Invoice #{{ $order->order_number }} - Yanas Fashion</title>
    <style>
        body {
            font-family: 'DejaVu Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 12px;
            color: #333333;
            line-height: 1.5;
            margin: 0;
            padding: 24px;
        }

        .invoice-header {
            width: 100%;
            border-bottom: 2px solid #730163;
            padding-bottom: 16px;
            margin-bottom: 24px;
        }

        .brand-title {
            font-size: 24px;
            font-weight: bold;
            color: #730163;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0;
        }

        .brand-subtitle {
            font-size: 10px;
            color: #888888;
            margin-top: 2px;
        }

        .invoice-title {
            text-align: right;
            font-size: 20px;
            font-weight: bold;
            color: #F68625;
            text-transform: uppercase;
        }

        .info-table {
            width: 100%;
            margin-bottom: 24px;
        }

        .info-table td {
            vertical-align: top;
            width: 50%;
        }

        .box-title {
            font-size: 11px;
            font-weight: bold;
            color: #730163;
            text-transform: uppercase;
            margin-bottom: 6px;
            border-bottom: 1px solid #eeeeee;
            padding-bottom: 3px;
        }

        .customer-info p, .order-meta p {
            margin: 2px 0;
            font-size: 11px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }

        .items-table th {
            background-color: #f7f1f6;
            color: #730163;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 8px 10px;
            border-bottom: 1px solid #e0d0df;
            text-align: left;
        }

        .items-table td {
            padding: 10px;
            border-bottom: 1px solid #eeeeee;
            font-size: 11px;
        }

        .items-table .text-right {
            text-align: right;
        }

        .items-table .text-center {
            text-align: center;
        }

        .summary-container {
            width: 100%;
            margin-top: 10px;
        }

        .summary-table {
            width: 45%;
            float: right;
            border-collapse: collapse;
        }

        .summary-table td {
            padding: 5px 8px;
            font-size: 11px;
        }

        .summary-table .total-row td {
            border-top: 2px solid #730163;
            font-size: 14px;
            font-weight: bold;
            color: #730163;
            padding-top: 8px;
        }

        .badge-cod {
            display: inline-block;
            background-color: #fff3e8;
            border: 1px solid #F68625;
            color: #F68625;
            font-weight: bold;
            font-size: 10px;
            padding: 3px 8px;
            border-radius: 4px;
            text-transform: uppercase;
        }

        .footer-note {
            margin-top: 60px;
            border-top: 1px solid #eeeeee;
            padding-top: 14px;
            text-align: center;
            font-size: 10px;
            color: #888888;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <table class="invoice-header">
        <tr>
            <td style="vertical-align: middle;">
                <table style="border-collapse: collapse; margin: 0; padding: 0;">
                    <tr>
                        @php
                            $logoData = get_logo_base64();
                        @endphp
                        @if($logoData)
                            <td style="vertical-align: middle; padding-right: 12px; width: 44px;">
                                <img src="{{ $logoData }}" style="width: 44px; height: 44px; display: block;" alt="Logo">
                            </td>
                        @endif
                        <td style="vertical-align: middle;">
                            <div class="brand-title">{{ get_setting('site_name', 'Yanas Fashion') }}</div>
                            <div class="brand-subtitle">{{ get_setting('tagline', 'Bangladeshi Luxury & Contemporary Ethnic Wear') }}</div>
                            <div style="font-size: 10px; color: #666; margin-top: 4px;">
                                Hotline: {{ get_setting('hotline', '01713580400') }} | Web: {{ url('/') }}
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
            <td style="text-align: right; vertical-align: middle;">
                <div class="invoice-title">INVOICE</div>
                <div style="font-weight: bold; font-size: 12px; color: #333;">#{{ $order->order_number }}</div>
                <div style="font-size: 10px; color: #888;">Date: {{ $order->created_at->format('d M, Y h:i A') }}</div>
            </td>
        </tr>
    </table>

    <!-- Billing & Order Meta Information -->
    <table class="info-table">
        <tr>
            <td class="customer-info" style="padding-right: 15px;">
                <div class="box-title">Bill To (Customer Information)</div>
                <p><strong>Name:</strong> {{ $order->customer_name }}</p>
                <p><strong>Phone:</strong> {{ $order->customer_phone }}</p>
                <p><strong>Delivery Address:</strong> {{ $order->customer_address }}</p>
                <p><strong>Delivery Zone:</strong> {{ $order->zone_label }}</p>
                @if($order->customer_note)
                    <p style="margin-top: 4px; color: #666;"><em>Note: {{ $order->customer_note }}</em></p>
                @endif
            </td>
            <td class="order-meta" style="padding-left: 15px;">
                <div class="box-title">Order Details</div>
                <p><strong>Order Status:</strong> {{ ucfirst($order->order_status) }}</p>
                <p><strong>Payment Method:</strong> 
                    <span class="badge-cod">{{ strtoupper(str_replace('_', ' ', $order->payment_method)) }}</span>
                </p>
                <p><strong>Invoice No:</strong> INV-{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</p>
                @if($order->courier_notes)
                    <p><strong>Courier Note:</strong> {{ $order->courier_notes }}</p>
                @endif
            </td>
        </tr>
    </table>

    <!-- Line Items Table -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 50%;">Item & Description</th>
                <th style="width: 15%;" class="text-center">Size</th>
                <th style="width: 10%;" class="text-center">Qty</th>
                <th style="width: 20%;" class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $item->product_title }}</strong>
                    </td>
                    <td class="text-center">{{ $item->size ?? 'Standard' }}</td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">{{ number_format($item->subtotal) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Summary Calculation -->
    <div class="summary-container">
        <table class="summary-table">
            <tr>
                <td>Subtotal:</td>
                <td class="text-right">{{ number_format($order->subtotal) }}</td>
            </tr>
            @if($order->discount > 0)
                <tr style="color: #28a745;">
                    <td>Coupon Discount:</td>
                    <td class="text-right">-{{ number_format($order->discount) }}</td>
                </tr>
            @endif
            <tr>
                <td>Delivery Fee ({{ $order->zone_label }}):</td>
                <td class="text-right">
                    @if($order->delivery_charge == 0)
                        <span style="color: #28a745; font-weight: bold;">FREE</span>
                    @else
                        {{ number_format($order->delivery_charge) }}
                    @endif
                </td>
            </tr>
            <tr class="total-row">
                <td>Total Payable:</td>
                <td class="text-right">{{ number_format($order->total_amount) }}</td>
            </tr>
        </table>
        <div style="clear: both;"></div>
    </div>

    <!-- Footer -->
    <div class="footer-note">
        <p>Thank you for shopping with <strong>Yanas Fashion</strong>! We appreciate your trust in Bangladeshi craftsmanship.</p>
        <p>For exchange, returns, or queries, please contact our support hotline or WhatsApp within 3 days of delivery.</p>
    </div>

</body>
</html>

