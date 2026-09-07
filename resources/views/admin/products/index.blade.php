@extends('admin.layouts.master')

@section('title', 'Product Catalog — Admin')

@section('content')

    <!-- Header -->
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-800 dark:text-white">Product Catalog</h1>
            <p class="text-xs text-gray-500 dark:text-gray-400">Manage apparel inventory, pricing, and sizing</p>
        </div>
        <a href="{{ route('admin.products.create') }}" class="btn inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-xs font-bold text-white shadow-md hover:bg-primary-hover transition-all">
            <i class="fa-solid fa-plus"></i> Add New Product
        </a>
    </div>

    <!-- Filters & Search Bar Panel -->
    <div class="panel mb-6">
        <form action="{{ route('admin.products.index') }}" method="GET" class="flex flex-wrap items-center justify-between gap-4">
            
            <div class="flex items-center gap-3">
                <select name="category_id" onchange="this.form.submit()" 
                        class="h-9 rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] px-3 text-xs font-semibold focus:border-primary focus:outline-none dark:text-white">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="relative">
                <input type="text" name="search" placeholder="Search product or SKU..." value="{{ request('search') }}" 
                       class="h-9 w-64 rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] px-3.5 pr-8 text-xs font-semibold focus:border-primary focus:outline-none dark:text-white">
                <button type="submit" class="absolute right-2.5 top-2.5 text-gray-400 hover:text-primary">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </button>
            </div>

        </form>
    </div>

    <!-- Products Datatable Panel -->
    <div class="panel">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-[#192a43] text-gray-400 font-bold uppercase text-[10px]">
                        <th class="py-3.5 px-4">Product</th>
                        <th class="py-3.5 px-4">Category</th>
                        <th class="py-3.5 px-4">Price (৳)</th>
                        <th class="py-3.5 px-4">Stock</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-[#192a43]">
                    @forelse($products as $product)
                        <tr class="hover:bg-gray-50 dark:hover:bg-[#14233c] transition-all">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <img src="{{ asset($product->thumbnail) }}" alt="{{ $product->title }}" class="h-12 w-12 rounded-lg object-cover">
                                    <div>
                                        <a href="{{ route('product.show', $product->slug) }}" target="_blank" class="font-bold text-gray-800 dark:text-white hover:text-primary dark:hover:text-primary-light block">
                                            {{ $product->title }}
                                        </a>
                                        <span class="text-[11px] text-gray-400 font-mono">SKU: {{ $product->sku }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 font-semibold text-gray-600 dark:text-gray-300">
                                {{ $product->category->name ?? 'Uncategorized' }}
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-black text-gray-800 dark:text-white block">৳{{ number_format($product->effective_price) }}</span>
                                @if($product->sale_price)
                                    <span class="text-[11px] text-gray-400 line-through">৳{{ number_format($product->regular_price) }}</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-bold {{ $product->stock_qty <= 5 ? 'text-danger' : 'text-success' }}">
                                    {{ $product->stock_qty }} pcs
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="badge {{ $product->is_active ? 'badge-success' : 'badge-danger' }}">
                                    {{ $product->is_active ? 'Active' : 'Draft' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('product.show', $product->slug) }}" target="_blank" 
                                       class="p-1.5 rounded-lg bg-gray-100 dark:bg-[#14233c] text-gray-600 hover:text-primary dark:text-gray-300 transition-all" title="View Live">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </a>
                                    <a href="{{ route('admin.products.edit', $product->id) }}" 
                                       class="p-1.5 rounded-lg bg-primary-light text-primary hover:bg-primary hover:text-white transition-all" title="Edit">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    </a>
                                    <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this product?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg bg-danger-light text-danger hover:bg-danger hover:text-white transition-all" title="Delete">
                                            <i class="fa-solid fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-12 text-gray-400">
                                No products found in catalog.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-5 border-t border-gray-100 dark:border-[#192a43] pt-4">
            {{ $products->links() }}
        </div>
    </div>

@endsection

