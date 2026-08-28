@extends('admin.layouts.master')

@section('title', "Edit Coupon '{$coupon->code}' — Admin")

@section('content')

    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <a href="{{ route('admin.coupons.index') }}" class="text-xs font-bold text-gray-500 hover:text-primary transition-all">
                &larr; Back to Coupons
            </a>
            <h1 class="text-2xl font-extrabold text-gray-800 dark:text-white mt-1">Edit Coupon '{{ $coupon->code }}'</h1>
        </div>
    </div>

    <div class="panel max-w-2xl">
        <form action="{{ route('admin.coupons.update', $coupon->id) }}" method="POST" class="space-y-5 text-xs">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                
                <!-- Coupon Code -->
                <div class="sm:col-span-2">
                    <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">Coupon Code *</label>
                    <input type="text" name="code" required value="{{ old('code', $coupon->code) }}" 
                           class="w-full rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] p-3 text-xs font-bold font-mono uppercase focus:border-primary focus:outline-none dark:text-white tracking-wider text-base">
                </div>

                <!-- Discount Type -->
                <div>
                    <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">Discount Type *</label>
                    <select name="type" required class="w-full rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] p-3 text-xs font-semibold focus:border-primary focus:outline-none dark:text-white">
                        <option value="fixed" {{ old('type', $coupon->type) === 'fixed' ? 'selected' : '' }}>Fixed Amount Discount (৳ টাকা ছাড়)</option>
                        <option value="percent" {{ old('type', $coupon->type) === 'percent' ? 'selected' : '' }}>Percentage Discount (% পার্সেন্টেজ ছাড়)</option>
                    </select>
                </div>

                <!-- Value -->
                <div>
                    <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">Discount Value (৳ or %) *</label>
                    <input type="number" step="0.01" name="value" required value="{{ old('value', $coupon->value) }}" 
                           class="w-full rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] p-3 text-xs font-bold focus:border-primary focus:outline-none dark:text-white">
                </div>

                <!-- Minimum Cart Order -->
                <div>
                    <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">Minimum Cart Subtotal (৳)</label>
                    <input type="number" step="0.01" name="min_order" value="{{ old('min_order', $coupon->min_order) }}" 
                           class="w-full rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] p-3 text-xs font-semibold focus:border-primary focus:outline-none dark:text-white">
                </div>

                <!-- Expiration Date -->
                <div>
                    <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">Expiration Date & Time (Optional)</label>
                    <input type="datetime-local" name="expires_at" value="{{ old('expires_at', $coupon->expires_at ? $coupon->expires_at->format('Y-m-d\TH:i') : '') }}" 
                           class="w-full rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] p-3 text-xs font-semibold focus:border-primary focus:outline-none dark:text-white">
                </div>

                <!-- Checkbox -->
                <div class="sm:col-span-2 pt-2">
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $coupon->is_active) ? 'checked' : '' }} class="rounded text-primary focus:ring-primary h-4 w-4">
                        <span class="font-bold text-gray-700 dark:text-gray-300">Active (সক্রিয় রাখুন - গ্রাহকরা চেকআউটে ব্যবহার করতে পারবেন)</span>
                    </label>
                </div>

            </div>

            <!-- Submit -->
            <div class="flex items-center gap-3 pt-4 border-t border-gray-100 dark:border-[#192a43]">
                <button type="submit" class="btn bg-primary text-white font-bold px-6 py-2.5 rounded-lg hover:bg-primary-hover shadow-md transition-all">
                    <i class="fa-solid fa-check mr-1"></i> Update Coupon (আপডেট করুন)
                </button>
                <a href="{{ route('admin.coupons.index') }}" class="btn border border-gray-300 dark:border-[#192a43] px-5 py-2.5 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-50 transition-all font-semibold">
                    Cancel
                </a>
            </div>

        </form>
    </div>

@endsection
