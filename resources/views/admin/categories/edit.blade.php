@extends('admin.layouts.master')

@section('title', "Edit Category: {$category->name}")

@section('content')

    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('admin.categories.index') }}" class="text-xs font-bold text-gray-500 hover:text-primary transition-all">
            &larr; Back to Categories
        </a>
        <h1 class="text-2xl font-extrabold text-gray-800 dark:text-white mt-1">
            Edit Category: {{ $category->name }}
        </h1>
    </div>

    <div class="panel max-w-xl">
        <form action="{{ route('admin.categories.update', $category->id) }}" method="POST" class="space-y-4 text-xs">
            @csrf
            @method('PUT')

            <div>
                <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">Category Name (English) *</label>
                <input type="text" name="name" required value="{{ old('name', $category->name) }}" 
                       class="w-full rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] p-3 text-xs font-semibold focus:border-primary focus:outline-none dark:text-white">
            </div>

            <div>
                <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">Category Name (বাংলা)</label>
                <input type="text" name="name_bn" value="{{ old('name_bn', $category->name_bn) }}" 
                       class="w-full rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] p-3 text-xs font-semibold focus:border-primary focus:outline-none dark:text-white">
            </div>

            <div>
                <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">Image Path</label>
                <input type="text" name="image" value="{{ old('image', $category->image) }}" 
                       class="w-full rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] p-3 text-xs font-semibold focus:border-primary focus:outline-none dark:text-white">
            </div>

            <div>
                <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">Short Description</label>
                <textarea name="description" rows="2" 
                          class="w-full rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] p-3 text-xs font-semibold focus:border-primary focus:outline-none dark:text-white">{{ old('description', $category->description) }}</textarea>
            </div>

            <div>
                <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">Sort Order</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $category->sort_order) }}" 
                       class="w-full rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] p-3 text-xs font-semibold focus:border-primary focus:outline-none dark:text-white">
            </div>

            <div class="flex flex-wrap gap-6 pt-2">
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $category->is_featured) ? 'checked' : '' }} class="rounded text-primary focus:ring-primary h-4 w-4">
                    <span class="font-bold text-gray-700 dark:text-gray-300">Featured on Homepage</span>
                </label>
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $category->is_active) ? 'checked' : '' }} class="rounded text-primary focus:ring-primary h-4 w-4">
                    <span class="font-bold text-gray-700 dark:text-gray-300">Active</span>
                </label>
            </div>

            <div class="border-t border-gray-100 dark:border-[#192a43] pt-4 mt-6">
                <button type="submit" class="rounded-lg bg-primary px-6 py-2.5 text-xs font-bold text-white shadow-md hover:bg-primary-hover transition-all">
                    Update Category
                </button>
            </div>
        </form>
    </div>

@endsection
