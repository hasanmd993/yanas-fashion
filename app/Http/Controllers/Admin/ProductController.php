<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category')->latest();

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        $products = $query->paginate(15)->withQueryString();
        $categories = Category::all();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'title_bn' => 'nullable|string|max:255',
            'sku' => 'nullable|string|unique:products,sku',
            'regular_price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'stock_qty' => 'required|integer|min:0',
            'thumbnail' => 'nullable|string',
            'thumbnail_file' => 'nullable|image|max:10240', // up to 10MB, auto-optimized
            'gallery_files.*' => 'nullable|image|max:10240',
            'sizes' => 'nullable|string', // comma separated input
            'badge' => 'nullable|string|max:50',
            'short_desc' => 'nullable|string',
            'description' => 'nullable|string',
            'is_featured' => 'nullable|boolean',
            'is_trending' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        // Process Thumbnail File Upload with WebP Optimization
        if ($request->hasFile('thumbnail_file')) {
            $validated['thumbnail'] = ImageService::uploadAndOptimize($request->file('thumbnail_file'), 'products', 1200, 82);
        } elseif (empty($validated['thumbnail'])) {
            $validated['thumbnail'] = 'assets/category-men.jpg';
        }

        // Process Gallery Files
        $gallery = [];
        if ($request->hasFile('gallery_files')) {
            foreach ($request->file('gallery_files') as $gFile) {
                $gallery[] = ImageService::uploadAndOptimize($gFile, 'products/gallery', 1200, 82);
            }
        }
        $validated['gallery'] = $gallery;

        $sizes = [];
        if ($request->filled('sizes')) {
            $sizes = array_map('trim', explode(',', $request->sizes));
        }

        $validated['sizes'] = $sizes;
        $validated['is_featured'] = $request->has('is_featured');
        $validated['is_trending'] = $request->has('is_trending');
        $validated['is_active'] = $request->has('is_active') ? true : false;
        $validated['slug'] = Str::slug($request->title) . '-' . Str::random(4);

        Product::create($validated);

        return redirect()->route('admin.products.index')->with('success', 'New product created successfully!');
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'title_bn' => 'nullable|string|max:255',
            'sku' => 'required|string|unique:products,sku,' . $product->id,
            'regular_price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'stock_qty' => 'required|integer|min:0',
            'thumbnail' => 'nullable|string',
            'thumbnail_file' => 'nullable|image|max:10240',
            'gallery_files.*' => 'nullable|image|max:10240',
            'sizes' => 'nullable|string',
            'badge' => 'nullable|string|max:50',
            'short_desc' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        // Process Thumbnail File Upload with WebP Optimization
        if ($request->hasFile('thumbnail_file')) {
            // Delete old file if it was a stored upload
            ImageService::delete($product->thumbnail);
            $validated['thumbnail'] = ImageService::uploadAndOptimize($request->file('thumbnail_file'), 'products', 1200, 82);
        } elseif (empty($validated['thumbnail'])) {
            $validated['thumbnail'] = $product->thumbnail;
        }

        // Process Gallery Files
        if ($request->hasFile('gallery_files')) {
            $gallery = $product->gallery ?? [];
            foreach ($request->file('gallery_files') as $gFile) {
                $gallery[] = ImageService::uploadAndOptimize($gFile, 'products/gallery', 1200, 82);
            }
            $validated['gallery'] = $gallery;
        }

        $sizes = [];
        if ($request->filled('sizes')) {
            $sizes = array_map('trim', explode(',', $request->sizes));
        }

        $validated['sizes'] = $sizes;
        $validated['is_featured'] = $request->has('is_featured');
        $validated['is_trending'] = $request->has('is_trending');
        $validated['is_active'] = $request->has('is_active');

        $product->update($validated);

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully!');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        ImageService::delete($product->thumbnail);
        if (is_array($product->gallery)) {
            foreach ($product->gallery as $gPath) {
                ImageService::delete($gPath);
            }
        }
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully!');
    }
}

