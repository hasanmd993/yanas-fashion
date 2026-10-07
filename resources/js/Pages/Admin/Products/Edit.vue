<script setup>
import { ref } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import {
    ArrowLeft,
    Save,
    Image,
    Trash2,
    ExternalLink,
    Plus,
    Upload,
    X,
    Eye
} from 'lucide-vue-next';
import RichTextEditor from '@/Components/RichTextEditor.vue';

const props = defineProps({
    product: {
        type: Object,
        required: true,
    },
    categories: {
        type: Array,
        default: () => [],
    },
});

// Existing Gallery Images resolution
const parseInitialGallery = () => {
    if (Array.isArray(props.product.gallery_items) && props.product.gallery_items.length > 0) {
        return props.product.gallery_items.map(item => ({
            raw: item.raw,
            url: item.url,
        }));
    }
    if (Array.isArray(props.product.gallery)) {
        return props.product.gallery.map((raw, idx) => ({
            raw,
            url: props.product.gallery_urls?.[idx] || (raw.startsWith('http') || raw.startsWith('/') ? raw : `/storage/${raw}`),
        }));
    }
    return [];
};

const existingGallery = ref(parseInitialGallery());
const newGalleryFiles = ref([]);
const newGalleryPreviews = ref([]);

const form = useForm({
    _method: 'PUT',
    title: props.product.title || '',
    title_bn: props.product.title_bn || '',
    sku: props.product.sku || '',
    category_id: props.product.category_id || '',
    regular_price: props.product.regular_price || '',
    sale_price: props.product.sale_price || '',
    stock_qty: props.product.stock_qty ?? 0,
    sizes: Array.isArray(props.product.sizes) ? props.product.sizes.join(', ') : (props.product.sizes || ''),
    badge: props.product.badge || '',
    short_desc: props.product.short_desc || '',
    description: props.product.description || '',
    thumbnail_file: null,
    gallery_files: [],
    existing_gallery: parseInitialGallery().map(item => item.raw),
    is_featured: Boolean(props.product.is_featured),
    is_trending: Boolean(props.product.is_trending),
    is_active: Boolean(props.product.is_active),
});

const thumbnailPreview = ref(props.product.primary_image || (props.product.thumbnail?.startsWith('http') ? props.product.thumbnail : `/storage/${props.product.thumbnail}`));

const handleThumbnailChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.thumbnail_file = file;
        thumbnailPreview.value = URL.createObjectURL(file);
    }
};

const removeExistingGallery = (index) => {
    existingGallery.value.splice(index, 1);
    form.existing_gallery = existingGallery.value.map(item => item.raw);
};

const handleGalleryChange = (e) => {
    const files = Array.from(e.target.files);
    if (!files.length) return;

    files.forEach(file => {
        newGalleryFiles.value.push(file);
        newGalleryPreviews.value.push({
            file,
            name: file.name,
            size: (file.size / 1024).toFixed(0) + ' KB',
            url: URL.createObjectURL(file),
        });
    });

    form.gallery_files = newGalleryFiles.value;
    e.target.value = '';
};

const removeNewGallery = (index) => {
    newGalleryFiles.value.splice(index, 1);
    newGalleryPreviews.value.splice(index, 1);
    form.gallery_files = newGalleryFiles.value;
};

const submit = () => {
    form.existing_gallery = existingGallery.value.map(item => item.raw);
    form.transform((data) => ({
        ...data,
        _method: 'PUT',
    })).post(route('admin.products.update', props.product.id), {
        preserveScroll: true,
        onSuccess: () => {
            newGalleryFiles.value = [];
            newGalleryPreviews.value = [];
        }
    });
};

const handleDelete = () => {
    if (confirm(`Are you sure you want to permanently delete "${props.product.title}"?`)) {
        router.delete(route('admin.products.destroy', props.product.id));
    }
};
</script>

<template>
    <Head :title="`Edit ${product.title} — Admin`" />

    <AdminLayout :title="`Edit Product: ${product.title}`">
        <div class="max-w-4xl mx-auto space-y-6">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <Link
                        :href="route('admin.products.index')"
                        class="p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-50 transition-colors"
                    >
                        <ArrowLeft class="w-4 h-4" />
                    </Link>
                    <div>
                        <h2 class="text-xl font-extrabold text-slate-900 dark:text-white font-serif">
                            Edit Product
                        </h2>
                        <p class="text-xs text-slate-500 font-mono">SKU: {{ product.sku }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <a
                        :href="route('product.show', product.slug)"
                        target="_blank"
                        class="p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-50 transition-colors inline-flex items-center gap-1.5 text-xs font-bold"
                    >
                        <ExternalLink class="w-4 h-4" /> View Live
                    </a>

                    <button
                        type="button"
                        @click="handleDelete"
                        class="p-2.5 rounded-xl text-rose-600 bg-rose-50 dark:bg-rose-950/40 hover:bg-rose-100 transition-colors inline-flex items-center gap-1.5 text-xs font-bold"
                    >
                        <Trash2 class="w-4 h-4" /> Delete
                    </button>

                    <button
                        type="button"
                        @click="submit"
                        :disabled="form.processing"
                        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-[#730163] hover:bg-[#5b014e] text-white text-xs font-bold shadow-md shadow-[#730163]/25 transition-all disabled:opacity-50"
                    >
                        <Save class="w-4 h-4" />
                        <span>{{ form.processing ? 'Saving...' : 'Update Product' }}</span>
                    </button>
                </div>
            </div>

            <!-- Form Cards -->
            <form @submit.prevent="submit" class="space-y-6">
                <!-- 1. Basic Info -->
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-6 sm:p-8 shadow-sm space-y-5">
                    <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-900 dark:text-white pb-3 border-b border-slate-100 dark:border-slate-800">
                        1. Product Information
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                Product Title <span class="text-rose-600">*</span>
                            </label>
                            <input
                                v-model="form.title"
                                type="text"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#730163]/20"
                                required
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                Bangla Title
                            </label>
                            <input
                                v-model="form.title_bn"
                                type="text"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:bg-white focus:outline-none"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                SKU / Code <span class="text-rose-600">*</span>
                            </label>
                            <input
                                v-model="form.sku"
                                type="text"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-mono focus:bg-white focus:outline-none"
                                required
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

                <!-- 2. Pricing & Inventory -->
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-6 sm:p-8 shadow-sm space-y-5">
                    <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-900 dark:text-white pb-3 border-b border-slate-100 dark:border-slate-800">
                        2. Pricing & Inventory
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
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-mono focus:bg-white focus:outline-none"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                Badge Label
                            </label>
                            <input
                                v-model="form.badge"
                                type="text"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs uppercase focus:bg-white focus:outline-none"
                            />
                        </div>
                    </div>
                </div>

                <!-- 3. Visual Assets & Media Gallery -->
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-6 sm:p-8 shadow-sm space-y-6">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                        <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-900 dark:text-white flex items-center gap-2">
                            <Image class="w-4 h-4 text-[#730163]" />
                            3. Visual Assets & Media Gallery
                        </h3>
                        <span class="text-[11px] font-bold text-slate-400">
                            {{ existingGallery.length + newGalleryPreviews.length }} Gallery Images
                        </span>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                        <!-- Primary Thumbnail (Left: 4 cols) -->
                        <div class="lg:col-span-4 space-y-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                                    Primary Thumbnail
                                </label>
                                <p class="text-[11px] text-slate-400 mt-0.5">
                                    Primary cover image displayed on product cards, category lists, and carts.
                                </p>
                            </div>

                            <div class="relative group rounded-2xl bg-slate-100 dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-700 aspect-[3/4] flex items-center justify-center overflow-hidden shadow-sm">
                                <img
                                    v-if="thumbnailPreview"
                                    :src="thumbnailPreview"
                                    class="w-full h-full object-cover transition-transform group-hover:scale-105"
                                    alt="Product Thumbnail"
                                />
                                <div v-else class="text-center p-4">
                                    <Image class="w-8 h-8 text-slate-400 mx-auto mb-1" />
                                    <span class="text-[11px] text-slate-400 font-bold">No thumbnail set</span>
                                </div>

                                <label class="absolute inset-0 bg-black/50 text-white opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center gap-1.5 cursor-pointer p-4 text-center">
                                    <Upload class="w-6 h-6" />
                                    <span class="text-xs font-extrabold">Replace Thumbnail</span>
                                    <span class="text-[10px] text-slate-300">JPG, PNG, WebP up to 10MB</span>
                                    <input
                                        type="file"
                                        accept="image/*"
                                        @change="handleThumbnailChange"
                                        class="sr-only"
                                    />
                                </label>
                            </div>

                            <div class="flex items-center gap-2">
                                <label class="flex-1 py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-100 text-center cursor-pointer transition-colors">
                                    <span>Browse New Thumbnail</span>
                                    <input
                                        type="file"
                                        accept="image/*"
                                        @change="handleThumbnailChange"
                                        class="sr-only"
                                    />
                                </label>
                            </div>
                        </div>

                        <!-- Gallery Showcase Images (Right: 8 cols) -->
                        <div class="lg:col-span-8 space-y-3">
                            <div class="flex items-center justify-between">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                                        Gallery Showcase Images
                                    </label>
                                    <p class="text-[11px] text-slate-400 mt-0.5">
                                        Angle shots, detail closeups, and model views in product slider & lightbox.
                                    </p>
                                </div>
                                <span class="text-[11px] font-mono text-slate-400 font-bold">
                                    {{ existingGallery.length }} saved <span v-if="newGalleryPreviews.length">+ {{ newGalleryPreviews.length }} new</span>
                                </span>
                            </div>

                            <!-- Showcase Grid -->
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                                <!-- 1. Existing Gallery Images -->
                                <div
                                    v-for="(item, index) in existingGallery"
                                    :key="'existing-' + index"
                                    class="relative group aspect-[3/4] rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 shadow-sm"
                                >
                                    <img
                                        :src="item.url"
                                        class="w-full h-full object-cover transition-transform group-hover:scale-105"
                                        :alt="`Gallery image ${index + 1}`"
                                    />
                                    <span class="absolute top-2 left-2 px-1.5 py-0.5 rounded-md bg-black/60 backdrop-blur text-white text-[9px] font-bold uppercase tracking-wider">
                                        Saved
                                    </span>
                                    <button
                                        type="button"
                                        @click="removeExistingGallery(index)"
                                        class="absolute top-2 right-2 p-1.5 rounded-lg bg-rose-600 hover:bg-rose-700 text-white shadow-md opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer"
                                        title="Remove from gallery"
                                    >
                                        <Trash2 class="w-3.5 h-3.5" />
                                    </button>
                                </div>

                                <!-- 2. Newly Uploaded Previews -->
                                <div
                                    v-for="(item, index) in newGalleryPreviews"
                                    :key="'new-' + index"
                                    class="relative group aspect-[3/4] rounded-2xl overflow-hidden border-2 border-[#730163] bg-slate-100 dark:bg-slate-800 shadow-sm"
                                >
                                    <img
                                        :src="item.url"
                                        class="w-full h-full object-cover transition-transform group-hover:scale-105"
                                        :alt="item.name"
                                    />
                                    <span class="absolute top-2 left-2 px-1.5 py-0.5 rounded-md bg-[#730163] text-white text-[9px] font-bold uppercase tracking-wider">
                                        New
                                    </span>
                                    <button
                                        type="button"
                                        @click="removeNewGallery(index)"
                                        class="absolute top-2 right-2 p-1.5 rounded-lg bg-rose-600 hover:bg-rose-700 text-white shadow-md opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer"
                                        title="Cancel upload"
                                    >
                                        <X class="w-3.5 h-3.5" />
                                    </button>
                                    <div class="absolute bottom-1 left-1 right-1 px-1 py-0.5 rounded bg-black/70 text-white text-[9px] font-mono truncate text-center">
                                        {{ item.size }}
                                    </div>
                                </div>

                                <!-- 3. Add More Images Dropzone Tile -->
                                <label class="aspect-[3/4] rounded-2xl border-2 border-dashed border-slate-300 dark:border-slate-700 hover:border-[#730163] dark:hover:border-[#730163] bg-slate-50 dark:bg-slate-800/40 hover:bg-[#730163]/5 transition-all flex flex-col items-center justify-center p-3 text-center cursor-pointer group">
                                    <div class="w-10 h-10 rounded-full bg-white dark:bg-slate-800 group-hover:bg-[#730163] group-hover:text-white text-slate-500 shadow-sm flex items-center justify-center transition-colors mb-2">
                                        <Plus class="w-5 h-5" />
                                    </div>
                                    <span class="text-xs font-bold text-slate-700 dark:text-slate-300 group-hover:text-[#730163]">
                                        Add Images
                                    </span>
                                    <span class="text-[10px] text-slate-400 mt-0.5">
                                        Select multiple
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

                            <!-- Empty Gallery Notice -->
                            <div
                                v-if="existingGallery.length === 0 && newGalleryPreviews.length === 0"
                                class="p-4 rounded-xl border border-dashed border-slate-200 dark:border-slate-800 text-center text-xs text-slate-400"
                            >
                                No gallery showcase images currently assigned to this product. Click "Add Images" above to upload extra photos.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. Description & Toggles -->
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-6 sm:p-8 shadow-sm space-y-5">
                    <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-900 dark:text-white pb-3 border-b border-slate-100 dark:border-slate-800">
                        4. Description & Visibility
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

                <!-- Submit Button -->
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
                        <span>{{ form.processing ? 'Saving Changes...' : 'Save Changes' }}</span>
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
