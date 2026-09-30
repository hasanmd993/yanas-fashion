<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import StorefrontLayout from '@/Layouts/StorefrontLayout.vue';
import { useCartStore } from '@/Stores/cart';
import axios from 'axios';
import {
    ShieldCheck,
    Truck,
    CreditCard,
    CheckCircle2,
    Lock,
    ArrowRight,
    Tag,
    ChevronRight,
    AlertCircle
} from 'lucide-vue-next';

const props = defineProps({
    cart: {
        type: Array,
        default: () => [],
    },
    subtotal: {
        type: Number,
        default: 0,
    },
    insideDhaka: {
        type: Number,
        default: 70,
    },
    suburbs: {
        type: Number,
        default: 100,
    },
    outsideDhaka: {
        type: Number,
        default: 130,
    },
    freeShippingThreshold: {
        type: Number,
        default: 2500,
    },
    appliedCoupon: {
        type: Object,
        default: null,
    },
    discount: {
        type: Number,
        default: 0,
    },
});

const cartStore = useCartStore();

const form = useForm({
    customer_name: '',
    customer_phone: '',
    customer_address: '',
    delivery_zone: 'inside_dhaka',
    payment_method: 'cod',
    customer_note: '',
});

// Coupon State
const couponInput = ref('');
const couponLoading = ref(false);
const couponMessage = ref(null);
const couponError = ref(null);
const activeDiscount = ref(props.discount || 0);

const handleApplyCoupon = async () => {
    if (!couponInput.value.trim()) return;
    couponLoading.value = true;
    couponMessage.value = null;
    couponError.value = null;

    try {
        const response = await axios.post(route('checkout.coupon'), { code: couponInput.value });
        if (response.data.success) {
            activeDiscount.value = response.data.discount;
            couponMessage.value = response.data.message;
        }
    } catch (err) {
        couponError.value = err.response?.data?.message || 'Invalid coupon code';
    } finally {
        couponLoading.value = false;
    }
};

const deliveryFee = computed(() => {
    if (props.subtotal >= props.freeShippingThreshold && form.delivery_zone === 'inside_dhaka') {
        return 0;
    }
    if (form.delivery_zone === 'inside_dhaka') return props.insideDhaka;
    if (form.delivery_zone === 'dhaka_suburbs') return props.suburbs;
    return props.outsideDhaka;
});

const grandTotal = computed(() => {
    return Math.max(0, props.subtotal + deliveryFee.value - activeDiscount.value);
});

const submitOrder = () => {
    form.transform((data) => ({
        ...data,
        items: cartStore.items,
    })).post(route('checkout.store'), {
        onSuccess: () => {
            cartStore.clearCart();
        },
    });
};
</script>

<template>
    <Head title="Express Checkout — Yanas Fashion" />

    <StorefrontLayout>
        <!-- Breadcrumb -->
        <div class="bg-white dark:bg-slate-900 border-b border-slate-100 dark:border-slate-800 py-4 px-4 sm:px-6 lg:px-8 text-xs text-slate-400">
            <div class="max-w-7xl mx-auto flex items-center gap-2">
                <Link :href="route('home')" class="hover:text-rose-600">Home</Link>
                <ChevronRight class="w-3.5 h-3.5" />
                <Link :href="route('cart.index')" class="hover:text-rose-600">Shopping Bag</Link>
                <ChevronRight class="w-3.5 h-3.5" />
                <span class="text-slate-800 dark:text-white font-bold">Checkout</span>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="mb-8">
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white font-serif">
                    Express 1-Page Checkout
                </h1>
                <p class="text-xs text-slate-500 mt-1">
                    Fill in your delivery address to confirm your order with Cash on Delivery across Bangladesh.
                </p>
            </div>

            <form @submit.prevent="submitOrder">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
                    <!-- Left: Customer Info & Shipping (Col 1-7) -->
                    <div class="lg:col-span-7 space-y-6">
                        <!-- Step 1: Customer Details -->
                        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-6 sm:p-8 shadow-sm space-y-5">
                            <div class="flex items-center gap-3 pb-4 border-b border-slate-100 dark:border-slate-800">
                                <span class="w-8 h-8 rounded-full bg-rose-600 text-white flex items-center justify-center font-extrabold text-xs">1</span>
                                <h2 class="text-sm font-extrabold uppercase tracking-wider text-slate-900 dark:text-white">
                                    Customer & Delivery Address
                                </h2>
                            </div>

                            <div class="space-y-4">
                                <!-- Full Name -->
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                        Full Name <span class="text-rose-600">*</span>
                                    </label>
                                    <input
                                        v-model="form.customer_name"
                                        type="text"
                                        placeholder="Enter your full name"
                                        class="w-full px-4 py-3 rounded-xl border bg-slate-50 dark:bg-slate-800 text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500/20"
                                        :class="form.errors.customer_name ? 'border-rose-500 ring-2 ring-rose-500/10' : 'border-slate-200 dark:border-slate-700'"
                                    />
                                    <p v-if="form.errors.customer_name" class="text-rose-600 text-[11px] mt-1 font-semibold">
                                        {{ form.errors.customer_name }}
                                    </p>
                                </div>

                                <!-- Mobile Number -->
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                        Mobile Number <span class="text-rose-600">* (For delivery verification)</span>
                                    </label>
                                    <input
                                        v-model="form.customer_phone"
                                        type="tel"
                                        placeholder="e.g. 01712345678"
                                        class="w-full px-4 py-3 rounded-xl border bg-slate-50 dark:bg-slate-800 text-xs font-mono focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500/20"
                                        :class="form.errors.customer_phone ? 'border-rose-500 ring-2 ring-rose-500/10' : 'border-slate-200 dark:border-slate-700'"
                                    />
                                    <p v-if="form.errors.customer_phone" class="text-rose-600 text-[11px] mt-1 font-semibold">
                                        {{ form.errors.customer_phone }}
                                    </p>
                                </div>

                                <!-- Delivery Zone -->
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                        Delivery Area <span class="text-rose-600">*</span>
                                    </label>
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                        <label
                                            class="p-3.5 rounded-2xl border cursor-pointer transition-all flex flex-col justify-between"
                                            :class="form.delivery_zone === 'inside_dhaka' ? 'border-rose-600 bg-rose-50/50 dark:bg-rose-950/30 ring-2 ring-rose-500/20' : 'border-slate-200 dark:border-slate-700 hover:border-slate-400'"
                                        >
                                            <input type="radio" v-model="form.delivery_zone" value="inside_dhaka" class="sr-only" />
                                            <span class="text-xs font-bold text-slate-900 dark:text-white">Inside Dhaka</span>
                                            <span class="text-[11px] font-mono text-rose-600 font-bold mt-1">৳{{ insideDhaka }} (Free over ৳2,500)</span>
                                        </label>

                                        <label
                                            class="p-3.5 rounded-2xl border cursor-pointer transition-all flex flex-col justify-between"
                                            :class="form.delivery_zone === 'dhaka_suburbs' ? 'border-rose-600 bg-rose-50/50 dark:bg-rose-950/30 ring-2 ring-rose-500/20' : 'border-slate-200 dark:border-slate-700 hover:border-slate-400'"
                                        >
                                            <input type="radio" v-model="form.delivery_zone" value="dhaka_suburbs" class="sr-only" />
                                            <span class="text-xs font-bold text-slate-900 dark:text-white">Dhaka Suburbs</span>
                                            <span class="text-[11px] font-mono text-rose-600 font-bold mt-1">৳{{ suburbs }}</span>
                                        </label>

                                        <label
                                            class="p-3.5 rounded-2xl border cursor-pointer transition-all flex flex-col justify-between"
                                            :class="form.delivery_zone === 'outside_dhaka' ? 'border-rose-600 bg-rose-50/50 dark:bg-rose-950/30 ring-2 ring-rose-500/20' : 'border-slate-200 dark:border-slate-700 hover:border-slate-400'"
                                        >
                                            <input type="radio" v-model="form.delivery_zone" value="outside_dhaka" class="sr-only" />
                                            <span class="text-xs font-bold text-slate-900 dark:text-white">Outside Dhaka</span>
                                            <span class="text-[11px] font-mono text-rose-600 font-bold mt-1">৳{{ outsideDhaka }}</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Full Address -->
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                        Full Street Address <span class="text-rose-600">*</span>
                                    </label>
                                    <textarea
                                        v-model="form.customer_address"
                                        rows="2"
                                        placeholder="House number, Road/Sector, Area, Thana & District"
                                        class="w-full px-4 py-3 rounded-xl border bg-slate-50 dark:bg-slate-800 text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500/20"
                                        :class="form.errors.customer_address ? 'border-rose-500 ring-2 ring-rose-500/10' : 'border-slate-200 dark:border-slate-700'"
                                    />
                                    <p v-if="form.errors.customer_address" class="text-rose-600 text-[11px] mt-1 font-semibold">
                                        {{ form.errors.customer_address }}
                                    </p>
                                </div>

                                <!-- Order Notes -->
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                        Special Delivery Instructions (Optional)
                                    </label>
                                    <input
                                        v-model="form.customer_note"
                                        type="text"
                                        placeholder="e.g. Call before delivery, deliver in afternoon"
                                        class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: Payment Method -->
                        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-6 sm:p-8 shadow-sm space-y-5">
                            <div class="flex items-center gap-3 pb-4 border-b border-slate-100 dark:border-slate-800">
                                <span class="w-8 h-8 rounded-full bg-rose-600 text-white flex items-center justify-center font-extrabold text-xs">2</span>
                                <h2 class="text-sm font-extrabold uppercase tracking-wider text-slate-900 dark:text-white">
                                    Payment Method
                                </h2>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <label
                                    class="p-4 rounded-2xl border cursor-pointer transition-all flex items-center gap-3"
                                    :class="form.payment_method === 'cod' ? 'border-rose-600 bg-rose-50/50 dark:bg-rose-950/30 ring-2 ring-rose-500/20' : 'border-slate-200 dark:border-slate-700'"
                                >
                                    <input type="radio" v-model="form.payment_method" value="cod" class="sr-only" />
                                    <div class="w-4 h-4 rounded-full border-2 flex items-center justify-center" :class="form.payment_method === 'cod' ? 'border-rose-600 bg-rose-600' : 'border-slate-300'">
                                        <div v-if="form.payment_method === 'cod'" class="w-1.5 h-1.5 bg-white rounded-full" />
                                    </div>
                                    <div>
                                        <span class="text-xs font-bold text-slate-900 dark:text-white block">Cash on Delivery</span>
                                        <span class="text-[10px] text-slate-400">Pay when receiving parcel</span>
                                    </div>
                                </label>

                                <label
                                    class="p-4 rounded-2xl border cursor-pointer transition-all flex items-center gap-3"
                                    :class="form.payment_method === 'bkash' ? 'border-rose-600 bg-rose-50/50 dark:bg-rose-950/30 ring-2 ring-rose-500/20' : 'border-slate-200 dark:border-slate-700'"
                                >
                                    <input type="radio" v-model="form.payment_method" value="bkash" class="sr-only" />
                                    <div class="w-4 h-4 rounded-full border-2 flex items-center justify-center" :class="form.payment_method === 'bkash' ? 'border-rose-600 bg-rose-600' : 'border-slate-300'">
                                        <div v-if="form.payment_method === 'bkash'" class="w-1.5 h-1.5 bg-white rounded-full" />
                                    </div>
                                    <div>
                                        <span class="text-xs font-bold text-rose-600 block font-mono">bKash</span>
                                        <span class="text-[10px] text-slate-400">Merchant Payment</span>
                                    </div>
                                </label>

                                <label
                                    class="p-4 rounded-2xl border cursor-pointer transition-all flex items-center gap-3"
                                    :class="form.payment_method === 'nagad' ? 'border-rose-600 bg-rose-50/50 dark:bg-rose-950/30 ring-2 ring-rose-500/20' : 'border-slate-200 dark:border-slate-700'"
                                >
                                    <input type="radio" v-model="form.payment_method" value="nagad" class="sr-only" />
                                    <div class="w-4 h-4 rounded-full border-2 flex items-center justify-center" :class="form.payment_method === 'nagad' ? 'border-rose-600 bg-rose-600' : 'border-slate-300'">
                                        <div v-if="form.payment_method === 'nagad'" class="w-1.5 h-1.5 bg-white rounded-full" />
                                    </div>
                                    <div>
                                        <span class="text-xs font-bold text-amber-600 block font-mono">Nagad</span>
                                        <span class="text-[10px] text-slate-400">Direct Gateway</span>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Order Items & Pricing Summary (Col 8-12) -->
                    <div class="lg:col-span-5 space-y-6">
                        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-6 shadow-sm space-y-6 sticky top-28">
                            <h2 class="text-sm font-extrabold uppercase tracking-wider text-slate-900 dark:text-white pb-3 border-b border-slate-100 dark:border-slate-800">
                                Order Overview ({{ cart.length }} items)
                            </h2>

                            <!-- Mini Items Strip -->
                            <div class="space-y-3 max-h-60 overflow-y-auto divide-y divide-slate-100 dark:divide-slate-800 pr-1">
                                <div v-for="item in cart" :key="item.product_id" class="pt-3 first:pt-0 flex items-center gap-3">
                                    <img
                                        :src="item.thumbnail ? (item.thumbnail.startsWith('http') ? item.thumbnail : (item.thumbnail.startsWith('/') ? item.thumbnail : (item.thumbnail.startsWith('assets/') ? `/${item.thumbnail}` : `/storage/${item.thumbnail}`))) : '/images/placeholder.jpg'"
                                        :alt="item.title"
                                        class="w-12 h-14 object-cover rounded-xl border border-slate-100 dark:border-slate-800 bg-slate-50 shrink-0"
                                    />
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-xs font-bold text-slate-800 dark:text-white truncate">{{ item.title }}</h4>
                                        <p class="text-[11px] text-slate-400">
                                            Qty: {{ item.quantity }} <span v-if="item.size">• Size: {{ item.size }}</span>
                                        </p>
                                    </div>
                                    <div class="text-xs font-bold font-mono text-slate-900 dark:text-white">
                                        ৳{{ item.price * item.quantity }}
                                    </div>
                                </div>
                            </div>

                            <!-- Coupon Applicator Input -->
                            <div class="pt-3 border-t border-slate-100 dark:border-slate-800 space-y-2">
                                <div class="flex gap-2">
                                    <input
                                        v-model="couponInput"
                                        type="text"
                                        placeholder="Coupon code"
                                        class="flex-1 px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-mono uppercase focus:outline-none focus:ring-2 focus:ring-rose-500/20"
                                    />
                                    <button
                                        type="button"
                                        @click="handleApplyCoupon"
                                        :disabled="couponLoading"
                                        class="px-4 py-2.5 rounded-xl bg-slate-900 dark:bg-white text-white dark:text-slate-900 text-xs font-bold hover:opacity-90 transition-opacity disabled:opacity-50"
                                    >
                                        {{ couponLoading ? 'Applying...' : 'Apply' }}
                                    </button>
                                </div>
                                <p v-if="couponMessage" class="text-[11px] text-emerald-600 font-semibold flex items-center gap-1">
                                    <CheckCircle2 class="w-3.5 h-3.5" /> {{ couponMessage }}
                                </p>
                                <p v-if="couponError" class="text-[11px] text-rose-600 font-semibold flex items-center gap-1">
                                    <AlertCircle class="w-3.5 h-3.5" /> {{ couponError }}
                                </p>
                            </div>

                            <!-- Totals Breakdown -->
                            <div class="space-y-3 text-xs text-slate-600 dark:text-slate-400 pt-3 border-t border-slate-100 dark:border-slate-800">
                                <div class="flex justify-between">
                                    <span>Subtotal</span>
                                    <span class="font-bold text-slate-900 dark:text-white font-mono">৳{{ subtotal }}</span>
                                </div>
                                <div v-if="activeDiscount > 0" class="flex justify-between text-emerald-600 font-semibold">
                                    <span>Coupon Discount</span>
                                    <span class="font-mono">-৳{{ activeDiscount }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Delivery Fee</span>
                                    <span class="font-mono font-bold">{{ deliveryFee === 0 ? 'FREE' : '৳' + deliveryFee }}</span>
                                </div>
                                <div class="flex justify-between text-base font-extrabold text-slate-900 dark:text-white pt-3 border-t border-slate-100 dark:border-slate-800">
                                    <span>Total Payable</span>
                                    <span class="text-rose-600 font-mono text-xl">৳{{ grandTotal }}</span>
                                </div>
                            </div>

                            <!-- Confirm Order Button -->
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="w-full py-4 px-6 rounded-2xl bg-gradient-to-r from-rose-600 to-rose-700 hover:from-rose-500 hover:to-rose-600 text-white text-xs font-extrabold uppercase tracking-wider shadow-xl shadow-rose-600/25 transition-all flex items-center justify-center gap-2 hover:scale-[1.01] disabled:opacity-50"
                            >
                                <Lock class="w-4 h-4" />
                                <span>{{ form.processing ? 'Placing Your Order...' : 'Confirm Order (Cash on Delivery)' }}</span>
                            </button>

                            <div class="flex items-center justify-center gap-2 text-[11px] text-slate-400 text-center">
                                <ShieldCheck class="w-4 h-4 text-emerald-500" />
                                <span>100% Secure & Verified Checkout</span>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </StorefrontLayout>
</template>
