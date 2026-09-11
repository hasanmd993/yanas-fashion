<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class OrdersExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $status;
    protected $search;

    public function __construct($status = null, $search = null)
    {
        $this->status = $status;
        $this->search = $search;
    }

    public function collection(): \Illuminate\Support\Enumerable
    {
        $query = Order::with('items')->latest();
        if ($this->status && $this->status !== 'all') {
            $query->where('order_status', $this->status);
        }
        if ($this->search) {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }
        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Order #',
            'Order Date',
            'Customer Name',
            'Phone Number',
            'Delivery Address',
            'Delivery Zone',
            'Ordered Items',
            'Subtotal (BDT)',
            'Discount (BDT)',
            'Delivery Fee (BDT)',
            'Total Amount (BDT)',
            'Payment Method',
            'Payment Status',
            'Order Status',
            'Customer Note',
        ];
    }

    public function map($order): array
    {
        $itemsSummary = $order->items->map(function ($item) {
            return "{$item->product_title} (" . ($item->size ?? 'Standard') . ") x{$item->quantity}";
        })->implode(', ');

        return [
            $order->order_number,
            $order->created_at->format('Y-m-d H:i'),
            $order->customer_name,
            $order->customer_phone,
            $order->customer_address,
            $order->zone_label,
            $itemsSummary,
            $order->subtotal,
            $order->discount,
            $order->delivery_charge,
            $order->total_amount,
            strtoupper($order->payment_method),
            ucfirst($order->payment_status),
            ucfirst($order->order_status),
            $order->customer_note ?? '',
        ];
    }
}

