<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import StorefrontLayout from '@/Layouts/StorefrontLayout.vue';
import ProductCard from '@/Components/ProductCard.vue';
import {
    Sparkles,
    ArrowRight,
    ChevronLeft,
    ChevronRight,
    ShieldCheck,
    Truck,
    Flame,
    TrendingUp,
    Clock,
    Star
} from 'lucide-vue-next';

const props = defineProps({
    sliders: {
        type: Array,
        default: () => [],
    },
    categories: {
        type: Array,
        default: () => [],
    },
    featuredProducts: {
        type: Array,
        default: () => [],
    },
    trendingProducts: {
        type: Array,
        default: () => [],
    },
    latestProducts: {
        type: Array,
        default: () => [],
    },
});

const currentSlide = ref(0);
let slideTimer = null;

const startAutoplay = () => {
    stopAutoplay();
    if (props.sliders.length > 1) {
        slideTimer = setInterval(nextSlide, 5000);
    }
};

const stopAutoplay = () => {
    if (slideTimer) {
        clearInterval(slideTimer);
        slideTimer = null;
    }
};

const nextSlide = () => {
    if (props.sliders.length === 0) return;
    currentSlide.value = (currentSlide.value + 1) % props.sliders.length;
};

const prevSlide = () => {
    if (props.sliders.length === 0) return;
    currentSlide.value = (currentSlide.value - 1 + props.sliders.length) % props.sliders.length;
};

const goToSlide = (index) => {
    currentSlide.value = index;
    startAutoplay();
};

// Touch gestures handling for mobile swipe
const touchStartX = ref(0);
const touchStartY = ref(0);
const isSwiping = ref(false);

const onTouchStart = (e) => {
    if (props.sliders.length <= 1) return;
    touchStartX.value = e.touches[0].clientX;
    touchStartY.value = e.touches[0].clientY;
    isSwiping.value = true;
    stopAutoplay();
};

const onTouchMove = () => {
    // Keep touch state active
};

const onTouchEnd = (e) => {
    if (!isSwiping.value) return;
    isSwiping.value = false;

    const touchEndX = e.changedTouches[0].clientX;
    const touchEndY = e.changedTouches[0].clientY;
    const diffX = touchEndX - touchStartX.value;
    const diffY = touchEndY - touchStartY.value;

    // Verify intentional horizontal swipe (horizontal movement significantly greater than vertical movement)
    // and threshold >= 40px
    if (Math.abs(diffX) > Math.abs(diffY) && Math.abs(diffX) > 40) {
        if (diffX < 0) {
            nextSlide();
        } else {
            prevSlide();
        }
    }

    startAutoplay();
};

onMounted(() => {
    startAutoplay();
});

onUnmounted(() => {
    stopAutoplay();
});

// Active product showcase tab
const activeTab = ref('featured'); // 'featured', 'trending', 'latest'

const currentProducts = computed(() => {
    if (activeTab.value === 'trending') return props.trendingProducts;
    if (activeTab.value === 'latest') return props.latestProducts;
    return props.featuredProducts;
});

// Detect whether a slider has a real editorial title or is a graphic banner
const hasTextOverlay = (slider) => {
    if (!slider || !slider.title) return false;
    const t = slider.title.trim().toLowerCase();
    if (
        t.startsWith('cover') ||
        t.startsWith('banner') ||
        t.startsWith('slide') ||
        t === 'untitled' ||
        t === 'yanas' ||
        t === 'yanas fashion'
    ) {
        return false;
    }
    return true;
};
</script>

<template>
    <Head title="Luxury Bangladeshi Fashion, Tailored Panjabis & Modern Outfits" />

    <StorefrontLayout>
        <!-- Hero Slider Section (Mobile Optimized, Crisp Artwork, Touch Swipe Enabled) -->
        <section
            class="relative bg-slate-950 text-white overflow-hidden select-none"
            @mouseenter="stopAutoplay"
            @mouseleave="startAutoplay"
            @touchstart="onTouchStart"
            @touchmove="onTouchMove"
            @touchend="onTouchEnd"
        >
            <div v-if="sliders.length > 0" class="relative w-full h-[220px] xs:h-[260px] sm:h-[360px] md:h-[420px] lg:h-[480px] flex items-center touch-pan-y overflow-hidden">
                <div
                    v-for="(slider, index) in sliders"
                    :key="slider.id"
                    class="absolute inset-0 transition-opacity duration-700 ease-in-out"
                    :class="index === currentSlide ? 'opacity-100 z-10' : 'opacity-0 z-0 pointer-events-none'"
                >
                    <!-- Clickable Banner Slide Link -->
                    <Link
                        :href="slider.button_link || route('shop.index')"
                        class="block w-full h-full relative group cursor-pointer"
                    >
                        <!-- Background Image: Perfectly Centered and Un-Cropped -->
                        <img
                            :src="slider.image_url || (slider.image ? (slider.image.startsWith('http') ? slider.image : (slider.image.startsWith('/') ? slider.image : `/${slider.image}`)) : '/images/hero-bg.jpg')"
                            :alt="slider.title || 'Yanas Fashion'"
                            class="w-full h-full object-cover object-center transition-transform duration-700 sm:group-hover:scale-[1.01]"
                            loading="eager"
                        />

                        <!-- Scrim Gradient: ONLY applied when there is real text overlay to ensure text contrast -->
                        <div
                            v-if="hasTextOverlay(slider)"
                            class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/40 to-transparent sm:bg-gradient-to-r sm:from-slate-950/85 sm:via-slate-950/40 sm:to-transparent pointer-events-none"
                        />

                        <!-- Floating Action Pill for Graphic Banners (Clean, Non-Intrusive, Luxury Feel) -->
                        <div
                            v-if="!hasTextOverlay(slider) && slider.button_text"
                            class="absolute bottom-3 right-3 sm:bottom-6 sm:right-8 z-20 pointer-events-none"
                        >
                            <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 sm:px-5 sm:py-2.5 rounded-full bg-slate-950/85 hover:bg-rose-600 text-white font-bold text-[10px] sm:text-xs uppercase tracking-wider backdrop-blur-md shadow-xl border border-white/15 transition-all">
                                {{ slider.button_text }} <ArrowRight class="w-3 h-3 sm:w-3.5 sm:h-3.5" />
                            </span>
                        </div>
                    </Link>

                    <!-- Text Overlay Content (Rendered only when a real headline/promotion exists) -->
                    <div
                        v-if="hasTextOverlay(slider)"
                        class="absolute inset-0 flex items-end pb-8 sm:items-center sm:pb-0 z-20 pointer-events-none"
                    >
                        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
                            <div class="max-w-lg space-y-2.5 sm:space-y-4 py-4 sm:py-12 pointer-events-auto">
                                <div v-if="slider.tag" class="inline-flex items-center gap-1.5 px-2.5 py-0.5 sm:px-3 sm:py-1 rounded-full bg-rose-500/25 border border-rose-500/40 text-rose-300 text-[10px] sm:text-[11px] font-bold uppercase tracking-wider backdrop-blur-md">
                                    <Sparkles class="w-3 h-3 text-rose-400" /> {{ slider.tag }}
                                </div>

                                <h1 class="text-xl sm:text-3xl lg:text-4xl font-extrabold text-white tracking-tight leading-snug font-serif drop-shadow-md">
                                    {{ slider.title }}
                                </h1>

                                <p v-if="slider.subtitle" class="text-xs sm:text-sm text-slate-200 leading-relaxed max-w-md line-clamp-2 drop-shadow">
                                    {{ slider.subtitle }}
                                </p>

                                <div class="flex flex-wrap items-center gap-2 sm:gap-3 pt-1">
                                    <Link
                                        v-if="slider.button_text"
                                        :href="slider.button_link || route('shop.index')"
                                        class="inline-flex items-center gap-2 px-4 py-2 sm:px-6 sm:py-2.5 rounded-xl bg-gradient-to-r from-rose-600 to-rose-700 hover:from-rose-500 hover:to-rose-600 text-white font-bold text-xs uppercase tracking-wider shadow-lg shadow-rose-600/30 transition-all active:scale-95 sm:hover:scale-105"
                                    >
                                        {{ slider.button_text }} <ArrowRight class="w-3.5 h-3.5" />
                                    </Link>

                                    <Link
                                        v-if="slider.secondary_button_text"
                                        :href="slider.secondary_button_link || route('shop.index')"
                                        class="inline-flex items-center gap-2 px-3.5 py-2 sm:px-5 sm:py-2.5 rounded-xl bg-white/15 hover:bg-white/25 border border-white/25 text-white font-bold text-xs uppercase tracking-wider backdrop-blur-sm transition-all active:scale-95"
                                    >
                                        {{ slider.secondary_button_text }}
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Slider Arrows: Desktop and Tablet -->
                <div v-if="sliders.length > 1" class="hidden sm:flex absolute inset-x-4 lg:inset-x-8 top-1/2 -translate-y-1/2 justify-between z-30 pointer-events-none">
                    <button
                        @click="prevSlide"
                        class="pointer-events-auto p-3 rounded-full bg-slate-900/60 hover:bg-rose-600 text-white backdrop-blur-md border border-white/10 transition-all shadow-lg hover:scale-110 active:scale-95"
                        aria-label="Previous Slide"
                    >
                        <ChevronLeft class="w-5 h-5" />
                    </button>
                    <button
                        @click="nextSlide"
                        class="pointer-events-auto p-3 rounded-full bg-slate-900/60 hover:bg-rose-600 text-white backdrop-blur-md border border-white/10 transition-all shadow-lg hover:scale-110 active:scale-95"
                        aria-label="Next Slide"
                    >
                        <ChevronRight class="w-5 h-5" />
                    </button>
                </div>

                <!-- Slider Indicators: High-Contrast Floating Capsule (Visible on any background) -->
                <div v-if="sliders.length > 1" class="absolute bottom-2.5 sm:bottom-4 inset-x-0 flex justify-center items-center z-30 pointer-events-none">
                    <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-slate-950/40 backdrop-blur-md border border-white/10 pointer-events-auto">
                        <button
                            v-for="(_, idx) in sliders"
                            :key="idx"
                            @click="goToSlide(idx)"
                            class="h-1.5 rounded-full transition-all duration-300"
                            :class="idx === currentSlide ? 'w-5 sm:w-7 bg-rose-500' : 'w-1.5 bg-white/40 hover:bg-white/80'"
                            :aria-label="`Go to slide ${idx + 1}`"
                        />
                    </div>
                </div>
            </div>

            <!-- Fallback Static Hero if no sliders exist -->
            <div v-else class="relative py-16 sm:py-24 px-4 max-w-7xl mx-auto flex flex-col items-center text-center">
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-rose-500/20 border border-rose-500/30 text-rose-300 text-xs font-bold uppercase tracking-widest mb-4">
                    <Sparkles class="w-3.5 h-3.5" /> Festive Collection 2026
                </span>
                <h1 class="text-3xl sm:text-5xl lg:text-6xl font-extrabold text-white font-serif max-w-2xl leading-tight">
                    Contemporary Luxury & Tailored Panjabis
                </h1>
                <p class="text-slate-300 text-xs sm:text-sm max-w-lg mt-4 mb-8">
                    Discover handpicked festive designs, luxury stitched fabrics, and modern lifestyle outfits delivered right to your doorstep.
                </p>
                <Link
                    :href="route('shop.index')"
                    class="inline-flex items-center gap-2 px-7 py-3.5 sm:px-8 sm:py-4 rounded-xl bg-gradient-to-r from-rose-600 to-rose-700 text-white font-bold text-xs uppercase tracking-wider shadow-lg shadow-rose-600/30 hover:scale-105 active:scale-95 transition-all"
                >
                    Explore Shop <ArrowRight class="w-4 h-4" />
                </Link>
            </div>
        </section>

        <!-- Categories Visual Circular Grid -->
        <section v-if="categories.length > 0" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14">
            <div class="flex items-center justify-between mb-6 sm:mb-8">
                <div>
                    <h2 class="text-lg sm:text-2xl font-extrabold text-slate-900 dark:text-white font-serif">
                        Shop By Category
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">Explore curated styles tailored for every occasion</p>
                </div>
                <Link
                    :href="route('shop.index')"
                    class="inline-flex items-center gap-1.5 text-xs font-bold text-rose-600 hover:text-rose-700"
                >
                    View All <ArrowRight class="w-3.5 h-3.5" />
                </Link>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3 sm:gap-6">
                <Link
                    v-for="cat in categories"
                    :key="cat.id"
                    :href="route('shop.index', { category: cat.slug })"
                    class="group flex flex-col items-center text-center p-3 sm:p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 shadow-sm hover:shadow-md hover:border-rose-200 dark:hover:border-rose-900/50 transition-all duration-300"
                >
                    <div class="w-16 h-16 sm:w-24 sm:h-24 rounded-full overflow-hidden bg-slate-100 dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 group-hover:border-rose-500 transition-colors mb-2.5 sm:mb-3">
                        <img
                            :src="cat.image_url"
                            :alt="cat.name"
                            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                            loading="lazy"
                        />
                    </div>
                    <h3 class="text-xs font-bold text-slate-800 dark:text-white group-hover:text-rose-600 transition-colors truncate w-full px-1">
                        {{ cat.name }}
                    </h3>
                </Link>
            </div>
        </section>

        <!-- Product Showcase Tabs (Featured / Trending / New Arrivals) -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 sm:mb-8">
                <div>
                    <h2 class="text-lg sm:text-2xl font-extrabold text-slate-900 dark:text-white font-serif">
                        Curated Collections
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">Handpicked premium outfits with artisanal perfection</p>
                </div>

                <!-- Tab Navigation Buttons -->
                <div class="flex items-center gap-1 sm:gap-2 bg-slate-100 dark:bg-slate-800/80 p-1 sm:p-1.5 rounded-xl overflow-x-auto no-scrollbar">
                    <button
                        @click="activeTab = 'featured'"
                        class="flex items-center gap-1.5 px-3 sm:px-4 py-2 rounded-lg text-xs font-bold transition-all whitespace-nowrap"
                        :class="activeTab === 'featured' ? 'bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow-sm' : 'text-slate-500 hover:text-slate-900 dark:hover:text-white'"
                    >
                        <Sparkles class="w-3.5 h-3.5 text-rose-500" /> Featured
                    </button>
                    <button
                        @click="activeTab = 'trending'"
                        class="flex items-center gap-1.5 px-3 sm:px-4 py-2 rounded-lg text-xs font-bold transition-all whitespace-nowrap"
                        :class="activeTab === 'trending' ? 'bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow-sm' : 'text-slate-500 hover:text-slate-900 dark:hover:text-white'"
                    >
                        <Flame class="w-3.5 h-3.5 text-amber-500" /> Trending
                    </button>
                    <button
                        @click="activeTab = 'latest'"
                        class="flex items-center gap-1.5 px-3 sm:px-4 py-2 rounded-lg text-xs font-bold transition-all whitespace-nowrap"
                        :class="activeTab === 'latest' ? 'bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow-sm' : 'text-slate-500 hover:text-slate-900 dark:hover:text-white'"
                    >
                        <Clock class="w-3.5 h-3.5 text-sky-500" /> New Arrivals
                    </button>
                </div>
            </div>

            <!-- Product Cards Grid -->
            <div v-if="currentProducts.length > 0" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-6">
                <ProductCard
                    v-for="product in currentProducts"
                    :key="product.id"
                    :product="product"
                />
            </div>
            <div v-else class="py-16 text-center text-slate-400">
                <p class="text-sm">No products found in this category.</p>
            </div>

            <!-- Browse More Button -->
            <div class="text-center mt-12">
                <Link
                    :href="route('shop.index')"
                    class="inline-flex items-center gap-2 px-8 py-3.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-800 dark:text-white text-xs font-bold uppercase tracking-wider shadow-sm transition-all"
                >
                    Explore Complete Catalog <ArrowRight class="w-4 h-4 text-rose-500" />
                </Link>
            </div>
        </section>

    </StorefrontLayout>
</template>
