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
            'order_number.required' => 'অর্ডার নম্বর লিখুন (যেমন: YF-91024)',
            'phone.required' => 'অর্ডার করার সময় ব্যবহৃত মোবাইল নম্বর লিখুন',
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
                ->with('error', 'কোন অর্ডার পাওয়া যায়নি! অনুগ্রহ করে সঠিক অর্ডার নম্বর ও ফোন নম্বর যাচাই করুন।');
        }

        return view('tracking.index', compact('order'));
    }
}
