<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function show($slug)
    {
        $product = Product::with(['category', 'reviews'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->take(4)
            ->get();

        return view('shop.show', compact('product', 'relatedProducts'));
    }

    public function storeReview(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'nullable|email|max:150',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1500',
        ]);

        $review = new Review();
        $review->product_id = $product->id;
        $review->name = $validated['name'];
        $review->email = $validated['email'] ?? null;
        $review->rating = $validated['rating'];
        $review->comment = $validated['comment'];
        $review->is_verified_buyer = true;
        $review->is_approved = true;
        $review->save();

        // Dynamically update product's aggregate rating and reviews count
        $avgRating = $product->reviews()->avg('rating');
        $count = $product->reviews()->count();

        $product->update([
            'rating' => $avgRating ? round($avgRating, 2) : 5.00,
            'reviews_count' => $count,
        ]);

        return redirect()->back()->with('success', 'Thank you! Your review has been submitted successfully.');
    }

    public function quickView($id)
    {
        $product = Product::with(['category', 'reviews'])->findOrFail($id);
        return response()->json($product);
    }
}

