@extends('admin.layouts.master')

@section('title', 'Admin Analytics Dashboard')

@section('content')

    <!-- Dashboard Header -->
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-800 dark:text-white">Store Analytics & Dashboard</h1>
            <p class="text-xs text-gray-500 dark:text-gray-400">Real-time performance metrics and sales overview</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.products.create') }}" class="btn inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-xs font-bold text-white shadow-md hover:bg-primary-hover transition-all">
                <i class="fa-solid fa-plus"></i> Add Product
            </a>
            <a href="{{ route('admin.orders.index') }}" class="btn inline-flex items-center gap-2 rounded-lg bg-secondary px-4 py-2 text-xs font-bold text-white shadow-md hover:bg-secondary-hover transition-all">
                <i class="fa-solid fa-bag-shopping"></i> View Orders
            </a>
        </div>
    </div>

    <!-- 4 Key Stat Cards -->
    <div class="mb-6 grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-4">
        
        <!-- Total Revenue -->
        <div class="panel flex items-center justify-between">
            <div>
                <div class="text-xs font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500">Total Sales</div>
                <div class="mt-2 text-2xl font-black text-primary dark:text-white">৳{{ number_format($stats['total_revenue']) }}</div>
                <div class="mt-1 flex items-center text-xs text-success font-semibold">
                    <i class="fa-solid fa-arrow-trend-up mr-1"></i> Lifetime Gross
                </div>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary-light text-primary dark:bg-primary-dark-light text-xl">
                <i class="fa-solid fa-money-bill-wave"></i>
            </div>
        </div>

        <!-- Today's Sales -->
        <div class="panel flex items-center justify-between">
            <div>
                <div class="text-xs font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500">Today's Sales</div>
                <div class="mt-2 text-2xl font-black text-success">৳{{ number_format($stats['today_sales']) }}</div>
                <div class="mt-1 flex items-center text-xs text-gray-400">
                    <span>{{ date('d M, Y') }}</span>
                </div>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-success-light text-success dark:bg-success-dark-light text-xl">
                <i class="fa-solid fa-chart-line"></i>
            </div>
        </div>

        <!-- Pending Orders -->
        <div class="panel flex items-center justify-between">
            <div>
                <div class="text-xs font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500">Pending Orders</div>
                <div class="mt-2 text-2xl font-black text-secondary">{{ $stats['pending_orders'] }}</div>
                <div class="mt-1 flex items-center text-xs text-secondary font-semibold">
                    <i class="fa-solid fa-clock mr-1"></i> Requires fulfillment
                </div>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-secondary-light text-secondary dark:bg-secondary-dark-light text-xl">
                <i class="fa-solid fa-box"></i>
            </div>
        </div>

        <!-- Active Products -->
        <div class="panel flex items-center justify-between">
            <div>
                <div class="text-xs font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500">Active Products</div>
                <div class="mt-2 text-2xl font-black text-gray-800 dark:text-white">{{ $stats['total_products'] }}</div>
                <div class="mt-1 flex items-center text-xs text-gray-400">
                    <span>In Catalog</span>
                </div>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-info-light text-info dark:bg-info-dark-light text-xl">
                <i class="fa-solid fa-shirt"></i>
            </div>
        </div>

    </div>

    <!-- Charts & Analytics Section -->
    <div class="mb-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
        
        <!-- Sales Trend Chart (2 columns) -->
        <div class="panel lg:col-span-2" x-data="{ currentMetric: 'revenue' }">
            <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h3 class="text-base font-bold text-gray-800 dark:text-white">Revenue & Orders Activity</h3>
                    <p class="text-xs text-gray-400">7-Day sales performance</p>
                </div>
                
                <!-- Metric View Switcher Pills -->
                <div class="inline-flex rounded-lg bg-gray-100 dark:bg-[#14233c] p-1 text-xs">
                    <button type="button" 
                            @click="currentMetric = 'revenue'; switchChartMetric('revenue')" 
                            :class="currentMetric === 'revenue' ? 'bg-primary text-white shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'"
                            class="rounded-md px-3 py-1.5 font-bold transition-all">
                        💰 Revenue (৳)
                    </button>
                    <button type="button" 
                            @click="currentMetric = 'orders'; switchChartMetric('orders')" 
                            :class="currentMetric === 'orders' ? 'bg-secondary text-white shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'"
                            class="rounded-md px-3 py-1.5 font-bold transition-all">
                        📦 Orders
                    </button>
                    <button type="button" 
                            @click="currentMetric = 'both'; switchChartMetric('both')" 
                            :class="currentMetric === 'both' ? 'bg-info text-white shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'"
                            class="rounded-md px-3 py-1.5 font-bold transition-all">
                        📊 Combined
                    </button>
                </div>
            </div>

            <!-- Chart Container with overflow control -->
            <div class="w-full overflow-hidden relative">
                <div id="revenue-chart" class="w-full" style="min-height: 310px;"></div>
            </div>
        </div>

        <!-- Top Selling / Low Stock (1 column) -->
        <div class="panel">
            <h3 class="text-base font-bold text-gray-800 dark:text-white mb-4">Top Catalog Items</h3>
            <div class="space-y-3">
                @foreach($topProducts as $tp)
                    <div class="flex items-center justify-between p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-[#14233c] transition-all">
                        <div class="flex items-center gap-3">
                            <img src="{{ asset($tp->thumbnail) }}" alt="{{ $tp->title }}" class="h-10 w-10 rounded-lg object-cover">
                            <div>
                                <h4 class="text-xs font-bold text-gray-800 dark:text-white line-clamp-1">{{ $tp->title }}</h4>
                                <span class="text-[11px] text-gray-400">Stock: {{ $tp->stock_qty }} pcs</span>
                            </div>
                        </div>
                        <span class="text-xs font-bold text-primary dark:text-primary-light">৳{{ number_format($tp->effective_price) }}</span>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

    <!-- Recent Orders Table Panel -->
    <div class="panel">
        <div class="mb-5 flex flex-wrap items-center justify-between gap-4 border-b border-gray-100 dark:border-[#192a43] pb-4">
            <div>
                <h3 class="text-base font-bold text-gray-800 dark:text-white">Recent Orders</h3>
                <p class="text-xs text-gray-400">Latest customer orders requiring review</p>
            </div>
            <a href="{{ route('admin.orders.index') }}" class="text-xs font-bold text-primary hover:underline">
                View All Orders &rarr;
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-[#192a43] text-gray-400 font-bold uppercase text-[10px]">
                        <th class="py-3 px-4">Order #</th>
                        <th class="py-3 px-4">Customer</th>
                        <th class="py-3 px-4">Phone</th>
                        <th class="py-3 px-4">Delivery Zone</th>
                        <th class="py-3 px-4">Total</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Date</th>
                        <th class="py-3 px-4 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-[#192a43]">
                    @forelse($recentOrders as $ro)
                        <tr class="hover:bg-gray-50 dark:hover:bg-[#14233c] transition-all">
                            <td class="py-3.5 px-4 font-bold text-primary dark:text-primary-light">#{{ $ro->order_number }}</td>
                            <td class="py-3.5 px-4 font-semibold text-gray-800 dark:text-white">{{ $ro->customer_name }}</td>
                            <td class="py-3.5 px-4 text-gray-500 font-mono">{{ $ro->customer_phone }}</td>
                            <td class="py-3.5 px-4">{{ $ro->zone_label }}</td>
                            <td class="py-3.5 px-4 font-black text-gray-800 dark:text-white">৳{{ number_format($ro->total_amount) }}</td>
                            <td class="py-3.5 px-4">
                                @php
                                    $badgeClass = match($ro->order_status) {
                                        'pending' => 'badge-warning',
                                        'processing' => 'badge-info',
                                        'shipped' => 'badge-secondary',
                                        'delivered' => 'badge-success',
                                        'cancelled' => 'badge-danger',
                                        default => 'badge-dark',
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ ucfirst($ro->order_status) }}</span>
                            </td>
                            <td class="py-3.5 px-4 text-gray-400">{{ $ro->created_at->format('d M, Y') }}</td>
                            <td class="py-3.5 px-4 text-center">
                                <a href="{{ route('admin.orders.show', $ro->id) }}" class="inline-flex items-center gap-1 px-3 py-1 rounded bg-primary-light text-primary font-bold text-[11px] hover:bg-primary hover:text-white transition-all">
                                    Manage &rarr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-8 text-gray-400">No orders recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    let globalChart = null;
    const daysData = {!! json_encode($chartDays) !!};
    const revenueData = {!! json_encode($chartRevenue) !!};
    const ordersData = {!! json_encode($chartOrders) !!};

    function getChartConfig(metricMode) {
        const isDark = document.documentElement.classList.contains('dark');
        const textColor = isDark ? '#888ea8' : '#64748b';
        const borderColor = isDark ? '#192a43' : '#e0e6ed';

        let series = [];
        let colors = [];
        let yaxis = [];
        let chartType = 'area';

        if (metricMode === 'revenue') {
            series = [{
                name: 'Daily Revenue (৳)',
                data: revenueData
            }];
            colors = ['#730163'];
            chartType = 'area';
            yaxis = [{
                labels: {
                    formatter: function (val) {
                        return '৳' + Math.round(val).toLocaleString();
                    },
                    style: { colors: textColor, fontSize: '11px' }
                }
            }];
        } else if (metricMode === 'orders') {
            series = [{
                name: 'Orders Placed',
                data: ordersData
            }];
            colors = ['#F68625'];
            chartType = 'bar';
            yaxis = [{
                labels: {
                    formatter: function (val) {
                        return Math.round(val) + ' pcs';
                    },
                    style: { colors: textColor, fontSize: '11px' }
                },
                min: 0
            }];
        } else {
            // Combined mode with clean spacing
            series = [
                {
                    name: 'Revenue (৳)',
                    type: 'area',
                    data: revenueData
                },
                {
                    name: 'Orders (Count)',
                    type: 'line',
                    data: ordersData
                }
            ];
            colors = ['#730163', '#F68625'];
            chartType = 'line';
            yaxis = [
                {
                    labels: {
                        formatter: function (val) {
                            return '৳' + Math.round(val).toLocaleString();
                        },
                        style: { colors: textColor, fontSize: '11px' }
                    }
                },
                {
                    opposite: true,
                    labels: {
                        formatter: function (val) {
                            return Math.round(val);
                        },
                        style: { colors: textColor, fontSize: '11px' }
                    },
                    min: 0
                }
            ];
        }

        return {
            series: series,
            chart: {
                height: 310,
                type: chartType,
                toolbar: { show: false },
                fontFamily: 'Nunito, sans-serif',
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 400
                }
            },
            colors: colors,
            fill: {
                type: chartType === 'area' || chartType === 'line' ? 'gradient' : 'solid',
                gradient: {
                    shade: isDark ? 'dark' : 'light',
                    type: 'vertical',
                    shadeIntensity: 0.5,
                    opacityFrom: 0.45,
                    opacityTo: 0.05,
                    stops: [0, 100]
                }
            },
            plotOptions: {
                bar: {
                    borderRadius: 6,
                    columnWidth: '35%',
                }
            },
            stroke: {
                curve: 'smooth',
                width: chartType === 'bar' ? 0 : 3
            },
            markers: {
                size: chartType === 'bar' ? 0 : 4,
                strokeWidth: 2,
                hover: { size: 6 }
            },
            dataLabels: { enabled: false },
            xaxis: {
                categories: daysData,
                axisBorder: { color: borderColor },
                axisTicks: { color: borderColor },
                labels: {
                    style: {
                        colors: textColor,
                        fontSize: '11px',
                        fontWeight: 600
                    }
                }
            },
            yaxis: yaxis,
            grid: {
                borderColor: borderColor,
                strokeDashArray: 4,
                padding: {
                    left: 10,
                    right: 10,
                    top: 0,
                    bottom: 0
                }
            },
            legend: {
                position: 'top',
                horizontalAlign: 'right',
                labels: {
                    colors: isDark ? '#e0e6ed' : '#1e293b'
                }
            },
            tooltip: {
                theme: isDark ? 'dark' : 'light',
                y: {
                    formatter: function (val, { seriesIndex }) {
                        if (metricMode === 'orders' || (metricMode === 'both' && seriesIndex === 1)) {
                            return val + ' orders';
                        }
                        return '৳' + Number(val).toLocaleString();
                    }
                }
            }
        };
    }

    function initChart() {
        const chartEl = document.querySelector("#revenue-chart");
        if (!chartEl || typeof ApexCharts === 'undefined') return;

        if (globalChart) {
            globalChart.destroy();
        }

        const config = getChartConfig('revenue');
        globalChart = new ApexCharts(chartEl, config);
        globalChart.render();
    }

    function switchChartMetric(mode) {
        if (!globalChart) return;
        const newConfig = getChartConfig(mode);
        globalChart.updateOptions(newConfig, true, true);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initChart);
    } else {
        initChart();
    }
</script>
@endpush

