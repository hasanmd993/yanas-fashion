<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import {
    ArrowLeft,
    Save,
    UploadCloud,
    Image,
    Sparkles,
    Check,
    Layers,
    Plus,
    Upload,
    X
} from 'lucide-vue-next';
import RichTextEditor from '@/Components/RichTextEditor.vue';

const props = defineProps({
    categories: {
        type: Array,
        default: () => [],
    },
});

const form = useForm({
    title: '',
    title_bn: '',
    sku: '',
    category_id: props.categories[0]?.id || '',
    regular_price: '',
    sale_price: '',
    stock_qty: 10,
    sizes: 'M, L, XL, XXL',
    badge: 'NEW',
    short_desc: '',
    description: '',
    thumbnail_file: null,
    gallery_files: [],
    is_featured: false,
    is_trending: false,
    is_active: true,
});

const thumbnailPreview = ref(null);
const galleryFiles = ref([]);
const galleryPreviews = ref([]);

const handleThumbnailChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.thumbnail_file = file;
        thumbnailPreview.value = URL.createObjectURL(file);
    }
};

const handleGalleryChange = (e) => {
    const files = Array.from(e.target.files);
    if (!files.length) return;

    files.forEach(file => {
        galleryFiles.value.push(file);
        galleryPreviews.value.push({
            file,
            name: file.name,
            size: (file.size / 1024).toFixed(0) + ' KB',
            url: URL.createObjectURL(file),
        });
    });

    form.gallery_files = galleryFiles.value;
    e.target.value = '';
};

const removeGallery = (index) => {
    galleryFiles.value.splice(index, 1);
    galleryPreviews.value.splice(index, 1);
    form.gallery_files = galleryFiles.value;
};

const submit = () => {
    form.post(route('admin.products.store'));
};
</script>

<template>
    <Head title="Create New Product — Admin" />

    <AdminLayout title="Add New Product">
        <div class="max-w-4xl mx-auto space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <Link
                        :href="route('admin.products.index')"
                        class="p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-50 transition-colors"
                    >
                        <ArrowLeft class="w-4 h-4" />
                    </Link>
                    <div>
                        <h2 class="text-xl font-extrabold text-slate-900 dark:text-white font-serif">
                            Create New Product
                        </h2>
                        <p class="text-xs text-slate-500">Add luxury apparel item to your catalog</p>
                    </div>
                </div>

                <button
                    type="button"
                    @click="submit"
                    :disabled="form.processing"
                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-[#730163] hover:bg-[#5b014e] text-white text-xs font-bold shadow-md shadow-[#730163]/25 transition-all disabled:opacity-50"
                >
                    <Save class="w-4 h-4" />
                    <span>{{ form.processing ? 'Saving Product...' : 'Save Product' }}</span>
                </button>
            </div>

            <!-- Main Form Card -->
            <form @submit.prevent="submit" class="space-y-6">
                <!-- Basic Info Panel -->
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-6 sm:p-8 shadow-sm space-y-5">
                    <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-900 dark:text-white pb-3 border-b border-slate-100 dark:border-slate-800">
                        1. Basic Information & Classification
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                Product Title <span class="text-rose-600">*</span>
                            </label>
                            <input
                                v-model="form.title"
                                type="text"
                                placeholder="e.g. Royal Embroidered Silk Panjabi"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#730163]/20"
                                required
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                Bangla Title (Optional)
                            </label>
                            <input
                                v-model="form.title_bn"
                                type="text"
                                placeholder="e.g. রয়্যাল এমব্রয়ডারি সিল্ক পাঞ্জাবি"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:bg-white focus:outline-none"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                SKU / Code (Leave empty for auto-generation)
                            </label>
                            <input
                                v-model="form.sku"
                                type="text"
                                placeholder="e.g. YF-PANJABI-01"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-mono focus:bg-white focus:outline-none"
                            />
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                Category <span class="text-rose-600">*</span>
                            </label>
                            <select
                                v-model="form.category_id"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-bold text-slate-800 dark:text-white focus:outline-none"
                                required
                            >
                                <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                                    {{ cat.parent ? `${cat.parent.name} → ${cat.name}` : cat.name }}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Pricing & Inventory Panel -->
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-6 sm:p-8 shadow-sm space-y-5">
                    <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-900 dark:text-white pb-3 border-b border-slate-100 dark:border-slate-800">
                        2. Pricing, Inventory & Sizes
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                Regular Price (BDT) <span class="text-rose-600">*</span>
                            </label>
                            <input
                                v-model="form.regular_price"
                                type="number"
                                step="0.01"
                                placeholder="e.g. 2400"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-mono font-bold focus:bg-white focus:outline-none"
                                required
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                Sale / Discounted Price (BDT)
                            </label>
                            <input
                                v-model="form.sale_price"
                                type="number"
                                step="0.01"
                                placeholder="e.g. 1950"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-mono font-bold focus:bg-white focus:outline-none"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                Stock Quantity <span class="text-rose-600">*</span>
                            </label>
                            <input
                                v-model="form.stock_qty"
                                type="number"
                                min="0"
                                placeholder="e.g. 25"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-mono font-bold focus:bg-white focus:outline-none"
                                required
                            />
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                Available Sizes (Comma separated)
                            </label>
                            <input
                                v-model="form.sizes"
                                type="text"
                                placeholder="e.g. 38, 40, 42, 44 or S, M, L, XL"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-mono focus:bg-white focus:outline-none"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                Badge Label (Optional)
                            </label>
                            <input
                                v-model="form.badge"
                                type="text"
                                placeholder="e.g. NEW, EID SPECIAL"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs uppercase focus:bg-white focus:outline-none"
                            />
                        </div>
                    </div>
                </div>

                <!-- 3. Product Visuals & Media Gallery -->
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-6 sm:p-8 shadow-sm space-y-6">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                        <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-900 dark:text-white flex items-center gap-2">
                            <Image class="w-4 h-4 text-[#730163]" />
                            3. Product Visuals & Media Gallery (Auto WebP)
                        </h3>
                        <span class="text-[11px] font-bold text-slate-400">
                            {{ galleryPreviews.length }} Gallery Images Added
                        </span>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                        <!-- Primary Thumbnail -->
                        <div class="lg:col-span-4 space-y-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                                    Primary Thumbnail Image <span class="text-rose-500">*</span>
                                </label>
                                <p class="text-[11px] text-slate-400 mt-0.5">Primary cover image displayed on cards & category catalogs.</p>
                            </div>

                            <div class="relative group rounded-2xl bg-slate-100 dark:bg-slate-800 border-2 border-dashed border-slate-200 dark:border-slate-700 aspect-[3/4] flex items-center justify-center overflow-hidden shadow-sm">
                                <img v-if="thumbnailPreview" :src="thumbnailPreview" class="w-full h-full object-cover" alt="Thumbnail Preview" />
                                <div v-else class="text-center p-4">
                                    <Image class="w-8 h-8 text-slate-400 mx-auto mb-1" />
                                    <span class="text-[11px] text-slate-400 font-bold">Select Thumbnail</span>
                                </div>

                                <label class="absolute inset-0 bg-black/40 text-white opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center gap-1.5 cursor-pointer p-4 text-center">
                                    <Upload class="w-6 h-6" />
                                    <span class="text-xs font-extrabold">Choose File</span>
                                    <input
                                        type="file"
                                        accept="image/*"
                                        @change="handleThumbnailChange"
                                        class="sr-only"
                                    />
                                </label>
                            </div>

                            <input
                                type="file"
                                accept="image/*"
                                @change="handleThumbnailChange"
                                class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#730163]/10 file:text-[#730163] hover:file:bg-[#730163]/20 cursor-pointer"
                            />
                        </div>

                        <!-- Gallery Files -->
                        <div class="lg:col-span-8 space-y-3">
                            <div class="flex items-center justify-between">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                                        Gallery Showcase Images
                                    </label>
                                    <p class="text-[11px] text-slate-400 mt-0.5">
                                        Multi-angle shots, model photos, and fabric textures for customer lightbox slider.
                                    </p>
                                </div>
                                <span class="text-[11px] font-mono text-slate-400 font-bold">
                                    {{ galleryPreviews.length }} selected
                                </span>
                            </div>

                            <!-- Showcase Grid -->
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                                <div
                                    v-for="(item, index) in galleryPreviews"
                                    :key="index"
                                    class="relative group aspect-[3/4] rounded-2xl overflow-hidden border-2 border-[#730163] bg-slate-100 dark:bg-slate-800 shadow-sm"
                                >
                                    <img :src="item.url" class="w-full h-full object-cover transition-transform group-hover:scale-105" :alt="item.name" />
                                    <span class="absolute top-2 left-2 px-1.5 py-0.5 rounded-md bg-[#730163] text-white text-[9px] font-bold uppercase tracking-wider">
                                        New
                                    </span>
                                    <button
                                        type="button"
                                        @click="removeGallery(index)"
                                        class="absolute top-2 right-2 p-1.5 rounded-lg bg-rose-600 hover:bg-rose-700 text-white shadow-md opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer"
                                        title="Cancel this image"
                                    >
                                        <X class="w-3.5 h-3.5" />
                                    </button>
                                    <div class="absolute bottom-1 left-1 right-1 px-1 py-0.5 rounded bg-black/70 text-white text-[9px] font-mono truncate text-center">
                                        {{ item.size }}
                                    </div>
                                </div>

                                <!-- Add More Tile -->
                                <label class="aspect-[3/4] rounded-2xl border-2 border-dashed border-slate-300 dark:border-slate-700 hover:border-[#730163] dark:hover:border-[#730163] bg-slate-50 dark:bg-slate-800/40 hover:bg-[#730163]/5 transition-all flex flex-col items-center justify-center p-3 text-center cursor-pointer group">
                                    <div class="w-10 h-10 rounded-full bg-white dark:bg-slate-800 group-hover:bg-[#730163] group-hover:text-white text-slate-500 shadow-sm flex items-center justify-center transition-colors mb-2">
                                        <Plus class="w-5 h-5" />
                                    </div>
                                    <span class="text-xs font-bold text-slate-700 dark:text-slate-300 group-hover:text-[#730163]">
                                        Add Images
                                    </span>
                                    <span class="text-[10px] text-slate-400 mt-0.5">
                                        Multi-select
                                    </span>
                                    <input
                                        type="file"
                                        accept="image/*"
                                        multiple
                                        @change="handleGalleryChange"
                                        class="sr-only"
                                    />
                                </label>
                            </div>

                            <div
                                v-if="galleryPreviews.length === 0"
                                class="p-4 rounded-xl border border-dashed border-slate-200 dark:border-slate-800 text-center text-xs text-slate-400"
                            >
                                No gallery showcase images chosen yet. Click "Add Images" above to attach angle shots.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Descriptions & Flags -->
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-6 sm:p-8 shadow-sm space-y-5">
                    <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-900 dark:text-white pb-3 border-b border-slate-100 dark:border-slate-800">
                        4. Product Story & Visibility
                    </h3>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">
                                Full Product Story & Description (Rich Text Editor)
                            </label>
                            <RichTextEditor
                                v-model="form.description"
                                placeholder="Describe the luxury fabric weave, fit, collar stitching, care instructions..."
                            />
                        </div>

                        <!-- Toggles -->
                        <div class="flex flex-wrap gap-6 pt-2">
                            <label class="flex items-center gap-2.5 cursor-pointer text-xs font-bold text-slate-800 dark:text-white">
                                <input type="checkbox" v-model="form.is_featured" class="w-4 h-4 rounded text-[#730163] focus:ring-[#730163]" />
                                <span>Featured on Homepage</span>
                            </label>

                            <label class="flex items-center gap-2.5 cursor-pointer text-xs font-bold text-slate-800 dark:text-white">
                                <input type="checkbox" v-model="form.is_trending" class="w-4 h-4 rounded text-[#730163] focus:ring-[#730163]" />
                                <span>Trending Collection</span>
                            </label>

                            <label class="flex items-center gap-2.5 cursor-pointer text-xs font-bold text-slate-800 dark:text-white">
                                <input type="checkbox" v-model="form.is_active" class="w-4 h-4 rounded text-[#730163] focus:ring-[#730163]" />
                                <span>Published & Active in Store</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Submit Action -->
                <div class="flex justify-end gap-3">
                    <Link
                        :href="route('admin.products.index')"
                        class="px-6 py-3 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 transition-colors"
                    >
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="inline-flex items-center gap-2 px-8 py-3 rounded-xl bg-[#730163] hover:bg-[#5b014e] text-white text-xs font-bold shadow-md shadow-[#730163]/25 transition-all disabled:opacity-50"
                    >
                        <Save class="w-4 h-4" />
                        <span>{{ form.processing ? 'Saving...' : 'Save Product' }}</span>
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
