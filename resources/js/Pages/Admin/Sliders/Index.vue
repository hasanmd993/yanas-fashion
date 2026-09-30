<script setup>
import { ref } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import {
    Image,
    Plus,
    Save,
    Edit,
    Trash2,
    Sparkles,
    ExternalLink
} from 'lucide-vue-next';

const props = defineProps({
    sliders: {
        type: Array,
        default: () => [],
    },
});

const editingId = ref(null);
const imagePreview = ref(null);

const form = useForm({
    title: '',
    tag: 'FESTIVE COLLECTION',
    subtitle: '',
    button_text: 'Shop Now',
    button_link: '/shop',
    secondary_button_text: 'Explore Luxury',
    secondary_button_link: '/shop',
    sort_order: 0,
    is_active: true,
    image_file: null,
});

const handleImageChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.image_file = file;
        imagePreview.value = URL.createObjectURL(file);
    }
};

const editSlider = (slider) => {
    editingId.value = slider.id;
    form.title = slider.title;
    form.tag = slider.tag || '';
    form.subtitle = slider.subtitle || '';
    form.button_text = slider.button_text;
    form.button_link = slider.button_link;
    form.secondary_button_text = slider.secondary_button_text || '';
    form.secondary_button_link = slider.secondary_button_link || '';
    form.sort_order = slider.sort_order ?? 0;
    form.is_active = Boolean(slider.is_active);
    form.image_file = null;
    imagePreview.value = slider.image_url || (slider.image ? (slider.image.startsWith('http') ? slider.image : (slider.image.startsWith('/') ? slider.image : `/${slider.image}`)) : null);
};

const cancelEdit = () => {
    editingId.value = null;
    form.reset();
    imagePreview.value = null;
};

const submit = () => {
    if (editingId.value) {
        form.transform((data) => ({
            ...data,
            _method: 'PUT',
        })).post(route('admin.sliders.update', editingId.value), {
            onSuccess: () => cancelEdit(),
        });
    } else {
        form.post(route('admin.sliders.store'), {
            onSuccess: () => cancelEdit(),
        });
    }
};

const handleDelete = (id, title) => {
    if (confirm(`Are you sure you want to delete slide "${title}"?`)) {
        router.delete(route('admin.sliders.destroy', id));
    }
};
</script>

<template>
    <Head title="Hero Sliders & Banners — Admin" />

    <AdminLayout title="Hero Banners">
        <div class="space-y-6">
            <div>
                <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white font-serif">
                    Hero Sliders & Promotional Banners
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    Customize homepage hero visuals, campaign headlines, and call-to-action buttons
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <!-- Left: Form (Col 1-5) -->
                <div class="lg:col-span-5">
                    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-6 shadow-sm space-y-5 sticky top-28">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                            <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-900 dark:text-white flex items-center gap-2">
                                <Image class="w-4 h-4 text-[#730163]" />
                                <span>{{ editingId ? 'Edit Hero Banner' : 'Add New Hero Banner' }}</span>
                            </h3>
                            <button
                                v-if="editingId"
                                @click="cancelEdit"
                                class="text-xs text-slate-400 hover:text-rose-600 font-bold"
                            >
                                Cancel
                            </button>
                        </div>

                        <form @submit.prevent="submit" class="space-y-4 text-xs">
                            <div>
                                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">
                                    Tagline Badge
                                </label>
                                <input
                                    v-model="form.tag"
                                    type="text"
                                    placeholder="e.g. EID SPECIAL 2026, FESTIVE WEAR"
                                    class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 uppercase focus:outline-none font-bold"
                                />
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">
                                    Main Headline <span class="text-rose-600">*</span>
                                </label>
                                <input
                                    v-model="form.title"
                                    type="text"
                                    placeholder="e.g. Contemporary Luxury & Tailored Panjabis"
                                    class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 font-bold focus:outline-none"
                                    required
                                />
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">
                                    Subtitle / Caption
                                </label>
                                <textarea
                                    v-model="form.subtitle"
                                    rows="2"
                                    placeholder="Short promotional subtitle text..."
                                    class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 focus:outline-none"
                                />
                            </div>

                            <!-- Banner Image File -->
                            <div class="space-y-2">
                                <label class="block font-bold text-slate-700 dark:text-slate-300">
                                    Hero Banner High-Res Image
                                </label>
                                <div class="w-full h-28 rounded-2xl bg-slate-100 dark:bg-slate-800 border-2 border-dashed border-slate-200 dark:border-slate-700 overflow-hidden flex items-center justify-center relative">
                                    <img v-if="imagePreview" :src="imagePreview" class="w-full h-full object-cover" />
                                    <span v-else class="text-[11px] text-slate-400">1920x800 recommended</span>
                                </div>
                                <input
                                    type="file"
                                    accept="image/*"
                                    @change="handleImageChange"
                                    class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-[11px] file:font-bold file:bg-[#730163]/10 file:text-[#730163] hover:file:bg-[#730163]/20 cursor-pointer"
                                />
                            </div>

                            <!-- Buttons -->
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">
                                        Primary Button Text
                                    </label>
                                    <input
                                        v-model="form.button_text"
                                        type="text"
                                        class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 font-bold focus:outline-none"
                                        required
                                    />
                                </div>

                                <div>
                                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">
                                        Button Link URL
                                    </label>
                                    <input
                                        v-model="form.button_link"
                                        type="text"
                                        class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 font-mono focus:outline-none"
                                        required
                                    />
                                </div>
                            </div>

                            <label class="flex items-center gap-2 cursor-pointer font-bold text-slate-800 dark:text-white pt-1">
                                <input type="checkbox" v-model="form.is_active" class="w-4 h-4 rounded text-[#730163] focus:ring-[#730163]" />
                                <span>Active on Homepage Slider</span>
                            </label>

                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="w-full py-3 rounded-xl bg-[#730163] hover:bg-[#5b014e] text-white font-bold shadow-md shadow-[#730163]/25 transition-all flex items-center justify-center gap-2 disabled:opacity-50"
                            >
                                <Save class="w-4 h-4" />
                                <span>{{ editingId ? 'Update Banner' : 'Create Banner' }}</span>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Right: Banners List (Col 6-12) -->
                <div class="lg:col-span-7 space-y-4">
                    <div
                        v-for="slider in sliders"
                        :key="slider.id"
                        class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 overflow-hidden shadow-sm"
                    >
                        <div class="relative aspect-[21/9] bg-slate-950 overflow-hidden">
                            <img
                                :src="slider.image_url || (slider.image ? (slider.image.startsWith('http') ? slider.image : (slider.image.startsWith('/') ? slider.image : `/${slider.image}`)) : '/images/placeholder.jpg')"
                                :alt="slider.title"
                                class="w-full h-full object-cover"
                            />
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/30 to-transparent p-6 flex flex-col justify-end text-white">
                                <span v-if="slider.tag" class="text-[10px] font-extrabold uppercase tracking-widest text-[#F68625]">
                                    {{ slider.tag }}
                                </span>
                                <h3 class="text-base sm:text-lg font-extrabold font-serif leading-snug">
                                    {{ slider.title }}
                                </h3>
                            </div>
                        </div>

                        <div class="p-4 flex items-center justify-between">
                            <div class="flex items-center gap-2 text-xs">
                                <span
                                    class="px-2.5 py-0.5 rounded-full text-[10px] font-bold"
                                    :class="slider.is_active ? 'bg-emerald-500/10 text-emerald-600' : 'bg-slate-100 text-slate-400'"
                                >
                                    {{ slider.is_active ? 'Active' : 'Disabled' }}
                                </span>
                                <span class="text-slate-400 font-mono text-[11px]">Sort: #{{ slider.sort_order }}</span>
                            </div>

                            <div class="flex items-center gap-2">
                                <button
                                    @click="editSlider(slider)"
                                    class="p-2 rounded-xl text-slate-400 hover:text-[#730163] hover:bg-purple-50 transition-colors"
                                    title="Edit"
                                >
                                    <Edit class="w-4 h-4" />
                                </button>
                                <button
                                    @click="handleDelete(slider.id, slider.title)"
                                    class="p-2 rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors"
                                    title="Delete"
                                >
                                    <Trash2 class="w-4 h-4" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
