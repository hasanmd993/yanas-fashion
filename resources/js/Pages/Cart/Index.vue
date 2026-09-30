<script setup>
import { computed } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import StorefrontLayout from '@/Layouts/StorefrontLayout.vue';
import { useCartStore } from '@/Stores/cart';
import {
    ShoppingBag,
    Plus,
    Minus,
    Trash2,
    ArrowRight,
    Sparkles,
    Truck,
    ShieldCheck,
    ChevronRight,
    MessageCircle
} from 'lucide-vue-next';

const cart = useCartStore();
const page = usePage();

const freeShippingThreshold = 2500;
const freeShippingRemaining = computed(() => Math.max(0, freeShippingThreshold - cart.subtotal));
const freeShippingProgress = computed(() => Math.min(100, Math.round((cart.subtotal / freeShippingThreshold) * 100)));

const handleWhatsAppCartOrder = () => {
    if (cart.items.length === 0) return;
    const rawNumber = page.props.settings?.whatsapp || '8801713580400';
    const cleanNumber = String(rawNumber).replace(/[^0-9]/g, '');

    let text = `*✨ New Bag Order Inquiry - Yanas Fashion ✨*\n\n`;
    text += `📦 *Order Summary (${cart.totalCount} items):*\n`;

    cart.items.forEach((item, index) => {
        text += `\n${index + 1}. *${item.name}*`;
        if (item.size) text += ` | Size: ${item.size}`;
        if (item.color) text += ` | Color: ${item.color}`;
        text += `\n   Qty: ${item.quantity} x ৳${item.price} = ৳${item.quantity * item.price}`;
    });

    text += `\n\n------------------------\n`;
    text += `💰 *Subtotal:* ৳${cart.subtotal}\n`;
    if (cart.discountAmount > 0) {
        text += `🎁 *Discount:* -৳${cart.discountAmount}\n`;
    }
    text += `🚚 *Delivery Zone:* ${cart.deliveryType}\n`;
    text += `💵 *Grand Total:* ৳${cart.grandTotal}\n\n`;
    text += `Please confirm my order and share delivery details. Thank you!`;

    const url = `https://api.whatsapp.com/send?phone=${cleanNumber}&text=${encodeURIComponent(text)}`;
    window.open(url, '_blank');
};
</script>

<template>
    <Head title="Shopping Bag — Yanas Fashion" />

    <StorefrontLayout>
        <!-- Breadcrumb -->
        <div class="bg-white dark:bg-slate-900 border-b border-slate-100 dark:border-slate-800 py-4 px-4 sm:px-6 lg:px-8 text-xs text-slate-400">
            <div class="max-w-7xl mx-auto flex items-center gap-2">
                <Link :href="route('home')" class="hover:text-rose-600">Home</Link>
                <ChevronRight class="w-3.5 h-3.5" />
                <span class="text-slate-800 dark:text-white font-bold">Shopping Bag</span>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="mb-8">
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white font-serif">
                    Shopping Bag
                </h1>
                <p class="text-xs text-slate-500 mt-1">Review your luxury items before proceeding to express checkout</p>
            </div>

            <!-- Empty Cart State -->
            <div v-if="cart.items.length === 0" class="py-20 text-center bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-8 shadow-sm">
                <div class="w-20 h-20 rounded-full bg-rose-50 dark:bg-rose-950/40 text-rose-600 flex items-center justify-center mx-auto mb-4">
                    <ShoppingBag class="w-10 h-10" />
                </div>
                <h2 class="text-lg font-bold text-slate-800 dark:text-white">Your shopping bag is empty</h2>
                <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">Discover our handcrafted panjabis, festive wear, and tailored lifestyle apparel.</p>
                <Link
                    :href="route('shop.index')"
                    class="mt-6 inline-flex items-center gap-2 px-8 py-3.5 rounded-xl bg-gradient-to-r from-rose-600 to-rose-700 text-white font-bold text-xs uppercase tracking-wider shadow-lg shadow-rose-600/30 hover:scale-105 transition-all"
                >
                    Start Shopping <ArrowRight class="w-4 h-4" />
                </Link>
            </div>

            <!-- Cart Items and Summary Grid -->
            <div v-else class="grid grid-cols-1 lg:grid-cols-12 gap-10">
                <!-- Left: Items List (Col 1-8) -->
                <div class="lg:col-span-8 space-y-6">
                    <!-- Free Shipping Progress -->
                    <div class="p-4 rounded-2xl bg-rose-50/60 dark:bg-rose-950/30 border border-rose-100 dark:border-rose-900/40">
                        <div class="flex items-center justify-between text-xs font-semibold mb-2">
                            <span class="flex items-center gap-2 text-rose-700 dark:text-rose-400">
                                <Truck class="w-4 h-4 text-rose-600" />
                                <span v-if="freeShippingRemaining > 0">
                                    Add <strong class="font-bold">৳{{ freeShippingRemaining }}</strong> more to unlock <strong class="text-emerald-600">FREE Delivery</strong>
                                </span>
                                <span v-else class="text-emerald-600 font-bold flex items-center gap-1">
                                    <Sparkles class="w-4 h-4" /> Congratulations! You've unlocked FREE Delivery across Dhaka!
                                </span>
                            </span>
                            <span class="text-rose-600 font-mono text-xs">{{ freeShippingProgress }}%</span>
                        </div>
                        <div class="w-full bg-rose-100 dark:bg-rose-900/40 rounded-full h-2 overflow-hidden">
                            <div
                                class="bg-gradient-to-r from-rose-500 to-emerald-500 h-2 rounded-full transition-all duration-500"
                                :style="{ width: `${freeShippingProgress}%` }"
                            />
                        </div>
                    </div>

                    <!-- Items Card Table -->
                    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden divide-y divide-slate-100 dark:divide-slate-800">
                        <div
                            v-for="item in cart.items"
                            :key="item.key"
                            class="p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4"
                        >
                            <div class="flex items-center gap-4 flex-1 min-w-0">
                                <img
                                    :src="item.image"
                                    :alt="item.name"
                                    class="w-20 h-24 object-cover rounded-2xl border border-slate-100 dark:border-slate-800 bg-slate-50 shrink-0"
                                />
                                <div class="min-w-0">
                                    <h3 class="text-sm font-bold text-slate-900 dark:text-white truncate">
                                        <Link :href="route('product.show', item.slug)" class="hover:text-rose-600 transition-colors">
                                            {{ item.name }}
                                        </Link>
                                    </h3>
                                    <div class="flex items-center gap-2 mt-1 text-xs text-slate-500">
                                        <span v-if="item.size" class="px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 font-mono">Size: {{ item.size }}</span>
                                        <span v-if="item.color" class="px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800">Color: {{ item.color }}</span>
                                    </div>
                                    <div class="mt-2 text-xs font-bold text-slate-900 dark:text-white font-mono">
                                        Unit: ৳{{ item.price }}
                                    </div>
                                </div>
                            </div>

                            <!-- Stepper & Line Total -->
                            <div class="flex items-center justify-between w-full sm:w-auto sm:gap-8">
                                <div class="flex items-center border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-800 p-1">
                                    <button
                                        @click="cart.updateQuantity(item.key, item.quantity - 1)"
                                        class="w-7 h-7 flex items-center justify-center text-slate-500 hover:text-slate-900 dark:hover:text-white"
                                    >
                                        <Minus class="w-3.5 h-3.5" />
                                    </button>
                                    <span class="w-8 text-center text-xs font-bold font-mono text-slate-900 dark:text-white">
                                        {{ item.quantity }}
                                    </span>
                                    <button
                                        @click="cart.updateQuantity(item.key, item.quantity + 1)"
                                        class="w-7 h-7 flex items-center justify-center text-slate-500 hover:text-slate-900 dark:hover:text-white"
                                    >
                                        <Plus class="w-3.5 h-3.5" />
                                    </button>
                                </div>

                                <div class="text-sm font-extrabold text-slate-900 dark:text-white font-mono min-w-[70px] text-right">
                                    ৳{{ item.price * item.quantity }}
                                </div>

                                <button
                                    @click="cart.removeItem(item.key)"
                                    class="p-2 rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition-colors"
                                    title="Remove item"
                                >
                                    <Trash2 class="w-4 h-4" />
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between items-center pt-2">
                        <Link
                            :href="route('shop.index')"
                            class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-600 hover:text-rose-600"
                        >
                            ← Continue Shopping
                        </Link>
                        <button
                            @click="cart.clearCart"
                            class="text-xs font-bold text-rose-600 hover:underline"
                        >
                            Clear All Bag Items
                        </button>
                    </div>
                </div>

                <!-- Right: Summary Sidebar (Col 9-12) -->
                <div class="lg:col-span-4">
                    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-6 shadow-sm space-y-6 sticky top-28">
                        <h2 class="text-sm font-extrabold uppercase tracking-wider text-slate-900 dark:text-white pb-3 border-b border-slate-100 dark:border-slate-800">
                            Order Summary
                        </h2>

                        <!-- Delivery Selection -->
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-700 dark:text-slate-300">Delivery Zone</label>
                            <select
                                v-model="cart.deliveryType"
                                class="w-full p-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-bold text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-rose-500/20"
                            >
                                <option value="inside_dhaka">Inside Dhaka (৳70 / Free over ৳2,500)</option>
                                <option value="dhaka_suburbs">Dhaka Suburbs / Savar / Gazipur (৳100)</option>
                                <option value="outside_dhaka">Outside Dhaka / 64 Districts (৳130)</option>
                            </select>
                        </div>

                        <!-- Price Breakdown -->
                        <div class="space-y-3 text-xs text-slate-600 dark:text-slate-400">
                            <div class="flex justify-between">
                                <span>Subtotal ({{ cart.totalCount }} items)</span>
                                <span class="font-bold text-slate-900 dark:text-white font-mono">৳{{ cart.subtotal }}</span>
                            </div>

                            <div v-if="cart.discountAmount > 0" class="flex justify-between text-emerald-600 font-semibold">
                                <span>Coupon Discount</span>
                                <span class="font-mono">-৳{{ cart.discountAmount }}</span>
                            </div>

                            <div class="flex justify-between">
                                <span>Delivery Charge</span>
                                <span class="font-mono font-bold">{{ cart.shippingCharge === 0 ? 'FREE' : '৳' + cart.shippingCharge }}</span>
                            </div>

                            <div class="flex justify-between text-base font-extrabold text-slate-900 dark:text-white pt-3 border-t border-slate-100 dark:border-slate-800">
                                <span>Total Amount</span>
                                <span class="text-rose-600 font-mono text-lg">৳{{ cart.grandTotal }}</span>
                            </div>
                        </div>

                        <!-- Proceed to Checkout -->
                        <div class="space-y-2.5">
                            <button
                                type="button"
                                @click="cart.proceedToCheckout()"
                                class="w-full py-4 px-6 rounded-2xl bg-gradient-to-r from-rose-600 to-rose-700 hover:from-rose-500 hover:to-rose-600 text-white text-xs font-extrabold uppercase tracking-wider shadow-xl shadow-rose-600/25 transition-all flex items-center justify-center gap-2 hover:scale-[1.01] cursor-pointer"
                            >
                                <span>Proceed to Checkout</span>
                                <ArrowRight class="w-4 h-4" />
                            </button>

                            <button
                                @click="handleWhatsAppCartOrder"
                                type="button"
                                class="w-full py-3.5 px-6 rounded-2xl bg-[#25D366] hover:bg-[#20bd5a] text-white text-xs font-bold tracking-wide shadow-lg shadow-emerald-500/20 transition-all flex items-center justify-center gap-2.5 cursor-pointer hover:scale-[1.01]"
                            >
                                <MessageCircle class="w-4 h-4 fill-white" />
                                <span>Order Bag via WhatsApp</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </StorefrontLayout>
</template>
