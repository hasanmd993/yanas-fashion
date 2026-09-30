<script setup>
import { ref } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import {
    ArrowLeft,
    Save,
    Image,
    Trash2,
    ExternalLink
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
    is_featured: Boolean(props.product.is_featured),
    is_trending: Boolean(props.product.is_trending),
    is_active: Boolean(props.product.is_active),
});

const thumbnailPreview = ref(props.product.primary_image || (props.product.thumbnail?.startsWith('http') ? props.product.thumbnail : `/storage/${props.product.thumbnail}`));
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
    form.gallery_files = files;
    galleryPreviews.value = files.map(file => URL.createObjectURL(file));
};

const submit = () => {
    form.transform((data) => ({
        ...data,
        _method: 'PUT',
    })).post(route('admin.products.update', props.product.id));
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

                <!-- 3. Images Upload -->
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-6 sm:p-8 shadow-sm space-y-5">
                    <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-900 dark:text-white pb-3 border-b border-slate-100 dark:border-slate-800">
                        3. Visual Assets
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                                Primary Thumbnail (Leave blank to keep existing)
                            </label>
                            <div class="flex items-center gap-4">
                                <div class="w-24 h-32 rounded-2xl bg-slate-100 dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-700 flex items-center justify-center overflow-hidden shrink-0">
                                    <img v-if="thumbnailPreview" :src="thumbnailPreview" class="w-full h-full object-cover" />
                                    <Image v-else class="w-6 h-6 text-slate-400" />
                                </div>
                                <div class="flex-1">
                                    <input
                                        type="file"
                                        accept="image/*"
                                        @change="handleThumbnailChange"
                                        class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#730163]/10 file:text-[#730163] hover:file:bg-[#730163]/20 cursor-pointer"
                                    />
                                </div>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                                Gallery Showcase Images (Add more)
                            </label>
                            <input
                                type="file"
                                accept="image/*"
                                multiple
                                @change="handleGalleryChange"
                                class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer"
                            />
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
