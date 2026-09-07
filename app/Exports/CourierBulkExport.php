<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class CourierBulkExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $status;

    public function __construct($status = 'pending')
    {
        $this->status = $status;
    }

    public function collection(): \Illuminate\Support\Enumerable
    {
        $query = Order::with('items')->latest();
        if ($this->status && $this->status !== 'all') {
            $query->where('order_status', $this->status);
        }
        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Invoice / Order Number',
            'Customer Name',
            'Customer Phone',
            'Customer Address',
            'Delivery Zone',
            'COD Amount (BDT)',
            'Note / Product Description',
        ];
    }

    public function map($order): array
    {
        $itemsList = $order->items->map(function ($item) {
            return "{$item->product_title} (" . ($item->size ?? 'Std') . ") x{$item->quantity}";
        })->implode('; ');

        return [
            $order->order_number,
            $order->customer_name,
            $order->customer_phone,
            $order->customer_address,
            $order->zone_label,
            $order->payment_method === 'cod' ? $order->total_amount : 0,
            $itemsList . ($order->customer_note ? " | " . $order->customer_note : ''),
        ];
    }
}

