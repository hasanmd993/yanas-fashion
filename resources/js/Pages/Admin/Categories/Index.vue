<script setup>
import { ref } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import {
    Plus,
    Save,
    Edit,
    Trash2,
    Layers,
    Image,
    Check,
    X,
    FolderTree
} from 'lucide-vue-next';

const props = defineProps({
    categories: {
        type: Array,
        default: () => [],
    },
    parentCategories: {
        type: Array,
        default: () => [],
    },
});

const editingId = ref(null);
const imagePreview = ref(null);

const form = useForm({
    parent_id: '',
    name: '',
    name_bn: '',
    sort_order: 0,
    is_featured: false,
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

const editCategory = (cat) => {
    editingId.value = cat.id;
    form.parent_id = cat.parent_id || '';
    form.name = cat.name;
    form.name_bn = cat.name_bn || '';
    form.sort_order = cat.sort_order ?? 0;
    form.is_featured = Boolean(cat.is_featured);
    form.is_active = Boolean(cat.is_active);
    form.image_file = null;
    imagePreview.value = cat.image_url;
};

const cancelEdit = () => {
    editingId.value = null;
    form.reset();
    form.parent_id = '';
    form.sort_order = 0;
    form.is_active = true;
    imagePreview.value = null;
};

const submit = () => {
    if (editingId.value) {
        form.post(route('admin.categories.update', editingId.value), {
            onSuccess: () => cancelEdit(),
        });
    } else {
        form.post(route('admin.categories.store'), {
            onSuccess: () => cancelEdit(),
        });
    }
};

const handleDelete = (id, name) => {
    if (confirm(`Are you sure you want to delete category "${name}"?`)) {
        router.delete(route('admin.categories.destroy', id));
    }
};
</script>

<template>
    <Head title="Categories Hierarchy — Admin" />

    <AdminLayout title="Categories Hierarchy">
        <div class="space-y-6">
            <div>
                <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white font-serif">
                    Categories Hierarchy
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    Structure 3-tier catalog navigation (Parent Collections, Subcategories, Styles)
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <!-- Left: Category Form (Col 1-5) -->
                <div class="lg:col-span-5">
                    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-6 shadow-sm space-y-5 sticky top-28">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                            <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-900 dark:text-white flex items-center gap-2">
                                <FolderTree class="w-4 h-4 text-[#730163]" />
                                <span>{{ editingId ? 'Edit Category' : 'Add New Category' }}</span>
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
                            <!-- Parent Category -->
                            <div>
                                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">
                                    Parent Category (Optional)
                                </label>
                                <select
                                    v-model="form.parent_id"
                                    class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 font-semibold focus:outline-none"
                                >
                                    <option value="">— None (Top-Level Parent) —</option>
                                    <option
                                        v-for="parent in parentCategories"
                                        :key="parent.id"
                                        :value="parent.id"
                                        :disabled="editingId === parent.id"
                                    >
                                        {{ parent.name }}
                                    </option>
                                </select>
                            </div>

                            <!-- Name & Bangla Name -->
                            <div>
                                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">
                                    Category Name <span class="text-rose-600">*</span>
                                </label>
                                <input
                                    v-model="form.name"
                                    type="text"
                                    placeholder="e.g. Silk Panjabis"
                                    class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 focus:bg-white focus:outline-none"
                                    required
                                />
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">
                                    Bangla Name (Optional)
                                </label>
                                <input
                                    v-model="form.name_bn"
                                    type="text"
                                    placeholder="e.g. সিল্ক পাঞ্জাবি"
                                    class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 focus:bg-white focus:outline-none"
                                />
                            </div>

                            <!-- Image Upload -->
                            <div class="space-y-2">
                                <label class="block font-bold text-slate-700 dark:text-slate-300">
                                    Category Image (Thumbnail)
                                </label>
                                <div class="flex items-center gap-3">
                                    <div class="w-14 h-14 rounded-full bg-slate-100 dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-700 overflow-hidden flex items-center justify-center shrink-0">
                                        <img v-if="imagePreview" :src="imagePreview" class="w-full h-full object-cover" />
                                        <Image v-else class="w-5 h-5 text-slate-400" />
                                    </div>
                                    <input
                                        type="file"
                                        accept="image/*"
                                        @change="handleImageChange"
                                        class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-[11px] file:font-bold file:bg-[#730163]/10 file:text-[#730163] hover:file:bg-[#730163]/20 cursor-pointer"
                                    />
                                </div>
                            </div>

                            <!-- Sort Order -->
                            <div>
                                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">
                                    Display Sort Order
                                </label>
                                <input
                                    v-model="form.sort_order"
                                    type="number"
                                    class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 font-mono focus:outline-none"
                                />
                            </div>

                            <!-- Toggles -->
                            <div class="space-y-2 pt-2">
                                <label class="flex items-center gap-2 cursor-pointer font-semibold text-slate-800 dark:text-white">
                                    <input type="checkbox" v-model="form.is_featured" class="w-4 h-4 rounded text-[#730163] focus:ring-[#730163]" />
                                    <span>Feature on Homepage Grid</span>
                                </label>

                                <label class="flex items-center gap-2 cursor-pointer font-semibold text-slate-800 dark:text-white">
                                    <input type="checkbox" v-model="form.is_active" class="w-4 h-4 rounded text-[#730163] focus:ring-[#730163]" />
                                    <span>Active in Store Navigation</span>
                                </label>
                            </div>

                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="w-full py-3 rounded-xl bg-[#730163] hover:bg-[#5b014e] text-white font-bold shadow-md shadow-[#730163]/25 transition-all flex items-center justify-center gap-2 disabled:opacity-50"
                            >
                                <Save class="w-4 h-4" />
                                <span>{{ editingId ? 'Update Category' : 'Save Category' }}</span>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Right: Categories List (Col 6-12) -->
                <div class="lg:col-span-7 bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden">
                    <div class="p-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                        <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-900 dark:text-white">
                            All Categories ({{ categories.length }})
                        </h3>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead>
                                <tr class="border-b border-slate-100 dark:border-slate-800 text-slate-400 uppercase font-bold text-[10px] bg-slate-50/50 dark:bg-slate-800/30">
                                    <th class="py-3 px-4">Image & Category</th>
                                    <th class="py-3 px-3">Level</th>
                                    <th class="py-3 px-3">Products</th>
                                    <th class="py-3 px-3">Status</th>
                                    <th class="py-3 px-4 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                <tr
                                    v-for="cat in categories"
                                    :key="cat.id"
                                    class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors"
                                >
                                    <td class="py-3 px-4">
                                        <div class="flex items-center gap-3">
                                            <img
                                                :src="cat.image_url"
                                                :alt="cat.name"
                                                class="w-10 h-10 rounded-full object-cover border border-slate-200 dark:border-slate-700 bg-slate-100 shrink-0"
                                            />
                                            <div>
                                                <div class="font-bold text-slate-900 dark:text-white flex items-center gap-1.5">
                                                    <span v-if="cat.parent_id" class="text-slate-400">└─</span>
                                                    <span>{{ cat.name }}</span>
                                                </div>
                                                <div v-if="cat.parent" class="text-[10px] text-slate-400">
                                                    Under: {{ cat.parent.name }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3 px-3">
                                        <span
                                            class="px-2 py-0.5 rounded text-[10px] font-bold"
                                            :class="!cat.parent_id ? 'bg-purple-50 dark:bg-purple-950/40 text-[#730163]' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300'"
                                        >
                                            {{ !cat.parent_id ? 'Parent' : 'Subcategory' }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-3 font-mono font-bold text-slate-700 dark:text-slate-300">
                                        {{ cat.products_count || 0 }}
                                    </td>
                                    <td class="py-3 px-3">
                                        <span
                                            class="px-2 py-0.5 rounded-full text-[10px] font-bold"
                                            :class="cat.is_active ? 'bg-emerald-500/10 text-emerald-600' : 'bg-slate-100 text-slate-400'"
                                        >
                                            {{ cat.is_active ? 'Active' : 'Hidden' }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-right">
                                        <div class="flex items-center justify-end gap-1">
                                            <button
                                                @click="editCategory(cat)"
                                                class="p-1.5 rounded-lg text-slate-400 hover:text-[#730163] hover:bg-purple-50 transition-colors"
                                                title="Edit"
                                            >
                                                <Edit class="w-3.5 h-3.5" />
                                            </button>
                                            <button
                                                @click="handleDelete(cat.id, cat.name)"
                                                class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors"
                                                title="Delete"
                                            >
                                                <Trash2 class="w-3.5 h-3.5" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
