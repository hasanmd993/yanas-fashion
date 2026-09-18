@extends('admin.layouts.master')

@section('title', "Edit Product: {$product->title}")

@section('content')

    <!-- Header -->
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-800 dark:text-white">
                Edit Product: {{ $product->title }}
            </h1>
            <p class="text-xs text-gray-500 dark:text-gray-400">Update product specifications, pricing, inventory and media</p>
        </div>
        <a href="{{ route('admin.products.index') }}" 
           class="btn inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-xs font-bold text-white shadow-md hover:bg-primary-hover transition-all">
            <i class="fa-solid fa-arrow-left"></i> Back to Products
        </a>
    </div>

    <div class="panel">
        <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6 text-xs">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                
                <!-- Title (EN) -->
                <div class="sm:col-span-2">
                    <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">Product Title *</label>
                    <input type="text" name="title" required value="{{ old('title', $product->title) }}" 
                           class="w-full rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] p-3 text-xs font-semibold focus:border-primary focus:outline-none dark:text-white">
                </div>

                <!-- Title (BN) -->
                <div class="sm:col-span-2">
<label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">Alternative Title (optional)</label>
                    <input type="text" name="title_bn" placeholder="e.g. Alternative product title" value="{{ old('title_bn', $product->title_bn) }}"
                           class="w-full rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] p-3 text-xs font-semibold focus:border-primary focus:outline-none dark:text-white">
                </div>

                <!-- Category -->
                <div>
                    <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">Category *</label>
                    <select name="category_id" required class="w-full rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] p-3 text-xs font-semibold focus:border-primary focus:outline-none dark:text-white">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->hierarchy_path }}
                            </option>
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
                    <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">Sale / Discount Price (৳)</label>
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

                <!-- Thumbnail Upload (Intervention Image Auto-WebP) -->
                <div class="sm:col-span-2 p-4 rounded-xl bg-gray-50 dark:bg-[#14233c] border border-dashed border-gray-300 dark:border-[#192a43]">
                    <div class="flex items-center justify-between mb-2">
                        <label class="block font-bold text-gray-800 dark:text-white">
                            Thumbnail Image
                        </label>
                        <span class="badge badge-success text-[10px]">✨ Auto WebP Optimized</span>
                    </div>

                    @if($product->thumbnail)
                        <div class="flex items-center gap-3 mb-3">
                            <img src="{{ asset($product->thumbnail) }}" alt="{{ $product->title }}" class="h-16 w-16 rounded-lg object-cover border border-gray-200 dark:border-[#192a43]">
                            <div>
                                <span class="text-[11px] text-gray-400 font-mono block">{{ $product->thumbnail }}</span>
                                <span class="text-[10px] text-primary font-semibold">Current active thumbnail</span>
                            </div>
                        </div>
                    @endif
                    
                    <input type="file" name="thumbnail_file" accept="image/*" 
                           class="w-full rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] p-2 text-xs font-semibold file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-primary file:text-white hover:file:bg-primary-hover">
                    
                    <div class="mt-3 text-gray-400 text-[11px]">
                        Or keep existing path:
                        <input type="text" name="thumbnail" value="{{ old('thumbnail', $product->thumbnail) }}" 
                               class="mt-1 w-full rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] p-2.5 text-xs font-mono dark:text-white">
                    </div>
                </div>

                <!-- Gallery Upload -->
                <div class="sm:col-span-2 p-4 rounded-xl bg-gray-50 dark:bg-[#14233c] border border-dashed border-gray-300 dark:border-[#192a43]">
                    <label class="block font-bold text-gray-800 dark:text-white mb-1">
                        Gallery Extra Images
                    </label>
                    <input type="file" name="gallery_files[]" multiple accept="image/*" 
                           class="w-full rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] p-2 text-xs font-semibold file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-secondary file:text-white hover:file:bg-secondary-hover">
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

                <!-- Full HTML Description (Quill Rich Text Editor) -->
                <div class="sm:col-span-2">
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block font-bold text-gray-700 dark:text-gray-300">
                            Full Description & Specifications
                        </label>
                        <span class="text-[11px] text-gray-400 dark:text-gray-500 font-medium">
                            <i class="fa-solid fa-feather-pointed text-primary mr-1"></i>Rich Text (Visual Editor)
                        </span>
                    </div>

                    <input type="hidden" name="description" id="hidden_description" value="{{ old('description', $product->description) }}">
                    
                    <div class="quill-editor-wrapper">
                        <div id="quill-editor" class="min-h-[200px] text-sm">{!! old('description', $product->description) !!}</div>
                    </div>
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

@push('styles')
    <!-- Quill.js CSS -->
    <link href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css" rel="stylesheet">
    <style>
        .ql-toolbar.ql-snow {
            border-top-left-radius: 0.5rem;
            border-top-right-radius: 0.5rem;
            border-color: #e2e8f0;
            background-color: #f8fafc;
        }
        .ql-container.ql-snow {
            border-bottom-left-radius: 0.5rem;
            border-bottom-right-radius: 0.5rem;
            border-color: #e2e8f0;
            background-color: #ffffff;
            font-family: inherit;
            font-size: 0.8125rem;
            min-height: 220px;
        }
        .ql-editor {
            min-height: 220px;
        }
        /* Dark Theme overrides */
        .dark .ql-toolbar.ql-snow {
            background-color: #14233c;
            border-color: #192a43;
        }
        .dark .ql-container.ql-snow {
            background-color: #0e1726;
            border-color: #192a43;
            color: #e0e6ed;
        }
        .dark .ql-snow .ql-stroke {
            stroke: #94a3b8;
        }
        .dark .ql-snow .ql-fill {
            fill: #94a3b8;
        }
        .dark .ql-snow .ql-picker {
            color: #94a3b8;
        }
        .dark .ql-snow .ql-picker-options {
            background-color: #14233c;
            border-color: #192a43;
            color: #e0e6ed;
        }
        .dark .ql-editor.ql-blank::before {
            color: #64748b;
        }
    </style>
@endpush

@push('scripts')
    <!-- Quill.js Library -->
    <script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const quill = new Quill('#quill-editor', {
                theme: 'snow',
                placeholder: 'Write detailed product specifications, fabric highlights, care instructions...',
                modules: {
                    toolbar: [
                        [{ 'header': [2, 3, 4, false] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                        ['blockquote', 'code-block'],
                        [{ 'color': [] }, { 'background': [] }],
                        ['link', 'clean']
                    ]
                }
            });

            // Sync editor HTML on form submission
            const form = document.querySelector('form');
            const hiddenInput = document.getElementById('hidden_description');

            form.addEventListener('submit', function() {
                // If editor is just empty line, save empty string
                const html = quill.root.innerHTML;
                hiddenInput.value = (html === '<p><br></p>') ? '' : html;
            });
        });
    </script>
@endpush

