@extends('admin.layouts.master')

@section('title', 'Hero Sliders Management — Admin')

@section('content')

    <!-- Header -->
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-800 dark:text-white">Hero Sliders Management</h1>
            <p class="text-xs text-gray-500 dark:text-gray-400">Control homepage banner slides, texts, and promotion links</p>
        </div>
        <a href="{{ route('admin.sliders.create') }}" class="btn inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-xs font-bold text-white shadow-md hover:bg-primary-hover transition-all">
            <i class="fa-solid fa-plus"></i> Add New Slide
        </a>
    </div>

    <!-- Sliders Datatable Panel -->
    <div class="panel">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-[#192a43] text-gray-400 font-bold uppercase text-[10px]">
                        <th class="py-3.5 px-4">Slide Preview</th>
                        <th class="py-3.5 px-4">Tag & Heading</th>
                        <th class="py-3.5 px-4">Primary Button</th>
                        <th class="py-3.5 px-4">Order</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-[#192a43]">
                    @forelse($sliders as $slider)
                        <tr class="hover:bg-gray-50 dark:hover:bg-[#14233c] transition-all">
                            <td class="py-3.5 px-4">
                                <img src="{{ asset($slider->image) }}" alt="{{ $slider->title }}" class="h-14 w-28 rounded-lg object-cover shadow-sm">
                            </td>
                            <td class="py-3.5 px-4">
                                @if($slider->tag)
                                    <span class="badge badge-warning text-[9px] mb-1">{{ $slider->tag }}</span>
                                @endif
                                <span class="font-bold text-gray-800 dark:text-white block text-sm">{{ $slider->title }}</span>
                                <span class="text-[11px] text-gray-400 line-clamp-1 max-w-sm">{{ $slider->subtitle }}</span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-primary dark:text-primary-light block">{{ $slider->button_text }}</span>
                                <span class="text-[10px] text-gray-400 font-mono">{{ $slider->button_link }}</span>
                            </td>
                            <td class="py-3.5 px-4 font-bold text-gray-600 dark:text-gray-300">
                                #{{ $slider->sort_order }}
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="badge {{ $slider->is_active ? 'badge-success' : 'badge-danger' }}">
                                    {{ $slider->is_active ? 'Active' : 'Disabled' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('admin.sliders.edit', $slider->id) }}" 
                                       class="p-1.5 rounded-lg bg-primary-light text-primary hover:bg-primary hover:text-white transition-all" title="Edit">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    </a>
                                    <form action="{{ route('admin.sliders.destroy', $slider->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this slide?');" class="inline">
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
                                No slides found. Click "Add New Slide" to create one.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection

