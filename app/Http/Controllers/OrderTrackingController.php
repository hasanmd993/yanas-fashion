<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderTrackingController extends Controller
{
    public function index()
    {
        return \Inertia\Inertia::render('Tracking/Index', [
            'order' => null,
        ]);
    }

    public function track(Request $request)
    {
        $request->validate([
            'order_number' => 'required|string',
            'phone' => [
                'required',
                'string',
                'regex:/^(?:\+88|88)?(01[3-9]\d{8})$/'
            ],
        ], [
            'order_number.required' => 'Please enter your order number (e.g. YF-261007...)',
            'phone.required' => 'Please enter the mobile number used for the order',
            'phone.regex' => 'Please enter a valid 11-digit Bangladeshi mobile number (01XXXXXXXXX)',
        ]);

        $orderNum = trim(strtoupper($request->order_number));
        $phone = preg_replace('/[^0-9]/', '', $request->phone);
        if (str_starts_with($phone, '880')) {
            $phone = substr($phone, 2);
        }

        $order = Order::with('items')
            ->where(function ($q) use ($orderNum) {
                $q->where('order_number', $orderNum)
                  ->orWhere('order_number', 'YF-' . $orderNum)
                  ->orWhere('courier_tracking_code', $orderNum)
                  ->orWhere('courier_consignment_id', $orderNum);
            })
            ->where('customer_phone', $phone)
            ->first();

        if (!$order) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'No order found! Please verify the correct order number and phone number.');
        }

        return \Inertia\Inertia::render('Tracking/Index', [
            'order' => $order,
        ]);
    }
}

