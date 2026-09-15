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
        $currentCategory = null;
        $activeParentCategory = null;

        // Filter by category (including all descendants)
        if ($request->filled('category')) {
            $categorySlug = $request->query('category');
            $currentCategory = Category::with('parent.parent')->where('slug', $categorySlug)->first();

            if ($currentCategory) {
                $descendantIds = $currentCategory->getAllDescendantIds();
                $query->whereIn('category_id', $descendantIds);

                // Find top-level root parent category
                $cursor = $currentCategory;
                while ($cursor->parent) {
                    $cursor = $cursor->parent;
                }
                $activeParentCategory = $cursor;
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

        // 3-Tier Hierarchical Categories for Storefront
        $parentCategories = Category::whereNull('parent_id')
            ->where('is_active', true)
            ->with(['activeChildren.activeChildren'])
            ->orderBy('sort_order', 'asc')
            ->get();

        $categories = Category::where('is_active', true)->orderBy('sort_order', 'asc')->get();

        return view('shop.index', compact('products', 'categories', 'currentCategory', 'parentCategories', 'activeParentCategory'));
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
