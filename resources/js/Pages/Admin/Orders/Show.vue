<script setup>
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import {
    ArrowLeft,
    Download,
    Printer,
    Save,
    User,
    Phone,
    MapPin,
    Package,
    ShieldCheck,
    CreditCard,
    MessageCircle
} from 'lucide-vue-next';

const props = defineProps({
    order: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    order_status: props.order.order_status || 'pending',
    payment_status: props.order.payment_status || 'pending',
    admin_notes: props.order.admin_notes || '',
});

const updateStatus = () => {
    form.patch(route('admin.orders.update_status', props.order.id));
};
</script>

<template>
    <Head :title="`Order #${order.order_number} Details — Admin`" />

    <AdminLayout :title="`Order #${order.order_number}`">
        <div class="max-w-5xl mx-auto space-y-6">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <Link
                        :href="route('admin.orders.index')"
                        class="p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-50 transition-colors"
                    >
                        <ArrowLeft class="w-4 h-4" />
                    </Link>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-xl font-extrabold text-slate-900 dark:text-white font-mono">
                                #{{ order.order_number }}
                            </h2>
                            <span
                                class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider"
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
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Placed on {{ new Date(order.created_at).toLocaleString() }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <a
                        :href="route('admin.orders.download_invoice', order.id)"
                        class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-bold hover:bg-slate-50 transition-colors shadow-sm"
                    >
                        <Download class="w-4 h-4 text-[#730163]" /> Invoice PDF
                    </a>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <!-- Left: Line Items & Customer Details (Col 1-7) -->
                <div class="lg:col-span-7 space-y-6">
                    <!-- Customer Information Card -->
                    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-6 shadow-sm space-y-4">
                        <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-900 dark:text-white pb-3 border-b border-slate-100 dark:border-slate-800">
                            Customer & Delivery Info
                        </h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                            <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800 space-y-1">
                                <div class="text-[10px] text-slate-400 font-bold uppercase">Customer Name</div>
                                <div class="font-bold text-slate-900 dark:text-white flex items-center gap-1.5">
                                    <User class="w-3.5 h-3.5 text-[#730163]" /> {{ order.customer_name }}
                                </div>
                            </div>

                            <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800 space-y-1">
                                <div class="text-[10px] text-slate-400 font-bold uppercase">Contact Phone</div>
                                <a :href="`tel:${order.customer_phone}`" class="font-bold text-[#730163] flex items-center gap-1.5 font-mono hover:underline">
                                    <Phone class="w-3.5 h-3.5" /> {{ order.customer_phone }}
                                </a>
                            </div>

                            <div class="sm:col-span-2 p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800 space-y-1">
                                <div class="text-[10px] text-slate-400 font-bold uppercase">Shipping Address</div>
                                <div class="text-slate-700 dark:text-slate-300 flex items-start gap-1.5">
                                    <MapPin class="w-3.5 h-3.5 text-[#730163] shrink-0 mt-0.5" />
                                    <span>{{ order.customer_address }} ({{ order.delivery_zone?.replace('_', ' ') }})</span>
                                </div>
                            </div>

                            <div v-if="order.customer_note" class="sm:col-span-2 p-3.5 rounded-2xl bg-amber-50/60 text-amber-900 text-xs">
                                <strong>Customer Instruction:</strong> {{ order.customer_note }}
                            </div>
                        </div>
                    </div>

                    <!-- Ordered Items List -->
                    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-6 shadow-sm space-y-4">
                        <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-900 dark:text-white pb-3 border-b border-slate-100 dark:border-slate-800">
                            Purchased Items ({{ order.items?.length || 0 }})
                        </h3>

                        <div class="divide-y divide-slate-100 dark:divide-slate-800">
                            <div
                                v-for="item in order.items"
                                :key="item.id"
                                class="py-3.5 first:pt-0 flex items-center justify-between gap-4"
                            >
                                <div class="flex items-center gap-3 min-w-0">
                                    <img
                                        :src="item.product_thumbnail ? (item.product_thumbnail.startsWith('http') ? item.product_thumbnail : (item.product_thumbnail.startsWith('/') ? item.product_thumbnail : (item.product_thumbnail.startsWith('assets/') ? `/${item.product_thumbnail}` : `/storage/${item.product_thumbnail}`))) : '/images/placeholder.jpg'"
                                        :alt="item.product_name"
                                        class="w-12 h-14 object-cover rounded-xl border border-slate-100 dark:border-slate-800 shrink-0"
                                    />
                                    <div class="min-w-0">
                                        <h4 class="text-xs font-bold text-slate-900 dark:text-white truncate">{{ item.product_name }}</h4>
                                        <p class="text-[11px] text-slate-400">
                                            Qty: {{ item.quantity }} <span v-if="item.size">• Size: {{ item.size }}</span>
                                        </p>
                                    </div>
                                </div>

                                <div class="text-xs font-bold font-mono text-slate-900 dark:text-white shrink-0">
                                    ৳{{ Number(item.total_price).toLocaleString() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Status Management & Financials (Col 8-12) -->
                <div class="lg:col-span-5 space-y-6">
                    <!-- Status Control Card -->
                    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-6 shadow-sm space-y-5">
                        <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-900 dark:text-white pb-3 border-b border-slate-100 dark:border-slate-800">
                            Order Status Management
                        </h3>

                        <form @submit.prevent="updateStatus" class="space-y-4 text-xs">
                            <div>
                                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">
                                    Delivery / Fulfillment Status
                                </label>
                                <select
                                    v-model="form.order_status"
                                    class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 font-bold capitalize focus:outline-none"
                                >
                                    <option value="pending">Pending</option>
                                    <option value="processing">Processing</option>
                                    <option value="shipped">Shipped</option>
                                    <option value="delivered">Delivered</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">
                                    Payment Status
                                </label>
                                <select
                                    v-model="form.payment_status"
                                    class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 font-bold capitalize focus:outline-none"
                                >
                                    <option value="pending">Pending (Unpaid)</option>
                                    <option value="paid">Paid (Received)</option>
                                    <option value="failed">Failed</option>
                                </select>
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">
                                    Admin Internal Notes
                                </label>
                                <textarea
                                    v-model="form.admin_notes"
                                    rows="2"
                                    placeholder="e.g. Courier tracking code #ST-88910"
                                    class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:outline-none"
                                />
                            </div>

                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="w-full py-3 rounded-xl bg-[#730163] hover:bg-[#5b014e] text-white font-bold shadow-md transition-all flex items-center justify-center gap-2"
                            >
                                <Save class="w-4 h-4" />
                                <span>{{ form.processing ? 'Updating...' : 'Save Order Status' }}</span>
                            </button>
                        </form>
                    </div>

                    <!-- Financial Summary Card -->
                    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-6 shadow-sm space-y-3 text-xs text-slate-600 dark:text-slate-400">
                        <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-900 dark:text-white pb-3 border-b border-slate-100 dark:border-slate-800">
                            Billing Breakdown
                        </h3>

                        <div class="flex justify-between">
                            <span>Items Subtotal</span>
                            <span class="font-bold text-slate-900 dark:text-white font-mono">৳{{ Number(order.subtotal).toLocaleString() }}</span>
                        </div>

                        <div v-if="order.discount > 0" class="flex justify-between text-emerald-600 font-semibold">
                            <span>Coupon Discount</span>
                            <span class="font-mono">-৳{{ Number(order.discount).toLocaleString() }}</span>
                        </div>

                        <div class="flex justify-between">
                            <span>Delivery Fee</span>
                            <span class="font-mono">{{ order.delivery_charge === 0 ? 'FREE' : '৳' + order.delivery_charge }}</span>
                        </div>

                        <div class="flex justify-between text-base font-extrabold text-slate-900 dark:text-white pt-3 border-t border-slate-100 dark:border-slate-800">
                            <span>Total Payable ({{ order.payment_method?.toUpperCase() }})</span>
                            <span class="text-[#730163] font-mono text-lg">৳{{ Number(order.total_amount).toLocaleString() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
