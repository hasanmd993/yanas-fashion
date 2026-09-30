<script setup>
import { ref, computed } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import { useCartStore } from '@/Stores/cart';
import {
    Search,
    ShoppingBag,
    Heart,
    User,
    Menu,
    X,
    Phone,
    MapPin,
    ChevronDown,
    Sparkles,
    ShieldAlert,
    LayoutDashboard
} from 'lucide-vue-next';

const page = usePage();
const cart = useCartStore();

const searchQuery = ref('');
const isMobileMenuOpen = ref(false);
const activeDropdown = ref(null);

const settings = computed(() => page.props.settings || {});
const categories = computed(() => page.props.navigation_categories || []);
const authUser = computed(() => page.props.auth?.user);

const handleSearch = () => {
    if (!searchQuery.value.trim()) return;
    router.get(route('shop.index'), { search: searchQuery.value }, { preserveState: true });
};
</script>

<template>
    <header class="w-full z-40 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md border-b border-slate-100 dark:border-slate-800 sticky top-0 transition-all">
        <!-- Top Announcement Bar -->
        <div class="bg-gradient-to-r from-slate-900 via-rose-950 to-slate-900 text-white text-[11px] py-1.5 px-4">
            <div class="max-w-7xl mx-auto flex items-center justify-between">
                <div class="flex items-center gap-2 truncate">
                    <span class="inline-flex items-center gap-1 bg-rose-500/20 border border-rose-500/30 text-rose-300 px-2 py-0.5 rounded-full font-bold text-[10px]">
                        <Sparkles class="w-3 h-3 text-rose-400" /> EID SPECIAL
                    </span>
                    <span class="truncate text-slate-300">
                        Free delivery in Dhaka on orders over <strong class="text-rose-300">৳2,500</strong> | Cash on Delivery Available
                    </span>
                </div>
                <div class="hidden sm:flex items-center gap-4 text-slate-300 font-medium">
                    <Link :href="route('tracking.index')" class="hover:text-white transition-colors flex items-center gap-1">
                        <MapPin class="w-3 h-3 text-rose-400" /> Order Tracking
                    </Link>
                    <a :href="`tel:${settings.hotline}`" class="hover:text-white transition-colors flex items-center gap-1">
                        <Phone class="w-3 h-3 text-rose-400" /> {{ settings.hotline }}
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Header -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20 gap-4">
                <!-- Mobile Menu Button -->
                <button
                    @click="isMobileMenuOpen = !isMobileMenuOpen"
                    class="lg:hidden p-2 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800"
                    aria-label="Toggle Navigation"
                >
                    <Menu v-if="!isMobileMenuOpen" class="w-6 h-6" />
                    <X v-else class="w-6 h-6" />
                </button>

                <!-- Brand Logo -->
                <Link :href="route('home')" class="flex items-center gap-3 shrink-0">
                    <img
                        :src="settings.site_logo"
                        :alt="settings.site_name"
                        class="h-10 w-auto object-contain rounded-lg"
                    />
                    <div class="hidden sm:block">
                        <span class="font-extrabold text-lg tracking-tight text-slate-900 dark:text-white block leading-none font-serif">
                            YANAS
                        </span>
                        <span class="text-[9px] tracking-[0.25em] text-rose-600 font-bold uppercase block mt-0.5">
                            LUXURY FASHION
                        </span>
                    </div>
                </Link>

                <!-- Desktop Navigation Links -->
                <nav class="hidden lg:flex items-center gap-8 text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-200">
                    <Link :href="route('home')" class="hover:text-rose-600 transition-colors py-2" :class="{ 'text-rose-600 font-extrabold': route().current('home') }">
                        Home
                    </Link>

                    <Link :href="route('shop.index')" class="hover:text-rose-600 transition-colors py-2" :class="{ 'text-rose-600 font-extrabold': route().current('shop.index') }">
                        All Products
                    </Link>

                    <!-- Categories Mega-Dropdown -->
                    <div class="relative group">
                        <button
                            @mouseenter="activeDropdown = 'categories'"
                            class="inline-flex items-center gap-1 hover:text-rose-600 transition-colors py-2"
                        >
                            Categories <ChevronDown class="w-3.5 h-3.5 opacity-60 group-hover:rotate-180 transition-transform duration-200" />
                        </button>

                        <div
                            class="absolute top-full left-0 w-64 bg-white dark:bg-slate-900 rounded-2xl shadow-xl border border-slate-100 dark:border-slate-800 py-3 px-2 invisible group-hover:visible opacity-0 group-hover:opacity-100 transition-all duration-200 transform translate-y-2 group-hover:translate-y-0"
                        >
                            <Link
                                v-for="cat in categories"
                                :key="cat.id"
                                :href="route('shop.index', { category: cat.slug })"
                                class="flex items-center justify-between px-3 py-2.5 rounded-xl hover:bg-rose-50 dark:hover:bg-rose-950/30 text-slate-700 dark:text-slate-200 hover:text-rose-600 text-xs font-semibold transition-colors"
                            >
                                <span>{{ cat.name }}</span>
                                <span v-if="cat.active_children?.length" class="text-[10px] text-slate-400">
                                    {{ cat.active_children.length }} items
                                </span>
                            </Link>
                        </div>
                    </div>

                    <Link :href="route('shop.index', { sort: 'newest' })" class="hover:text-rose-600 transition-colors py-2">
                        New Arrivals
                    </Link>

                    <Link :href="route('tracking.index')" class="hover:text-rose-600 transition-colors py-2" :class="{ 'text-rose-600 font-extrabold': route().current('tracking.*') }">
                        Track Order
                    </Link>
                </nav>

                <!-- Search Input Bar -->
                <div class="hidden md:flex flex-1 max-w-xs relative">
                    <form @submit.prevent="handleSearch" class="w-full relative">
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Search luxury outfits, panjabis..."
                            class="w-full pl-9 pr-4 py-2 rounded-full border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all text-slate-900 dark:text-white"
                        />
                        <Search class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" />
                    </form>
                </div>

                <!-- Right Action Buttons -->
                <div class="flex items-center gap-3">
                    <!-- Auth / Dashboard -->
                    <template v-if="authUser">
                        <a
                            v-if="authUser.is_admin"
                            :href="route('admin.dashboard')"
                            class="hidden sm:inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-slate-900 dark:bg-white text-white dark:text-slate-900 text-xs font-bold shadow-sm hover:opacity-90 transition-opacity"
                        >
                            <LayoutDashboard class="w-3.5 h-3.5" /> Admin
                        </a>
                        <div v-else class="hidden sm:flex items-center gap-1.5 text-xs font-bold text-slate-700 dark:text-slate-300">
                            <User class="w-4 h-4 text-rose-500" /> {{ authUser.name }}
                        </div>
                    </template>
                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="p-2.5 rounded-xl text-slate-700 dark:text-slate-300 hover:text-rose-600 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                            title="Account Login"
                        >
                            <User class="w-5 h-5" />
                        </Link>
                    </template>

                    <!-- Cart Toggle Button -->
                    <button
                        @click="cart.toggleDrawer(true)"
                        class="relative flex items-center gap-2 px-3.5 py-2.5 rounded-xl bg-gradient-to-r from-rose-600 to-rose-700 hover:from-rose-500 hover:to-rose-600 text-white shadow-md shadow-rose-600/20 transition-all"
                        aria-label="View Cart"
                    >
                        <ShoppingBag class="w-4 h-4" />
                        <span class="hidden sm:inline text-xs font-bold">Bag</span>
                        <span
                            v-if="cart.totalCount > 0"
                            class="bg-white text-rose-600 text-[11px] font-extrabold px-1.5 py-0.2 rounded-full min-w-[18px] text-center"
                        >
                            {{ cart.totalCount }}
                        </span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Search and Menu Drawer -->
        <div v-if="isMobileMenuOpen" class="lg:hidden border-t border-slate-100 dark:border-slate-800 px-4 py-4 space-y-4 bg-white dark:bg-slate-900">
            <form @submit.prevent="handleSearch" class="relative">
                <input
                    v-model="searchQuery"
                    type="text"
                    placeholder="Search catalog..."
                    class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:outline-none focus:ring-2 focus:ring-rose-500/20"
                />
                <Search class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" />
            </form>

            <nav class="flex flex-col space-y-2 text-xs font-bold uppercase tracking-wider text-slate-800 dark:text-slate-200">
                <Link :href="route('home')" @click="isMobileMenuOpen = false" class="px-3 py-2 rounded-lg hover:bg-rose-50 hover:text-rose-600">Home</Link>
                <Link :href="route('shop.index')" @click="isMobileMenuOpen = false" class="px-3 py-2 rounded-lg hover:bg-rose-50 hover:text-rose-600">All Products</Link>
                <div class="px-3 py-1 text-[10px] text-slate-400 font-extrabold uppercase">Categories</div>
                <Link
                    v-for="cat in categories"
                    :key="cat.id"
                    :href="route('shop.index', { category: cat.slug })"
                    @click="isMobileMenuOpen = false"
                    class="pl-6 py-1.5 text-slate-600 hover:text-rose-600"
                >
                    {{ cat.name }}
                </Link>
                <Link :href="route('tracking.index')" @click="isMobileMenuOpen = false" class="px-3 py-2 rounded-lg hover:bg-rose-50 hover:text-rose-600">Track Order</Link>
            </nav>
        </div>
    </header>
</template>
