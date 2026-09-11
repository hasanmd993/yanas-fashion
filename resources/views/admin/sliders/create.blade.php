@extends('admin.layouts.master')

@section('title', 'Add New Hero Slide — Admin')

@section('content')

    <!-- Header -->
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-800 dark:text-white">
                Add New Hero Slide
            </h1>
            <p class="text-xs text-gray-500 dark:text-gray-400">Add a new homepage slider banner</p>
        </div>
        <a href="{{ route('admin.sliders.index') }}" 
           class="btn inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-xs font-bold text-white shadow-md hover:bg-primary-hover transition-all">
            <i class="fa-solid fa-arrow-left"></i> Back to Sliders
        </a>
    </div>

    <div class="panel max-w-3xl">
        <form action="{{ route('admin.sliders.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5 text-xs">
            @csrf

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                
                <!-- Tag / Eyebrow -->
                <div class="sm:col-span-2">
                    <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">Tag / Eyebrow Text</label>
                    <input type="text" name="tag" placeholder="e.g. Pohela Boishakh & Festive Edit 2026" value="{{ old('tag') }}" 
                           class="w-full rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] p-3 text-xs font-semibold focus:border-primary focus:outline-none dark:text-white">
                </div>

                <!-- Main Heading -->
                <div class="sm:col-span-2">
                    <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">Main Heading (Title) *</label>
                    <input type="text" name="title" required placeholder="e.g. Heritage Craft, Modern Elegance" value="{{ old('title') }}" 
                           class="w-full rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] p-3 text-xs font-semibold focus:border-primary focus:outline-none dark:text-white text-base">
                </div>

                <!-- Subtitle -->
                <div class="sm:col-span-2">
                    <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">Subtitle / Description</label>
                    <textarea name="subtitle" rows="3" placeholder="Description or special offer details..." 
                              class="w-full rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] p-3 text-xs font-semibold focus:border-primary focus:outline-none dark:text-white">{{ old('subtitle') }}</textarea>
                </div>

                <!-- Background Image Upload (Intervention Image Auto-WebP) -->
                <div class="sm:col-span-2 p-4 rounded-xl bg-gray-50 dark:bg-[#14233c] border border-dashed border-gray-300 dark:border-[#192a43]">
                    <div class="flex items-center justify-between mb-2">
                        <label class="block font-bold text-gray-800 dark:text-white">
                            Background Banner Image
                        </label>
                        <span class="badge badge-success text-[10px]">✨ Auto WebP 1920px Optimized</span>
                    </div>

                    <input type="file" name="image_file" accept="image/*" 
                           class="w-full rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] p-2 text-xs font-semibold file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-primary file:text-white hover:file:bg-primary-hover">
                    
                    <div class="mt-3 text-gray-400 text-[11px]">
                        Or enter existing image path:
                        <input type="text" name="image" placeholder="/assets/hero.jpg" value="{{ old('image', '/assets/hero.jpg') }}" 
                               class="mt-1 w-full rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] p-2.5 text-xs font-mono dark:text-white">
                    </div>
                </div>

                <!-- Primary Button Text -->
                <div>
                    <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">Primary Button Text *</label>
                    <input type="text" name="button_text" required placeholder="Explore Collection" value="{{ old('button_text', 'Explore Collection') }}" 
                           class="w-full rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] p-3 text-xs font-semibold focus:border-primary focus:outline-none dark:text-white">
                </div>

                <!-- Primary Button Link -->
                <div>
                    <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">Primary Button Link *</label>
                    <input type="text" name="button_link" required placeholder="/shop" value="{{ old('button_link', '/shop') }}" 
                           class="w-full rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] p-3 text-xs font-semibold focus:border-primary focus:outline-none dark:text-white font-mono">
                </div>

                <!-- Secondary Button Text -->
                <div>
                    <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">Secondary Button Text (Optional)</label>
                    <input type="text" name="secondary_button_text" placeholder="Panjabi Collection" value="{{ old('secondary_button_text') }}" 
                           class="w-full rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] p-3 text-xs font-semibold focus:border-primary focus:outline-none dark:text-white">
                </div>

                <!-- Secondary Button Link -->
                <div>
                    <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">Secondary Button Link</label>
                    <input type="text" name="secondary_button_link" placeholder="/shop?category=festive-panjabi" value="{{ old('secondary_button_link') }}" 
                           class="w-full rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] p-3 text-xs font-semibold focus:border-primary focus:outline-none dark:text-white font-mono">
                </div>

                <!-- Sort Order -->
                <div>
                    <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">Sort Order (1 = First)</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', 1) }}" 
                           class="w-full rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] p-3 text-xs font-semibold focus:border-primary focus:outline-none dark:text-white">
                </div>

                <!-- Active Toggle -->
                <div class="flex items-center pt-6">
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" checked class="rounded text-primary focus:ring-primary h-4 w-4">
                        <span class="font-bold text-gray-700 dark:text-gray-300">Active</span>
                    </label>
                </div>

            </div>

            <div class="border-t border-gray-100 dark:border-[#192a43] pt-4 mt-6">
                <button type="submit" class="rounded-lg bg-primary px-6 py-2.5 text-xs font-bold text-white shadow-md hover:bg-primary-hover transition-all">
                    Create Hero Slide
                </button>
            </div>
        </form>
    </div>

@endsection

