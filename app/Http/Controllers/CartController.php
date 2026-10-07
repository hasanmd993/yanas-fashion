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

        return \Inertia\Inertia::render('Cart/Index', [
            'cart' => array_values($cart),
            'total' => $total,
        ]);
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

        return redirect()->back()->with('success', 'Product added to cart!');
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

        return redirect()->back()->with('success', 'Cart updated!');
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

        return redirect()->back()->with('success', 'Product removed from cart!');
    }

    public function clear()
    {
        session()->forget('cart');
        return redirect()->back()->with('success', 'Cart cleared!');
    }

    public function sync(Request $request)
    {
        $request->validate([
            'items' => 'nullable|array|max:50',
            'items.*.id' => 'nullable|integer',
            'items.*.product_id' => 'nullable|integer',
            'items.*.quantity' => 'nullable|integer|min:1|max:99',
            'items.*.size' => 'nullable|string|max:50',
            'items.*.color' => 'nullable|string|max:50',
        ]);

        $items = $request->input('items', []);
        $cart = [];

        $productIds = collect($items)->map(fn($item) => $item['id'] ?? $item['product_id'] ?? null)->filter()->unique()->values();
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

        foreach ($items as $item) {
            $productId = $item['id'] ?? $item['product_id'] ?? null;
            if (!$productId || !isset($products[$productId])) continue;

            $product = $products[$productId];

            $key = $item['key'] ?? ($productId . '-' . ($item['size'] ?? 'default') . '-' . ($item['color'] ?? 'default'));
            $quantity = max(1, (int) ($item['quantity'] ?? 1));
            $price = (float) ($product->sale_price ?? $product->regular_price ?? ($item['price'] ?? 0));
            $regularPrice = (float) ($product->regular_price ?? ($item['regularPrice'] ?? $item['regular_price'] ?? $price));

            $cart[$key] = [
                'key' => $key,
                'product_id' => $product->id,
                'title' => $product->title,
                'title_bn' => $product->title_bn,
                'slug' => $product->slug,
                'price' => $price,
                'regular_price' => $regularPrice,
                'thumbnail' => $item['image'] ?? $item['thumbnail'] ?? $product->primary_image ?? $product->thumbnail,
                'size' => $item['size'] ?? null,
                'color' => $item['color'] ?? null,
                'quantity' => $quantity,
            ];
        }

        session()->put('cart', $cart);

        return response()->json([
            'success' => true,
            'items' => array_values($cart),
            'count' => count($cart),
        ]);
    }
}

