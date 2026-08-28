<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')->orderBy('sort_order', 'asc')->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_bn' => 'nullable|string|max:255',
            'image' => 'nullable|string',
            'image_file' => 'nullable|image|max:8192',
            'icon' => 'nullable|string',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
        ]);

        if ($request->hasFile('image_file')) {
            $validated['image'] = ImageService::uploadAndOptimize($request->file('image_file'), 'categories', 600, 80);
        } elseif (empty($validated['image'])) {
            $validated['image'] = 'assets/category-men.jpg';
        }

        $validated['slug'] = Str::slug($request->name);
        $validated['is_featured'] = $request->has('is_featured');
        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = $request->sort_order ?? 0;

        Category::create($validated);

        return redirect()->route('admin.categories.index')->with('success', 'ক্যাটাগরি সফলভাবে তৈরি হয়েছে!');
    }

    public function edit($id)
    {
        $category = Category::findOrFail($id);
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_bn' => 'nullable|string|max:255',
            'image' => 'nullable|string',
            'image_file' => 'nullable|image|max:8192',
            'icon' => 'nullable|string',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
        ]);

        if ($request->hasFile('image_file')) {
            ImageService::delete($category->image);
            $validated['image'] = ImageService::uploadAndOptimize($request->file('image_file'), 'categories', 600, 80);
        } elseif (empty($validated['image'])) {
            $validated['image'] = $category->image;
        }

        $validated['is_featured'] = $request->has('is_featured');
        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = $request->sort_order ?? 0;

        $category->update($validated);

        return redirect()->route('admin.categories.index')->with('success', 'ক্যাটাগরি সফলভাবে আপডেট হয়েছে!');
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        ImageService::delete($category->image);
        $category->delete();
        return redirect()->route('admin.categories.index')->with('success', 'ক্যাটাগরি মুছে ফেলা হয়েছে!');
    }
}
