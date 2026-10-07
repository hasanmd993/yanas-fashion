<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { useCartStore } from '@/Stores/cart';
import { ShoppingBag, Eye, Star, Heart } from 'lucide-vue-next';

const props = defineProps({
    product: {
        type: Object,
        required: true,
    },
});

const cart = useCartStore();

const resolveMediaUrl = (img) => {
    if (!img) return '/images/placeholder.jpg';
    if (typeof img === 'object' && img.url) return img.url;
    if (typeof img !== 'string') return '/images/placeholder.jpg';
    if (img.startsWith('http://') || img.startsWith('https://')) return img;
    const trimmed = img.replace(/^\/+/, '');
    if (trimmed.startsWith('assets/') || trimmed.startsWith('images/')) {
        return `/${trimmed}`;
    }
    if (trimmed.startsWith('storage/')) {
        return `/${trimmed}`;
    }
    return `/storage/${trimmed}`;
};

const primaryImage = computed(() => {
    const raw = props.product.primary_image || props.product.thumbnail || (props.product.gallery && props.product.gallery[0]) || props.product.image_url || (props.product.images && props.product.images[0]?.image_url);
    return resolveMediaUrl(raw);
});

const secondaryImage = computed(() => {
    if (props.product.gallery && props.product.gallery.length > 1) {
        return resolveMediaUrl(props.product.gallery[1]);
    }
    if (props.product.images && props.product.images.length > 1) {
        return resolveMediaUrl(props.product.images[1].image_url);
    }
    return primaryImage.value;
});

const sellingPrice = computed(() => {
    return Number(props.product.effective_price || props.product.selling_price || props.product.regular_price || 0);
});

const regularPrice = computed(() => {
    return Number(props.product.regular_price || 0);
});

const discountPercentage = computed(() => {
    if (regularPrice.value > sellingPrice.value && regularPrice.value > 0) {
        return Math.round(((regularPrice.value - sellingPrice.value) / regularPrice.value) * 100);
    }
    return 0;
});

const isOutOfStock = computed(() => {
    return props.product.stock_quantity !== null && props.product.stock_quantity <= 0;
});

const handleQuickAdd = (e) => {
    e.preventDefault();
    if (isOutOfStock.value) return;

    // Pick first available size if exists
    const defaultSize = props.product.sizes?.length ? props.product.sizes[0] : null;
    const defaultColor = props.product.colors?.length ? props.product.colors[0] : null;

    cart.addItem(props.product, 1, defaultSize, defaultColor);
};
</script>

<template>
    <div class="group relative bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col overflow-hidden">
        <!-- Image Container -->
        <div class="relative aspect-[3/4] bg-slate-100 dark:bg-slate-800 overflow-hidden">
            <Link :href="route('product.show', product.slug)" class="block w-full h-full">
                <!-- Primary Image -->
                <img
                    :src="primaryImage"
                    :alt="product.name"
                    class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-500 ease-out"
                    loading="lazy"
                    @error="$event.target.src = '/images/placeholder.jpg'"
                />
            </Link>

            <!-- Badges -->
            <div class="absolute top-3 left-3 flex flex-col gap-1.5 z-10 pointer-events-none">
                <span
                    v-if="discountPercentage > 0"
                    class="px-2 py-0.5 rounded-full bg-rose-600 text-white font-extrabold text-[10px] tracking-wider uppercase shadow-sm"
                >
                    -{{ discountPercentage }}% OFF
                </span>
                <span
                    v-if="product.is_featured"
                    class="px-2 py-0.5 rounded-full bg-slate-900 text-white font-bold text-[10px] uppercase shadow-sm"
                >
                    Featured
                </span>
                <span
                    v-if="isOutOfStock"
                    class="px-2 py-0.5 rounded-full bg-slate-800/90 text-rose-300 font-bold text-[10px] uppercase shadow-sm"
                >
                    Sold Out
                </span>
            </div>

            <!-- Mobile Quick Add Floating Button (Touch-Friendly) -->
            <button
                @click.prevent="handleQuickAdd"
                :disabled="isOutOfStock"
                class="sm:hidden absolute bottom-2 right-2 p-2 rounded-xl bg-white/95 dark:bg-slate-900/95 text-slate-800 dark:text-white shadow-md active:scale-90 transition-transform flex items-center justify-center disabled:opacity-40 z-10"
                :title="isOutOfStock ? 'Sold Out' : 'Quick Add'"
                :aria-label="isOutOfStock ? 'Sold Out' : 'Quick Add'"
            >
                <ShoppingBag class="w-3.5 h-3.5 text-rose-600" />
            </button>

            <!-- Desktop Quick Add Button Overlay (Hover) -->
            <div class="hidden sm:flex absolute inset-x-3 bottom-3 opacity-0 group-hover:opacity-100 transform translate-y-2 group-hover:translate-y-0 transition-all duration-300 gap-2 z-10">
                <button
                    @click="handleQuickAdd"
                    :disabled="isOutOfStock"
                    class="flex-1 py-2.5 px-3 rounded-xl bg-white/95 dark:bg-slate-900/95 text-slate-900 dark:text-white font-bold text-xs shadow-lg hover:bg-rose-600 hover:text-white transition-colors flex items-center justify-center gap-1.5 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <ShoppingBag class="w-3.5 h-3.5" />
                    <span>{{ isOutOfStock ? 'Out of Stock' : 'Quick Bag' }}</span>
                </button>
                <Link
                    :href="route('product.show', product.slug)"
                    class="p-2.5 rounded-xl bg-white/95 dark:bg-slate-900/95 text-slate-900 dark:text-white hover:bg-rose-600 hover:text-white shadow-lg transition-colors flex items-center justify-center"
                    title="View Details"
                >
                    <Eye class="w-4 h-4" />
                </Link>
            </div>
        </div>

        <!-- Product Info Content -->
        <div class="p-3 sm:p-4 flex-1 flex flex-col justify-between">
            <div>
                <!-- Category -->
                <div v-if="product.category" class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-rose-600 mb-1">
                    {{ product.category.name }}
                </div>

                <!-- Title -->
                <h3 class="text-xs font-bold text-slate-800 dark:text-white leading-snug line-clamp-2 hover:text-rose-600 transition-colors">
                    <Link :href="route('product.show', product.slug)">
                        {{ product.name }}
                    </Link>
                </h3>
            </div>

            <!-- Pricing Row -->
            <div class="mt-2.5 sm:mt-3 pt-2 sm:pt-2.5 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <div class="flex items-baseline gap-1 sm:gap-1.5 font-mono">
                    <span class="text-xs sm:text-sm font-extrabold text-slate-900 dark:text-white">
                        ৳{{ sellingPrice }}
                    </span>
                    <span
                        v-if="regularPrice > sellingPrice"
                        class="text-[10px] sm:text-[11px] text-slate-400 line-through"
                    >
                        ৳{{ regularPrice }}
                    </span>
                </div>

                <div v-if="product.sku" class="text-[9px] sm:text-[10px] text-slate-400 font-mono">
                    {{ product.sku }}
                </div>
            </div>
        </div>
    </div>
</template>
