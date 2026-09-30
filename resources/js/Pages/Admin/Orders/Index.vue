<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import {
    Search,
    FileSpreadsheet,
    FileText,
    Eye,
    Trash2,
    Truck,
    Clock,
    CheckCircle2,
    AlertCircle,
    ChevronDown,
    Printer,
    Download
} from 'lucide-vue-next';

const props = defineProps({
    orders: {
        type: Object,
        required: true,
    },
    statusCounts: {
        type: Object,
        default: () => ({}),
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
});

const searchQuery = ref(props.filters?.search || '');
const activeStatus = ref(props.filters?.status || '');

const handleFilter = (status = null) => {
    if (status !== null) activeStatus.value = status;
    router.get(route('admin.orders.index'), {
        status: activeStatus.value,
        search: searchQuery.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const handleStatusChange = (orderId, newStatus) => {
    router.patch(route('admin.orders.update_status', orderId), {
        order_status: newStatus,
        payment_status: newStatus === 'delivered' ? 'paid' : 'pending',
    }, {
        preserveScroll: true,
    });
};

const handleDelete = (id, orderNumber) => {
    if (confirm(`Are you sure you want to delete order #${orderNumber}?`)) {
        router.delete(route('admin.orders.destroy', id));
    }
};
</script>

<template>
    <Head title="Orders Management — Admin" />

    <AdminLayout title="Orders Management">
        <div class="space-y-6">
            <!-- Header & Export Buttons -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white font-serif">
                        Store Orders
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Track dispatch, customer details, and Cash on Delivery payments ({{ orders.total }} total)
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <a
                        :href="route('admin.orders.export_excel', { status: activeStatus, search: searchQuery })"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-bold hover:bg-slate-50 transition-colors shadow-sm"
                    >
                        <FileSpreadsheet class="w-4 h-4 text-emerald-600" /> Export Excel
                    </a>

                    <a
                        :href="route('admin.orders.export_pdf', { status: activeStatus, search: searchQuery, download: 1 })"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-bold hover:bg-slate-50 transition-colors shadow-sm"
                    >
                        <FileText class="w-4 h-4 text-rose-600" /> Export PDF
                    </a>
                </div>
            </div>

            <!-- Status Tabs & Search -->
            <div class="space-y-4">
                <!-- Status Filter Tabs -->
                <div class="flex items-center gap-2 overflow-x-auto pb-2">
                    <button
                        @click="handleFilter('')"
                        class="px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition-all flex items-center gap-2"
                        :class="!activeStatus ? 'bg-[#730163] text-white shadow-md shadow-[#730163]/20' : 'bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-50'"
                    >
                        <span>All Orders</span>
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] font-mono" :class="!activeStatus ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600'">
                            {{ statusCounts.all || 0 }}
                        </span>
                    </button>

                    <button
                        @click="handleFilter('pending')"
                        class="px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition-all flex items-center gap-2"
                        :class="activeStatus === 'pending' ? 'bg-amber-600 text-white shadow-md shadow-amber-600/20' : 'bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-50'"
                    >
                        <span>Pending</span>
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] font-mono bg-amber-500/20 text-amber-700">
                            {{ statusCounts.pending || 0 }}
                        </span>
                    </button>

                    <button
                        @click="handleFilter('processing')"
                        class="px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition-all flex items-center gap-2"
                        :class="activeStatus === 'processing' ? 'bg-sky-600 text-white shadow-md shadow-sky-600/20' : 'bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-50'"
                    >
                        <span>Processing</span>
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] font-mono bg-sky-500/20 text-sky-700">
                            {{ statusCounts.processing || 0 }}
                        </span>
                    </button>

                    <button
                        @click="handleFilter('shipped')"
                        class="px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition-all flex items-center gap-2"
                        :class="activeStatus === 'shipped' ? 'bg-purple-600 text-white shadow-md shadow-purple-600/20' : 'bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-50'"
                    >
                        <span>Shipped</span>
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] font-mono bg-purple-500/20 text-purple-700">
                            {{ statusCounts.shipped || 0 }}
                        </span>
                    </button>

                    <button
                        @click="handleFilter('delivered')"
                        class="px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition-all flex items-center gap-2"
                        :class="activeStatus === 'delivered' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20' : 'bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-50'"
                    >
                        <span>Delivered</span>
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] font-mono bg-emerald-500/20 text-emerald-700">
                            {{ statusCounts.delivered || 0 }}
                        </span>
                    </button>

                    <button
                        @click="handleFilter('cancelled')"
                        class="px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition-all flex items-center gap-2"
                        :class="activeStatus === 'cancelled' ? 'bg-rose-600 text-white shadow-md shadow-rose-600/20' : 'bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-50'"
                    >
                        <span>Cancelled</span>
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] font-mono bg-rose-500/20 text-rose-700">
                            {{ statusCounts.cancelled || 0 }}
                        </span>
                    </button>
                </div>

                <!-- Search Form -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 p-3 shadow-sm flex items-center gap-3">
                    <div class="relative flex-1">
                        <input
                            v-model="searchQuery"
                            @keyup.enter="handleFilter()"
                            type="text"
                            placeholder="Search by Order ID, Customer Name, or Phone..."
                            class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:outline-none focus:ring-2 focus:ring-[#730163]/20"
                        />
                        <Search class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" />
                    </div>
                    <button
                        @click="handleFilter()"
                        class="px-5 py-2 rounded-xl bg-slate-900 dark:bg-white text-white dark:text-slate-900 text-xs font-bold hover:opacity-90 transition-opacity"
                    >
                        Search
                    </button>
                </div>
            </div>

            <!-- Orders Table -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-slate-100 dark:border-slate-800 text-slate-400 uppercase font-bold text-[10px] bg-slate-50/50 dark:bg-slate-800/30">
                                <th class="py-3.5 px-4">Order ID</th>
                                <th class="py-3.5 px-3">Date</th>
                                <th class="py-3.5 px-3">Customer</th>
                                <th class="py-3.5 px-3">Zone</th>
                                <th class="py-3.5 px-3">Items</th>
                                <th class="py-3.5 px-3">Total Payable</th>
                                <th class="py-3.5 px-3">Order Status</th>
                                <th class="py-3.5 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            <tr
                                v-for="order in orders.data"
                                :key="order.id"
                                class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors"
                            >
                                <td class="py-3.5 px-4">
                                    <Link
                                        :href="route('admin.orders.show', order.id)"
                                        class="font-mono font-bold text-[#730163] hover:underline"
                                    >
                                        #{{ order.order_number }}
                                    </Link>
                                </td>
                                <td class="py-3.5 px-3 text-slate-500 whitespace-nowrap">
                                    {{ new Date(order.created_at).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', hour: '2-digit', minute: '2-digit' }) }}
                                </td>
                                <td class="py-3.5 px-3">
                                    <div class="font-bold text-slate-900 dark:text-white">{{ order.customer_name }}</div>
                                    <div class="text-[10px] text-slate-400 font-mono">{{ order.customer_phone }}</div>
                                </td>
                                <td class="py-3.5 px-3 text-slate-500 capitalize whitespace-nowrap">
                                    {{ order.delivery_zone?.replace('_', ' ') }}
                                </td>
                                <td class="py-3.5 px-3">
                                    <span class="badge px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-[11px] font-bold">
                                        {{ order.items?.length || 0 }} items
                                    </span>
                                </td>
                                <td class="py-3.5 px-3 font-mono font-bold text-slate-900 dark:text-white whitespace-nowrap">
                                    ৳{{ Number(order.total_amount).toLocaleString() }}
                                </td>
                                <td class="py-3.5 px-3">
                                    <!-- Inline Status Select -->
                                    <select
                                        :value="order.order_status"
                                        @change="handleStatusChange(order.id, $event.target.value)"
                                        class="text-[11px] font-bold px-2.5 py-1 rounded-lg border focus:outline-none capitalize"
                                        :class="{
                                            'bg-amber-50 text-amber-700 border-amber-200': order.order_status === 'pending',
                                            'bg-sky-50 text-sky-700 border-sky-200': order.order_status === 'processing',
                                            'bg-purple-50 text-purple-700 border-purple-200': order.order_status === 'shipped',
                                            'bg-emerald-50 text-emerald-700 border-emerald-200': order.order_status === 'delivered',
                                            'bg-rose-50 text-rose-700 border-rose-200': order.order_status === 'cancelled',
                                        }"
                                    >
                                        <option value="pending">Pending</option>
                                        <option value="processing">Processing</option>
                                        <option value="shipped">Shipped</option>
                                        <option value="delivered">Delivered</option>
                                        <option value="cancelled">Cancelled</option>
                                    </select>
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <Link
                                            :href="route('admin.orders.show', order.id)"
                                            class="p-2 rounded-xl text-slate-400 hover:text-[#730163] hover:bg-purple-50 dark:hover:bg-purple-950/40 transition-colors"
                                            title="View Order Details"
                                        >
                                            <Eye class="w-4 h-4" />
                                        </Link>

                                        <a
                                            :href="route('admin.orders.download_invoice', order.id)"
                                            class="p-2 rounded-xl text-slate-400 hover:text-slate-800 hover:bg-slate-100 transition-colors"
                                            title="Download Invoice PDF"
                                        >
                                            <Download class="w-4 h-4" />
                                        </a>

                                        <button
                                            @click="handleDelete(order.id, order.order_number)"
                                            class="p-2 rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition-colors"
                                            title="Delete Order"
                                        >
                                            <Trash2 class="w-4 h-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr v-if="orders.data.length === 0">
                                <td colspan="8" class="py-12 text-center text-slate-400">
                                    No orders found matching your search.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="orders.links && orders.links.length > 3" class="p-4 border-t border-slate-100 dark:border-slate-800 flex justify-center items-center gap-1.5">
                    <template v-for="(link, index) in orders.links" :key="index">
                        <Link
                            v-if="link.url"
                            :href="link.url"
                            class="px-3 py-1.5 rounded-xl text-xs font-bold transition-colors"
                            :class="link.active ? 'bg-[#730163] text-white shadow-sm' : 'border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-50'"
                            v-html="link.label"
                        />
                        <span
                            v-else
                            class="px-3 py-1.5 text-xs text-slate-300 dark:text-slate-700"
                            v-html="link.label"
                        />
                    </template>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
