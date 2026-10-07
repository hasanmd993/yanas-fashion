<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
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

        return \Inertia\Inertia::render('Shop/Show', [
            'product' => $product,
            'relatedProducts' => $relatedProducts,
        ]);
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

        $cleanName = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $validated['name']);
        $cleanName = strip_tags(trim($cleanName));

        $cleanComment = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $validated['comment']);
        $cleanComment = strip_tags(trim($cleanComment));

        $email = $validated['email'] ?? null;

        // Verified buyer check: customer must have a delivered, completed, or shipped order containing this product
        $isVerifiedBuyer = false;
        if (!empty($cleanName)) {
            $isVerifiedBuyer = OrderItem::where('product_id', $product->id)
                ->whereHas('order', function ($query) use ($cleanName) {
                    $query->where('customer_name', $cleanName)
                        ->whereIn('order_status', ['delivered', 'completed', 'shipped']);
                })
                ->exists();
        }

        $review = new Review();
        $review->product_id = $product->id;
        $review->name = $cleanName;
        $review->email = $email;
        $review->rating = $validated['rating'];
        $review->comment = $cleanComment;
        $review->is_verified_buyer = $isVerifiedBuyer;
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

