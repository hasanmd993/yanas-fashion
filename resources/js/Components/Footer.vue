<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { Phone, Mail, MapPin, ShieldCheck, Truck, RefreshCw, MessageCircle } from 'lucide-vue-next';

const page = usePage();
const settings = computed(() => page.props.settings || {});
const categories = computed(() => page.props.navigation_categories || []);
</script>

<template>
    <footer class="bg-slate-950 text-slate-400 text-xs border-t border-slate-900 mt-20">
        <!-- Value Propositions Grid -->
        <div class="border-b border-slate-900/80 bg-slate-900/40">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="flex items-center gap-4 p-4 rounded-2xl bg-slate-900/60 border border-slate-800/60">
                        <div class="w-12 h-12 rounded-xl bg-rose-500/10 text-rose-400 flex items-center justify-center shrink-0">
                            <Truck class="w-6 h-6" />
                        </div>
                        <div>
                            <h4 class="font-bold text-white text-sm">64 Districts Delivery</h4>
                            <p class="text-[11px] text-slate-400 mt-0.5">Express shipping via Steadfast & Pathao</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 p-4 rounded-2xl bg-slate-900/60 border border-slate-800/60">
                        <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center shrink-0">
                            <ShieldCheck class="w-6 h-6" />
                        </div>
                        <div>
                            <h4 class="font-bold text-white text-sm">100% Authentic Quality</h4>
                            <p class="text-[11px] text-slate-400 mt-0.5">Premium curated fabrics & stitching</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 p-4 rounded-2xl bg-slate-900/60 border border-slate-800/60">
                        <div class="w-12 h-12 rounded-xl bg-sky-500/10 text-sky-400 flex items-center justify-center shrink-0">
                            <RefreshCw class="w-6 h-6" />
                        </div>
                        <div>
                            <h4 class="font-bold text-white text-sm">Easy Exchanges</h4>
                            <p class="text-[11px] text-slate-400 mt-0.5">Hassle-free 7-day size replacement</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 p-4 rounded-2xl bg-slate-900/60 border border-slate-800/60">
                        <div class="w-12 h-12 rounded-xl bg-purple-500/10 text-purple-400 flex items-center justify-center shrink-0">
                            <MessageCircle class="w-6 h-6" />
                        </div>
                        <div>
                            <h4 class="font-bold text-white text-sm">Live Support</h4>
                            <p class="text-[11px] text-slate-400 mt-0.5">Daily 10 AM to 11 PM WhatsApp hotline</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Footer Links -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-10">
                <!-- Col 1: Bio & Contact -->
                <div class="lg:col-span-2 space-y-4">
                    <div class="flex items-center gap-3">
                        <img
                            :src="settings.site_logo"
                            :alt="settings.site_name"
                            class="h-10 w-auto object-contain rounded-lg"
                        />
                        <div>
                            <span class="font-extrabold text-lg tracking-tight text-white block leading-none font-serif">
                                YANAS
                            </span>
                            <span class="text-[9px] tracking-[0.25em] text-rose-500 font-bold uppercase block mt-0.5">
                                LUXURY FASHION
                            </span>
                        </div>
                    </div>

                    <p class="text-xs leading-relaxed text-slate-400 max-w-sm">
                        Yanas Fashion is Bangladesh's premier luxury apparel brand specializing in contemporary tailored panjabis, festive wear, and modern lifestyle outfits with premium finishes.
                    </p>

                    <div class="space-y-2 pt-2">
                        <a :href="`tel:${settings.hotline}`" class="flex items-center gap-2.5 text-slate-300 hover:text-rose-400 transition-colors">
                            <Phone class="w-4 h-4 text-rose-500 shrink-0" />
                            <span>Hotline: <strong class="text-white">{{ settings.hotline }}</strong></span>
                        </a>
                        <a :href="`https://wa.me/${settings.whatsapp}`" target="_blank" class="flex items-center gap-2.5 text-slate-300 hover:text-emerald-400 transition-colors">
                            <MessageCircle class="w-4 h-4 text-emerald-500 shrink-0" />
                            <span>WhatsApp: {{ settings.whatsapp }}</span>
                        </a>
                        <div class="flex items-center gap-2.5 text-slate-300">
                            <MapPin class="w-4 h-4 text-rose-500 shrink-0" />
                            <span>{{ settings.address }}</span>
                        </div>
                    </div>
                </div>

                <!-- Col 2: Shop Categories -->
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-widest text-white mb-4">Categories</h3>
                    <ul class="space-y-2.5">
                        <li v-for="cat in categories.slice(0, 6)" :key="cat.id">
                            <Link :href="route('shop.index', { category: cat.slug })" class="hover:text-rose-400 transition-colors">
                                {{ cat.name }}
                            </Link>
                        </li>
                    </ul>
                </div>

                <!-- Col 3: Customer Care -->
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-widest text-white mb-4">Customer Care</h3>
                    <ul class="space-y-2.5">
                        <li><Link :href="route('tracking.index')" class="hover:text-rose-400 transition-colors">Track Order</Link></li>
                        <li><Link :href="route('shop.index')" class="hover:text-rose-400 transition-colors">All Products</Link></li>
                        <li><Link :href="route('cart.index')" class="hover:text-rose-400 transition-colors">Shopping Bag</Link></li>
                        <li><Link :href="route('checkout.index')" class="hover:text-rose-400 transition-colors">Express Checkout</Link></li>
                    </ul>
                </div>

                <!-- Col 4: Trust & Payment -->
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-widest text-white mb-4">Payment Methods</h3>
                    <p class="text-[11px] text-slate-400 mb-3">We accept all major mobile banking, card, and COD methods:</p>
                    <div class="flex flex-wrap gap-2 text-xs font-bold">
                        <span class="px-2.5 py-1 rounded bg-slate-900 border border-slate-800 text-rose-400 font-mono">bKash</span>
                        <span class="px-2.5 py-1 rounded bg-slate-900 border border-slate-800 text-amber-400 font-mono">Nagad</span>
                        <span class="px-2.5 py-1 rounded bg-slate-900 border border-slate-800 text-purple-400 font-mono">Rocket</span>
                        <span class="px-2.5 py-1 rounded bg-slate-900 border border-slate-800 text-emerald-400 font-mono">Cash on Delivery</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Copyright -->
        <div class="border-t border-slate-900 py-6 text-center text-[11px] text-slate-500">
            <div class="max-w-7xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-2">
                <p>© {{ new Date().getFullYear() }} {{ settings.site_name }}. All rights reserved.</p>
                <p>Designed with elegance for Bangladesh fashion enthusiasts.</p>
            </div>
        </div>
    </footer>
</template>
