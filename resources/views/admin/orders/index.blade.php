@extends('admin.layouts.master')

@section('title', 'Orders Management — Admin')

@section('content')

    <!-- Header -->
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-800 dark:text-white">Orders Management (অর্ডারসমূহ)</h1>
            <p class="text-xs text-gray-500 dark:text-gray-400">Track, process, export and fulfill customer orders</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.orders.export_excel', ['status' => request('status', 'all')]) }}" 
               class="btn inline-flex items-center gap-2 rounded-lg bg-[#107c41] px-3.5 py-2 text-xs font-bold text-white shadow-sm hover:bg-[#0b5c30] transition-all" title="Export all filtered orders to Excel (.xlsx)">
                <i class="fa-solid fa-file-excel"></i> Export Excel
            </a>
            <a href="{{ route('admin.orders.export_courier', ['status' => request('status', 'pending')]) }}" 
               class="btn inline-flex items-center gap-2 rounded-lg bg-secondary px-3.5 py-2 text-xs font-bold text-white shadow-sm hover:bg-secondary-hover transition-all" title="Bulk Courier Format for Steadfast / Pathao">
                <i class="fa-solid fa-truck-fast"></i> Steadfast / Pathao CSV
            </a>
        </div>
    </div>

    <!-- Filters & Search Bar Panel -->
    <div class="panel mb-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            
            <!-- Status Tabs -->
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.orders.index') }}" 
                   class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all {{ !request('status') ? 'bg-primary text-white shadow-sm' : 'bg-gray-100 dark:bg-[#14233c] text-gray-600 dark:text-gray-300 hover:bg-gray-200' }}">
                    All ({{ $statusCounts['all'] }})
                </a>
                <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" 
                   class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all {{ request('status') === 'pending' ? 'bg-secondary text-white shadow-sm' : 'bg-gray-100 dark:bg-[#14233c] text-gray-600 dark:text-gray-300 hover:bg-gray-200' }}">
                    Pending ({{ $statusCounts['pending'] }})
                </a>
                <a href="{{ route('admin.orders.index', ['status' => 'processing']) }}" 
                   class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all {{ request('status') === 'processing' ? 'bg-info text-white shadow-sm' : 'bg-gray-100 dark:bg-[#14233c] text-gray-600 dark:text-gray-300 hover:bg-gray-200' }}">
                    Processing ({{ $statusCounts['processing'] }})
                </a>
                <a href="{{ route('admin.orders.index', ['status' => 'shipped']) }}" 
                   class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all {{ request('status') === 'shipped' ? 'bg-primary text-white shadow-sm' : 'bg-gray-100 dark:bg-[#14233c] text-gray-600 dark:text-gray-300 hover:bg-gray-200' }}">
                    In Transit ({{ $statusCounts['shipped'] }})
                </a>
                <a href="{{ route('admin.orders.index', ['status' => 'delivered']) }}" 
                   class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all {{ request('status') === 'delivered' ? 'bg-success text-white shadow-sm' : 'bg-gray-100 dark:bg-[#14233c] text-gray-600 dark:text-gray-300 hover:bg-gray-200' }}">
                    Delivered ({{ $statusCounts['delivered'] }})
                </a>
            </div>

            <!-- Search Form -->
            <form action="{{ route('admin.orders.index') }}" method="GET" class="flex items-center gap-2">
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                <div class="relative">
                    <input type="text" name="search" placeholder="Search phone, name, #..." value="{{ request('search') }}" 
                           class="h-9 w-64 rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] px-3.5 pr-8 text-xs font-semibold focus:border-primary focus:outline-none dark:text-white">
                    <button type="submit" class="absolute right-2.5 top-2.5 text-gray-400 hover:text-primary">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </button>
                </div>
            </form>

        </div>
    </div>

    <!-- Orders Datatable Panel -->
    <div class="panel">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-[#192a43] text-gray-400 font-bold uppercase text-[10px]">
                        <th class="py-3.5 px-4">Order #</th>
                        <th class="py-3.5 px-4">Customer</th>
                        <th class="py-3.5 px-4">Phone</th>
                        <th class="py-3.5 px-4">Delivery Zone</th>
                        <th class="py-3.5 px-4">Total Amount</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4">Date</th>
                        <th class="py-3.5 px-4 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-[#192a43]">
                    @forelse($orders as $order)
                        <tr class="hover:bg-gray-50 dark:hover:bg-[#14233c] transition-all">
                            <td class="py-3.5 px-4 font-bold text-primary dark:text-primary-light">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="hover:underline">
                                    #{{ $order->order_number }}
                                </a>
                            </td>
                            <td class="py-3.5 px-4 font-semibold text-gray-800 dark:text-white">{{ $order->customer_name }}</td>
                            <td class="py-3.5 px-4 font-mono text-gray-500">{{ $order->customer_phone }}</td>
                            <td class="py-3.5 px-4">{{ $order->zone_label }}</td>
                            <td class="py-3.5 px-4 font-black text-gray-800 dark:text-white">৳{{ number_format($order->total_amount) }}</td>
                            <td class="py-3.5 px-4">
                                @php
                                    $badgeClass = match($order->order_status) {
                                        'pending' => 'badge-warning',
                                        'processing' => 'badge-info',
                                        'shipped' => 'badge-secondary',
                                        'delivered' => 'badge-success',
                                        'cancelled' => 'badge-danger',
                                        default => 'badge-dark',
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ ucfirst($order->order_status) }}</span>
                            </td>
                            <td class="py-3.5 px-4 text-gray-400">{{ $order->created_at->format('d M, Y h:i A') }}</td>
                            <td class="py-3.5 px-4 text-center">
                                <a href="{{ route('admin.orders.show', $order->id) }}" 
                                   class="inline-flex items-center gap-1 px-3 py-1 rounded-lg bg-primary-light text-primary font-bold text-xs hover:bg-primary hover:text-white transition-all">
                                    Manage &rarr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-12 text-gray-400">
                                <i class="fa-solid fa-inbox text-3xl mb-2 block"></i>
                                No orders found matching criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-5 border-t border-gray-100 dark:border-[#192a43] pt-4">
            {{ $orders->links() }}
        </div>
    </div>

@endsection

