@extends('admin.layouts.master')

@section('title', "Edit Product: {$product->title}")

@section('content')

    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('admin.products.index') }}" class="text-xs font-bold text-gray-500 hover:text-primary transition-all">
            &larr; Back to Products
        </a>
        <h1 class="text-2xl font-extrabold text-gray-800 dark:text-white mt-1">
            Edit Product: {{ $product->title }}
        </h1>
    </div>

    <div class="panel max-w-4xl">
        <form action="{{ route('admin.products.update', $product->id) }}" method="POST" class="space-y-6 text-xs">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                
                <!-- Title (EN) -->
                <div class="sm:col-span-2">
                    <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">Product Title (English) *</label>
                    <input type="text" name="title" required value="{{ old('title', $product->title) }}" 
                           class="w-full rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] p-3 text-xs font-semibold focus:border-primary focus:outline-none dark:text-white">
                </div>

                <!-- Title (BN) -->
                <div class="sm:col-span-2">
                    <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">Product Title (বাংলা)</label>
                    <input type="text" name="title_bn" value="{{ old('title_bn', $product->title_bn) }}" 
                           class="w-full rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] p-3 text-xs font-semibold focus:border-primary focus:outline-none dark:text-white">
                </div>

                <!-- Category -->
                <div>
                    <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">Category *</label>
                    <select name="category_id" required class="w-full rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] p-3 text-xs font-semibold focus:border-primary focus:outline-none dark:text-white">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- SKU -->
                <div>
                    <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">SKU Code *</label>
                    <input type="text" name="sku" required value="{{ old('sku', $product->sku) }}" 
                           class="w-full rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] p-3 text-xs font-semibold focus:border-primary focus:outline-none dark:text-white font-mono">
                </div>

                <!-- Regular Price -->
                <div>
                    <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">Regular Price (৳) *</label>
                    <input type="number" step="0.01" name="regular_price" required value="{{ old('regular_price', $product->regular_price) }}" 
                           class="w-full rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] p-3 text-xs font-semibold focus:border-primary focus:outline-none dark:text-white">
                </div>

                <!-- Sale Price -->
                <div>
                    <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">Sale Price (৳)</label>
                    <input type="number" step="0.01" name="sale_price" value="{{ old('sale_price', $product->sale_price) }}" 
                           class="w-full rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] p-3 text-xs font-semibold focus:border-primary focus:outline-none dark:text-white">
                </div>

                <!-- Stock Qty -->
                <div>
                    <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">Stock Quantity *</label>
                    <input type="number" name="stock_qty" required value="{{ old('stock_qty', $product->stock_qty) }}" 
                           class="w-full rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] p-3 text-xs font-semibold focus:border-primary focus:outline-none dark:text-white">
                </div>

                <!-- Badge -->
                <div>
                    <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">Badge Tag</label>
                    <input type="text" name="badge" value="{{ old('badge', $product->badge) }}" 
                           class="w-full rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] p-3 text-xs font-semibold focus:border-primary focus:outline-none dark:text-white">
                </div>

                <!-- Thumbnail -->
                <div class="sm:col-span-2">
                    <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">Thumbnail Image Path *</label>
                    <input type="text" name="thumbnail" required value="{{ old('thumbnail', $product->thumbnail) }}" 
                           class="w-full rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] p-3 text-xs font-semibold focus:border-primary focus:outline-none dark:text-white">
                </div>

                <!-- Sizes -->
                <div class="sm:col-span-2">
                    <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">Sizes (Comma separated)</label>
                    <input type="text" name="sizes" value="{{ old('sizes', is_array($product->sizes) ? implode(', ', $product->sizes) : '') }}" 
                           class="w-full rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] p-3 text-xs font-semibold focus:border-primary focus:outline-none dark:text-white">
                </div>

                <!-- Short Description -->
                <div class="sm:col-span-2">
                    <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">Short Description</label>
                    <textarea name="short_desc" rows="2" 
                              class="w-full rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] p-3 text-xs font-semibold focus:border-primary focus:outline-none dark:text-white">{{ old('short_desc', $product->short_desc) }}</textarea>
                </div>

                <!-- Full HTML Description -->
                <div class="sm:col-span-2">
                    <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">Full Description & Specs (HTML)</label>
                    <textarea name="description" rows="4" 
                              class="w-full rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] p-3 text-xs font-semibold focus:border-primary focus:outline-none dark:text-white font-mono">{{ old('description', $product->description) }}</textarea>
                </div>

                <!-- Checkboxes -->
                <div class="sm:col-span-2 flex flex-wrap gap-6 pt-2">
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }} class="rounded text-primary focus:ring-primary h-4 w-4">
                        <span class="font-bold text-gray-700 dark:text-gray-300">Featured</span>
                    </label>
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_trending" value="1" {{ old('is_trending', $product->is_trending) ? 'checked' : '' }} class="rounded text-primary focus:ring-primary h-4 w-4">
                        <span class="font-bold text-gray-700 dark:text-gray-300">Trending Now</span>
                    </label>
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }} class="rounded text-primary focus:ring-primary h-4 w-4">
                        <span class="font-bold text-gray-700 dark:text-gray-300">Active</span>
                    </label>
                </div>

            </div>

            <div class="border-t border-gray-100 dark:border-[#192a43] pt-4">
                <button type="submit" class="rounded-lg bg-primary px-6 py-2.5 text-xs font-bold text-white shadow-md hover:bg-primary-hover transition-all">
                    Update Product
                </button>
            </div>
        </form>
    </div>

@endsection
