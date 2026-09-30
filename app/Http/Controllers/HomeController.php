<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Slider;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $sliders = Slider::active()->get();

        // Show featured gents categories on homepage category grid
        $categories = Category::where('is_active', true)
            ->where(function ($query) {
                $query->where('is_featured', true)
                    ->orWhereHas('products');
            })
            ->orderBy('sort_order', 'asc')
            ->take(12)
            ->get();

        $featuredProducts = Product::with('category')
            ->where('is_active', true)
            ->where('is_featured', true)
            ->take(8)
            ->get();

        $trendingProducts = Product::with('category')
            ->where('is_active', true)
            ->where('is_trending', true)
            ->take(8)
            ->get();

        $latestProducts = Product::with('category')
            ->where('is_active', true)
            ->latest()
            ->take(8)
            ->get();

        return \Inertia\Inertia::render('Home', [
            'sliders' => $sliders,
            'categories' => $categories,
            'featuredProducts' => $featuredProducts,
            'trendingProducts' => $trendingProducts,
            'latestProducts' => $latestProducts,
        ]);
    }

    public function sitemap()
    {
        $path = public_path('sitemap.xml');
        if (!file_exists($path)) {
            \Illuminate\Support\Facades\Artisan::call('sitemap:generate');
        }
        return response()->file($path, ['Content-Type' => 'application/xml']);
    }
}

