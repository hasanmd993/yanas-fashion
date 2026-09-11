<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Orders Report - Yanas Fashion</title>
    <style>
        @page {
            size: a4 portrait;
            margin: 14mm 10mm 14mm 10mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 8.5px;
            color: #2b2b2b;
            line-height: 1.35;
            margin: 0;
            padding: 0;
        }

        /* Header */
        .report-header {
            width: 100%;
            margin-bottom: 12px;
            padding-bottom: 10px;
            border-bottom: 2px solid #730163;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table td {
            vertical-align: middle;
        }

        .brand-name {
            font-size: 20px;
            font-weight: bold;
            color: #730163;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin: 0;
        }

        .brand-sub {
            font-size: 8.5px;
            color: #777777;
            margin-top: 2px;
            letter-spacing: 0.3px;
        }

        .report-title {
            text-align: right;
            font-size: 16px;
            font-weight: bold;
            color: #F68625;
            text-transform: uppercase;
            margin: 0;
        }

        .report-meta {
            text-align: right;
            font-size: 8px;
            color: #666666;
            margin-top: 3px;
            line-height: 1.4;
        }

        /* KPI Cards Strip */
        .kpi-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 6px 0;
            margin-bottom: 12px;
        }

        .kpi-box {
            background-color: #faf5f9;
            border: 1px solid #edd5e8;
            border-radius: 4px;
            padding: 6px 8px;
            text-align: center;
        }

        .kpi-label {
            font-size: 7.5px;
            font-weight: bold;
            color: #730163;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            margin-bottom: 2px;
        }

        .kpi-value {
            font-size: 13px;
            font-weight: bold;
            color: #222222;
        }

        .kpi-highlight {
            color: #F68625;
        }

        /* Orders Table */
        .orders-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-bottom: 12px;
        }

        .orders-table th {
            background-color: #730163;
            color: #ffffff;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            padding: 6px 5px;
            border: 1px solid #730163;
            text-align: left;
            overflow: hidden;
        }

        .orders-table th.text-center {
            text-align: center;
        }

        .orders-table th.text-right {
            text-align: right;
        }

        .orders-table td {
            padding: 5px;
            border-bottom: 1px solid #e8e8e8;
            border-left: 1px solid #f2f2f2;
            border-right: 1px solid #f2f2f2;
            font-size: 8px;
            vertical-align: top;
            word-wrap: break-word;
            overflow: hidden;
        }

        .orders-table tr:nth-child(even) td {
            background-color: #fafafa;
        }

        .order-num {
            font-weight: bold;
            color: #730163;
            font-size: 8.5px;
        }

        .order-date {
            font-size: 7.5px;
            color: #888888;
            margin-top: 2px;
        }

        .cust-name {
            font-weight: bold;
            color: #222222;
            font-size: 8.5px;
        }

        .cust-phone {
            font-size: 8px;
            color: #444444;
            margin-top: 1px;
        }

        .cust-address {
            font-size: 7.5px;
            color: #777777;
            margin-top: 1px;
            line-height: 1.25;
        }

        .items-list {
            font-size: 7.8px;
            color: #333333;
            line-height: 1.25;
        }

        .item-row {
            margin-bottom: 2px;
        }

        .item-size {
            color: #888888;
            font-size: 7.5px;
        }

        /* Status Badges */
        .badge {
            display: inline-block;
            padding: 1px 4px;
            border-radius: 3px;
            font-size: 7.2px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.2px;
            text-align: center;
            white-space: nowrap;
        }

        .badge-pending {
            background-color: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
        }

        .badge-processing {
            background-color: #e0f2fe;
            color: #0369a1;
            border: 1px solid #bae6fd;
        }

        .badge-shipped {
            background-color: #f3e8ff;
            color: #6b21a8;
            border: 1px solid #e9d5ff;
        }

        .badge-delivered {
            background-color: #dcfce7;
            color: #15803d;
            border: 1px solid #bbf7d0;
        }

        .badge-cancelled {
            background-color: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        .badge-paid {
            background-color: #dcfce7;
            color: #15803d;
        }

        .badge-unpaid {
            background-color: #fef3c7;
            color: #b45309;
        }

        .amount-col {
            text-align: right;
            font-weight: bold;
            color: #111111;
            font-size: 8.5px;
            white-space: nowrap;
        }

        .amount-detail {
            font-size: 7px;
            color: #888888;
            font-weight: normal;
        }

        /* Grand Total Row */
        .total-row td {
            background-color: #f7f1f6 !important;
            border-top: 2px solid #730163 !important;
            border-bottom: 2px solid #730163 !important;
            font-weight: bold;
            font-size: 9px;
            color: #730163;
            padding: 6px 5px;
        }

        /* Footer */
        .report-footer {
            margin-top: 10px;
            padding-top: 6px;
            border-top: 1px solid #e5e5e5;
            font-size: 7.5px;
            color: #888888;
            width: 100%;
        }

        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }

        .footer-table td {
            vertical-align: middle;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }
    </style>
</head>

<body>

    <!-- Header -->
    <div class="report-header">
        <table class="header-table">
            <tr>
                <td style="width: 55%;">
                    <table style="border-collapse: collapse; margin: 0; padding: 0;">
                        <tr>
                            @php
                                $logoData = get_logo_base64();
                            @endphp
                            @if($logoData)
                                <td style="vertical-align: middle; padding-right: 10px; width: 42px;">
                                    <img src="{{ $logoData }}" style="width: 42px; height: 42px; display: block;" alt="Logo">
                                </td>
                            @endif
                            <td style="vertical-align: middle;">
                                <div class="brand-name">{{ get_setting('site_name', 'Yanas Fashion') }}</div>
                                <div class="brand-sub">{{ get_setting('tagline', 'Dhaka · Contemporary & Luxury Fashion · Bangladesh') }}</div>
                            </td>
                        </tr>
                    </table>
                </td>
                <td style="width: 45%;">
                    <div class="report-title">Orders Report</div>
                    <div class="report-meta">
                        <strong>Paper:</strong> A4 &nbsp;|&nbsp; <strong>Filter:</strong> {{ strtoupper($status) }}
                        @if(!empty($search))
                            &nbsp;|&nbsp; <strong>Search:</strong> "{{ $search }}"
                        @endif
                        <br>
                        <strong>Generated:</strong> {{ date('d M, Y - h:i A') }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Summary KPI Cards -->
    <table class="kpi-table">
        <tr>
            <td class="kpi-box" style="width: 25%;">
                <div class="kpi-label">Total Orders</div>
                <div class="kpi-value">{{ number_format($statusCounts['total']) }}</div>
            </td>
            <td class="kpi-box" style="width: 25%;">
                <div class="kpi-label">Total Revenue</div>
                <div class="kpi-value kpi-highlight">{{ number_format($totalRevenue, 2) }}</div>
            </td>
            <td class="kpi-box" style="width: 25%;">
                <div class="kpi-label">Delivered</div>
                <div class="kpi-value" style="color: #15803d;">{{ number_format($statusCounts['delivered']) }}</div>
            </td>
            <td class="kpi-box" style="width: 25%;">
                <div class="kpi-label">Pending / In Progress</div>
                <div class="kpi-value" style="color: #b45309;">
                    {{ number_format($statusCounts['pending'] + $statusCounts['processing']) }}</div>
            </td>
        </tr>
    </table>

    <!-- Orders Table (A4 Portrait Fitted) -->
    <table class="orders-table">
        <thead>
            <tr>
                <th style="width: 4%;" class="text-center">#</th>
                <th style="width: 14%;">Order #</th>
                <th style="width: 23%;">Customer & Contact</th>
                <th style="width: 28%;">Items Summary</th>
                <th style="width: 11%;">Payment</th>
                <th style="width: 8%;" class="text-center">Status</th>
                <th style="width: 12%;" class="text-right">Total (BDT)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $idx => $order)
                <tr>
                    <td class="text-center" style="color: #777;">{{ $idx + 1 }}</td>
                    <td>
                        <div class="order-num">#{{ $order->order_number }}</div>
                        <div class="order-date">{{ $order->created_at->format('d M Y') }}</div>
                    </td>
                    <td>
                        <div class="cust-name">{{ $order->customer_name }}</div>
                        <div class="cust-phone">{{ $order->customer_phone }}</div>
                        <div class="cust-address">
                            {{ Str::limit($order->shipping_address ?? $order->customer_address, 55) }}
                            @if($order->shipping_city)
                                <br><em>{{ $order->shipping_city }}</em>
                            @endif
                        </div>
                    </td>
                    <td>
                        <div class="items-list">
                            @foreach($order->items as $item)
                                <div class="item-row">
                                    • {{ $item->product_title ?? 'Product' }}
                                    @if($item->size)
                                        <span class="item-size">({{ $item->size }})</span>
                                    @endif
                                    <strong>x{{ $item->quantity }}</strong>
                                    — {{ number_format($item->total_price ?? ($item->unit_price * $item->quantity), 0) }}
                                </div>
                            @endforeach
                        </div>
                    </td>
                    <td>
                        <div style="font-weight: bold; font-size: 7.8px; text-transform: uppercase;">
                            {{ $order->payment_method ?? 'COD' }}
                        </div>
                        <div style="margin-top: 2px;">
                            @if(strtolower($order->payment_status) === 'paid')
                                <span class="badge badge-paid">PAID</span>
                            @else
                                <span class="badge badge-unpaid">{{ strtoupper($order->payment_status ?? 'PENDING') }}</span>
                            @endif
                        </div>
                    </td>
                    <td class="text-center">
                        @php
                            $st = strtolower($order->order_status);
                            $badgeClass = match ($st) {
                                'pending' => 'badge-pending',
                                'processing' => 'badge-processing',
                                'shipped' => 'badge-shipped',
                                'delivered' => 'badge-delivered',
                                'cancelled' => 'badge-cancelled',
                                default => 'badge-pending'
                            };
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ ucfirst($order->order_status) }}</span>
                    </td>
                    <td class="amount-col">
                        {{ number_format($order->total_amount, 2) }}
                        @if($order->delivery_charge > 0)
                            <div class="amount-detail">+{{ number_format($order->delivery_charge, 0) }} deliv</div>
                        @endif
                        @if($order->discount_amount > 0)
                            <div class="amount-detail" style="color: #15803d;">-{{ number_format($order->discount_amount, 0) }}
                                disc</div>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 20px; color: #888888;">
                        No orders found matching the selected filter criteria.
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if($orders->count() > 0)
            <tfoot>
                <tr class="total-row">
                    <td colspan="5" class="text-right">GRAND TOTAL ({{ $orders->count() }} Orders):</td>
                    <td></td>
                    <td class="amount-col" style="font-size: 9.5px; color: #730163;">
                        {{ number_format($totalRevenue, 2) }}
                    </td>
                </tr>
            </tfoot>
        @endif
    </table>

    <!-- Footer -->
    <div class="report-footer">
        <table class="footer-table">
            <tr>
                <td style="width: 50%;">
                    Confidential · Internal Business Document · Yanas Fashion
                </td>
                <td style="width: 50%;" class="text-right">
                    Total Records: {{ $orders->count() }} | Generated from Yanas Fashion Admin Panel
                </td>
            </tr>
        </table>
    </div>

</body>

</html>