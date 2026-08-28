<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category')->where('is_active', true);

        // Filter by category
        if ($request->filled('category')) {
            $categorySlug = $request->query('category');
            $category = Category::where('slug', $categorySlug)->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }

        // Search query
        if ($request->filled('q')) {
            $search = $request->query('q');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('title_bn', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('short_desc', 'like', "%{$search}%");
            });
        }

        // Sort
        switch ($request->query('sort')) {
            case 'price_low':
                $query->orderByRaw('COALESCE(sale_price, regular_price) ASC');
                break;
            case 'price_high':
                $query->orderByRaw('COALESCE(sale_price, regular_price) DESC');
                break;
            case 'popular':
                $query->orderBy('reviews_count', 'desc');
                break;
            default:
                $query->latest();
                break;
        }

        $products = $query->paginate(12)->withQueryString();
        $categories = Category::where('is_active', true)->orderBy('sort_order', 'asc')->get();
        $currentCategory = $request->filled('category') ? Category::where('slug', $request->query('category'))->first() : null;

        return view('shop.index', compact('products', 'categories', 'currentCategory'));
    }

    public function searchApi(Request $request)
    {
        $keyword = trim($request->query('q', ''));
        if (strlen($keyword) < 2) {
            return response()->json([]);
        }

        $products = Product::where('is_active', true)
            ->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                  ->orWhere('title_bn', 'like', "%{$keyword}%")
                  ->orWhere('sku', 'like', "%{$keyword}%");
            })
            ->take(6)
            ->get(['id', 'title', 'title_bn', 'slug', 'thumbnail', 'regular_price', 'sale_price', 'badge']);

        return response()->json($products);
    }
}
