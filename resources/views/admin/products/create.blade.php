@extends('admin.layouts.master')

@section('title', 'Add New Product — Admin')

@section('content')

    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('admin.products.index') }}" class="text-xs font-bold text-gray-500 hover:text-primary transition-all">
            &larr; Back to Products
        </a>
        <h1 class="text-2xl font-extrabold text-gray-800 dark:text-white mt-1">
            Add New Product
        </h1>
    </div>

    <div class="panel max-w-4xl">
        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6 text-xs">
            @csrf

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                
                <!-- Title (EN) -->
                <div class="sm:col-span-2">
                    <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">Product Title (English) *</label>
                    <input type="text" name="title" required placeholder="e.g. Royal Embroidered Cotton Panjabi" value="{{ old('title') }}" 
                           class="w-full rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] p-3 text-xs font-semibold focus:border-primary focus:outline-none dark:text-white">
                </div>

                <!-- Title (BN) -->
                <div class="sm:col-span-2">
                    <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">Product Title (বাংলা)</label>
                    <input type="text" name="title_bn" placeholder="যেমন: রয়্যাল এমব্রয়ডারি কটন পাঞ্জাবি" value="{{ old('title_bn') }}" 
                           class="w-full rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] p-3 text-xs font-semibold focus:border-primary focus:outline-none dark:text-white">
                </div>

                <!-- Category -->
                <div>
                    <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">Category *</label>
                    <select name="category_id" required class="w-full rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] p-3 text-xs font-semibold focus:border-primary focus:outline-none dark:text-white">
                        <option value="">Select Category</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- SKU -->
                <div>
                    <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">SKU Code (Auto if empty)</label>
                    <input type="text" name="sku" placeholder="e.g. YF-PNJ-10" value="{{ old('sku') }}" 
                           class="w-full rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] p-3 text-xs font-semibold focus:border-primary focus:outline-none dark:text-white font-mono">
                </div>

                <!-- Regular Price -->
                <div>
                    <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">Regular Price (৳) *</label>
                    <input type="number" step="0.01" name="regular_price" required placeholder="4500" value="{{ old('regular_price') }}" 
                           class="w-full rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] p-3 text-xs font-semibold focus:border-primary focus:outline-none dark:text-white">
                </div>

                <!-- Sale Price -->
                <div>
                    <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">Sale / Discount Price (৳)</label>
                    <input type="number" step="0.01" name="sale_price" placeholder="3850" value="{{ old('sale_price') }}" 
                           class="w-full rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] p-3 text-xs font-semibold focus:border-primary focus:outline-none dark:text-white">
                </div>

                <!-- Stock Qty -->
                <div>
                    <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">Stock Quantity *</label>
                    <input type="number" name="stock_qty" required placeholder="20" value="{{ old('stock_qty', 20) }}" 
                           class="w-full rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] p-3 text-xs font-semibold focus:border-primary focus:outline-none dark:text-white">
                </div>

                <!-- Badge -->
                <div>
                    <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">Badge Tag</label>
                    <input type="text" name="badge" placeholder="e.g. Eid Special, 20% OFF" value="{{ old('badge') }}" 
                           class="w-full rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] p-3 text-xs font-semibold focus:border-primary focus:outline-none dark:text-white">
                </div>

                <!-- Thumbnail Upload (Intervention Image Auto-WebP) -->
                <div class="sm:col-span-2 p-4 rounded-xl bg-gray-50 dark:bg-[#14233c] border border-dashed border-gray-300 dark:border-[#192a43]">
                    <div class="flex items-center justify-between mb-2">
                        <label class="block font-bold text-gray-800 dark:text-white">
                            Thumbnail Image (প্রধান ছবি আপলোড করুন)
                        </label>
                        <span class="badge badge-success text-[10px]">✨ Auto WebP Optimized</span>
                    </div>
                    
                    <input type="file" name="thumbnail_file" accept="image/*" 
                           class="w-full rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] p-2 text-xs font-semibold file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-primary file:text-white hover:file:bg-primary-hover">
                    
                    <div class="mt-3 text-gray-400 text-[11px]">
                        Or enter existing image path:
                        <input type="text" name="thumbnail" placeholder="/assets/product-embroidered-panjabi.jpg" value="{{ old('thumbnail') }}" 
                               class="mt-1 w-full rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] p-2.5 text-xs font-mono dark:text-white">
                    </div>
                </div>

                <!-- Gallery Upload -->
                <div class="sm:col-span-2 p-4 rounded-xl bg-gray-50 dark:bg-[#14233c] border border-dashed border-gray-300 dark:border-[#192a43]">
                    <label class="block font-bold text-gray-800 dark:text-white mb-1">
                        Gallery Extra Images (গ্যালারি অতিরিক্ত ছবিসমূহ - একাধিক সিলেক্ট করতে পারেন)
                    </label>
                    <input type="file" name="gallery_files[]" multiple accept="image/*" 
                           class="w-full rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] p-2 text-xs font-semibold file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-secondary file:text-white hover:file:bg-secondary-hover">
                </div>

                <!-- Sizes -->
                <div class="sm:col-span-2">
                    <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">Sizes (Comma separated)</label>
                    <input type="text" name="sizes" placeholder="M (38), L (40), XL (42), XXL (44)" value="{{ old('sizes') }}" 
                           class="w-full rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] p-3 text-xs font-semibold focus:border-primary focus:outline-none dark:text-white">
                </div>

                <!-- Short Desc -->
                <div class="sm:col-span-2">
                    <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">Short Description</label>
                    <textarea name="short_desc" rows="2" placeholder="Brief summary for product cards..." 
                              class="w-full rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] p-3 text-xs font-semibold focus:border-primary focus:outline-none dark:text-white">{{ old('short_desc') }}</textarea>
                </div>

                <!-- Full HTML Description -->
                <div class="sm:col-span-2">
                    <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">Full Description & Specs (HTML)</label>
                    <textarea name="description" rows="4" placeholder="<p>Detailed product specifications...</p>" 
                              class="w-full rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] p-3 text-xs font-semibold focus:border-primary focus:outline-none dark:text-white font-mono">{{ old('description') }}</textarea>
                </div>

                <!-- Checkboxes -->
                <div class="sm:col-span-2 flex flex-wrap gap-6 pt-2">
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }} class="rounded text-primary focus:ring-primary h-4 w-4">
                        <span class="font-bold text-gray-700 dark:text-gray-300">Featured (প্রচ্ছদে দেখান)</span>
                    </label>
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_trending" value="1" {{ old('is_trending') ? 'checked' : '' }} class="rounded text-primary focus:ring-primary h-4 w-4">
                        <span class="font-bold text-gray-700 dark:text-gray-300">Trending Now</span>
                    </label>
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" checked class="rounded text-primary focus:ring-primary h-4 w-4">
                        <span class="font-bold text-gray-700 dark:text-gray-300">Active in Storefront</span>
                    </label>
                </div>

            </div>

            <div class="border-t border-gray-100 dark:border-[#192a43] pt-4">
                <button type="submit" class="rounded-lg bg-primary px-6 py-2.5 text-xs font-bold text-white shadow-md hover:bg-primary-hover transition-all">
                    Create Product & Publish
                </button>
            </div>
        </form>
    </div>

@endsection
