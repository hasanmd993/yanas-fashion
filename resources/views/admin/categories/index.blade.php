@extends('admin.layouts.master')

@section('title', 'Categories Hierarchy Management — Admin')

@section('content')

    <!-- Header -->
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-800 dark:text-white">Categories Management</h1>
            <p class="text-xs text-gray-500 dark:text-gray-400">Manage 3-Tier Hierarchy: Parent Categories &rarr; Subcategories &rarr; Child Categories</p>
        </div>
        <a href="{{ route('admin.categories.create') }}" class="btn inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-xs font-bold text-white shadow-md hover:bg-primary-hover transition-all">
            <i class="fa-solid fa-plus"></i> Add Category
        </a>
    </div>

    <!-- Categories Hierarchy Panel -->
    <div class="panel">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-[#192a43] text-gray-400 font-bold uppercase text-[10px]">
                        <th class="py-3.5 px-4">Category Name</th>
                        <th class="py-3.5 px-4">Tier / Level</th>
                        <th class="py-3.5 px-4">Products</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-[#192a43]">
                    @forelse($categories as $category)
                        @php
                            $level = $category->level;
                        @endphp
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-[#14233c] transition-all {{ $level === 0 ? 'bg-gray-50/40 dark:bg-[#101b2d]' : '' }}">
                            <!-- Category Name & Tree Indicator -->
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-2.5" style="padding-left: {{ $level * 22 }}px;">
                                    @if($level > 0)
                                        <span class="text-gray-300 dark:text-gray-600 font-mono text-sm leading-none">&rdsh;</span>
                                    @endif

                                    @if($category->image)
                                        <img src="{{ asset($category->image) }}" alt="{{ $category->name }}" class="h-8 w-8 rounded-lg object-cover border border-gray-200 dark:border-[#192a43]">
                                    @elseif($category->icon)
                                        <span class="h-8 w-8 rounded-lg bg-gray-100 dark:bg-[#1b2e4b] flex items-center justify-center text-primary dark:text-primary-light">
                                            <i class="{{ $category->icon }}"></i>
                                        </span>
                                    @else
                                        <span class="h-8 w-8 rounded-lg bg-gray-100 dark:bg-[#1b2e4b] flex items-center justify-center text-gray-400">
                                            <i class="fa-solid fa-folder"></i>
                                        </span>
                                    @endif

                                    <div>
                                        <span class="font-bold {{ $level === 0 ? 'text-sm text-gray-900 dark:text-white' : ($level === 1 ? 'text-xs text-gray-800 dark:text-gray-200' : 'text-xs text-gray-600 dark:text-gray-400') }}">
                                            {{ $category->name }}
                                        </span>
                                        @if($category->name_bn)
                                            <span class="block text-[11px] text-gray-400 font-normal">{{ $category->name_bn }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Tier Level Badge -->
                            <td class="py-3.5 px-4">
                                @if($level === 0)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-primary/10 text-primary dark:bg-primary/20 dark:text-white px-2.5 py-0.5 text-[10px] font-black uppercase tracking-wider">
                                        <i class="fa-solid fa-crown text-[9px]"></i> Parent
                                    </span>
                                @elseif($level === 1)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-secondary/10 text-secondary dark:bg-secondary/20 dark:text-amber-300 px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider">
                                        <i class="fa-solid fa-folder-tree text-[9px]"></i> Subcategory
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 dark:bg-[#1b2e4b] text-gray-600 dark:text-gray-300 px-2.5 py-0.5 text-[10px] font-medium uppercase tracking-wider">
                                        <i class="fa-solid fa-tag text-[9px]"></i> Child
                                    </span>
                                @endif
                            </td>

                            <!-- Products Count -->
                            <td class="py-3.5 px-4">
                                <span class="badge badge-info">{{ $category->products_count }} items</span>
                            </td>

                            <!-- Active Status -->
                            <td class="py-3.5 px-4">
                                <span class="badge {{ $category->is_active ? 'badge-success' : 'badge-danger' }}">
                                    {{ $category->is_active ? 'Active' : 'Hidden' }}
                                </span>
                            </td>

                            <!-- Actions -->
                            <td class="py-3.5 px-4 text-center">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('admin.categories.edit', $category->id) }}" 
                                       class="p-1.5 rounded-lg bg-primary-light text-primary hover:bg-primary hover:text-white transition-all" title="Edit Category">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    </a>
                                    <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this category?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg bg-danger-light text-danger hover:bg-danger hover:text-white transition-all" title="Delete Category">
                                            <i class="fa-solid fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-12 text-gray-400">No categories found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection
