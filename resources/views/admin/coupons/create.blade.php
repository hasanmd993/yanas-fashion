@extends('admin.layouts.master')

@section('title', 'Add New Coupon — Admin')

@section('content')

    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-800 dark:text-white">Add New Coupon</h1>
            <p class="text-xs text-gray-500 dark:text-gray-400">Create a new discount coupon code for your store</p>
        </div>
        <a href="{{ route('admin.coupons.index') }}" 
           class="btn inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-xs font-bold text-white shadow-md hover:bg-primary-hover transition-all">
            <i class="fa-solid fa-arrow-left"></i> Back to Coupons
        </a>
    </div>

    <div class="panel max-w-2xl">
        <form action="{{ route('admin.coupons.store') }}" method="POST" class="space-y-5 text-xs">
            @csrf

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                
                <!-- Coupon Code -->
                <div class="sm:col-span-2">
                    <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">Coupon Code *</label>
                    <input type="text" name="code" required placeholder="e.g. BOISHAKH26 or EID200" value="{{ old('code') }}" 
                           class="w-full rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] p-3 text-xs font-bold font-mono uppercase focus:border-primary focus:outline-none dark:text-white tracking-wider text-base">
                    <span class="text-[11px] text-gray-400">Characters will automatically be converted to uppercase.</span>
                </div>

                <!-- Discount Type -->
                <div>
                    <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">Discount Type *</label>
                    <select name="type" required class="w-full rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] p-3 text-xs font-semibold focus:border-primary focus:outline-none dark:text-white">
<option value="fixed" {{ old('type') === 'fixed' ? 'selected' : '' }}>Fixed Amount Discount (৳)</option>
                        <option value="percent" {{ old('type') === 'percent' ? 'selected' : '' }}>Percentage Discount (%)</option>
                    </select>
                </div>

                <!-- Value -->
                <div>
                    <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">Discount Value (৳ or %) *</label>
                    <input type="number" step="0.01" name="value" required placeholder="e.g. 200 or 15" value="{{ old('value') }}" 
                           class="w-full rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] p-3 text-xs font-bold focus:border-primary focus:outline-none dark:text-white">
                </div>

                <!-- Minimum Cart Order -->
                <div>
                    <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">Minimum Cart Subtotal (৳)</label>
                    <input type="number" step="0.01" name="min_order" placeholder="0 for no minimum" value="{{ old('min_order', 0) }}" 
                           class="w-full rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] p-3 text-xs font-semibold focus:border-primary focus:outline-none dark:text-white">
                </div>

                <!-- Expiration Date -->
                <div>
                    <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">Expiration Date & Time (Optional)</label>
                    <input type="datetime-local" name="expires_at" value="{{ old('expires_at') }}" 
                           class="w-full rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] p-3 text-xs font-semibold focus:border-primary focus:outline-none dark:text-white">
                </div>

                <!-- Checkbox -->
                <div class="sm:col-span-2 pt-2">
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" checked class="rounded text-primary focus:ring-primary h-4 w-4">
                        <span class="font-bold text-gray-700 dark:text-gray-300">Active (customers can use at checkout)</span>
                    </label>
                </div>

            </div>

            <!-- Submit -->
            <div class="flex items-center gap-3 pt-4 border-t border-gray-100 dark:border-[#192a43]">
                <button type="submit" class="btn bg-primary text-white font-bold px-6 py-2.5 rounded-lg hover:bg-primary-hover shadow-md transition-all">
                    <i class="fa-solid fa-check mr-1"></i> Save Coupon
                </button>
                <a href="{{ route('admin.coupons.index') }}" class="btn border border-gray-300 dark:border-[#192a43] px-5 py-2.5 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-50 transition-all font-semibold">
                    Cancel
                </a>
            </div>

        </form>
    </div>

@endsection

