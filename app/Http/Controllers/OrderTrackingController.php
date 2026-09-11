<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderTrackingController extends Controller
{
    public function index()
    {
        return view('tracking.index');
    }

    public function track(Request $request)
    {
        $request->validate([
            'order_number' => 'required|string',
            'phone' => 'required|string',
        ], [
            'order_number.required' => 'Please enter your order number (e.g. YF-91024)',
            'phone.required' => 'Please enter the mobile number used for the order',
        ]);

        $orderNum = trim(strtoupper($request->order_number));
        $phone = preg_replace('/[^0-9]/', '', $request->phone);

        $order = Order::with('items')
            ->where(function ($q) use ($orderNum) {
                $q->where('order_number', $orderNum)
                  ->orWhere('order_number', 'YF-' . $orderNum)
                  ->orWhere('id', $orderNum);
            })
            ->where(function ($q) use ($phone) {
                $q->where('customer_phone', 'like', "%{$phone}%");
            })
            ->first();

        if (!$order) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'No order found! Please verify the correct order number and phone number.');
        }

        return view('tracking.index', compact('order'));
    }
}

