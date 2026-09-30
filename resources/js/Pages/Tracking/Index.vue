<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import StorefrontLayout from '@/Layouts/StorefrontLayout.vue';
import {
    Search,
    MapPin,
    Package,
    Truck,
    CheckCircle2,
    Clock,
    AlertCircle,
    ChevronRight,
    User,
    Phone
} from 'lucide-vue-next';

const props = defineProps({
    order: {
        type: Object,
        default: null,
    },
});

const form = useForm({
    order_number: '',
    phone: '',
});

const trackOrder = () => {
    form.post(route('tracking.track'), {
        preserveScroll: true,
    });
};

const getStatusStep = (status) => {
    const s = (status || '').toLowerCase();
    if (s === 'delivered') return 4;
    if (s === 'shipped' || s === 'out_for_delivery') return 3;
    if (s === 'processing') return 2;
    return 1; // pending
};
</script>

<template>
    <Head title="Live Order Tracking — Yanas Fashion" />

    <StorefrontLayout>
        <!-- Breadcrumb -->
        <div class="bg-white dark:bg-slate-900 border-b border-slate-100 dark:border-slate-800 py-4 px-4 sm:px-6 lg:px-8 text-xs text-slate-400">
            <div class="max-w-7xl mx-auto flex items-center gap-2">
                <Link :href="route('home')" class="hover:text-rose-600">Home</Link>
                <ChevronRight class="w-3.5 h-3.5" />
                <span class="text-slate-800 dark:text-white font-bold">Track Your Order</span>
            </div>
        </div>

        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-10">
            <!-- Header & Search Form -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-8 sm:p-10 shadow-sm text-center space-y-6">
                <div class="w-16 h-16 rounded-full bg-rose-50 dark:bg-rose-950/40 text-rose-600 flex items-center justify-center mx-auto">
                    <Truck class="w-8 h-8" />
                </div>

                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white font-serif">
                        Track Your Parcel Status
                    </h1>
                    <p class="text-xs text-slate-500 mt-1 max-w-md mx-auto">
                        Enter your Order ID (e.g. #YF-102934) and mobile number to see real-time dispatch and delivery updates.
                    </p>
                </div>

                <form @submit.prevent="trackOrder" class="grid grid-cols-1 sm:grid-cols-12 gap-3 text-left">
                    <div class="sm:col-span-6">
                        <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">
                            Order Number
                        </label>
                        <input
                            v-model="form.order_number"
                            type="text"
                            placeholder="e.g. YF-102934 or 102934"
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-mono focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500/20"
                            required
                        />
                    </div>

                    <div class="sm:col-span-6">
                        <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">
                            Mobile Number
                        </label>
                        <input
                            v-model="form.phone"
                            type="tel"
                            placeholder="e.g. 01712345678"
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-mono focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500/20"
                            required
                        />
                    </div>

                    <div class="sm:col-span-12 pt-2">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full py-3.5 rounded-xl bg-gradient-to-r from-rose-600 to-rose-700 hover:from-rose-500 hover:to-rose-600 text-white font-bold text-xs uppercase tracking-wider shadow-lg shadow-rose-600/25 transition-all flex items-center justify-center gap-2 disabled:opacity-50"
                        >
                            <Search class="w-4 h-4" />
                            <span>{{ form.processing ? 'Locating Parcel...' : 'Check Status' }}</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Order Results Showcase -->
            <div v-if="order" class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-8 shadow-xl space-y-8">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-100 dark:border-slate-800">
                    <div>
                        <span class="text-[11px] font-bold text-rose-600 uppercase tracking-wider">Order Details</span>
                        <h2 class="text-xl font-extrabold text-slate-900 dark:text-white font-mono">
                            #{{ order.order_number }}
                        </h2>
                    </div>

                    <span
                        class="inline-block px-3.5 py-1.5 rounded-full text-xs font-extrabold uppercase tracking-wider"
                        :class="{
                            'bg-amber-500/10 text-amber-600 border border-amber-500/20': order.order_status === 'pending',
                            'bg-sky-500/10 text-sky-600 border border-sky-500/20': order.order_status === 'processing',
                            'bg-purple-500/10 text-purple-600 border border-purple-500/20': order.order_status === 'shipped',
                            'bg-emerald-500/10 text-emerald-600 border border-emerald-500/20': order.order_status === 'delivered',
                            'bg-rose-500/10 text-rose-600 border border-rose-500/20': order.order_status === 'cancelled',
                        }"
                    >
                        Status: {{ order.order_status?.toUpperCase() }}
                    </span>
                </div>

                <!-- 4-Step Visual Timeline -->
                <div class="py-4">
                    <div class="grid grid-cols-4 gap-2 text-center relative">
                        <!-- Step 1: Received -->
                        <div class="flex flex-col items-center gap-2">
                            <div
                                class="w-10 h-10 rounded-full flex items-center justify-center text-xs font-bold transition-all"
                                :class="getStatusStep(order.order_status) >= 1 ? 'bg-rose-600 text-white ring-4 ring-rose-500/20' : 'bg-slate-100 text-slate-400'"
                            >
                                <Clock class="w-5 h-5" />
                            </div>
                            <span class="text-[11px] font-bold text-slate-800 dark:text-slate-200">Order Placed</span>
                        </div>

                        <!-- Step 2: Processing -->
                        <div class="flex flex-col items-center gap-2">
                            <div
                                class="w-10 h-10 rounded-full flex items-center justify-center text-xs font-bold transition-all"
                                :class="getStatusStep(order.order_status) >= 2 ? 'bg-rose-600 text-white ring-4 ring-rose-500/20' : 'bg-slate-100 text-slate-400'"
                            >
                                <Package class="w-5 h-5" />
                            </div>
                            <span class="text-[11px] font-bold text-slate-800 dark:text-slate-200">Packaging</span>
                        </div>

                        <!-- Step 3: Shipped -->
                        <div class="flex flex-col items-center gap-2">
                            <div
                                class="w-10 h-10 rounded-full flex items-center justify-center text-xs font-bold transition-all"
                                :class="getStatusStep(order.order_status) >= 3 ? 'bg-rose-600 text-white ring-4 ring-rose-500/20' : 'bg-slate-100 text-slate-400'"
                            >
                                <Truck class="w-5 h-5" />
                            </div>
                            <span class="text-[11px] font-bold text-slate-800 dark:text-slate-200">On The Way</span>
                        </div>

                        <!-- Step 4: Delivered -->
                        <div class="flex flex-col items-center gap-2">
                            <div
                                class="w-10 h-10 rounded-full flex items-center justify-center text-xs font-bold transition-all"
                                :class="getStatusStep(order.order_status) >= 4 ? 'bg-emerald-600 text-white ring-4 ring-emerald-500/20' : 'bg-slate-100 text-slate-400'"
                            >
                                <CheckCircle2 class="w-5 h-5" />
                            </div>
                            <span class="text-[11px] font-bold text-slate-800 dark:text-slate-200">Delivered</span>
                        </div>
                    </div>
                </div>

                <!-- Recipient & Destination -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800 space-y-1">
                        <div class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Recipient</div>
                        <div class="font-bold text-slate-800 dark:text-white flex items-center gap-1.5">
                            <User class="w-3.5 h-3.5 text-rose-500" /> {{ order.customer_name }}
                        </div>
                        <div class="text-slate-600 dark:text-slate-300 font-mono flex items-center gap-1.5">
                            <Phone class="w-3.5 h-3.5 text-rose-500" /> {{ order.customer_phone }}
                        </div>
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800 space-y-1">
                        <div class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Delivery Destination</div>
                        <div class="text-slate-700 dark:text-slate-300 flex items-start gap-1.5">
                            <MapPin class="w-3.5 h-3.5 text-rose-500 shrink-0 mt-0.5" />
                            <span>{{ order.customer_address }}</span>
                        </div>
                    </div>
                </div>

                <!-- Items -->
                <div class="border border-slate-100 dark:border-slate-800 rounded-2xl overflow-hidden divide-y divide-slate-100 dark:divide-slate-800 text-xs">
                    <div
                        v-for="item in order.items"
                        :key="item.id"
                        class="p-4 flex items-center justify-between gap-4"
                    >
                        <div class="font-bold text-slate-800 dark:text-white truncate">
                            {{ item.product_name }} <span class="text-slate-400 font-normal">x {{ item.quantity }}</span>
                        </div>
                        <div class="font-bold font-mono text-slate-900 dark:text-white shrink-0">
                            ৳{{ item.total_price }}
                        </div>
                    </div>
                </div>

                <!-- Order Financials -->
                <div class="flex justify-between items-center p-4 rounded-2xl bg-rose-50/50 dark:bg-rose-950/20 border border-rose-100 dark:border-rose-900/40 text-xs font-bold">
                    <span class="text-slate-700 dark:text-slate-300">Total Order Amount ({{ order.payment_method?.toUpperCase() }})</span>
                    <span class="text-base font-extrabold text-rose-600 font-mono">৳{{ order.total_amount }}</span>
                </div>
            </div>
        </div>
    </StorefrontLayout>
</template>
