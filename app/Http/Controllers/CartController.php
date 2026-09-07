<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        return view('cart.index', compact('cart', 'total'));
    }

    public function getCart()
    {
        $cart = session()->get('cart', []);
        $total = 0;
        $count = 0;

        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
            $count += $item['quantity'];
        }

        return response()->json([
            'items' => array_values($cart),
            'total' => $total,
            'count' => $count,
        ]);
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'nullable|integer|min:1',
            'size' => 'nullable|string',
        ]);

        $product = Product::findOrFail($request->product_id);
        $quantity = (int) ($request->quantity ?? 1);
        $size = $request->size ?? ($product->sizes ? $product->sizes[0] : null);

        $cartKey = $product->id . ($size ? '-' . $size : '');

        $cart = session()->get('cart', []);

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] += $quantity;
        } else {
            $cart[$cartKey] = [
                'key' => $cartKey,
                'product_id' => $product->id,
                'title' => $product->title,
                'title_bn' => $product->title_bn,
                'slug' => $product->slug,
                'price' => (float) ($product->sale_price ?? $product->regular_price),
                'regular_price' => (float) $product->regular_price,
                'thumbnail' => $product->thumbnail,
                'size' => $size,
                'quantity' => $quantity,
            ];
        }

        session()->put('cart', $cart);

        if ($request->wantsJson() || $request->ajax()) {
            return $this->getCart();
        }

        return redirect()->back()->with('success', 'পণ্যটি কার্ট-এ যুক্ত হয়েছে!');
    }

    public function update(Request $request)
    {
        $request->validate([
            'key' => 'required|string',
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = session()->get('cart', []);

        if (isset($cart[$request->key])) {
            $cart[$request->key]['quantity'] = (int) $request->quantity;
            session()->put('cart', $cart);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return $this->getCart();
        }

        return redirect()->back()->with('success', 'কার্ট আপডেট হয়েছে!');
    }

    public function remove(Request $request)
    {
        $request->validate([
            'key' => 'required|string',
        ]);

        $cart = session()->get('cart', []);

        if (isset($cart[$request->key])) {
            unset($cart[$request->key]);
            session()->put('cart', $cart);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return $this->getCart();
        }

        return redirect()->back()->with('success', 'পণ্যটি কার্ট থেকে সরানো হয়েছে!');
    }

    public function clear()
    {
        session()->forget('cart');
        return redirect()->back()->with('success', 'কার্ট খালি করা হয়েছে!');
    }
}

