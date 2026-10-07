<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm, router, usePage } from '@inertiajs/vue3';
import StorefrontLayout from '@/Layouts/StorefrontLayout.vue';
import ProductCard from '@/Components/ProductCard.vue';
import { useCartStore } from '@/Stores/cart';
import {
    ShoppingBag,
    Zap,
    Heart,
    Share2,
    Star,
    Truck,
    ShieldCheck,
    RefreshCw,
    Check,
    ChevronRight,
    Ruler,
    X,
    MessageSquarePlus,
    MessageCircle
} from 'lucide-vue-next';

const props = defineProps({
    product: {
        type: Object,
        required: true,
    },
    relatedProducts: {
        type: Array,
        default: () => [],
    },
});

const cart = useCartStore();

// Safe Image URL Resolver
const resolveMediaUrl = (img) => {
    if (!img) return null;
    if (typeof img === 'object' && img.url) return img.url;
    if (typeof img !== 'string') return null;
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

// Images Gallery (combines primary_image, gallery_urls, gallery_items, and gallery raw)
const allImages = computed(() => {
    const list = [];
    
    // 1. Primary image
    const primary = resolveMediaUrl(props.product.primary_image || props.product.thumbnail);
    if (primary && !list.includes(primary)) {
        list.push(primary);
    }

    // 2. Gallery URLs (Backend Accessor)
    if (Array.isArray(props.product.gallery_urls) && props.product.gallery_urls.length > 0) {
        props.product.gallery_urls.forEach(url => {
            const resolved = resolveMediaUrl(url);
            if (resolved && !list.includes(resolved)) {
                list.push(resolved);
            }
        });
    }

    // 3. Gallery Items (Backend Accessor)
    if (Array.isArray(props.product.gallery_items) && props.product.gallery_items.length > 0) {
        props.product.gallery_items.forEach(item => {
            const resolved = resolveMediaUrl(item?.url || item?.raw);
            if (resolved && !list.includes(resolved)) {
                list.push(resolved);
            }
        });
    }

    // 4. Raw Gallery array fallback
    if (Array.isArray(props.product.gallery) && props.product.gallery.length > 0) {
        props.product.gallery.forEach(img => {
            const resolved = resolveMediaUrl(img);
            if (resolved && !list.includes(resolved)) {
                list.push(resolved);
            }
        });
    }

    return list.length > 0 ? list : ['/images/placeholder.jpg'];
});

const selectedImage = ref(allImages.value[0] || '/images/placeholder.jpg');

// Variants & Options
const availableSizes = computed(() => {
    if (Array.isArray(props.product.sizes)) return props.product.sizes;
    return [];
});

const availableColors = computed(() => {
    if (Array.isArray(props.product.colors)) return props.product.colors;
    return [];
});

const selectedSize = ref(availableSizes.value.length ? availableSizes.value[0] : null);
const selectedColor = ref(availableColors.value.length ? availableColors.value[0] : null);
const quantity = ref(1);

const isSizeModalOpen = ref(false);

const sellingPrice = computed(() => Number(props.product.effective_price || props.product.selling_price || props.product.regular_price || 0));
const regularPrice = computed(() => Number(props.product.regular_price || 0));
const discountPercent = computed(() => props.product.discount_percent || 0);
const isOutOfStock = computed(() => props.product.stock_quantity !== null && props.product.stock_quantity <= 0);

const handleAddToCart = () => {
    if (isOutOfStock.value) return;
    cart.addItem(props.product, quantity.value, selectedSize.value, selectedColor.value);
};

const page = usePage();

const handleBuyNow = () => {
    if (isOutOfStock.value) return;
    cart.addItem(props.product, quantity.value, selectedSize.value, selectedColor.value);
    cart.proceedToCheckout();
};

const handleWhatsAppOrder = () => {
    const rawNumber = page.props.settings?.whatsapp || '8801713580400';
    const cleanNumber = String(rawNumber).replace(/[^0-9]/g, '');
    const currentUrl = typeof window !== 'undefined' ? window.location.href : '';

    let text = `*✨ New Order Inquiry - Yanas Fashion ✨*\n\n`;
    text += `🛍️ *Product:* ${props.product.name}\n`;
    if (props.product.sku) {
        text += `🏷️ *SKU:* ${props.product.sku}\n`;
    }
    text += `💰 *Price:* ৳${sellingPrice.value}\n`;
    if (selectedSize.value) {
        text += `📏 *Size:* ${selectedSize.value}\n`;
    }
    if (selectedColor.value) {
        text += `🎨 *Color:* ${selectedColor.value}\n`;
    }
    text += `📦 *Quantity:* ${quantity.value}\n`;
    text += `💵 *Item Total:* ৳${sellingPrice.value * quantity.value}\n`;
    if (currentUrl) {
        text += `🔗 *Product Link:* ${currentUrl}\n\n`;
    }
    text += `Please confirm availability and process my order. Thank you!`;

    const url = `https://api.whatsapp.com/send?phone=${cleanNumber}&text=${encodeURIComponent(text)}`;
    window.open(url, '_blank');
};

// Review Form
const reviewForm = useForm({
    name: '',
    email: '',
    rating: 5,
    comment: '',
});

const submitReview = () => {
    reviewForm.post(route('product.review.store', props.product.id), {
        preserveScroll: true,
        onSuccess: () => reviewForm.reset(),
    });
};
</script>

<template>
    <Head :title="`${product.name} — Yanas Fashion`" />

    <StorefrontLayout>
        <!-- Breadcrumbs -->
        <div class="bg-white dark:bg-slate-900 border-b border-slate-100 dark:border-slate-800 py-3.5 px-4 sm:px-6 lg:px-8 text-xs text-slate-400">
            <div class="max-w-7xl mx-auto flex items-center gap-2 truncate">
                <Link :href="route('home')" class="hover:text-rose-600">Home</Link>
                <ChevronRight class="w-3.5 h-3.5 shrink-0" />
                <Link :href="route('shop.index')" class="hover:text-rose-600">Shop</Link>
                <template v-if="product.category">
                    <ChevronRight class="w-3.5 h-3.5 shrink-0" />
                    <Link :href="route('shop.index', { category: product.category.slug })" class="hover:text-rose-600">
                        {{ product.category.name }}
                    </Link>
                </template>
                <ChevronRight class="w-3.5 h-3.5 shrink-0" />
                <span class="text-slate-800 dark:text-white font-bold truncate">{{ product.name }}</span>
            </div>
        </div>

        <!-- Product Main Showcase Section -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
                <!-- Gallery Section (Col 1-7) -->
                <div class="lg:col-span-7 flex flex-col-reverse sm:flex-row gap-4">
                    <!-- Thumbnails Strip -->
                    <div v-if="allImages.length > 1" class="flex sm:flex-col gap-3 overflow-x-auto sm:overflow-y-auto sm:max-h-[560px] shrink-0 pb-2 sm:pb-0">
                        <button
                            v-for="(img, idx) in allImages"
                            :key="idx"
                            @click="selectedImage = img"
                            class="w-16 h-20 rounded-xl overflow-hidden border-2 bg-slate-100 dark:bg-slate-800 shrink-0 transition-all"
                            :class="selectedImage === img ? 'border-rose-600 shadow-md ring-2 ring-rose-500/20' : 'border-slate-200 dark:border-slate-700 opacity-70 hover:opacity-100'"
                        >
                            <img
                                :src="img"
                                :alt="`${product.name} preview ${idx + 1}`"
                                class="w-full h-full object-cover"
                                @error="$event.target.src = '/images/placeholder.jpg'"
                            />
                        </button>
                    </div>

                    <!-- Main Image Preview -->
                    <div class="flex-1 relative aspect-[3/4] rounded-3xl overflow-hidden bg-slate-100 dark:bg-slate-800 border border-slate-100 dark:border-slate-800 shadow-sm">
                        <img
                            :src="selectedImage"
                            :alt="product.name"
                            class="w-full h-full object-cover object-center transition-all duration-300"
                            @error="$event.target.src = '/images/placeholder.jpg'"
                        />

                        <!-- Badges Overlay -->
                        <div class="absolute top-4 left-4 flex flex-col gap-2">
                            <span v-if="discountPercent > 0" class="px-2.5 py-1 rounded-full bg-rose-600 text-white font-extrabold text-xs shadow-md">
                                -{{ discountPercent }}% OFF
                            </span>
                            <span v-if="product.is_featured" class="px-2.5 py-1 rounded-full bg-slate-900 text-white font-bold text-xs shadow-md">
                                Featured
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Product Purchase Details (Col 8-12) -->
                <div class="lg:col-span-5 space-y-6">
                    <div>
                        <div v-if="product.category" class="text-xs font-bold uppercase tracking-wider text-rose-600 mb-1.5">
                            {{ product.category.name }}
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white font-serif leading-tight">
                            {{ product.name }}
                        </h1>
                        <div class="flex items-center gap-3 mt-2 text-xs text-slate-500">
                            <span v-if="product.sku" class="font-mono">SKU: <strong class="text-slate-800 dark:text-slate-300">{{ product.sku }}</strong></span>
                            <span>•</span>
                            <span class="flex items-center gap-1 text-amber-500 font-bold">
                                <Star class="w-4 h-4 fill-amber-400" />
                                <span>{{ product.rating || 5.0 }}</span>
                                <span class="text-slate-400 font-normal">({{ product.reviews_count || 0 }} reviews)</span>
                            </span>
                        </div>
                    </div>

                    <!-- Price Card -->
                    <div class="p-4 rounded-2xl bg-rose-50/50 dark:bg-rose-950/20 border border-rose-100 dark:border-rose-900/40 flex items-center justify-between">
                        <div>
                            <div class="text-[11px] text-slate-500 font-semibold uppercase tracking-wider">Price</div>
                            <div class="flex items-baseline gap-2 font-mono mt-0.5">
                                <span class="text-2xl font-extrabold text-slate-900 dark:text-white">
                                    ৳{{ sellingPrice }}
                                </span>
                                <span v-if="regularPrice > sellingPrice" class="text-sm text-slate-400 line-through">
                                    ৳{{ regularPrice }}
                                </span>
                            </div>
                        </div>

                        <div v-if="regularPrice > sellingPrice" class="text-right">
                            <span class="inline-block px-2.5 py-1 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 font-bold text-xs">
                                You Save ৳{{ regularPrice - sellingPrice }}
                            </span>
                        </div>
                    </div>

                    <!-- Size Variant Selector -->
                    <div v-if="availableSizes.length > 0" class="space-y-2.5">
                        <div class="flex items-center justify-between text-xs font-bold">
                            <span class="text-slate-800 dark:text-white">Select Size:</span>
                            <button
                                v-if="product.size_chart_html"
                                @click="isSizeModalOpen = true"
                                class="inline-flex items-center gap-1 text-rose-600 hover:text-rose-700"
                            >
                                <Ruler class="w-3.5 h-3.5" /> Size Guide
                            </button>
                        </div>
                        <div class="flex flex-wrap gap-2.5">
                            <button
                                v-for="size in availableSizes"
                                :key="size"
                                @click="selectedSize = size"
                                class="min-w-[44px] h-11 px-3.5 rounded-xl text-xs font-bold font-mono transition-all border"
                                :class="selectedSize === size ? 'bg-slate-900 dark:bg-white text-white dark:text-slate-900 border-slate-900 shadow-md' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:border-slate-400'"
                            >
                                {{ size }}
                            </button>
                        </div>
                    </div>

                    <!-- Color Variant Selector -->
                    <div v-if="availableColors.length > 0" class="space-y-2.5">
                        <div class="text-xs font-bold text-slate-800 dark:text-white">
                            Select Color: <span class="font-normal text-slate-500">{{ selectedColor }}</span>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <button
                                v-for="color in availableColors"
                                :key="color"
                                @click="selectedColor = color"
                                class="px-3.5 py-2 rounded-xl text-xs font-semibold border transition-all"
                                :class="selectedColor === color ? 'bg-rose-50 dark:bg-rose-950/40 border-rose-500 text-rose-600 font-bold ring-2 ring-rose-500/20' : 'bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300'"
                            >
                                {{ color }}
                            </button>
                        </div>
                    </div>

                    <!-- Quantity Stepper & Buttons -->
                    <div class="space-y-3 pt-2">
                        <div class="flex items-center gap-3">
                            <div class="flex items-center border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-800 p-1">
                                <button
                                    @click="quantity = Math.max(1, quantity - 1)"
                                    class="w-9 h-9 flex items-center justify-center text-slate-500 hover:text-slate-900 dark:hover:text-white font-bold"
                                >
                                    -
                                </button>
                                <input
                                    v-model.number="quantity"
                                    type="number"
                                    min="1"
                                    class="w-12 text-center text-xs font-extrabold font-mono bg-transparent border-none focus:outline-none"
                                />
                                <button
                                    @click="quantity = quantity + 1"
                                    class="w-9 h-9 flex items-center justify-center text-slate-500 hover:text-slate-900 dark:hover:text-white font-bold"
                                >
                                    +
                                </button>
                            </div>

                            <!-- Add to Bag Button -->
                            <button
                                @click="handleAddToCart"
                                :disabled="isOutOfStock"
                                class="flex-1 py-3.5 px-6 rounded-xl bg-slate-900 hover:bg-slate-800 dark:bg-white dark:text-slate-900 dark:hover:bg-slate-100 text-white text-xs font-bold uppercase tracking-wider shadow-lg transition-all flex items-center justify-center gap-2 disabled:opacity-50"
                            >
                                <ShoppingBag class="w-4 h-4" />
                                <span>{{ isOutOfStock ? 'Sold Out' : 'Add to Bag' }}</span>
                            </button>
                        </div>

                        <!-- Buy It Now Button -->
                        <button
                            @click="handleBuyNow"
                            :disabled="isOutOfStock"
                            class="w-full py-4 px-6 rounded-xl bg-gradient-to-r from-rose-600 to-rose-700 hover:from-rose-500 hover:to-rose-600 text-white text-xs font-extrabold uppercase tracking-wider shadow-xl shadow-rose-600/25 transition-all flex items-center justify-center gap-2 disabled:opacity-50 hover:scale-[1.01] cursor-pointer"
                        >
                            <Zap class="w-4 h-4 fill-white" />
                            <span>Buy It Now (Express Checkout)</span>
                        </button>

                        <!-- Direct WhatsApp Quick Order Button -->
                        <button
                            @click="handleWhatsAppOrder"
                            type="button"
                            class="w-full py-3.5 px-6 rounded-xl bg-[#25D366] hover:bg-[#20bd5a] text-white text-xs font-black tracking-wide shadow-lg shadow-emerald-500/25 transition-all flex items-center justify-center gap-2.5 hover:scale-[1.01] cursor-pointer"
                        >
                            <MessageCircle class="w-4 h-4 fill-white" />
                            <span>Order via WhatsApp (হোয়াটসঅ্যাপে অর্ডার)</span>
                        </button>
                    </div>

                    <!-- Fast Delivery Info Card -->
                    <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 space-y-2.5 text-xs text-slate-600 dark:text-slate-400">
                        <div class="flex items-center gap-3">
                            <Truck class="w-4 h-4 text-rose-500 shrink-0" />
                            <span><strong>Inside Dhaka:</strong> ৳70 (24–48 Hours) | <strong>64 Districts:</strong> ৳130</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <ShieldCheck class="w-4 h-4 text-emerald-500 shrink-0" />
                            <span><strong>Free Delivery</strong> on orders over ৳2,500</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <RefreshCw class="w-4 h-4 text-sky-500 shrink-0" />
                            <span>Cash on Delivery & 7-Day Hassle-free Exchange</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Description & Specifications Section -->
            <div class="mt-16 pt-10 border-t border-slate-200 dark:border-slate-800">
                <div class="max-w-3xl mx-auto space-y-8">
                    <div>
                        <h2 class="text-lg font-extrabold text-slate-900 dark:text-white font-serif mb-4">
                            Product Details & Craftsmanship
                        </h2>
                        <div
                            class="prose prose-sm dark:prose-invert max-w-none text-slate-600 dark:text-slate-300 leading-relaxed"
                            v-html="product.description || product.short_desc || 'Handcrafted luxury apparel with premium detailing and tailored fit.'"
                        />
                    </div>
                </div>
            </div>

            <!-- Related Products Section -->
            <div v-if="relatedProducts.length > 0" class="mt-20 pt-10 border-t border-slate-200 dark:border-slate-800">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white font-serif">
                            You May Also Like
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">Explore matching luxury styles from this collection</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
                    <ProductCard
                        v-for="rel in relatedProducts"
                        :key="rel.id"
                        :product="rel"
                    />
                </div>
            </div>
        </section>

        <!-- Size Chart Modal -->
        <Transition
            enter-active-class="ease-out duration-200"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="ease-in duration-150"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="isSizeModalOpen" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
                <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 dark:border-slate-800 relative">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <Ruler class="w-5 h-5 text-rose-600" /> Size Chart & Measurements
                        </h3>
                        <button @click="isSizeModalOpen = false" class="p-1 text-slate-400 hover:text-slate-700">
                            <X class="w-5 h-5" />
                        </button>
                    </div>
                    <div class="prose prose-sm dark:prose-invert max-w-none text-xs" v-html="product.size_chart_html" />
                </div>
            </div>
        </Transition>
    </StorefrontLayout>
</template>
