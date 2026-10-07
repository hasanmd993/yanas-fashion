<script setup>
import { Head, Link } from '@inertiajs/vue3';
import StorefrontLayout from '@/Layouts/StorefrontLayout.vue';
import {
    CheckCircle2,
    MessageCircle,
    Download,
    ShoppingBag,
    Package,
    Truck,
    MapPin,
    Phone,
    User,
    ArrowRight
} from 'lucide-vue-next';

const props = defineProps({
    order: {
        type: Object,
        required: true,
    },
    whatsappUrl: {
        type: String,
        required: true,
    },
    invoiceUrl: {
        type: String,
        default: null,
    },
});
</script>

<template>
    <Head :title="`Order #${order.order_number} Confirmed — Yanas Fashion`" />

    <StorefrontLayout>
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <!-- Success Card -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-8 sm:p-12 shadow-xl text-center space-y-6">
                <div class="w-20 h-20 rounded-full bg-emerald-50 dark:bg-emerald-950/40 text-emerald-500 flex items-center justify-center mx-auto ring-8 ring-emerald-500/10">
                    <CheckCircle2 class="w-10 h-10" />
                </div>

                <div class="space-y-2">
                    <span class="inline-block px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 font-extrabold text-xs tracking-wider uppercase">
                        Order Placed Successfully
                    </span>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white font-serif">
                        Thank You For Your Order!
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 max-w-md mx-auto">
                        Your luxury order <strong class="text-slate-800 dark:text-white font-mono">#{{ order.order_number }}</strong> has been recorded and is currently being processed by our packaging team in Dhaka.
                    </p>
                </div>

                <!-- Instant WhatsApp Verification Button -->
                <div class="p-6 rounded-2xl bg-emerald-50/70 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800/40 space-y-3">
                    <h3 class="text-xs font-bold text-emerald-900 dark:text-emerald-200">
                        ⚡ Fast-Track Instant WhatsApp Confirmation
                    </h3>
                    <p class="text-[11px] text-emerald-700 dark:text-emerald-400">
                        Click below to verify your address with our WhatsApp customer care representative for same-day dispatch.
                    </p>
                    <a
                        :href="whatsappUrl"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-[#25D366] hover:bg-[#20bd5a] text-white text-xs font-extrabold shadow-lg shadow-emerald-500/25 transition-all hover:scale-105"
                    >
                        <MessageCircle class="w-4 h-4 fill-white" />
                        <span>Confirm via WhatsApp (One-Click)</span>
                    </a>
                </div>

                <!-- Order Details Summary -->
                <div class="border-t border-slate-100 dark:border-slate-800 pt-8 text-left space-y-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800 space-y-1.5">
                            <div class="text-[11px] text-slate-400 font-bold uppercase tracking-wider">Customer Details</div>
                            <div class="font-bold text-slate-800 dark:text-white flex items-center gap-1.5">
                                <User class="w-3.5 h-3.5 text-rose-500" /> {{ order.customer_name }}
                            </div>
                            <div class="text-slate-600 dark:text-slate-300 font-mono flex items-center gap-1.5">
                                <Phone class="w-3.5 h-3.5 text-rose-500" /> {{ order.customer_phone }}
                            </div>
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800 space-y-1.5">
                            <div class="text-[11px] text-slate-400 font-bold uppercase tracking-wider">Shipping Destination</div>
                            <div class="text-slate-700 dark:text-slate-300 flex items-start gap-1.5">
                                <MapPin class="w-3.5 h-3.5 text-rose-500 shrink-0 mt-0.5" />
                                <span>{{ order.customer_address }} ({{ order.delivery_zone?.replace('_', ' ') }})</span>
                            </div>
                        </div>
                    </div>

                    <!-- Items Table -->
                    <div class="border border-slate-100 dark:border-slate-800 rounded-2xl overflow-hidden divide-y divide-slate-100 dark:divide-slate-800 text-xs">
                        <div
                            v-for="item in order.items"
                            :key="item.id"
                            class="p-4 flex items-center justify-between gap-4"
                        >
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-500 shrink-0">
                                    <Package class="w-4 h-4" />
                                </div>
                                <div class="truncate">
                                    <div class="font-bold text-slate-800 dark:text-white truncate">{{ item.product_name }}</div>
                                    <div class="text-[11px] text-slate-400">Qty: {{ item.quantity }} <span v-if="item.size">• Size: {{ item.size }}</span></div>
                                </div>
                            </div>
                            <div class="font-bold font-mono text-slate-900 dark:text-white shrink-0">
                                ৳{{ item.total_price }}
                            </div>
                        </div>
                    </div>

                    <!-- Financials -->
                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800 space-y-2 text-xs text-slate-600 dark:text-slate-400">
                        <div class="flex justify-between">
                            <span>Subtotal</span>
                            <span class="font-bold text-slate-900 dark:text-white font-mono">৳{{ order.subtotal }}</span>
                        </div>
                        <div v-if="order.discount > 0" class="flex justify-between text-emerald-600 font-semibold">
                            <span>Discount</span>
                            <span class="font-mono">-৳{{ order.discount }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Delivery Charge</span>
                            <span class="font-mono">{{ order.delivery_charge === 0 ? 'FREE' : '৳' + order.delivery_charge }}</span>
                        </div>
                        <div class="flex justify-between text-base font-extrabold text-slate-900 dark:text-white pt-2 border-t border-slate-200 dark:border-slate-700">
                            <span>Total Payable ({{ order.payment_method?.toUpperCase() }})</span>
                            <span class="text-rose-600 font-mono text-lg">৳{{ order.total_amount }}</span>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="pt-4 flex flex-col sm:flex-row items-center justify-center gap-4">
                    <a
                        :href="invoiceUrl || route('order.invoice', order.order_number)"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold text-slate-800 dark:text-white hover:bg-slate-50 transition-colors"
                    >
                        <Download class="w-4 h-4" /> Download PDF Invoice
                    </a>

                    <Link
                        :href="route('shop.index')"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-7 py-3 rounded-xl bg-slate-900 dark:bg-white text-white dark:text-slate-900 text-xs font-bold shadow-md hover:opacity-90 transition-opacity"
                    >
                        <span>Continue Shopping</span>
                        <ArrowRight class="w-4 h-4" />
                    </Link>
                </div>
            </div>
        </div>
    </StorefrontLayout>
</template>
