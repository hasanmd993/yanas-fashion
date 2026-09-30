<script setup>
import { ref, computed, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import StorefrontLayout from '@/Layouts/StorefrontLayout.vue';
import ProductCard from '@/Components/ProductCard.vue';
import {
    Filter,
    SlidersHorizontal,
    Search,
    X,
    ChevronRight,
    ChevronDown,
    ArrowUpDown,
    ShoppingBag
} from 'lucide-vue-next';

const props = defineProps({
    products: {
        type: Object,
        required: true,
    },
    categories: {
        type: Array,
        default: () => [],
    },
    parentCategories: {
        type: Array,
        default: () => [],
    },
    currentCategory: {
        type: Object,
        default: null,
    },
    activeParentCategory: {
        type: Object,
        default: null,
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
});

const isMobileFilterOpen = ref(false);
const searchQuery = ref(props.filters?.q || '');
const selectedSort = ref(props.filters?.sort || 'latest');

const applyFilter = (params) => {
    router.get(route('shop.index'), {
        ...props.filters,
        ...params,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const handleSortChange = () => {
    applyFilter({ sort: selectedSort.value });
};

const handleSearch = () => {
    applyFilter({ q: searchQuery.value });
};

const clearFilters = () => {
    searchQuery.value = '';
    selectedSort.value = 'latest';
    router.get(route('shop.index'));
};
</script>

<template>
    <Head :title="currentCategory ? `${currentCategory.name} — Shop Catalog` : 'All Products — Yanas Fashion'" />

    <StorefrontLayout>
        <!-- Page Header & Breadcrumb -->
        <div class="bg-white dark:bg-slate-900 border-b border-slate-100 dark:border-slate-800 py-8 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto">
                <nav class="flex items-center gap-2 text-xs text-slate-400 mb-3">
                    <Link :href="route('home')" class="hover:text-rose-600 transition-colors">Home</Link>
                    <ChevronRight class="w-3.5 h-3.5" />
                    <Link :href="route('shop.index')" class="hover:text-rose-600 transition-colors">Shop</Link>
                    <template v-if="currentCategory">
                        <ChevronRight class="w-3.5 h-3.5" />
                        <span class="text-slate-800 dark:text-white font-bold">{{ currentCategory.name }}</span>
                    </template>
                </nav>

                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white font-serif">
                            {{ currentCategory ? currentCategory.name : 'All Products' }}
                        </h1>
                        <p class="text-xs text-slate-500 mt-1">
                            Showing {{ products.total || (products.data ? products.data.length : 0) }} premium items
                        </p>
                    </div>

                    <!-- Sort Select & Mobile Filter Toggle -->
                    <div class="flex items-center gap-3">
                        <button
                            @click="isMobileFilterOpen = true"
                            class="lg:hidden inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold text-slate-800 dark:text-white"
                        >
                            <SlidersHorizontal class="w-4 h-4" /> Filters
                        </button>

                        <div class="relative flex items-center">
                            <select
                                v-model="selectedSort"
                                @change="handleSortChange"
                                class="appearance-none pl-3 pr-8 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-rose-500/20"
                            >
                                <option value="latest">Sort by: Newest</option>
                                <option value="price_low">Price: Low to High</option>
                                <option value="price_high">Price: High to Low</option>
                                <option value="popular">Most Popular</option>
                            </select>
                            <ChevronDown class="w-4 h-4 text-slate-400 absolute right-2.5 pointer-events-none" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Catalog Container -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <!-- Sidebar Filters (Desktop) -->
                <aside class="hidden lg:block space-y-6">
                    <!-- Search Filter -->
                    <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm">
                        <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-900 dark:text-white mb-3">
                            Search Products
                        </h3>
                        <form @submit.prevent="handleSearch" class="relative">
                            <input
                                v-model="searchQuery"
                                type="text"
                                placeholder="Search by title, SKU..."
                                class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:outline-none focus:ring-2 focus:ring-rose-500/20"
                            />
                            <Search class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" />
                        </form>
                    </div>

                    <!-- Categories Hierarchy -->
                    <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-900 dark:text-white">
                                Categories
                            </h3>
                            <button
                                v-if="currentCategory || searchQuery"
                                @click="clearFilters"
                                class="text-[11px] font-bold text-rose-600 hover:underline"
                            >
                                Reset
                            </button>
                        </div>

                        <ul class="space-y-1.5 text-xs">
                            <li>
                                <Link
                                    :href="route('shop.index')"
                                    class="flex items-center justify-between py-1.5 px-2.5 rounded-lg transition-colors font-bold"
                                    :class="!currentCategory ? 'bg-rose-50 dark:bg-rose-950/40 text-rose-600' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800'"
                                >
                                    <span>All Products</span>
                                </Link>
                            </li>
                            <li v-for="cat in parentCategories" :key="cat.id">
                                <Link
                                    :href="route('shop.index', { category: cat.slug })"
                                    class="flex items-center justify-between py-1.5 px-2.5 rounded-lg transition-colors font-semibold"
                                    :class="currentCategory?.slug === cat.slug ? 'bg-rose-50 dark:bg-rose-950/40 text-rose-600 font-bold' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800'"
                                >
                                    <span>{{ cat.name }}</span>
                                    <span v-if="cat.active_children?.length" class="text-[10px] text-slate-400">
                                        {{ cat.active_children.length }}
                                    </span>
                                </Link>

                                <!-- Subcategories -->
                                <ul v-if="cat.active_children?.length" class="pl-4 mt-1 space-y-1 border-l border-slate-100 dark:border-slate-800 ml-2">
                                    <li v-for="child in cat.active_children" :key="child.id">
                                        <Link
                                            :href="route('shop.index', { category: child.slug })"
                                            class="block py-1 px-2 rounded-md text-[11px] transition-colors"
                                            :class="currentCategory?.slug === child.slug ? 'text-rose-600 font-bold bg-rose-50/50' : 'text-slate-500 hover:text-slate-900 dark:hover:text-white'"
                                        >
                                            {{ child.name }}
                                        </Link>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </aside>

                <!-- Products Grid & Results -->
                <div class="lg:col-span-3 space-y-8">
                    <!-- Products Cards -->
                    <div v-if="products.data && products.data.length > 0" class="grid grid-cols-2 sm:grid-cols-3 gap-4 sm:gap-6">
                        <ProductCard
                            v-for="product in products.data"
                            :key="product.id"
                            :product="product"
                        />
                    </div>

                    <!-- Empty State -->
                    <div v-else class="py-20 text-center bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-8">
                        <div class="w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center mx-auto text-slate-400 mb-4">
                            <ShoppingBag class="w-8 h-8" />
                        </div>
                        <h3 class="text-base font-bold text-slate-800 dark:text-white">No products match your filter</h3>
                        <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                            Try adjusting your search query, sorting criteria, or selected category to view available luxury styles.
                        </p>
                        <button
                            @click="clearFilters"
                            class="mt-6 inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-rose-600 text-white font-bold text-xs shadow-md hover:bg-rose-500 transition-colors"
                        >
                            Reset All Filters
                        </button>
                    </div>

                    <!-- Pagination -->
                    <div v-if="products.links && products.links.length > 3" class="flex justify-center items-center gap-1.5 pt-6">
                        <template v-for="(link, index) in products.links" :key="index">
                            <Link
                                v-if="link.url"
                                :href="link.url"
                                class="px-3.5 py-2 rounded-xl text-xs font-bold transition-colors"
                                :class="link.active ? 'bg-rose-600 text-white shadow-md' : 'bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800'"
                                v-html="link.label"
                            />
                            <span
                                v-else
                                class="px-3 py-2 text-xs text-slate-300 dark:text-slate-700"
                                v-html="link.label"
                            />
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </StorefrontLayout>
</template>
