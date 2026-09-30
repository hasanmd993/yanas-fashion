<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { useCartStore } from '@/Stores/cart';
import { ShoppingBag, X, Plus, Minus, Trash2, ArrowRight, Sparkles, Truck, MessageCircle } from 'lucide-vue-next';

const cart = useCartStore();
const page = usePage();

const freeShippingThreshold = 2500;
const freeShippingRemaining = computed(() => {
    return Math.max(0, freeShippingThreshold - cart.subtotal);
});
const freeShippingProgress = computed(() => {
    return Math.min(100, Math.round((cart.subtotal / freeShippingThreshold) * 100));
});

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
    text += `💵 *Grand Total:* ৳${cart.grandTotal}\n\n`;
    text += `Please confirm my order and provide delivery assistance. Thank you!`;

    const url = `https://api.whatsapp.com/send?phone=${cleanNumber}&text=${encodeURIComponent(text)}`;
    window.open(url, '_blank');
};
</script>

<template>
    <div>
        <!-- Backdrop -->
        <Transition
            enter-active-class="ease-in-out duration-300"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="ease-in-out duration-300"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="cart.isOpen"
                @click="cart.toggleDrawer(false)"
                class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[100] transition-opacity"
            />
        </Transition>

        <!-- Slide Drawer Panel -->
        <Transition
            enter-active-class="transform transition ease-in-out duration-300 sm:duration-400"
            enter-from-class="translate-x-full"
            enter-to-class="translate-x-0"
            leave-active-class="transform transition ease-in-out duration-300 sm:duration-400"
            leave-from-class="translate-x-0"
            leave-to-class="translate-x-full"
        >
            <div
                v-if="cart.isOpen"
                class="fixed inset-y-0 right-0 max-w-full flex pl-10 z-[101]"
            >
                <div class="w-screen max-w-md bg-white dark:bg-slate-900 shadow-2xl flex flex-col justify-between border-l border-slate-200 dark:border-slate-800">
                    <!-- Drawer Header -->
                    <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-xl bg-rose-50 dark:bg-rose-950/50 text-rose-600 flex items-center justify-center font-bold">
                                <ShoppingBag class="w-5 h-5" />
                            </div>
                            <div>
                                <h2 class="text-base font-bold text-slate-800 dark:text-white">Shopping Bag</h2>
                                <p class="text-xs text-slate-500">{{ cart.totalCount }} {{ cart.totalCount === 1 ? 'item' : 'items' }}</p>
                            </div>
                        </div>
                        <button
                            @click="cart.toggleDrawer(false)"
                            class="p-2 rounded-lg text-slate-400 hover:text-slate-700 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                        >
                            <X class="w-5 h-5" />
                        </button>
                    </div>

                    <!-- Free Shipping Bar -->
                    <div class="px-6 py-3 bg-rose-50/50 dark:bg-rose-950/20 border-b border-rose-100 dark:border-rose-900/30">
                        <div class="flex items-center justify-between text-xs font-semibold mb-1.5">
                            <span class="flex items-center gap-1.5 text-rose-700 dark:text-rose-400">
                                <Truck class="w-3.5 h-3.5" />
                                <span v-if="freeShippingRemaining > 0">
                                    Add <strong class="font-bold">৳{{ freeShippingRemaining }}</strong> more for <strong class="text-emerald-600">FREE Delivery</strong>
                                </span>
                                <span v-else class="text-emerald-600 font-bold flex items-center gap-1">
                                    <Sparkles class="w-3.5 h-3.5" /> You've unlocked FREE Delivery!
                                </span>
                            </span>
                            <span class="text-rose-600 font-mono text-[11px]">{{ freeShippingProgress }}%</span>
                        </div>
                        <div class="w-full bg-rose-100 dark:bg-rose-900/40 rounded-full h-1.5 overflow-hidden">
                            <div
                                class="bg-gradient-to-r from-rose-500 to-emerald-500 h-1.5 rounded-full transition-all duration-500"
                                :style="{ width: `${freeShippingProgress}%` }"
                            />
                        </div>
                    </div>

                    <!-- Items List -->
                    <div class="flex-1 overflow-y-auto px-6 py-4 space-y-4 divide-y divide-slate-100 dark:divide-slate-800">
                        <div v-if="cart.items.length === 0" class="py-16 text-center">
                            <div class="w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center mx-auto text-slate-400 mb-3">
                                <ShoppingBag class="w-8 h-8" />
                            </div>
                            <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200">Your bag is empty</h3>
                            <p class="text-xs text-slate-400 mt-1 max-w-xs mx-auto">Explore our luxury panjabis, festive collections, and trending styles.</p>
                            <button
                                @click="cart.toggleDrawer(false)"
                                class="mt-5 inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-slate-900 dark:bg-white text-white dark:text-slate-900 text-xs font-bold shadow-md hover:opacity-90 transition-opacity"
                            >
                                Start Shopping
                            </button>
                        </div>

                        <div
                            v-for="item in cart.items"
                            :key="item.key"
                            class="pt-4 first:pt-0 flex items-center gap-4"
                        >
                            <!-- Thumbnail -->
                            <img
                                :src="item.image"
                                :alt="item.name"
                                class="w-16 h-20 object-cover rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 shrink-0"
                            />

                            <!-- Details -->
                            <div class="flex-1 min-w-0">
                                <h4 class="text-xs font-bold text-slate-800 dark:text-white truncate">
                                    {{ item.name }}
                                </h4>
                                <div class="flex items-center gap-2 mt-1 text-[11px] text-slate-500">
                                    <span v-if="item.size" class="px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 font-mono">Size: {{ item.size }}</span>
                                    <span v-if="item.color" class="px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800">Color: {{ item.color }}</span>
                                </div>
                                <div class="mt-2 flex items-center justify-between">
                                    <div class="text-xs font-bold text-slate-900 dark:text-white font-mono">
                                        ৳{{ item.price }}
                                    </div>

                                    <!-- Quantity Stepper -->
                                    <div class="flex items-center border border-slate-200 dark:border-slate-700 rounded-lg overflow-hidden bg-slate-50 dark:bg-slate-800">
                                        <button
                                            @click="cart.updateQuantity(item.key, item.quantity - 1)"
                                            class="w-6 h-6 flex items-center justify-center text-slate-500 hover:text-slate-900 dark:hover:text-white"
                                        >
                                            <Minus class="w-3 h-3" />
                                        </button>
                                        <span class="w-7 text-center text-xs font-bold text-slate-800 dark:text-white font-mono">
                                            {{ item.quantity }}
                                        </span>
                                        <button
                                            @click="cart.updateQuantity(item.key, item.quantity + 1)"
                                            class="w-6 h-6 flex items-center justify-center text-slate-500 hover:text-slate-900 dark:hover:text-white"
                                        >
                                            <Plus class="w-3 h-3" />
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Remove Button -->
                            <button
                                @click="cart.removeItem(item.key)"
                                class="p-1.5 rounded-lg text-slate-300 hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition-colors"
                                title="Remove item"
                            >
                                <Trash2 class="w-4 h-4" />
                            </button>
                        </div>
                    </div>

                    <!-- Drawer Footer -->
                    <div v-if="cart.items.length > 0" class="px-6 py-4 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50 space-y-3">
                        <div class="space-y-1.5 text-xs text-slate-600 dark:text-slate-400">
                            <div class="flex justify-between">
                                <span>Subtotal</span>
                                <span class="font-bold text-slate-900 dark:text-white font-mono">৳{{ cart.subtotal }}</span>
                            </div>
                            <div v-if="cart.discountAmount > 0" class="flex justify-between text-emerald-600 font-semibold">
                                <span>Discount</span>
                                <span class="font-mono">-৳{{ cart.discountAmount }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Estimated Shipping</span>
                                <span class="font-mono">{{ cart.shippingCharge === 0 ? 'FREE' : '৳' + cart.shippingCharge }}</span>
                            </div>
                            <div class="flex justify-between text-sm font-extrabold text-slate-900 dark:text-white pt-2 border-t border-slate-200 dark:border-slate-800">
                                <span>Total Amount</span>
                                <span class="text-rose-600 font-mono text-base">৳{{ cart.grandTotal }}</span>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="pt-2 space-y-2">
                            <div class="grid grid-cols-2 gap-2.5">
                                <Link
                                    :href="route('cart.index')"
                                    @click="cart.toggleDrawer(false)"
                                    class="inline-flex items-center justify-center px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                                >
                                    View Cart
                                </Link>
                                <button
                                    type="button"
                                    @click="cart.proceedToCheckout()"
                                    class="inline-flex items-center justify-center gap-1.5 px-4 py-3 rounded-xl bg-gradient-to-r from-rose-600 to-rose-700 hover:from-rose-500 hover:to-rose-600 text-white text-xs font-bold shadow-lg shadow-rose-600/25 transition-all cursor-pointer"
                                >
                                    Checkout <ArrowRight class="w-4 h-4" />
                                </button>
                            </div>

                            <button
                                @click="handleWhatsAppCartOrder"
                                type="button"
                                class="w-full py-2.5 px-4 rounded-xl bg-[#25D366] hover:bg-[#20bd5a] text-white text-xs font-bold tracking-wide shadow-md shadow-emerald-500/20 transition-all flex items-center justify-center gap-2 cursor-pointer"
                            >
                                <MessageCircle class="w-4 h-4 fill-white" />
                                <span>Order Entire Bag on WhatsApp</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </Transition>
    </div>
</template>
