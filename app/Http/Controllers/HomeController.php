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

        $categories = Category::where('is_active', true)
            ->orderBy('sort_order', 'asc')
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

        return view('home', compact(
            'sliders',
            'categories',
            'featuredProducts',
            'trendingProducts',
            'latestProducts'
        ));
    }
}
