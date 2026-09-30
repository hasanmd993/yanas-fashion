<script setup>
import { ref, computed } from 'vue';
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
    FolderTree,
    ChevronRight,
    ChevronDown,
    ChevronsUpDown,
    Folder,
    FolderOpen,
    Search,
    CornerDownRight
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
const searchQuery = ref('');
const expandedIds = ref(new Set());

// Hierarchy computed helpers
const rootCategories = computed(() => {
    return props.categories.filter((c) => !c.parent_id);
});

const getChildren = (parentId) => {
    return props.categories.filter((c) => c.parent_id === parentId);
};

const hasChildren = (parentId) => {
    return props.categories.some((c) => c.parent_id === parentId);
};

const isExpanded = (id) => expandedIds.value.has(id);

const toggleExpand = (id) => {
    const next = new Set(expandedIds.value);
    if (next.has(id)) {
        next.delete(id);
    } else {
        next.add(id);
    }
    expandedIds.value = next;
};

const expandAll = () => {
    const allParentIds = props.categories
        .filter((c) => hasChildren(c.id))
        .map((c) => c.id);
    expandedIds.value = new Set(allParentIds);
};

const collapseAll = () => {
    expandedIds.value = new Set();
};

const filteredRootCategories = computed(() => {
    if (!searchQuery.value.trim()) {
        return rootCategories.value;
    }
    const q = searchQuery.value.toLowerCase().trim();
    // Keep root if root name matches OR any descendant matches
    return rootCategories.value.filter((root) => {
        if (root.name.toLowerCase().includes(q) || (root.name_bn && root.name_bn.toLowerCase().includes(q))) {
            return true;
        }
        const subs = getChildren(root.id);
        const subMatch = subs.some((sub) => {
            if (sub.name.toLowerCase().includes(q) || (sub.name_bn && sub.name_bn.toLowerCase().includes(q))) {
                return true;
            }
            const children = getChildren(sub.id);
            return children.some((c) => c.name.toLowerCase().includes(q) || (c.name_bn && c.name_bn.toLowerCase().includes(q)));
        });
        return subMatch;
    });
});

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
    imagePreview.value = cat.image_url || (cat.image ? (cat.image.startsWith('http') ? cat.image : (cat.image.startsWith('/') ? cat.image : `/${cat.image}`)) : null);

    // Auto-expand parent hierarchy if editing a subcategory
    if (cat.parent_id) {
        const next = new Set(expandedIds.value);
        next.add(cat.parent_id);
        const parentCat = props.categories.find((c) => c.id === cat.parent_id);
        if (parentCat && parentCat.parent_id) {
            next.add(parentCat.parent_id);
        }
        expandedIds.value = next;
    }
};

const addSubcategory = (parent) => {
    editingId.value = null;
    form.reset();
    form.parent_id = parent.id;
    form.sort_order = getChildren(parent.id).length + 1;
    form.is_active = true;
    form.is_featured = false;
    imagePreview.value = null;

    // Expand the parent so user sees the subcategories
    const next = new Set(expandedIds.value);
    next.add(parent.id);
    expandedIds.value = next;
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
        form.transform((data) => ({
            ...data,
            _method: 'PUT',
        })).post(route('admin.categories.update', editingId.value), {
            onSuccess: () => cancelEdit(),
        });
    } else {
        form.post(route('admin.categories.store'), {
            onSuccess: () => cancelEdit(),
        });
    }
};

const handleDelete = (id, name) => {
    if (confirm(`Are you sure you want to delete category "${name}"? All nested subcategories will be affected.`)) {
        router.delete(route('admin.categories.destroy', id));
    }
};
</script>

<template>
    <Head title="Categories Hierarchy — Admin" />

    <AdminLayout title="Categories Hierarchy">
        <div class="space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white font-serif">
                        Categories Hierarchy
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Interactive 3-tier catalog tree (Parent Collections, Subcategories, Styles)
                    </p>
                </div>

                <!-- Stats summary badge -->
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-purple-50 dark:bg-purple-950/40 border border-purple-100 dark:border-purple-900/50 text-[#730163] dark:text-purple-300 text-xs font-bold self-start sm:self-auto">
                    <FolderTree class="w-4 h-4" />
                    <span>{{ rootCategories.length }} Parent Collections</span>
                    <span class="text-slate-300 dark:text-slate-600">•</span>
                    <span>{{ categories.length }} Total Categories</span>
                </div>
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
                            <!-- Parent Category Selector with Indented Hierarchy -->
                            <div>
                                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">
                                    Parent Category (Optional)
                                </label>
                                <select
                                    v-model="form.parent_id"
                                    class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 font-semibold focus:outline-none text-slate-900 dark:text-white"
                                >
                                    <option value="">— None (Top-Level Parent Collection) —</option>
                                    <template v-for="parent in rootCategories" :key="parent.id">
                                        <option :value="parent.id" :disabled="editingId === parent.id" class="font-bold text-slate-900 dark:text-white">
                                            📁 {{ parent.name }}
                                        </option>
                                        <template v-for="sub in getChildren(parent.id)" :key="sub.id">
                                            <option :value="sub.id" :disabled="editingId === sub.id || editingId === parent.id" class="text-slate-600 dark:text-slate-300 pl-4">
                                                &nbsp;&nbsp;↳ 📂 {{ sub.name }}
                                            </option>
                                        </template>
                                    </template>
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
                                    placeholder="e.g. Silk Panjabis, Cargo Pants"
                                    class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 focus:bg-white focus:outline-none font-semibold"
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
                                    placeholder="e.g. সিল্ক পাঞ্জাবি, কার্গো প্যান্ট"
                                    class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 focus:bg-white focus:outline-none"
                                />
                            </div>

                            <!-- Image Upload -->
                            <div class="space-y-2">
                                <label class="block font-bold text-slate-700 dark:text-slate-300">
                                    Category Image (Thumbnail)
                                </label>
                                <div class="flex items-center gap-3">
                                    <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-700 overflow-hidden flex items-center justify-center shrink-0">
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

                <!-- Right: Expandable Category Tree (Col 6-12) -->
                <div class="lg:col-span-7 bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden flex flex-col">
                    <!-- Tree Header & Quick Controls -->
                    <div class="p-4 sm:p-5 border-b border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50/50 dark:bg-slate-800/30">
                        <div class="flex items-center gap-2">
                            <FolderTree class="w-4 h-4 text-[#730163]" />
                            <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-900 dark:text-white">
                                Interactive Category Tree
                            </h3>
                        </div>

                        <div class="flex items-center gap-2">
                            <!-- Expand / Collapse All -->
                            <button
                                @click="expandAll"
                                type="button"
                                class="px-2.5 py-1.5 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 text-[11px] font-bold hover:bg-slate-50 transition-colors inline-flex items-center gap-1.5"
                                title="Expand all subcategories"
                            >
                                <FolderOpen class="w-3.5 h-3.5 text-[#730163]" />
                                <span>Expand All</span>
                            </button>
                            <button
                                @click="collapseAll"
                                type="button"
                                class="px-2.5 py-1.5 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 text-[11px] font-bold hover:bg-slate-50 transition-colors inline-flex items-center gap-1.5"
                                title="Collapse to parent only"
                            >
                                <Folder class="w-3.5 h-3.5 text-slate-400" />
                                <span>Collapse All</span>
                            </button>
                        </div>
                    </div>

                    <!-- Search filter bar -->
                    <div class="px-4 py-2.5 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2 bg-white dark:bg-slate-900">
                        <Search class="w-3.5 h-3.5 text-slate-400 shrink-0" />
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Filter categories by name..."
                            class="w-full text-xs bg-transparent focus:outline-none text-slate-800 dark:text-white placeholder-slate-400"
                        />
                        <button
                            v-if="searchQuery"
                            @click="searchQuery = ''"
                            class="text-xs text-slate-400 hover:text-slate-600"
                        >
                            <X class="w-3.5 h-3.5" />
                        </button>
                    </div>

                    <!-- Tree Content -->
                    <div class="divide-y divide-slate-100 dark:divide-slate-800 text-xs">
                        <div v-if="filteredRootCategories.length === 0" class="p-8 text-center text-slate-400">
                            <FolderTree class="w-8 h-8 mx-auto mb-2 opacity-40 text-slate-400" />
                            <p class="font-semibold">No categories found matching your query.</p>
                        </div>

                        <!-- Parent Category Loop (Level 0) -->
                        <template v-for="parent in filteredRootCategories" :key="parent.id">
                            <!-- Parent Item Row -->
                            <div
                                class="group transition-colors border-l-4"
                                :class="[
                                    isExpanded(parent.id)
                                        ? 'bg-purple-50/30 dark:bg-purple-950/20 border-l-[#730163]'
                                        : 'hover:bg-slate-50/80 dark:hover:bg-slate-800/40 border-l-transparent'
                                ]"
                            >
                                <div class="p-3 sm:p-4 flex items-center justify-between gap-3">
                                    <!-- Left: Expand Toggle + Avatar + Name -->
                                    <div class="flex items-center gap-2.5 sm:gap-3 min-w-0 flex-1">
                                        <!-- Expand/Collapse Button (Chevron) -->
                                        <button
                                            v-if="hasChildren(parent.id)"
                                            @click.stop="toggleExpand(parent.id)"
                                            type="button"
                                            class="p-1 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-500 dark:text-slate-400 transition-all shrink-0"
                                            :title="isExpanded(parent.id) ? 'Collapse subcategories' : 'Expand subcategories'"
                                        >
                                            <ChevronDown
                                                v-if="isExpanded(parent.id)"
                                                class="w-4 h-4 text-[#730163]"
                                            />
                                            <ChevronRight
                                                v-else
                                                class="w-4 h-4 text-slate-400 group-hover:text-slate-600"
                                            />
                                        </button>
                                        <div v-else class="w-6 shrink-0 flex items-center justify-center">
                                            <div class="w-1.5 h-1.5 rounded-full bg-slate-300 dark:bg-slate-600" />
                                        </div>

                                        <!-- Category Image -->
                                        <img
                                            :src="parent.image_url"
                                            :alt="parent.name"
                                            class="w-10 h-10 rounded-xl object-cover border border-slate-200 dark:border-slate-700 bg-slate-100 shrink-0"
                                        />

                                        <!-- Category Details -->
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span
                                                    @click="hasChildren(parent.id) && toggleExpand(parent.id)"
                                                    class="font-extrabold text-slate-900 dark:text-white truncate"
                                                    :class="hasChildren(parent.id) ? 'cursor-pointer hover:text-[#730163]' : ''"
                                                >
                                                    {{ parent.name }}
                                                </span>
                                                <span v-if="parent.name_bn" class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">
                                                    ({{ parent.name_bn }})
                                                </span>
                                            </div>

                                            <div class="flex items-center gap-2 mt-0.5 flex-wrap">
                                                <span class="px-2 py-0.5 rounded-md text-[9px] font-extrabold uppercase tracking-wider bg-purple-100 dark:bg-purple-950/60 text-[#730163] dark:text-purple-300">
                                                    Parent
                                                </span>
                                                <button
                                                    v-if="hasChildren(parent.id)"
                                                    @click.stop="toggleExpand(parent.id)"
                                                    type="button"
                                                    class="inline-flex items-center gap-1 text-[10px] font-bold text-[#730163] hover:underline"
                                                >
                                                    <span>{{ getChildren(parent.id).length }} subcategories</span>
                                                    <span class="text-slate-400 font-normal">({{ isExpanded(parent.id) ? 'click to collapse' : 'click to expand' }})</span>
                                                </button>
                                                <span v-else class="text-[10px] text-slate-400">
                                                    No subcategories
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Right: Product Count, Status & Action Buttons -->
                                    <div class="flex items-center gap-2 sm:gap-3 shrink-0">
                                        <div class="text-right hidden sm:block">
                                            <span class="font-mono font-bold text-slate-700 dark:text-slate-300 text-xs">
                                                {{ parent.products_count || 0 }}
                                            </span>
                                            <span class="text-[10px] text-slate-400 block">items</span>
                                        </div>

                                        <span
                                            class="px-2 py-0.5 rounded-full text-[10px] font-bold"
                                            :class="parent.is_active ? 'bg-emerald-500/10 text-emerald-600' : 'bg-slate-100 text-slate-400'"
                                        >
                                            {{ parent.is_active ? 'Active' : 'Hidden' }}
                                        </span>

                                        <!-- Actions -->
                                        <div class="flex items-center gap-1">
                                            <button
                                                @click="addSubcategory(parent)"
                                                class="px-2 py-1 rounded-lg bg-[#730163]/10 hover:bg-[#730163]/20 text-[#730163] text-[11px] font-bold transition-colors inline-flex items-center gap-1"
                                                title="Add Subcategory under this parent"
                                            >
                                                <Plus class="w-3 h-3" />
                                                <span class="hidden md:inline">Add Sub</span>
                                            </button>
                                            <button
                                                @click="editCategory(parent)"
                                                class="p-1.5 rounded-lg text-slate-400 hover:text-[#730163] hover:bg-purple-50 dark:hover:bg-purple-950/40 transition-colors"
                                                title="Edit Parent Category"
                                            >
                                                <Edit class="w-3.5 h-3.5" />
                                            </button>
                                            <button
                                                @click="handleDelete(parent.id, parent.name)"
                                                class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition-colors"
                                                title="Delete Category"
                                            >
                                                <Trash2 class="w-3.5 h-3.5" />
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Subcategories Section (Level 1) - ONLY SHOWN WHEN PARENT IS EXPANDED -->
                                <div
                                    v-if="isExpanded(parent.id) && getChildren(parent.id).length > 0"
                                    class="border-t border-slate-100 dark:border-slate-800/80 bg-slate-50/60 dark:bg-slate-850/40 divide-y divide-slate-100/70 dark:divide-slate-800/60"
                                >
                                    <template v-for="sub in getChildren(parent.id)" :key="sub.id">
                                        <!-- Subcategory Row -->
                                        <div
                                            class="py-2.5 px-3 sm:px-4 pl-8 sm:pl-10 flex items-center justify-between gap-3 hover:bg-slate-100/70 dark:hover:bg-slate-800/60 transition-colors relative"
                                        >
                                            <!-- Tree visual connector line -->
                                            <div class="flex items-center gap-2.5 min-w-0 flex-1">
                                                <!-- Branch Icon / Expand Child Toggle -->
                                                <button
                                                    v-if="hasChildren(sub.id)"
                                                    @click.stop="toggleExpand(sub.id)"
                                                    type="button"
                                                    class="p-1 rounded hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-500 transition-all shrink-0"
                                                    :title="isExpanded(sub.id) ? 'Collapse child styles' : 'Expand child styles'"
                                                >
                                                    <ChevronDown
                                                        v-if="isExpanded(sub.id)"
                                                        class="w-3.5 h-3.5 text-indigo-600"
                                                    />
                                                    <ChevronRight
                                                        v-else
                                                        class="w-3.5 h-3.5 text-slate-400"
                                                    />
                                                </button>
                                                <span v-else class="text-slate-400 font-mono text-xs select-none shrink-0">
                                                    └──
                                                </span>

                                                <!-- Subcategory Thumbnail -->
                                                <img
                                                    :src="sub.image_url"
                                                    :alt="sub.name"
                                                    class="w-8 h-8 rounded-lg object-cover border border-slate-200 dark:border-slate-700 bg-white shrink-0"
                                                />

                                                <!-- Subcategory Details -->
                                                <div class="min-w-0">
                                                    <div class="flex items-center gap-1.5 flex-wrap">
                                                        <span
                                                            @click="hasChildren(sub.id) && toggleExpand(sub.id)"
                                                            class="font-bold text-slate-800 dark:text-slate-100 truncate"
                                                            :class="hasChildren(sub.id) ? 'cursor-pointer hover:text-indigo-600' : ''"
                                                        >
                                                            {{ sub.name }}
                                                        </span>
                                                        <span v-if="sub.name_bn" class="text-[10px] text-slate-400">
                                                            ({{ sub.name_bn }})
                                                        </span>
                                                    </div>
                                                    <div class="flex items-center gap-2 flex-wrap text-[10px]">
                                                        <span class="px-1.5 py-0.2 rounded text-[8px] font-extrabold uppercase tracking-wider bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-300">
                                                            Subcategory
                                                        </span>
                                                        <button
                                                            v-if="hasChildren(sub.id)"
                                                            @click.stop="toggleExpand(sub.id)"
                                                            type="button"
                                                            class="text-indigo-600 hover:underline font-bold"
                                                        >
                                                            {{ getChildren(sub.id).length }} styles ({{ isExpanded(sub.id) ? 'collapse' : 'expand' }})
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Subcategory Right Info & Actions -->
                                            <div class="flex items-center gap-2 shrink-0">
                                                <span class="font-mono text-xs font-semibold text-slate-600 dark:text-slate-400 hidden sm:inline">
                                                    {{ sub.products_count || 0 }} items
                                                </span>

                                                <span
                                                    class="px-1.5 py-0.5 rounded-full text-[9px] font-bold"
                                                    :class="sub.is_active ? 'bg-emerald-500/10 text-emerald-600' : 'bg-slate-100 text-slate-400'"
                                                >
                                                    {{ sub.is_active ? 'Active' : 'Hidden' }}
                                                </span>

                                                <div class="flex items-center gap-1">
                                                    <button
                                                        @click="addSubcategory(sub)"
                                                        class="p-1 rounded text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 transition-colors"
                                                        title="Add Child Category under this subcategory"
                                                    >
                                                        <Plus class="w-3 h-3" />
                                                    </button>
                                                    <button
                                                        @click="editCategory(sub)"
                                                        class="p-1 rounded text-slate-400 hover:text-[#730163] hover:bg-purple-50 transition-colors"
                                                        title="Edit Subcategory"
                                                    >
                                                        <Edit class="w-3 h-3" />
                                                    </button>
                                                    <button
                                                        @click="handleDelete(sub.id, sub.name)"
                                                        class="p-1 rounded text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors"
                                                        title="Delete Subcategory"
                                                    >
                                                        <Trash2 class="w-3 h-3" />
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Child Category Section (Level 2 / Grandchild) - ONLY SHOWN WHEN SUBCATEGORY IS EXPANDED -->
                                        <div
                                            v-if="isExpanded(sub.id) && getChildren(sub.id).length > 0"
                                            class="bg-slate-100/50 dark:bg-slate-900/60 divide-y divide-slate-200/50 dark:divide-slate-800/50"
                                        >
                                            <div
                                                v-for="child in getChildren(sub.id)"
                                                :key="child.id"
                                                class="py-2 px-3 sm:px-4 pl-14 sm:pl-16 flex items-center justify-between gap-3 hover:bg-slate-200/50 dark:hover:bg-slate-800/80 transition-colors"
                                            >
                                                <div class="flex items-center gap-2 min-w-0 flex-1">
                                                    <span class="text-slate-400 font-mono text-xs select-none shrink-0">
                                                        └── ↳
                                                    </span>
                                                    <img
                                                        :src="child.image_url"
                                                        :alt="child.name"
                                                        class="w-6 h-6 rounded-md object-cover border border-slate-200 dark:border-slate-700 bg-white shrink-0"
                                                    />
                                                    <div class="min-w-0">
                                                        <div class="flex items-center gap-1.5 flex-wrap">
                                                            <span class="font-semibold text-slate-700 dark:text-slate-200 truncate">
                                                                {{ child.name }}
                                                            </span>
                                                            <span v-if="child.name_bn" class="text-[9px] text-slate-400">
                                                                ({{ child.name_bn }})
                                                            </span>
                                                        </div>
                                                        <span class="px-1 py-0.2 rounded text-[7px] font-extrabold uppercase tracking-wider bg-teal-50 dark:bg-teal-950/50 text-teal-600 dark:text-teal-300">
                                                            Style / Child
                                                        </span>
                                                    </div>
                                                </div>

                                                <div class="flex items-center gap-2 shrink-0">
                                                    <span class="font-mono text-[10px] text-slate-500 hidden sm:inline">
                                                        {{ child.products_count || 0 }} items
                                                    </span>
                                                    <div class="flex items-center gap-1">
                                                        <button
                                                            @click="editCategory(child)"
                                                            class="p-1 rounded text-slate-400 hover:text-[#730163] hover:bg-purple-50 transition-colors"
                                                            title="Edit Child Category"
                                                        >
                                                            <Edit class="w-3 h-3" />
                                                        </button>
                                                        <button
                                                            @click="handleDelete(child.id, child.name)"
                                                            class="p-1 rounded text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors"
                                                            title="Delete Child Category"
                                                        >
                                                            <Trash2 class="w-3 h-3" />
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
