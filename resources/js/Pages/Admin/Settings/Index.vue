<script setup>
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { 
    Store, 
    Truck, 
    Phone, 
    Megaphone, 
    Save, 
    Upload, 
    Sparkles, 
    Check, 
    Image as ImageIcon,
    Globe,
    Share2
} from 'lucide-vue-next';

const props = defineProps({
    settings: {
        type: Object,
        default: () => ({})
    },
    logoUrl: {
        type: String,
        default: ''
    },
    faviconUrl: {
        type: String,
        default: ''
    }
});

const form = useForm({
    site_name: props.settings.site_name || 'Yanas Fashion',
    tagline: props.settings.tagline || 'Dhaka · Luxury Fashion',
    inside_dhaka_charge: props.settings.inside_dhaka_charge || 70,
    suburbs_charge: props.settings.suburbs_charge || 100,
    outside_dhaka_charge: props.settings.outside_dhaka_charge || 130,
    free_shipping_threshold: props.settings.free_shipping_threshold || 3000,
    hotline: props.settings.hotline || '01713580400',
    whatsapp_number: props.settings.whatsapp_number || '8801713580400',
    email: props.settings.email || 'support@yanasfashion.com',
    address: props.settings.address || 'House 42, Road 11, Banani, Dhaka',
    announcement_bar: props.settings.announcement_bar || '✨ Free Express Delivery in Dhaka on Orders Over ৳3,000 | 🚚 Nationwide COD Across All 64 Districts',
    facebook_url: props.settings.facebook_url || '',
    instagram_url: props.settings.instagram_url || '',
    tiktok_url: props.settings.tiktok_url || '',
    site_logo: null,
    site_favicon: null,
});

const logoPreview = ref(props.logoUrl);
const faviconPreview = ref(props.faviconUrl);

const handleLogoChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.site_logo = file;
        logoPreview.value = URL.createObjectURL(file);
    }
};

const handleFaviconChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.site_favicon = file;
        faviconPreview.value = URL.createObjectURL(file);
    }
};

const submit = () => {
    form.post(route('admin.settings.update'), {
        preserveScroll: true,
        onSuccess: () => {
            // Form submitted successfully
        }
    });
};
</script>

<template>
    <Head title="Store Settings — Admin" />

    <AdminLayout>
        <div class="space-y-6 max-w-5xl">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-black tracking-tight text-neutral-900 dark:text-white">Store Settings</h1>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">Configure brand identity, shipping rates, hotlines, and social links</p>
                </div>
                <button
                    type="button"
                    @click="submit"
                    :disabled="form.processing"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary text-white font-bold text-xs shadow-md shadow-primary/25 hover:bg-primary/95 transition-all disabled:opacity-50 cursor-pointer self-start sm:self-auto"
                >
                    <Save class="w-4 h-4" />
                    <span>{{ form.processing ? 'Saving...' : 'Save All Settings' }}</span>
                </button>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <!-- 1. Brand Identity & Media Assets -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-100 dark:border-neutral-800 p-6 shadow-sm">
                    <h2 class="text-sm font-black uppercase tracking-wider text-primary flex items-center gap-2 pb-3 border-b border-neutral-100 dark:border-neutral-800">
                        <Sparkles class="w-4 h-4" />
                        Brand Identity & Media Assets
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        <!-- Logo Upload -->
                        <div class="p-4 rounded-xl border border-neutral-200 dark:border-neutral-800 bg-neutral-50/50 dark:bg-neutral-950/40">
                            <label class="block text-xs font-bold text-neutral-800 dark:text-neutral-200 mb-1">Store / Website Logo</label>
                            <p class="text-[11px] text-neutral-400 mb-4">Shown across storefront header, footer, admin panel & PDF invoices.</p>
                            
                            <div class="flex items-center gap-4">
                                <div class="w-20 h-20 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 p-2 flex items-center justify-center shrink-0 shadow-sm overflow-hidden">
                                    <img v-if="logoPreview" :src="logoPreview" alt="Store Logo" class="max-h-full max-w-full object-contain" />
                                    <ImageIcon v-else class="w-8 h-8 text-neutral-300" />
                                </div>
                                <div class="flex-1">
                                    <label class="cursor-pointer inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 text-xs font-bold text-neutral-700 dark:text-neutral-300 hover:bg-neutral-50 dark:hover:bg-neutral-700 transition-colors shadow-sm">
                                        <Upload class="w-3.5 h-3.5 text-primary" />
                                        <span>Choose Logo</span>
                                        <input type="file" @change="handleLogoChange" accept="image/*" class="hidden" />
                                    </label>
                                    <span class="block text-[10px] text-neutral-400 mt-1.5">PNG, JPG, SVG, WebP (Max 4MB)</span>
                                </div>
                            </div>
                        </div>

                        <!-- Favicon Upload -->
                        <div class="p-4 rounded-xl border border-neutral-200 dark:border-neutral-800 bg-neutral-50/50 dark:bg-neutral-950/40">
                            <label class="block text-xs font-bold text-neutral-800 dark:text-neutral-200 mb-1">Browser Favicon</label>
                            <p class="text-[11px] text-neutral-400 mb-4">Small icon shown on browser tabs, bookmarks and mobile shortcuts.</p>
                            
                            <div class="flex items-center gap-4">
                                <div class="w-20 h-20 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 p-3 flex items-center justify-center shrink-0 shadow-sm overflow-hidden">
                                    <img v-if="faviconPreview" :src="faviconPreview" alt="Favicon" class="w-8 h-8 object-contain" />
                                    <Globe v-else class="w-8 h-8 text-neutral-300" />
                                </div>
                                <div class="flex-1">
                                    <label class="cursor-pointer inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 text-xs font-bold text-neutral-700 dark:text-neutral-300 hover:bg-neutral-50 dark:hover:bg-neutral-700 transition-colors shadow-sm">
                                        <Upload class="w-3.5 h-3.5 text-secondary" />
                                        <span>Choose Favicon</span>
                                        <input type="file" @change="handleFaviconChange" accept=".ico,image/png,image/svg+xml,image/x-icon" class="hidden" />
                                    </label>
                                    <span class="block text-[10px] text-neutral-400 mt-1.5">ICO, PNG, SVG (Max 2MB)</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                        <div>
                            <label class="block text-xs font-bold text-neutral-700 dark:text-neutral-300 mb-1.5">Store / Brand Name *</label>
                            <input
                                v-model="form.site_name"
                                type="text"
                                required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-neutral-950 text-xs font-semibold text-neutral-900 dark:text-white focus:outline-none focus:border-primary"
                            />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-neutral-700 dark:text-neutral-300 mb-1.5">Brand Tagline</label>
                            <input
                                v-model="form.tagline"
                                type="text"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-neutral-950 text-xs font-semibold text-neutral-900 dark:text-white focus:outline-none focus:border-primary"
                            />
                        </div>
                    </div>
                </div>

                <!-- 2. Delivery Charges & Shipping Policy -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-100 dark:border-neutral-800 p-6 shadow-sm">
                    <h2 class="text-sm font-black uppercase tracking-wider text-primary flex items-center gap-2 pb-3 border-b border-neutral-100 dark:border-neutral-800">
                        <Truck class="w-4 h-4" />
                        Delivery Charges & Shipping Rates
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-6">
                        <div>
                            <label class="block text-xs font-bold text-neutral-700 dark:text-neutral-300 mb-1.5">Inside Dhaka (৳) *</label>
                            <div class="relative">
                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-neutral-400">৳</span>
                                <input
                                    v-model.number="form.inside_dhaka_charge"
                                    type="number"
                                    required
                                    min="0"
                                    class="w-full pl-8 pr-3.5 py-2.5 rounded-xl border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-neutral-950 text-xs font-semibold text-neutral-900 dark:text-white focus:outline-none focus:border-primary"
                                />
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-neutral-700 dark:text-neutral-300 mb-1.5">Dhaka Suburbs (৳) *</label>
                            <div class="relative">
                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-neutral-400">৳</span>
                                <input
                                    v-model.number="form.suburbs_charge"
                                    type="number"
                                    required
                                    min="0"
                                    class="w-full pl-8 pr-3.5 py-2.5 rounded-xl border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-neutral-950 text-xs font-semibold text-neutral-900 dark:text-white focus:outline-none focus:border-primary"
                                />
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-neutral-700 dark:text-neutral-300 mb-1.5">Outside Dhaka (৳) *</label>
                            <div class="relative">
                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-neutral-400">৳</span>
                                <input
                                    v-model.number="form.outside_dhaka_charge"
                                    type="number"
                                    required
                                    min="0"
                                    class="w-full pl-8 pr-3.5 py-2.5 rounded-xl border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-neutral-950 text-xs font-semibold text-neutral-900 dark:text-white focus:outline-none focus:border-primary"
                                />
                            </div>
                        </div>

                        <div class="sm:col-span-3 mt-2">
                            <label class="block text-xs font-bold text-neutral-700 dark:text-neutral-300 mb-1.5">Free Delivery Order Threshold (৳)</label>
                            <div class="relative">
                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-neutral-400">৳</span>
                                <input
                                    v-model.number="form.free_shipping_threshold"
                                    type="number"
                                    min="0"
                                    class="w-full pl-8 pr-3.5 py-2.5 rounded-xl border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-neutral-950 text-xs font-semibold text-neutral-900 dark:text-white focus:outline-none focus:border-primary"
                                />
                            </div>
                            <p class="text-[11px] text-neutral-400 mt-1.5">Orders with subtotal exceeding this amount automatically qualify for free delivery within Dhaka.</p>
                        </div>
                    </div>
                </div>

                <!-- 3. Contact & Hotlines -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-100 dark:border-neutral-800 p-6 shadow-sm">
                    <h2 class="text-sm font-black uppercase tracking-wider text-primary flex items-center gap-2 pb-3 border-b border-neutral-100 dark:border-neutral-800">
                        <Phone class="w-4 h-4" />
                        Direct Customer Support & Hotlines
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-6">
                        <div>
                            <label class="block text-xs font-bold text-neutral-700 dark:text-neutral-300 mb-1.5">Hotline Phone Number *</label>
                            <input
                                v-model="form.hotline"
                                type="text"
                                required
                                class="w-full font-mono px-3.5 py-2.5 rounded-xl border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-neutral-950 text-xs font-semibold text-neutral-900 dark:text-white focus:outline-none focus:border-primary"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-neutral-700 dark:text-neutral-300 mb-1.5">WhatsApp Order Number *</label>
                            <input
                                v-model="form.whatsapp_number"
                                type="text"
                                required
                                class="w-full font-mono px-3.5 py-2.5 rounded-xl border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-neutral-950 text-xs font-semibold text-neutral-900 dark:text-white focus:outline-none focus:border-primary"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-neutral-700 dark:text-neutral-300 mb-1.5">Official Support Email</label>
                            <input
                                v-model="form.email"
                                type="email"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-neutral-950 text-xs font-semibold text-neutral-900 dark:text-white focus:outline-none focus:border-primary"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-neutral-700 dark:text-neutral-300 mb-1.5">Store / Flagship Address</label>
                            <input
                                v-model="form.address"
                                type="text"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-neutral-950 text-xs font-semibold text-neutral-900 dark:text-white focus:outline-none focus:border-primary"
                            />
                        </div>
                    </div>
                </div>

                <!-- 4. Announcement Bar & Social Links -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-100 dark:border-neutral-800 p-6 shadow-sm">
                    <h2 class="text-sm font-black uppercase tracking-wider text-primary flex items-center gap-2 pb-3 border-b border-neutral-100 dark:border-neutral-800">
                        <Megaphone class="w-4 h-4" />
                        Announcement Bar & Social Media Links
                    </h2>

                    <div class="space-y-4 mt-6">
                        <div>
                            <label class="block text-xs font-bold text-neutral-700 dark:text-neutral-300 mb-1.5">Top Banner Announcement Text</label>
                            <textarea
                                v-model="form.announcement_bar"
                                rows="2"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-neutral-950 text-xs font-semibold text-neutral-900 dark:text-white focus:outline-none focus:border-primary"
                            ></textarea>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-neutral-700 dark:text-neutral-300 mb-1.5">Facebook Page URL</label>
                                <input
                                    v-model="form.facebook_url"
                                    type="url"
                                    placeholder="https://facebook.com/yanasfashion"
                                    class="w-full px-3.5 py-2.5 rounded-xl border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-neutral-950 text-xs font-semibold text-neutral-900 dark:text-white focus:outline-none focus:border-primary"
                                />
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-neutral-700 dark:text-neutral-300 mb-1.5">Instagram Profile URL</label>
                                <input
                                    v-model="form.instagram_url"
                                    type="url"
                                    placeholder="https://instagram.com/yanasfashion"
                                    class="w-full px-3.5 py-2.5 rounded-xl border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-neutral-950 text-xs font-semibold text-neutral-900 dark:text-white focus:outline-none focus:border-primary"
                                />
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-neutral-700 dark:text-neutral-300 mb-1.5">TikTok Profile URL</label>
                                <input
                                    v-model="form.tiktok_url"
                                    type="url"
                                    placeholder="https://tiktok.com/@yanasfashion"
                                    class="w-full px-3.5 py-2.5 rounded-xl border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-neutral-950 text-xs font-semibold text-neutral-900 dark:text-white focus:outline-none focus:border-primary"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Save Bar -->
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="inline-flex items-center gap-2 px-8 py-3 rounded-xl bg-primary text-white font-bold text-xs shadow-lg shadow-primary/25 hover:bg-primary/95 transition-all disabled:opacity-50 cursor-pointer"
                    >
                        <Save class="w-4 h-4" />
                        <span>{{ form.processing ? 'Saving Changes...' : 'Save All Settings' }}</span>
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
