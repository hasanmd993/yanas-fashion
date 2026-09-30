<script setup>
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import {
    DollarSign,
    ShoppingBag,
    ShoppingCart,
    AlertTriangle,
    TrendingUp,
    Clock,
    CheckCircle2,
    Package,
    ArrowUpRight,
    Plus,
    ExternalLink,
    Database
} from 'lucide-vue-next';
import SalesTrendChart from '@/Components/Admin/SalesTrendChart.vue';

const props = defineProps({
    stats: {
        type: Object,
        required: true,
    },
    recentOrders: {
        type: Array,
        default: () => [],
    },
    topProducts: {
        type: Array,
        default: () => [],
    },
    chartDays: {
        type: Array,
        default: () => [],
    },
    chartRevenue: {
        type: Array,
        default: () => [],
    },
    chartOrders: {
        type: Array,
        default: () => [],
    },
});

const maxRevenue = computed(() => {
    return Math.max(...props.chartRevenue, 1);
});
</script>

<template>
    <Head title="Executive Overview — Admin" />

    <AdminLayout title="Executive Overview">
        <div class="space-y-8">
            <!-- Header Quick Actions -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white font-serif">
                        Performance Overview
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">Real-time revenue metrics, order dispatch, and store health</p>
                </div>

                <div class="flex flex-wrap items-center gap-2.5">
                    <Link
                        :href="route('admin.products.create')"
                        class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-[#730163] hover:bg-[#5b014e] text-white text-xs font-bold shadow-md shadow-[#730163]/25 transition-all hover:scale-105"
                    >
                        <Plus class="w-4 h-4" /> Add Product
                    </Link>

                    <Link
                        :href="route('admin.orders.index')"
                        class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white text-xs font-bold hover:bg-slate-50 transition-colors shadow-sm"
                    >
                        <ShoppingCart class="w-4 h-4 text-[#F68625]" /> View Orders
                    </Link>

                    <Link
                        :href="route('admin.backups.index')"
                        class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-slate-900 dark:bg-white text-white dark:text-slate-900 text-xs font-bold hover:opacity-90 transition-opacity shadow-sm"
                    >
                        <Database class="w-4 h-4" /> System Backup
                    </Link>
                </div>
            </div>

            <!-- 4 Key Stat Cards Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <!-- Card 1: Total Revenue -->
                <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 shadow-sm flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Gross Sales</span>
                        <div class="w-10 h-10 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 flex items-center justify-center">
                            <DollarSign class="w-5 h-5" />
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="text-2xl font-extrabold text-slate-900 dark:text-white font-mono">
                            ৳{{ Number(stats.total_revenue || 0).toLocaleString() }}
                        </div>
                        <div class="flex items-center gap-1.5 text-[11px] text-emerald-600 font-semibold mt-1">
                            <TrendingUp class="w-3.5 h-3.5" />
                            <span>৳{{ Number(stats.today_sales || 0).toLocaleString() }} earned today</span>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Total Orders -->
                <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 shadow-sm flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Orders</span>
                        <div class="w-10 h-10 rounded-2xl bg-rose-50 dark:bg-rose-950/40 text-rose-600 flex items-center justify-center">
                            <ShoppingCart class="w-5 h-5" />
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="text-2xl font-extrabold text-slate-900 dark:text-white font-mono">
                            {{ stats.total_orders || 0 }}
                        </div>
                        <div class="flex items-center gap-1.5 text-[11px] text-amber-600 font-semibold mt-1">
                            <Clock class="w-3.5 h-3.5" />
                            <span>{{ stats.pending_orders || 0 }} pending dispatch</span>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Active Products -->
                <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 shadow-sm flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Store Catalog</span>
                        <div class="w-10 h-10 rounded-2xl bg-purple-50 dark:bg-purple-950/40 text-[#730163] flex items-center justify-center">
                            <ShoppingBag class="w-5 h-5" />
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="text-2xl font-extrabold text-slate-900 dark:text-white font-mono">
                            {{ stats.total_products || 0 }}
                        </div>
                        <div class="text-[11px] text-slate-500 mt-1 font-medium">
                            Live luxury apparel items
                        </div>
                    </div>
                </div>

                <!-- Card 4: Low Stock Alert -->
                <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 shadow-sm flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Inventory Health</span>
                        <div class="w-10 h-10 rounded-2xl bg-amber-50 dark:bg-amber-950/40 text-amber-600 flex items-center justify-center">
                            <AlertTriangle class="w-5 h-5" />
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="text-2xl font-extrabold text-slate-900 dark:text-white font-mono">
                            {{ stats.low_stock_products || 0 }}
                        </div>
                        <div class="text-[11px] text-amber-600 font-semibold mt-1">
                            {{ stats.low_stock_products > 0 ? 'Items below 5 stock limit' : 'All items well stocked' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- 7-Day Sales & Revenue Trend Chart Component -->
            <SalesTrendChart
                :days="chartDays"
                :revenue="chartRevenue"
                :orders="chartOrders"
            />

            <!-- 2-Column Grid: Recent Orders & Top Products -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <!-- Recent Orders Table (Col 1-8) -->
                <div class="lg:col-span-8 bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-6 shadow-sm space-y-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Recent Store Orders</h3>
                            <p class="text-xs text-slate-400">Latest customer purchases across districts</p>
                        </div>
                        <Link :href="route('admin.orders.index')" class="text-xs font-bold text-[#730163] hover:underline">
                            View All Orders →
                        </Link>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead>
                                <tr class="border-b border-slate-100 dark:border-slate-800 text-slate-400 uppercase font-bold text-[10px]">
                                    <th class="pb-3 px-2">Order ID</th>
                                    <th class="pb-3 px-2">Customer</th>
                                    <th class="pb-3 px-2">Zone</th>
                                    <th class="pb-3 px-2">Amount</th>
                                    <th class="pb-3 px-2">Status</th>
                                    <th class="pb-3 px-2 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                <tr
                                    v-for="order in recentOrders"
                                    :key="order.id"
                                    class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors"
                                >
                                    <td class="py-3 px-2 font-mono font-bold text-slate-900 dark:text-white">
                                        #{{ order.order_number }}
                                    </td>
                                    <td class="py-3 px-2">
                                        <div class="font-bold text-slate-800 dark:text-white">{{ order.customer_name }}</div>
                                        <div class="text-[10px] text-slate-400 font-mono">{{ order.customer_phone }}</div>
                                    </td>
                                    <td class="py-3 px-2 text-slate-500 capitalize">
                                        {{ order.delivery_zone?.replace('_', ' ') }}
                                    </td>
                                    <td class="py-3 px-2 font-mono font-bold text-slate-900 dark:text-white">
                                        ৳{{ Number(order.total_amount).toLocaleString() }}
                                    </td>
                                    <td class="py-3 px-2">
                                        <span
                                            class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider"
                                            :class="{
                                                'bg-amber-500/10 text-amber-600': order.order_status === 'pending',
                                                'bg-sky-500/10 text-sky-600': order.order_status === 'processing',
                                                'bg-purple-500/10 text-purple-600': order.order_status === 'shipped',
                                                'bg-emerald-500/10 text-emerald-600': order.order_status === 'delivered',
                                                'bg-rose-500/10 text-rose-600': order.order_status === 'cancelled',
                                            }"
                                        >
                                            {{ order.order_status }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-2 text-right">
                                        <Link
                                            :href="route('admin.orders.index')"
                                            class="p-1.5 rounded-lg text-slate-400 hover:text-[#730163] hover:bg-slate-100 transition-colors inline-flex"
                                        >
                                            <ArrowUpRight class="w-4 h-4" />
                                        </Link>
                                    </td>
                                </tr>
                                <tr v-if="recentOrders.length === 0">
                                    <td colspan="6" class="py-8 text-center text-slate-400">
                                        No recent orders found.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Top Products & Quick Catalog (Col 9-12) -->
                <div class="lg:col-span-4 bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-6 shadow-sm space-y-5">
                    <div class="flex items-center justify-between">
                        <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Featured Catalog</h3>
                        <Link :href="route('admin.products.index')" class="text-xs font-bold text-[#730163] hover:underline">
                            View Catalog →
                        </Link>
                    </div>

                    <div class="space-y-3 divide-y divide-slate-100 dark:divide-slate-800">
                        <div
                            v-for="prod in topProducts"
                            :key="prod.id"
                            class="pt-3 first:pt-0 flex items-center justify-between gap-3"
                        >
                            <div class="flex items-center gap-3 min-w-0">
                                <img
                                    :src="prod.primary_image || (prod.thumbnail ? (prod.thumbnail.startsWith('http') ? prod.thumbnail : (prod.thumbnail.startsWith('/') ? prod.thumbnail : `/${prod.thumbnail}`)) : '/images/placeholder.jpg')"
                                    :alt="prod.title"
                                    class="w-12 h-14 object-cover rounded-xl border border-slate-100 dark:border-slate-800 shrink-0"
                                />
                                <div class="min-w-0">
                                    <h4 class="text-xs font-bold text-slate-800 dark:text-white truncate">
                                        {{ prod.title }}
                                    </h4>
                                    <p class="text-[11px] font-mono text-slate-400">৳{{ prod.effective_price || prod.selling_price || prod.regular_price }}</p>
                                </div>
                            </div>

                            <span
                                class="text-[10px] font-mono font-bold px-2 py-0.5 rounded-lg shrink-0"
                                :class="prod.stock_qty <= 5 ? 'bg-amber-500/10 text-amber-600' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300'"
                            >
                                {{ prod.stock_qty || 0 }} in stock
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
