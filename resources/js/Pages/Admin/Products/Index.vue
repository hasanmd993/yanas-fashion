<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import {
    Plus,
    Search,
    Edit,
    Trash2,
    ExternalLink,
    ShoppingBag,
    SlidersHorizontal,
    ChevronDown
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
    filters: {
        type: Object,
        default: () => ({}),
    },
});

const searchQuery = ref(props.filters?.search || '');
const selectedCategory = ref(props.filters?.category_id || '');

const handleFilter = () => {
    router.get(route('admin.products.index'), {
        search: searchQuery.value,
        category_id: selectedCategory.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const clearFilters = () => {
    searchQuery.value = '';
    selectedCategory.value = '';
    handleFilter();
};

const handleDelete = (id, title) => {
    if (confirm(`Are you sure you want to delete product "${title}"?`)) {
        router.delete(route('admin.products.destroy', id));
    }
};
</script>

<template>
    <Head title="Products Management — Admin" />

    <AdminLayout title="Products Catalog">
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white font-serif">
                        Products Catalog
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Manage inventory, prices, sizes, and showcase images ({{ products.total }} items total)
                    </p>
                </div>

                <Link
                    :href="route('admin.products.create')"
                    class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-[#730163] hover:bg-[#5b014e] text-white text-xs font-bold shadow-md shadow-[#730163]/25 transition-all hover:scale-105"
                >
                    <Plus class="w-4 h-4" /> Add New Product
                </Link>
            </div>

            <!-- Filter & Search Bar -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-4 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="w-full sm:max-w-xs relative">
                    <input
                        v-model="searchQuery"
                        @keyup.enter="handleFilter"
                        type="text"
                        placeholder="Search by title, SKU..."
                        class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:outline-none focus:ring-2 focus:ring-[#730163]/20"
                    />
                    <Search class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" />
                </div>

                <div class="flex flex-wrap items-center gap-2.5 w-full sm:w-auto">
                    <!-- Dynamic Category Dropdown -->
                    <select
                        v-model="selectedCategory"
                        @change="handleFilter"
                        class="w-full sm:w-auto px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-bold text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-[#730163]/20 cursor-pointer"
                    >
                        <option value="">All Categories</option>
                        <option
                            v-for="cat in categories"
                            :key="cat.id"
                            :value="cat.id"
                        >
                            {{ cat.name }}
                        </option>
                    </select>

                    <button
                        @click="handleFilter"
                        class="px-4 py-2.5 rounded-xl bg-slate-900 dark:bg-white text-white dark:text-slate-900 text-xs font-bold hover:opacity-90 transition-opacity"
                    >
                        Filter
                    </button>

                    <button
                        v-if="selectedCategory || searchQuery"
                        @click="clearFilters"
                        class="px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-500 hover:text-rose-600 hover:border-rose-300 text-xs font-bold transition-colors"
                        title="Clear Filters"
                    >
                        Reset
                    </button>
                </div>
            </div>

            <!-- Products Table -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-slate-100 dark:border-slate-800 text-slate-400 uppercase font-bold text-[10px] bg-slate-50/50 dark:bg-slate-800/30">
                                <th class="py-3.5 px-4">Thumbnail & Title</th>
                                <th class="py-3.5 px-3">SKU</th>
                                <th class="py-3.5 px-3">Category</th>
                                <th class="py-3.5 px-3">Pricing</th>
                                <th class="py-3.5 px-3">Stock</th>
                                <th class="py-3.5 px-3">Status</th>
                                <th class="py-3.5 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            <tr
                                v-for="product in products.data"
                                :key="product.id"
                                class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors"
                            >
                                <td class="py-3.5 px-4">
                                    <div class="flex items-center gap-3">
                                        <img
                                            :src="product.primary_image || (product.thumbnail ? (product.thumbnail.startsWith('http') ? product.thumbnail : (product.thumbnail.startsWith('/') ? product.thumbnail : `/${product.thumbnail}`)) : '/images/placeholder.jpg')"
                                            :alt="product.title"
                                            class="w-12 h-14 object-cover rounded-xl border border-slate-100 dark:border-slate-800 bg-slate-50 shrink-0"
                                        />
                                        <div class="min-w-0">
                                            <div class="font-bold text-slate-900 dark:text-white line-clamp-1">
                                                {{ product.title }}
                                            </div>
                                            <div v-if="product.title_bn" class="text-[10px] text-slate-400 truncate">
                                                {{ product.title_bn }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3.5 px-3 font-mono text-slate-500 font-bold">
                                    {{ product.sku }}
                                </td>
                                <td class="py-3.5 px-3 text-slate-600 dark:text-slate-300">
                                    {{ product.category?.name || 'Unassigned' }}
                                </td>
                                <td class="py-3.5 px-3 font-mono">
                                    <div class="font-bold text-slate-900 dark:text-white">
                                        ৳{{ product.effective_price || product.selling_price || product.regular_price }}
                                    </div>
                                    <div v-if="product.sale_price && product.regular_price > product.sale_price" class="text-[10px] text-slate-400 line-through">
                                        ৳{{ product.regular_price }}
                                    </div>
                                </td>
                                <td class="py-3.5 px-3 font-mono">
                                    <span
                                        class="px-2 py-0.5 rounded-lg text-[11px] font-bold"
                                        :class="product.stock_qty <= 5 ? 'bg-amber-500/10 text-amber-600' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300'"
                                    >
                                        {{ product.stock_qty }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-3">
                                    <span
                                        class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider"
                                        :class="product.is_active ? 'bg-emerald-500/10 text-emerald-600' : 'bg-slate-100 text-slate-400'"
                                    >
                                        {{ product.is_active ? 'Active' : 'Draft' }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a
                                            :href="route('product.show', product.slug)"
                                            target="_blank"
                                            class="p-2 rounded-xl text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors"
                                            title="View on Storefront"
                                        >
                                            <ExternalLink class="w-4 h-4" />
                                        </a>

                                        <Link
                                            :href="route('admin.products.edit', product.id)"
                                            class="p-2 rounded-xl text-slate-400 hover:text-[#730163] hover:bg-purple-50 dark:hover:bg-purple-950/40 transition-colors"
                                            title="Edit Product"
                                        >
                                            <Edit class="w-4 h-4" />
                                        </Link>

                                        <button
                                            @click="handleDelete(product.id, product.title)"
                                            class="p-2 rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition-colors"
                                            title="Delete Product"
                                        >
                                            <Trash2 class="w-4 h-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr v-if="products.data.length === 0">
                                <td colspan="7" class="py-12 text-center text-slate-400">
                                    <ShoppingBag class="w-8 h-8 mx-auto mb-2 opacity-40" />
                                    No products found matching your filter criteria.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="products.links && products.links.length > 3" class="p-4 border-t border-slate-100 dark:border-slate-800 flex justify-center items-center gap-1.5">
                    <template v-for="(link, index) in products.links" :key="index">
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
