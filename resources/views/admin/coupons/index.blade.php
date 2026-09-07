@extends('admin.layouts.master')

@section('title', 'Coupons & Discounts — Admin')

@section('content')

    <!-- Header -->
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-800 dark:text-white">Coupons & Campaigns (কুপন ও অফার)</h1>
            <p class="text-xs text-gray-500 dark:text-gray-400">Manage promotional discount codes, validity, and cart rules</p>
        </div>
        <a href="{{ route('admin.coupons.create') }}" class="btn inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-xs font-bold text-white shadow-md hover:bg-primary-hover transition-all">
            <i class="fa-solid fa-plus"></i> Add New Coupon (নতুন কুপন যোগ করুন)
        </a>
    </div>

    <!-- Stats Bar -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3 mb-6">
        <div class="panel p-4 flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 font-semibold">Total Campaigns</p>
                <h4 class="text-xl font-bold text-gray-800 dark:text-white mt-1">{{ $totalCoupons }}</h4>
            </div>
            <div class="h-10 w-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center text-lg">
                <i class="fa-solid fa-ticket"></i>
            </div>
        </div>
        <div class="panel p-4 flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 font-semibold">Active Coupons</p>
                <h4 class="text-xl font-bold text-success mt-1">{{ $activeCoupons }}</h4>
            </div>
            <div class="h-10 w-10 rounded-xl bg-success/10 text-success flex items-center justify-center text-lg">
                <i class="fa-solid fa-circle-check"></i>
            </div>
        </div>
        <div class="panel p-4 flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 font-semibold">Disabled / Expired</p>
                <h4 class="text-xl font-bold text-gray-400 mt-1">{{ $totalCoupons - $activeCoupons }}</h4>
            </div>
            <div class="h-10 w-10 rounded-xl bg-gray-100 dark:bg-[#14233c] text-gray-400 flex items-center justify-center text-lg">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
        </div>
    </div>

    <!-- Coupons Table Panel -->
    <div class="panel">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-[#192a43] text-gray-400 uppercase font-semibold">
                        <th class="pb-3 px-3">Coupon Code</th>
                        <th class="pb-3 px-3">Discount Type & Value</th>
                        <th class="pb-3 px-3">Min Order</th>
                        <th class="pb-3 px-3">Expires At</th>
                        <th class="pb-3 px-3">Status</th>
                        <th class="pb-3 px-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-[#192a43]">
                    @forelse($coupons as $coupon)
                        <tr class="hover:bg-gray-50 dark:hover:bg-[#14233c]/50 transition-colors">
                            <td class="py-3 px-3">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md bg-purple-50 dark:bg-[#1f1b3c] text-primary font-mono font-bold text-xs border border-primary/20">
                                    <i class="fa-solid fa-tag text-[10px]"></i> {{ $coupon->code }}
                                </span>
                            </td>
                            <td class="py-3 px-3">
                                @if($coupon->type === 'percent')
                                    <span class="font-bold text-secondary text-sm">{{ $coupon->value }}% OFF</span>
                                    <span class="text-[10px] text-gray-400 block">Percentage Discount</span>
                                @else
                                    <span class="font-bold text-primary text-sm">৳{{ number_format($coupon->value) }} OFF</span>
                                    <span class="text-[10px] text-gray-400 block">Fixed Amount</span>
                                @endif
                            </td>
                            <td class="py-3 px-3 font-semibold">
                                @if($coupon->min_order > 0)
                                    ৳{{ number_format($coupon->min_order) }}
                                @else
                                    <span class="text-gray-400">No Minimum</span>
                                @endif
                            </td>
                            <td class="py-3 px-3">
                                @if($coupon->expires_at)
                                    <span class="{{ $coupon->expires_at->isPast() ? 'text-danger font-bold' : 'text-gray-700 dark:text-gray-300' }}">
                                        {{ $coupon->expires_at->format('d M, Y') }}
                                    </span>
                                    @if($coupon->expires_at->isPast())
                                        <span class="badge badge-outline-danger text-[10px] ml-1">Expired</span>
                                    @endif
                                @else
                                    <span class="text-gray-400">Never (স্থায়ী)</span>
                                @endif
                            </td>
                            <td class="py-3 px-3">
                                @if($coupon->is_active && (!$coupon->expires_at || !$coupon->expires_at->isPast()))
                                    <span class="badge badge-success text-[10px]">Active</span>
                                @else
                                    <span class="badge badge-danger text-[10px]">Inactive</span>
                                @endif
                            </td>
                            <td class="py-3 px-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.coupons.edit', $coupon->id) }}" 
                                       class="h-8 w-8 rounded-lg bg-gray-100 dark:bg-[#14233c] text-gray-600 dark:text-gray-300 hover:bg-primary hover:text-white flex items-center justify-center transition-all" title="Edit">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    </a>
                                    <form action="{{ route('admin.coupons.destroy', $coupon->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete coupon {{ $coupon->code }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="h-8 w-8 rounded-lg bg-red-50 dark:bg-red-950/40 text-danger hover:bg-danger hover:text-white flex items-center justify-center transition-all" title="Delete">
                                            <i class="fa-solid fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-400">
                                <i class="fa-solid fa-ticket text-3xl mb-2 block"></i>
                                No coupons created yet. Click "Add New Coupon" to start.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $coupons->links() }}
        </div>
    </div>

@endsection

