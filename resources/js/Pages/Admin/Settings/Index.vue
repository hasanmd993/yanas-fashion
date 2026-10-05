<script setup>
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import axios from 'axios';
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
    Share2,
    MessageSquare,
    Send,
    Smartphone,
    RefreshCw,
    AlertCircle
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
    default_courier: props.settings.default_courier || 'steadfast',
    steadfast_api_key: props.settings.steadfast_api_key || '',
    steadfast_secret_key: props.settings.steadfast_secret_key || '',
    pathao_client_id: props.settings.pathao_client_id || '',
    pathao_client_secret: props.settings.pathao_client_secret || '',
    pathao_username: props.settings.pathao_username || '',
    pathao_password: props.settings.pathao_password || '',
    pathao_store_id: props.settings.pathao_store_id || '',
    pathao_sandbox: props.settings.pathao_sandbox === '1' || props.settings.pathao_sandbox === true,
    // Automated SMS Notifications
    sms_enabled: props.settings.sms_enabled === '1' || props.settings.sms_enabled === true,
    sms_provider: props.settings.sms_provider || 'log',
    sms_api_key: props.settings.sms_api_key || '',
    sms_sender_id: props.settings.sms_sender_id || '',
    sms_generic_url: props.settings.sms_generic_url || '',
    sms_order_placed_enabled: props.settings.sms_order_placed_enabled === undefined ? true : (props.settings.sms_order_placed_enabled === '1' || props.settings.sms_order_placed_enabled === true),
    sms_order_placed_template: props.settings.sms_order_placed_template || 'Dear {customer_name}, your order #{order_number} for BDT {total_amount} at {site_name} has been placed successfully! Track: {tracking_url}',
    sms_order_shipped_enabled: props.settings.sms_order_shipped_enabled === undefined ? true : (props.settings.sms_order_shipped_enabled === '1' || props.settings.sms_order_shipped_enabled === true),
    sms_order_shipped_template: props.settings.sms_order_shipped_template || 'Dear {customer_name}, your order #{order_number} has been shipped via {courier_name}! Tracking Code: {tracking_code}. Track: {tracking_url}',
    sms_admin_alert_enabled: props.settings.sms_admin_alert_enabled === '1' || props.settings.sms_admin_alert_enabled === true,
    sms_admin_phone: props.settings.sms_admin_phone || '',
    site_logo: null,
    site_favicon: null,
});

const testPhone = ref(props.settings.hotline || '01713580400');
const testMessage = ref('Test SMS from Yanas Fashion - Automated Gateway Verification.');
const isTestingSms = ref(false);
const testResult = ref(null);
const balanceInfo = ref(null);
const isCheckingBalance = ref(false);

const sendTestSms = async () => {
    isTestingSms.value = true;
    testResult.value = null;
    try {
        const res = await axios.post(route('admin.sms.test'), {
            phone: testPhone.value,
            message: testMessage.value,
        });
        testResult.value = { success: true, message: res.data.message, simulated: res.data.simulated };
    } catch (err) {
        testResult.value = { success: false, message: err.response?.data?.message || 'Failed to send test SMS.' };
    } finally {
        isTestingSms.value = false;
    }
};

const checkSmsBalance = async () => {
    isCheckingBalance.value = true;
    balanceInfo.value = null;
    try {
        const res = await axios.get(route('admin.sms.balance'));
        balanceInfo.value = res.data;
    } catch (err) {
        balanceInfo.value = { success: false, message: 'Could not fetch balance.' };
    } finally {
        isCheckingBalance.value = false;
    }
};

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

                <!-- 3. Courier API Integrations (Steadfast & Pathao) -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-100 dark:border-neutral-800 p-6 shadow-sm space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b border-neutral-100 dark:border-neutral-800 gap-2">
                        <h2 class="text-sm font-black uppercase tracking-wider text-primary flex items-center gap-2">
                            <Truck class="w-4 h-4" />
                            Courier Automation (Steadfast & Pathao)
                        </h2>
                        <div class="flex items-center gap-2">
                            <label class="text-xs font-bold text-neutral-600 dark:text-neutral-400">Default Courier:</label>
                            <select
                                v-model="form.default_courier"
                                class="px-3 py-1.5 rounded-lg border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800 text-xs font-bold text-neutral-900 dark:text-white"
                            >
                                <option value="steadfast">Steadfast Courier</option>
                                <option value="pathao">Pathao Courier</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Steadfast Configuration Box -->
                        <div class="p-5 rounded-2xl bg-neutral-50 dark:bg-neutral-800/60 border border-neutral-200 dark:border-neutral-700/60 space-y-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-emerald-600 text-white flex items-center justify-center font-black text-xs">
                                        ST
                                    </div>
                                    <div>
                                        <h3 class="text-xs font-black text-neutral-900 dark:text-white uppercase tracking-wider">Steadfast Courier API</h3>
                                        <p class="text-[10px] text-neutral-400">portal.steadfast.courier/api/v1</p>
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold" :class="form.steadfast_api_key && form.steadfast_secret_key ? 'bg-emerald-500/10 text-emerald-600 border border-emerald-500/20' : 'bg-neutral-200 dark:bg-neutral-700 text-neutral-500'">
                                    {{ form.steadfast_api_key && form.steadfast_secret_key ? 'Configured' : 'Missing Keys' }}
                                </span>
                            </div>

                            <div class="space-y-3">
                                <div>
                                    <label class="block text-xs font-bold text-neutral-700 dark:text-neutral-300 mb-1">Steadfast API Key</label>
                                    <input
                                        v-model="form.steadfast_api_key"
                                        type="text"
                                        placeholder="Enter your Steadfast API Key"
                                        class="w-full font-mono px-3.5 py-2.5 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-xs font-semibold text-neutral-900 dark:text-white focus:outline-none focus:border-primary"
                                    />
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-neutral-700 dark:text-neutral-300 mb-1">Steadfast Secret Key</label>
                                    <input
                                        v-model="form.steadfast_secret_key"
                                        type="password"
                                        placeholder="Enter your Steadfast Secret Key"
                                        class="w-full font-mono px-3.5 py-2.5 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-xs font-semibold text-neutral-900 dark:text-white focus:outline-none focus:border-primary"
                                    />
                                </div>
                            </div>
                            <p class="text-[10px] text-neutral-400 leading-relaxed">
                                Get your API Key & Secret Key from your <strong>Steadfast Courier Merchant Panel</strong> under <em>Settings &gt; API Integration</em>.
                            </p>
                        </div>

                        <!-- Pathao Configuration Box -->
                        <div class="p-5 rounded-2xl bg-neutral-50 dark:bg-neutral-800/60 border border-neutral-200 dark:border-neutral-700/60 space-y-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-red-600 text-white flex items-center justify-center font-black text-xs">
                                        PT
                                    </div>
                                    <div>
                                        <h3 class="text-xs font-black text-neutral-900 dark:text-white uppercase tracking-wider">Pathao Courier API</h3>
                                        <p class="text-[10px] text-neutral-400">api-hermes.pathao.com</p>
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold" :class="form.pathao_client_id && form.pathao_client_secret && form.pathao_username ? 'bg-red-500/10 text-red-600 border border-red-500/20' : 'bg-neutral-200 dark:bg-neutral-700 text-neutral-500'">
                                    {{ form.pathao_client_id && form.pathao_client_secret ? 'Configured' : 'Missing Keys' }}
                                </span>
                            </div>

                            <div class="space-y-3">
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-bold text-neutral-700 dark:text-neutral-300 mb-1">Client ID</label>
                                        <input
                                            v-model="form.pathao_client_id"
                                            type="text"
                                            placeholder="Client ID"
                                            class="w-full font-mono px-3 py-2 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-xs font-semibold text-neutral-900 dark:text-white focus:outline-none focus:border-primary"
                                        />
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-neutral-700 dark:text-neutral-300 mb-1">Store ID</label>
                                        <input
                                            v-model="form.pathao_store_id"
                                            type="text"
                                            placeholder="Store ID"
                                            class="w-full font-mono px-3 py-2 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-xs font-semibold text-neutral-900 dark:text-white focus:outline-none focus:border-primary"
                                        />
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-neutral-700 dark:text-neutral-300 mb-1">Client Secret</label>
                                    <input
                                        v-model="form.pathao_client_secret"
                                        type="password"
                                        placeholder="Pathao Client Secret"
                                        class="w-full font-mono px-3.5 py-2 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-xs font-semibold text-neutral-900 dark:text-white focus:outline-none focus:border-primary"
                                    />
                                </div>

                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-bold text-neutral-700 dark:text-neutral-300 mb-1">API Username</label>
                                        <input
                                            v-model="form.pathao_username"
                                            type="text"
                                            placeholder="Registered Email"
                                            class="w-full px-3 py-2 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-xs font-semibold text-neutral-900 dark:text-white focus:outline-none focus:border-primary"
                                        />
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-neutral-700 dark:text-neutral-300 mb-1">API Password</label>
                                        <input
                                            v-model="form.pathao_password"
                                            type="password"
                                            placeholder="API Password"
                                            class="w-full px-3 py-2 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-xs font-semibold text-neutral-900 dark:text-white focus:outline-none focus:border-primary"
                                        />
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 pt-1">
                                    <input
                                        v-model="form.pathao_sandbox"
                                        type="checkbox"
                                        id="pathao_sandbox"
                                        class="rounded text-primary focus:ring-primary w-4 h-4 cursor-pointer"
                                    />
                                    <label for="pathao_sandbox" class="text-xs font-bold text-neutral-700 dark:text-neutral-300 cursor-pointer">
                                        Use Pathao Sandbox (Test Mode)
                                    </label>
                                </div>
                            </div>
                            <p class="text-[10px] text-neutral-400 leading-relaxed">
                                Request your API credentials from Pathao Merchant Support or under <em>Developer &gt; API Client</em>.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- 4. Automated SMS Notifications (Greenweb / BulkSMSBD / Generic) -->
                <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-100 dark:border-neutral-800 p-6 shadow-sm space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b border-neutral-100 dark:border-neutral-800 gap-3">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-[#730163] text-white flex items-center justify-center">
                                <MessageSquare class="w-4 h-4" />
                            </div>
                            <div>
                                <h2 class="text-sm font-black uppercase tracking-wider text-primary flex items-center gap-2">
                                    Automated SMS Notifications (Order Placed / Shipped)
                                </h2>
                                <p class="text-[11px] text-neutral-400">Send real-time instant SMS updates to customer phones & receive admin alerts</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input
                                    v-model="form.sms_enabled"
                                    type="checkbox"
                                    class="sr-only peer"
                                />
                                <div class="w-11 h-6 bg-neutral-200 peer-focus:outline-none rounded-full peer dark:bg-neutral-800 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-neutral-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                                <span class="ml-2 text-xs font-bold text-neutral-800 dark:text-neutral-200">
                                    {{ form.sms_enabled ? 'SMS Active' : 'SMS Disabled' }}
                                </span>
                            </label>
                        </div>
                    </div>

                    <!-- SMS Provider Selection & Credentials -->
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                        <div class="lg:col-span-5 space-y-4 p-5 rounded-2xl bg-neutral-50 dark:bg-neutral-800/60 border border-neutral-200 dark:border-neutral-700/60">
                            <h3 class="text-xs font-black uppercase tracking-wider text-neutral-900 dark:text-white flex items-center justify-between">
                                <span>SMS Gateway Provider</span>
                                <span class="text-[10px] font-mono px-2 py-0.5 rounded-full" :class="form.sms_enabled ? 'bg-emerald-500/10 text-emerald-600 border border-emerald-500/20' : 'bg-neutral-200 text-neutral-500'">
                                    {{ form.sms_enabled ? (form.sms_provider === 'log' ? 'Simulation' : 'Live') : 'Inactive' }}
                                </span>
                            </h3>

                            <div>
                                <label class="block text-xs font-bold text-neutral-700 dark:text-neutral-300 mb-1">Select Gateway</label>
                                <select
                                    v-model="form.sms_provider"
                                    class="w-full px-3.5 py-2.5 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-xs font-bold text-neutral-900 dark:text-white focus:outline-none focus:border-primary"
                                >
                                    <option value="greenweb">Greenweb BD (api.greenweb.com.bd)</option>
                                    <option value="bulksmsbd">BulkSMSBD (bulksmsbd.net)</option>
                                    <option value="generic">Generic HTTP Gateway / Webhook</option>
                                    <option value="log">Simulation / Log Mode (Testing without API credits)</option>
                                </select>
                            </div>

                            <!-- Greenweb Fields -->
                            <div v-if="form.sms_provider === 'greenweb'" class="space-y-3">
                                <div>
                                    <label class="block text-xs font-bold text-neutral-700 dark:text-neutral-300 mb-1">Greenweb API Token *</label>
                                    <input
                                        v-model="form.sms_api_key"
                                        type="password"
                                        placeholder="e.g. 1029384756..."
                                        class="w-full font-mono px-3.5 py-2.5 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-xs font-semibold text-neutral-900 dark:text-white focus:outline-none focus:border-primary"
                                    />
                                </div>
                                <p class="text-[10px] text-neutral-400">
                                    Obtain your token from your <strong>Greenweb.com.bd</strong> dashboard under API settings.
                                </p>
                            </div>

                            <!-- BulkSMSBD Fields -->
                            <div v-else-if="form.sms_provider === 'bulksmsbd'" class="space-y-3">
                                <div>
                                    <label class="block text-xs font-bold text-neutral-700 dark:text-neutral-300 mb-1">BulkSMSBD API Key *</label>
                                    <input
                                        v-model="form.sms_api_key"
                                        type="password"
                                        placeholder="API Key"
                                        class="w-full font-mono px-3.5 py-2.5 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-xs font-semibold text-neutral-900 dark:text-white focus:outline-none focus:border-primary"
                                    />
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-neutral-700 dark:text-neutral-300 mb-1">Sender ID / Masking ID *</label>
                                    <input
                                        v-model="form.sms_sender_id"
                                        type="text"
                                        placeholder="e.g. 8809612345678 or Approved Masking"
                                        class="w-full font-mono px-3.5 py-2.5 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-xs font-semibold text-neutral-900 dark:text-white focus:outline-none focus:border-primary"
                                    />
                                </div>
                            </div>

                            <!-- Generic Gateway Fields -->
                            <div v-else-if="form.sms_provider === 'generic'" class="space-y-3">
                                <div>
                                    <label class="block text-xs font-bold text-neutral-700 dark:text-neutral-300 mb-1">API Key / Token</label>
                                    <input
                                        v-model="form.sms_api_key"
                                        type="text"
                                        placeholder="API Key"
                                        class="w-full font-mono px-3.5 py-2.5 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-xs font-semibold text-neutral-900 dark:text-white focus:outline-none focus:border-primary"
                                    />
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-neutral-700 dark:text-neutral-300 mb-1">Sender ID</label>
                                    <input
                                        v-model="form.sms_sender_id"
                                        type="text"
                                        placeholder="Sender ID"
                                        class="w-full font-mono px-3.5 py-2.5 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-xs font-semibold text-neutral-900 dark:text-white focus:outline-none focus:border-primary"
                                    />
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-neutral-700 dark:text-neutral-300 mb-1">Gateway URL Pattern</label>
                                    <input
                                        v-model="form.sms_generic_url"
                                        type="text"
                                        placeholder="https://api.sms.com/send?to={to}&msg={message}&key={api_key}"
                                        class="w-full font-mono px-3.5 py-2.5 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-xs font-semibold text-neutral-900 dark:text-white focus:outline-none focus:border-primary"
                                    />
                                    <p class="text-[10px] text-neutral-400 mt-1">
                                        Use placeholders: <code class="bg-neutral-200 dark:bg-neutral-800 px-1 py-0.5 rounded">{to}</code>, <code class="bg-neutral-200 dark:bg-neutral-800 px-1 py-0.5 rounded">{message}</code>, <code class="bg-neutral-200 dark:bg-neutral-800 px-1 py-0.5 rounded">{api_key}</code>
                                    </p>
                                </div>
                            </div>

                            <!-- Log Mode Info -->
                            <div v-else class="p-3 rounded-xl bg-amber-500/10 border border-amber-500/20 text-xs text-amber-700 dark:text-amber-300">
                                <strong>Simulation Mode Active:</strong> All SMS notifications will be processed, rendered, and recorded to the SMS delivery logs table without burning real SMS credits. Perfect for testing!
                            </div>

                            <!-- Balance Check Button -->
                            <div class="pt-2 border-t border-neutral-200 dark:border-neutral-700 flex items-center justify-between">
                                <button
                                    type="button"
                                    @click="checkSmsBalance"
                                    :disabled="isCheckingBalance"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-xs font-bold hover:bg-neutral-100 transition-colors cursor-pointer"
                                >
                                    <RefreshCw class="w-3.5 h-3.5 text-primary" :class="{ 'animate-spin': isCheckingBalance }" />
                                    <span>{{ isCheckingBalance ? 'Checking...' : 'Check Balance' }}</span>
                                </button>
                                <span v-if="balanceInfo" class="text-xs font-mono font-bold" :class="balanceInfo.success ? 'text-emerald-600' : 'text-rose-600'">
                                    {{ balanceInfo.success ? balanceInfo.balance : 'Error checking' }}
                                </span>
                            </div>
                        </div>

                        <!-- Test Gateway Sandbox Box -->
                        <div class="lg:col-span-7 space-y-4 p-5 rounded-2xl bg-neutral-50 dark:bg-neutral-800/60 border border-neutral-200 dark:border-neutral-700/60">
                            <div class="flex items-center justify-between">
                                <h3 class="text-xs font-black uppercase tracking-wider text-neutral-900 dark:text-white flex items-center gap-2">
                                    <Smartphone class="w-4 h-4 text-primary" />
                                    Test Gateway Dispatch
                                </h3>
                                <span class="text-[10px] text-neutral-400">Verify credentials instantly</span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-neutral-700 dark:text-neutral-300 mb-1">Recipient Mobile</label>
                                    <input
                                        v-model="testPhone"
                                        type="tel"
                                        placeholder="017XXXXXXXX"
                                        class="w-full font-mono px-3.5 py-2.5 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-xs font-semibold text-neutral-900 dark:text-white focus:outline-none focus:border-primary"
                                    />
                                </div>
                                <div class="flex items-end">
                                    <button
                                        type="button"
                                        @click="sendTestSms"
                                        :disabled="isTestingSms || !testPhone"
                                        class="w-full inline-flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl bg-primary text-white text-xs font-bold hover:bg-primary/90 transition-all disabled:opacity-50 cursor-pointer shadow-sm"
                                    >
                                        <Send class="w-3.5 h-3.5" :class="{ 'animate-pulse': isTestingSms }" />
                                        <span>{{ isTestingSms ? 'Sending...' : 'Send Test SMS' }}</span>
                                    </button>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-neutral-700 dark:text-neutral-300 mb-1">Test Message Content</label>
                                <textarea
                                    v-model="testMessage"
                                    rows="2"
                                    class="w-full px-3.5 py-2 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-xs font-semibold text-neutral-900 dark:text-white focus:outline-none focus:border-primary"
                                ></textarea>
                            </div>

                            <div v-if="testResult" class="p-3 rounded-xl border text-xs" :class="testResult.success ? 'bg-emerald-50 dark:bg-emerald-950/20 border-emerald-200 text-emerald-800 dark:text-emerald-300' : 'bg-rose-50 dark:bg-rose-950/20 border-rose-200 text-rose-800 dark:text-rose-300'">
                                <div class="font-bold flex items-center gap-1.5">
                                    <Check v-if="testResult.success" class="w-4 h-4 text-emerald-600" />
                                    <AlertCircle v-else class="w-4 h-4 text-rose-600" />
                                    <span>{{ testResult.success ? 'Test Successful' : 'Test Failed' }}</span>
                                    <span v-if="testResult.simulated" class="text-[10px] px-1.5 py-0.2 rounded bg-amber-500/20 text-amber-700 font-mono">Simulated</span>
                                </div>
                                <p class="text-[11px] mt-0.5 opacity-90">{{ testResult.message }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Trigger Rules & Templates Customization -->
                    <div class="space-y-4 pt-4 border-t border-neutral-100 dark:border-neutral-800">
                        <h3 class="text-xs font-black uppercase tracking-wider text-neutral-900 dark:text-white">
                            Automated SMS Triggers & Custom Templates
                        </h3>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- 1. Order Placed Trigger -->
                            <div class="p-5 rounded-2xl border border-neutral-200 dark:border-neutral-800 space-y-3 bg-neutral-50/50 dark:bg-neutral-900/50">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <input
                                            v-model="form.sms_order_placed_enabled"
                                            type="checkbox"
                                            id="sms_order_placed_enabled"
                                            class="rounded text-primary focus:ring-primary w-4 h-4 cursor-pointer"
                                        />
                                        <label for="sms_order_placed_enabled" class="text-xs font-extrabold text-neutral-900 dark:text-white cursor-pointer">
                                            1. Order Placed (Instant Confirmation)
                                        </label>
                                    </div>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full" :class="form.sms_order_placed_enabled ? 'bg-emerald-500/10 text-emerald-600 border border-emerald-500/20' : 'bg-neutral-200 text-neutral-500'">
                                        {{ form.sms_order_placed_enabled ? 'Enabled' : 'Disabled' }}
                                    </span>
                                </div>
                                <p class="text-[11px] text-neutral-500">Sent immediately to customer when checkout is submitted.</p>

                                <div>
                                    <label class="block text-[11px] font-bold text-neutral-700 dark:text-neutral-300 mb-1">SMS Template</label>
                                    <textarea
                                        v-model="form.sms_order_placed_template"
                                        rows="3"
                                        class="w-full px-3.5 py-2 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-950 text-xs font-mono text-neutral-900 dark:text-white focus:outline-none focus:border-primary"
                                    ></textarea>
                                </div>
                                <div class="flex flex-wrap gap-1 text-[10px] text-neutral-400">
                                    <span>Variables:</span>
                                    <span class="bg-neutral-200 dark:bg-neutral-800 px-1 rounded font-mono">{customer_name}</span>
                                    <span class="bg-neutral-200 dark:bg-neutral-800 px-1 rounded font-mono">{order_number}</span>
                                    <span class="bg-neutral-200 dark:bg-neutral-800 px-1 rounded font-mono">{total_amount}</span>
                                    <span class="bg-neutral-200 dark:bg-neutral-800 px-1 rounded font-mono">{site_name}</span>
                                    <span class="bg-neutral-200 dark:bg-neutral-800 px-1 rounded font-mono">{tracking_url}</span>
                                </div>
                            </div>

                            <!-- 2. Order Shipped Trigger -->
                            <div class="p-5 rounded-2xl border border-neutral-200 dark:border-neutral-800 space-y-3 bg-neutral-50/50 dark:bg-neutral-900/50">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <input
                                            v-model="form.sms_order_shipped_enabled"
                                            type="checkbox"
                                            id="sms_order_shipped_enabled"
                                            class="rounded text-primary focus:ring-primary w-4 h-4 cursor-pointer"
                                        />
                                        <label for="sms_order_shipped_enabled" class="text-xs font-extrabold text-neutral-900 dark:text-white cursor-pointer">
                                            2. Order Shipped / Dispatched (Courier)
                                        </label>
                                    </div>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full" :class="form.sms_order_shipped_enabled ? 'bg-purple-500/10 text-purple-600 border border-purple-500/20' : 'bg-neutral-200 text-neutral-500'">
                                        {{ form.sms_order_shipped_enabled ? 'Enabled' : 'Disabled' }}
                                    </span>
                                </div>
                                <p class="text-[11px] text-neutral-500">Sent automatically when order status becomes 'Shipped' or dispatched to courier.</p>

                                <div>
                                    <label class="block text-[11px] font-bold text-neutral-700 dark:text-neutral-300 mb-1">SMS Template</label>
                                    <textarea
                                        v-model="form.sms_order_shipped_template"
                                        rows="3"
                                        class="w-full px-3.5 py-2 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-950 text-xs font-mono text-neutral-900 dark:text-white focus:outline-none focus:border-primary"
                                    ></textarea>
                                </div>
                                <div class="flex flex-wrap gap-1 text-[10px] text-neutral-400">
                                    <span>Variables:</span>
                                    <span class="bg-neutral-200 dark:bg-neutral-800 px-1 rounded font-mono">{customer_name}</span>
                                    <span class="bg-neutral-200 dark:bg-neutral-800 px-1 rounded font-mono">{order_number}</span>
                                    <span class="bg-neutral-200 dark:bg-neutral-800 px-1 rounded font-mono">{courier_name}</span>
                                    <span class="bg-neutral-200 dark:bg-neutral-800 px-1 rounded font-mono">{tracking_code}</span>
                                    <span class="bg-neutral-200 dark:bg-neutral-800 px-1 rounded font-mono">{tracking_url}</span>
                                </div>
                            </div>
                        </div>

                        <!-- 3. Admin New Order Alert (Owner Notification) -->
                        <div class="p-5 rounded-2xl border border-neutral-200 dark:border-neutral-800 space-y-3 bg-neutral-50/30 dark:bg-neutral-900/30">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <input
                                        v-model="form.sms_admin_alert_enabled"
                                        type="checkbox"
                                        id="sms_admin_alert_enabled"
                                        class="rounded text-primary focus:ring-primary w-4 h-4 cursor-pointer"
                                    />
                                    <label for="sms_admin_alert_enabled" class="text-xs font-extrabold text-neutral-900 dark:text-white cursor-pointer">
                                        3. Store Owner SMS Alert (Notify Admin on New Order)
                                    </label>
                                </div>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full" :class="form.sms_admin_alert_enabled ? 'bg-sky-500/10 text-sky-600 border border-sky-500/20' : 'bg-neutral-200 text-neutral-500'">
                                    {{ form.sms_admin_alert_enabled ? 'Enabled' : 'Disabled' }}
                                </span>
                            </div>
                            <p class="text-[11px] text-neutral-500">Sends a real-time order alert SMS directly to the shop owner's personal mobile phone.</p>

                            <div v-if="form.sms_admin_alert_enabled" class="max-w-md pt-1">
                                <label class="block text-xs font-bold text-neutral-700 dark:text-neutral-300 mb-1">Owner Mobile Phone *</label>
                                <input
                                    v-model="form.sms_admin_phone"
                                    type="tel"
                                    placeholder="017XXXXXXXX"
                                    class="w-full font-mono px-3.5 py-2.5 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-xs font-semibold text-neutral-900 dark:text-white focus:outline-none focus:border-primary"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 5. Contact & Hotlines -->
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
