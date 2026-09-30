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

const nextSlide = () => {
    if (props.sliders.length === 0) return;
    currentSlide.value = (currentSlide.value + 1) % props.sliders.length;
};

const prevSlide = () => {
    if (props.sliders.length === 0) return;
    currentSlide.value = (currentSlide.value - 1 + props.sliders.length) % props.sliders.length;
};

onMounted(() => {
    if (props.sliders.length > 1) {
        slideTimer = setInterval(nextSlide, 5000);
    }
});

onUnmounted(() => {
    if (slideTimer) clearInterval(slideTimer);
});

// Active product showcase tab
const activeTab = ref('featured'); // 'featured', 'trending', 'latest'

const currentProducts = computed(() => {
    if (activeTab.value === 'trending') return props.trendingProducts;
    if (activeTab.value === 'latest') return props.latestProducts;
    return props.featuredProducts;
});
</script>

<template>
    <Head title="Luxury Bangladeshi Fashion, Tailored Panjabis & Modern Outfits" />

    <StorefrontLayout>
        <!-- Hero Slider Section -->
        <section class="relative bg-slate-950 text-white overflow-hidden">
            <div v-if="sliders.length > 0" class="relative min-h-[480px] sm:min-h-[580px] lg:min-h-[640px] flex items-center">
                <div
                    v-for="(slider, index) in sliders"
                    :key="slider.id"
                    class="absolute inset-0 transition-opacity duration-1000 ease-in-out"
                    :class="index === currentSlide ? 'opacity-100 z-10' : 'opacity-0 z-0 pointer-events-none'"
                >
                    <!-- Background Image with Gradient Overlay -->
                    <img
                        :src="slider.image_url || (slider.image ? (slider.image.startsWith('http') ? slider.image : (slider.image.startsWith('/') ? slider.image : `/${slider.image}`)) : '/images/hero-bg.jpg')"
                        :alt="slider.title"
                        class="w-full h-full object-cover object-center scale-105 transition-transform duration-10000 ease-linear"
                    />
                    <div class="absolute inset-0 bg-gradient-to-r from-slate-950/90 via-slate-950/60 to-transparent" />

                    <!-- Slide Content -->
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full flex items-center relative z-20">
                        <div class="max-w-xl space-y-6 py-16">
                            <div v-if="slider.tag" class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-rose-500/20 border border-rose-500/40 text-rose-300 text-xs font-bold uppercase tracking-widest backdrop-blur-sm">
                                <Sparkles class="w-3.5 h-3.5" /> {{ slider.tag }}
                            </div>

                            <h1 class="text-3xl sm:text-5xl lg:text-6xl font-extrabold text-white tracking-tight leading-[1.1] font-serif">
                                {{ slider.title }}
                            </h1>

                            <p v-if="slider.subtitle" class="text-sm sm:text-base text-slate-300 leading-relaxed max-w-lg">
                                {{ slider.subtitle }}
                            </p>

                            <div class="flex flex-wrap items-center gap-4 pt-2">
                                <Link
                                    v-if="slider.button_text"
                                    :href="slider.button_link || route('shop.index')"
                                    class="inline-flex items-center gap-2 px-7 py-3.5 rounded-xl bg-gradient-to-r from-rose-600 to-rose-700 hover:from-rose-500 hover:to-rose-600 text-white font-bold text-xs uppercase tracking-wider shadow-lg shadow-rose-600/30 transition-all hover:scale-105"
                                >
                                    {{ slider.button_text }} <ArrowRight class="w-4 h-4" />
                                </Link>

                                <Link
                                    v-if="slider.secondary_button_text"
                                    :href="slider.secondary_button_link || route('shop.index')"
                                    class="inline-flex items-center gap-2 px-7 py-3.5 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 text-white font-bold text-xs uppercase tracking-wider backdrop-blur-sm transition-all"
                                >
                                    {{ slider.secondary_button_text }}
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Slider Arrows -->
                <div v-if="sliders.length > 1" class="absolute inset-x-4 top-1/2 -translate-y-1/2 flex justify-between z-30 pointer-events-none">
                    <button
                        @click="prevSlide"
                        class="pointer-events-auto p-3 rounded-full bg-slate-900/60 hover:bg-rose-600 text-white backdrop-blur-md border border-white/10 transition-all"
                        aria-label="Previous Slide"
                    >
                        <ChevronLeft class="w-5 h-5" />
                    </button>
                    <button
                        @click="nextSlide"
                        class="pointer-events-auto p-3 rounded-full bg-slate-900/60 hover:bg-rose-600 text-white backdrop-blur-md border border-white/10 transition-all"
                        aria-label="Next Slide"
                    >
                        <ChevronRight class="w-5 h-5" />
                    </button>
                </div>

                <!-- Slider Indicators -->
                <div v-if="sliders.length > 1" class="absolute bottom-6 inset-x-0 flex justify-center gap-2 z-30">
                    <button
                        v-for="(_, idx) in sliders"
                        :key="idx"
                        @click="currentSlide = idx"
                        class="h-1.5 rounded-full transition-all duration-300"
                        :class="idx === currentSlide ? 'w-8 bg-rose-500' : 'w-2 bg-white/40'"
                        :aria-label="`Go to slide ${idx + 1}`"
                    />
                </div>
            </div>

            <!-- Fallback Static Hero if no sliders exist -->
            <div v-else class="relative py-24 px-4 max-w-7xl mx-auto flex flex-col items-center text-center">
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-rose-500/20 border border-rose-500/30 text-rose-300 text-xs font-bold uppercase tracking-widest mb-4">
                    <Sparkles class="w-3.5 h-3.5" /> Festive Collection 2026
                </span>
                <h1 class="text-4xl sm:text-6xl font-extrabold text-white font-serif max-w-2xl leading-tight">
                    Contemporary Luxury & Tailored Panjabis
                </h1>
                <p class="text-slate-300 text-sm max-w-lg mt-4 mb-8">
                    Discover handpicked festive designs, luxury stitched fabrics, and modern lifestyle outfits delivered right to your doorstep.
                </p>
                <Link
                    :href="route('shop.index')"
                    class="inline-flex items-center gap-2 px-8 py-4 rounded-xl bg-gradient-to-r from-rose-600 to-rose-700 text-white font-bold text-xs uppercase tracking-wider shadow-lg shadow-rose-600/30 hover:scale-105 transition-all"
                >
                    Explore Shop <ArrowRight class="w-4 h-4" />
                </Link>
            </div>
        </section>

        <!-- Categories Visual Circular Grid -->
        <section v-if="categories.length > 0" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white font-serif">
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

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4 sm:gap-6">
                <Link
                    v-for="cat in categories"
                    :key="cat.id"
                    :href="route('shop.index', { category: cat.slug })"
                    class="group flex flex-col items-center text-center p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 shadow-sm hover:shadow-md hover:border-rose-200 dark:hover:border-rose-900/50 transition-all duration-300"
                >
                    <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-full overflow-hidden bg-slate-100 dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 group-hover:border-rose-500 transition-colors mb-3">
                        <img
                            :src="cat.image_url"
                            :alt="cat.name"
                            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                            loading="lazy"
                        />
                    </div>
                    <h3 class="text-xs font-bold text-slate-800 dark:text-white group-hover:text-rose-600 transition-colors">
                        {{ cat.name }}
                    </h3>
                </Link>
            </div>
        </section>

        <!-- Product Showcase Tabs (Featured / Trending / New Arrivals) -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
                <div>
                    <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white font-serif">
                        Curated Collections
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">Handpicked premium outfits with artisanal perfection</p>
                </div>

                <!-- Tab Navigation Buttons -->
                <div class="flex items-center gap-2 bg-slate-100 dark:bg-slate-800/80 p-1.5 rounded-xl">
                    <button
                        @click="activeTab = 'featured'"
                        class="flex items-center gap-1.5 px-4 py-2 rounded-lg text-xs font-bold transition-all"
                        :class="activeTab === 'featured' ? 'bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow-sm' : 'text-slate-500 hover:text-slate-900 dark:hover:text-white'"
                    >
                        <Sparkles class="w-3.5 h-3.5 text-rose-500" /> Featured
                    </button>
                    <button
                        @click="activeTab = 'trending'"
                        class="flex items-center gap-1.5 px-4 py-2 rounded-lg text-xs font-bold transition-all"
                        :class="activeTab === 'trending' ? 'bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow-sm' : 'text-slate-500 hover:text-slate-900 dark:hover:text-white'"
                    >
                        <Flame class="w-3.5 h-3.5 text-amber-500" /> Trending
                    </button>
                    <button
                        @click="activeTab = 'latest'"
                        class="flex items-center gap-1.5 px-4 py-2 rounded-lg text-xs font-bold transition-all"
                        :class="activeTab === 'latest' ? 'bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow-sm' : 'text-slate-500 hover:text-slate-900 dark:hover:text-white'"
                    >
                        <Clock class="w-3.5 h-3.5 text-sky-500" /> New Arrivals
                    </button>
                </div>
            </div>

            <!-- Product Cards Grid -->
            <div v-if="currentProducts.length > 0" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
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

        <!-- Brand Banner / Fabric Craftsmanship Spotlight -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="relative rounded-3xl overflow-hidden bg-gradient-to-r from-slate-950 via-slate-900 to-rose-950 text-white p-8 sm:p-14 border border-slate-800 shadow-2xl">
                <div class="relative z-10 max-w-xl space-y-4">
                    <span class="inline-flex items-center gap-1.5 text-rose-400 text-xs font-bold tracking-widest uppercase">
                        <ShieldCheck class="w-4 h-4" /> THE YANAS STANDARD
                    </span>
                    <h2 class="text-2xl sm:text-4xl font-extrabold font-serif leading-tight">
                        Unmatched Quality & Tailored Distinction
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-300 leading-relaxed">
                        Every Yanas Fashion piece is tailored from high-thread-count pure fabrics with reinforced twel-stitch seams, tailored collars, and lustrous metallic snap buttons engineered for long-lasting luxury.
                    </p>
                    <div class="pt-4 flex flex-wrap gap-4">
                        <Link
                            :href="route('shop.index')"
                            class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs uppercase tracking-wider shadow-lg transition-all"
                        >
                            Shop The Collection
                        </Link>
                    </div>
                </div>
            </div>
        </section>
    </StorefrontLayout>
</template>
