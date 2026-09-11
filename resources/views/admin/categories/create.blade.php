@extends('admin.layouts.master')

@section('title', 'Add New Category — Admin')

@section('content')

    <!-- Header -->
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-800 dark:text-white">
                Add New Category
            </h1>
            <p class="text-xs text-gray-500 dark:text-gray-400">Create a new product collection</p>
        </div>
        <a href="{{ route('admin.categories.index') }}"
            class="btn inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-xs font-bold text-white shadow-md hover:bg-primary-hover transition-all">
            <i class="fa-solid fa-arrow-left"></i> Back to Categories
        </a>
    </div>

    <div class="panel max-w-xxl">
        <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data"
            class="space-y-4 text-xs">
            @csrf

            <!-- Parent Category (Hierarchy) -->
            <div>
                <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">Parent Category</label>
                <select name="parent_id"
                    class="w-full rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] p-3 text-xs font-semibold focus:border-primary focus:outline-none dark:text-white">
                    <option value="">&mdash; None (Top-Level Parent Category) &mdash;</option>
                    @foreach($parentCategories as $parent)
                        <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                            📁 {{ $parent->name }} (Parent)
                        </option>
                        @foreach($parent->children as $sub)
                            <option value="{{ $sub->id }}" {{ old('parent_id') == $sub->id ? 'selected' : '' }}>
                                &nbsp;&nbsp;&nbsp;&nbsp;&rdsh; {{ $sub->name }} (Subcategory)
                            </option>
                        @endforeach
                    @endforeach
                </select>
                <p class="text-[11px] text-gray-400 mt-1">Leave empty to create a top-level Parent Category, or pick a
                    parent to create a Subcategory or Child Category.</p>
            </div>

            <div>
                <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">Category Name *</label>
                <input type="text" name="name" required placeholder="e.g. Festive Panjabi" value="{{ old('name') }}"
                    class="w-full rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] p-3 text-xs font-semibold focus:border-primary focus:outline-none dark:text-white">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">Alternative / Bengali Name
                        (optional)</label>
                    <input type="text" name="name_bn" placeholder="e.g. উৎসব পাঞ্জাবি" value="{{ old('name_bn') }}"
                        class="w-full rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] p-3 text-xs font-semibold focus:border-primary focus:outline-none dark:text-white">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">Icon Class (optional)</label>
                    <input type="text" name="icon" placeholder="e.g. fa-solid fa-shirt" value="{{ old('icon') }}"
                        class="w-full rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] p-3 text-xs font-semibold focus:border-primary focus:outline-none dark:text-white font-mono">
                </div>
            </div>

            <div
                class="p-3.5 rounded-xl bg-gray-50 dark:bg-[#14233c] border border-dashed border-gray-300 dark:border-[#192a43]">
                <div class="flex items-center justify-between mb-1.5">
                    <label class="block font-bold text-gray-800 dark:text-white">
                        Category Image
                    </label>
                    <span class="badge badge-success text-[10px]">✨ Auto WebP</span>
                </div>
                <input type="file" name="image_file" accept="image/*"
                    class="w-full rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] p-2 text-xs font-semibold file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-primary file:text-white hover:file:bg-primary-hover">

                <div class="mt-2 text-gray-400 text-[11px]">
                    Or enter image path:
                    <input type="text" name="image" placeholder="/assets/category-men.jpg"
                        value="{{ old('image', '/assets/category-men.jpg') }}"
                        class="mt-1 w-full rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] p-2 text-xs font-mono dark:text-white">
                </div>
            </div>

            <div>
                <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">Short Description</label>
                <textarea name="description" rows="2" placeholder="Brief summary of category..."
                    class="w-full rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] p-3 text-xs font-semibold focus:border-primary focus:outline-none dark:text-white">{{ old('description') }}</textarea>
            </div>

            <div>
                <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">Sort Order (0 = First)</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}"
                    class="w-full rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] p-3 text-xs font-semibold focus:border-primary focus:outline-none dark:text-white">
            </div>

            <div class="flex flex-wrap gap-6 pt-2">
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_featured" value="1" checked
                        class="rounded text-primary focus:ring-primary h-4 w-4">
                    <span class="font-bold text-gray-700 dark:text-gray-300">Featured on Homepage</span>
                </label>
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" checked
                        class="rounded text-primary focus:ring-primary h-4 w-4">
                    <span class="font-bold text-gray-700 dark:text-gray-300">Active</span>
                </label>
            </div>

            <div class="border-t border-gray-100 dark:border-[#192a43] pt-4 mt-6">
                <button type="submit"
                    class="rounded-lg bg-primary px-6 py-2.5 text-xs font-bold text-white shadow-md hover:bg-primary-hover transition-all">
                    Create Category
                </button>
            </div>
        </form>
    </div>

@endsection