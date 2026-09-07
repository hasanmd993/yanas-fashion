@extends('admin.layouts.master')

@section('title', 'Categories Management — Admin')

@section('content')

    <!-- Header -->
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-800 dark:text-white">Categories Management</h1>
            <p class="text-xs text-gray-500 dark:text-gray-400">Organize store collections and navigational menus</p>
        </div>
        <a href="{{ route('admin.categories.create') }}" class="btn inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-xs font-bold text-white shadow-md hover:bg-primary-hover transition-all">
            <i class="fa-solid fa-plus"></i> Add Category
        </a>
    </div>

    <!-- Categories Datatable Panel -->
    <div class="panel">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-[#192a43] text-gray-400 font-bold uppercase text-[10px]">
                        <th class="py-3.5 px-4">Category</th>
                        <th class="py-3.5 px-4">Bengali Name</th>
                        <th class="py-3.5 px-4">Slug Identifier</th>
                        <th class="py-3.5 px-4">Products</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-[#192a43]">
                    @forelse($categories as $category)
                        <tr class="hover:bg-gray-50 dark:hover:bg-[#14233c] transition-all">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    @if($category->image)
                                        <img src="{{ asset($category->image) }}" alt="{{ $category->name }}" class="h-10 w-10 rounded-lg object-cover">
                                    @endif
                                    <span class="font-bold text-gray-800 dark:text-white">{{ $category->name }}</span>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 font-semibold text-gray-600 dark:text-gray-300">
                                {{ $category->name_bn ?? '-' }}
                            </td>
                            <td class="py-3.5 px-4 font-mono text-gray-400">
                                {{ $category->slug }}
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="badge badge-info">{{ $category->products_count }} items</span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="badge {{ $category->is_active ? 'badge-success' : 'badge-danger' }}">
                                    {{ $category->is_active ? 'Active' : 'Hidden' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('admin.categories.edit', $category->id) }}" 
                                       class="p-1.5 rounded-lg bg-primary-light text-primary hover:bg-primary hover:text-white transition-all" title="Edit">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    </a>
                                    <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this category?');" class="inline">
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
                            <td colspan="6" class="text-center py-12 text-gray-400">No categories found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection

