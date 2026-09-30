<script setup>
import { ref } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import {
    Tag,
    Plus,
    Save,
    Edit,
    Trash2,
    Clock,
    Percent,
    DollarSign
} from 'lucide-vue-next';

const props = defineProps({
    coupons: {
        type: Object,
        required: true,
    },
    totalCoupons: {
        type: Number,
        default: 0,
    },
    activeCoupons: {
        type: Number,
        default: 0,
    },
});

const editingId = ref(null);

const form = useForm({
    code: '',
    type: 'percent',
    value: '',
    min_order: 0,
    expires_at: '',
    is_active: true,
});

const editCoupon = (coupon) => {
    editingId.value = coupon.id;
    form.code = coupon.code;
    form.type = coupon.type;
    form.value = coupon.value;
    form.min_order = coupon.min_order ?? 0;
    form.expires_at = coupon.expires_at ? coupon.expires_at.substring(0, 10) : '';
    form.is_active = Boolean(coupon.is_active);
};

const cancelEdit = () => {
    editingId.value = null;
    form.reset();
    form.type = 'percent';
    form.min_order = 0;
    form.is_active = true;
};

const submit = () => {
    if (editingId.value) {
        form.put(route('admin.coupons.update', editingId.value), {
            onSuccess: () => cancelEdit(),
        });
    } else {
        form.post(route('admin.coupons.store'), {
            onSuccess: () => cancelEdit(),
        });
    }
};

const handleDelete = (id, code) => {
    if (confirm(`Are you sure you want to delete coupon code "${code}"?`)) {
        router.delete(route('admin.coupons.destroy', id));
    }
};
</script>

<template>
    <Head title="Coupons & Discounts — Admin" />

    <AdminLayout title="Coupons & Discounts">
        <div class="space-y-6">
            <div>
                <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white font-serif">
                    Promotional Coupons
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    Create percentage and fixed discount vouchers for campaigns & flash deals
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <!-- Left: Form (Col 1-5) -->
                <div class="lg:col-span-5">
                    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-6 shadow-sm space-y-5 sticky top-28">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                            <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-900 dark:text-white flex items-center gap-2">
                                <Tag class="w-4 h-4 text-[#730163]" />
                                <span>{{ editingId ? 'Edit Coupon' : 'Create New Coupon' }}</span>
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
                                    Coupon Code <span class="text-rose-600">*</span>
                                </label>
                                <input
                                    v-model="form.code"
                                    type="text"
                                    placeholder="e.g. EID2026, LUXURY15"
                                    class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 font-mono font-bold uppercase focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#730163]/20"
                                    required
                                />
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">
                                        Discount Type
                                    </label>
                                    <select
                                        v-model="form.type"
                                        class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 font-bold focus:outline-none"
                                    >
                                        <option value="percent">Percentage (%)</option>
                                        <option value="fixed">Fixed Amount (৳)</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">
                                        Discount Value <span class="text-rose-600">*</span>
                                    </label>
                                    <input
                                        v-model="form.value"
                                        type="number"
                                        step="0.01"
                                        placeholder="e.g. 15 or 250"
                                        class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 font-mono font-bold focus:bg-white focus:outline-none"
                                        required
                                    />
                                </div>
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">
                                    Minimum Order Amount (BDT)
                                </label>
                                <input
                                    v-model="form.min_order"
                                    type="number"
                                    placeholder="0 for no minimum"
                                    class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 font-mono focus:outline-none"
                                />
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">
                                    Expiry Date (Optional)
                                </label>
                                <input
                                    v-model="form.expires_at"
                                    type="date"
                                    class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:outline-none"
                                />
                            </div>

                            <label class="flex items-center gap-2 cursor-pointer font-bold text-slate-800 dark:text-white pt-1">
                                <input type="checkbox" v-model="form.is_active" class="w-4 h-4 rounded text-[#730163] focus:ring-[#730163]" />
                                <span>Active & Redeemable in Checkout</span>
                            </label>

                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="w-full py-3 rounded-xl bg-[#730163] hover:bg-[#5b014e] text-white font-bold shadow-md shadow-[#730163]/25 transition-all flex items-center justify-center gap-2 disabled:opacity-50"
                            >
                                <Save class="w-4 h-4" />
                                <span>{{ editingId ? 'Update Coupon' : 'Create Coupon' }}</span>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Right: Coupons Table (Col 6-12) -->
                <div class="lg:col-span-7 bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden">
                    <div class="p-5 border-b border-slate-100 dark:border-slate-800">
                        <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-900 dark:text-white">
                            Coupon Codes List ({{ coupons.total || coupons.data?.length }})
                        </h3>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead>
                                <tr class="border-b border-slate-100 dark:border-slate-800 text-slate-400 uppercase font-bold text-[10px] bg-slate-50/50 dark:bg-slate-800/30">
                                    <th class="py-3.5 px-4">Coupon Code</th>
                                    <th class="py-3.5 px-3">Discount</th>
                                    <th class="py-3.5 px-3">Min Order</th>
                                    <th class="py-3.5 px-3">Expiry</th>
                                    <th class="py-3.5 px-3">Status</th>
                                    <th class="py-3.5 px-4 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                <tr
                                    v-for="coupon in coupons.data"
                                    :key="coupon.id"
                                    class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors"
                                >
                                    <td class="py-3.5 px-4 font-mono font-bold text-slate-900 dark:text-white">
                                        {{ coupon.code }}
                                    </td>
                                    <td class="py-3.5 px-3 font-mono font-bold text-[#730163]">
                                        {{ coupon.type === 'percent' ? `${coupon.value}%` : `৳${coupon.value}` }}
                                    </td>
                                    <td class="py-3.5 px-3 font-mono text-slate-500">
                                        ৳{{ Number(coupon.min_order || 0).toLocaleString() }}
                                    </td>
                                    <td class="py-3.5 px-3 text-slate-500 whitespace-nowrap">
                                        {{ coupon.expires_at ? new Date(coupon.expires_at).toLocaleDateString() : 'Never' }}
                                    </td>
                                    <td class="py-3.5 px-3">
                                        <span
                                            class="px-2 py-0.5 rounded-full text-[10px] font-bold"
                                            :class="coupon.is_active ? 'bg-emerald-500/10 text-emerald-600' : 'bg-slate-100 text-slate-400'"
                                        >
                                            {{ coupon.is_active ? 'Active' : 'Disabled' }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-right">
                                        <div class="flex items-center justify-end gap-1">
                                            <button
                                                @click="editCoupon(coupon)"
                                                class="p-1.5 rounded-lg text-slate-400 hover:text-[#730163] hover:bg-purple-50 transition-colors"
                                                title="Edit"
                                            >
                                                <Edit class="w-3.5 h-3.5" />
                                            </button>
                                            <button
                                                @click="handleDelete(coupon.id, coupon.code)"
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
