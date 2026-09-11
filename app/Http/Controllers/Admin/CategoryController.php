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
        $categories = Category::with(['parent', 'children'])
            ->withCount('products')
            ->orderBy('sort_order', 'asc')
            ->get();

        // Sort them hierarchically: Roots first, then their children, then grandchildren
        $sortedCategories = collect();
        $roots = $categories->whereNull('parent_id')->sortBy('sort_order');
        foreach ($roots as $root) {
            $sortedCategories->push($root);
            $subs = $categories->where('parent_id', $root->id)->sortBy('sort_order');
            foreach ($subs as $sub) {
                $sortedCategories->push($sub);
                $children = $categories->where('parent_id', $sub->id)->sortBy('sort_order');
                foreach ($children as $child) {
                    $sortedCategories->push($child);
                }
            }
        }

        // Add any remaining unparented categories if any
        $remaining = $categories->diff($sortedCategories);
        $categories = $sortedCategories->concat($remaining);

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        $parentCategories = Category::with('children')
            ->whereNull('parent_id')
            ->orderBy('sort_order', 'asc')
            ->get();

        return view('admin.categories.create', compact('parentCategories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'parent_id' => 'nullable|exists:categories,id',
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

        $validated['parent_id'] = $request->filled('parent_id') ? $request->parent_id : null;
        $validated['slug'] = Str::slug($request->name);

        // Ensure unique slug
        $baseSlug = $validated['slug'];
        $count = 1;
        while (Category::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = "{$baseSlug}-{$count}";
            $count++;
        }

        $validated['is_featured'] = $request->has('is_featured');
        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = $request->sort_order ?? 0;

        Category::create($validated);

        return redirect()->route('admin.categories.index')->with('success', 'Category created successfully!');
    }

    public function edit($id)
    {
        $category = Category::with('parent')->findOrFail($id);
        $excludeIds = $category->getAllDescendantIds();

        $parentCategories = Category::with('children')
            ->whereNull('parent_id')
            ->whereNotIn('id', $excludeIds)
            ->orderBy('sort_order', 'asc')
            ->get();

        return view('admin.categories.edit', compact('category', 'parentCategories', 'excludeIds'));
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'parent_id' => 'nullable|exists:categories,id',
            'name' => 'required|string|max:255',
            'name_bn' => 'nullable|string|max:255',
            'image' => 'nullable|string',
            'image_file' => 'nullable|image|max:8192',
            'icon' => 'nullable|string',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
        ]);

        // Prevent circular parenting
        if ($request->filled('parent_id')) {
            $descendantIds = $category->getAllDescendantIds();
            if (in_array($request->parent_id, $descendantIds)) {
                return back()->withErrors(['parent_id' => 'A category cannot have itself or one of its descendants as its parent.'])->withInput();
            }
            $validated['parent_id'] = $request->parent_id;
        } else {
            $validated['parent_id'] = null;
        }

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

        return redirect()->route('admin.categories.index')->with('success', 'Category updated successfully!');
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        ImageService::delete($category->image);
        $category->delete();
        return redirect()->route('admin.categories.index')->with('success', 'Category deleted successfully!');
    }
}
