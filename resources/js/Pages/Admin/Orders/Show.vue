<script setup>
import { ref } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import {
    ArrowLeft,
    Download,
    Printer,
    Save,
    User,
    Phone,
    MapPin,
    Package,
    ShieldCheck,
    CreditCard,
    MessageCircle,
    Truck,
    Send,
    RefreshCw,
    ExternalLink,
    Copy,
    Check,
    MessageSquare,
    Smartphone
} from 'lucide-vue-next';

const props = defineProps({
    order: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    order_status: props.order.order_status || 'pending',
    payment_status: props.order.payment_status || 'pending',
    admin_notes: props.order.admin_notes || '',
});

const updateStatus = () => {
    form.post(route('admin.orders.update_status', props.order.id));
};

// Courier Dispatch State & Form
const courierForm = useForm({
    courier: 'steadfast', // 'steadfast' or 'pathao'
    cod_amount: props.order.payment_status === 'paid' ? 0 : Number(props.order.total_amount),
    note: props.order.customer_note || 'Yanas Fashion Luxury Apparel',
    weight: 0.5,
});

const isManualCourierOpen = ref(false);
const manualForm = useForm({
    courier_name: 'Sundarban Courier',
    courier_tracking_code: '',
    courier_status: 'shipped',
});

const dispatchCourier = () => {
    courierForm.post(route('admin.orders.dispatch_courier', props.order.id), {
        preserveScroll: true,
    });
};

const submitManualCourier = () => {
    manualForm.post(route('admin.orders.manual_courier', props.order.id), {
        preserveScroll: true,
        onSuccess: () => {
            isManualCourierOpen.value = false;
        }
    });
};

const isTrackingLoading = ref(false);
const refreshTracking = () => {
    isTrackingLoading.value = true;
    router.get(route('admin.orders.track_courier', props.order.id), {}, {
        preserveScroll: true,
        onFinish: () => {
            isTrackingLoading.value = false;
        }
    });
};

const copied = ref(false);
const copyTrackingCode = (code) => {
    if (!code) return;
    navigator.clipboard.writeText(code);
    copied.value = true;
    setTimeout(() => { copied.value = false; }, 2000);
};

// SMS Notifications Form
const isCustomSmsOpen = ref(false);
const smsForm = useForm({
    type: 'order_placed',
    custom_message: '',
});

const sendOrderSms = (type) => {
    smsForm.type = type;
    if (type !== 'custom') {
        smsForm.post(route('admin.orders.send_sms', props.order.id), {
            preserveScroll: true,
        });
    } else {
        isCustomSmsOpen.value = true;
    }
};

const submitCustomSms = () => {
    smsForm.type = 'custom';
    smsForm.post(route('admin.orders.send_sms', props.order.id), {
        preserveScroll: true,
        onSuccess: () => {
            isCustomSmsOpen.value = false;
            smsForm.custom_message = '';
        }
    });
};
</script>

<template>
    <Head :title="`Order #${order.order_number} Details — Admin`" />

    <AdminLayout :title="`Order #${order.order_number}`">
        <div class="max-w-5xl mx-auto space-y-6">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <Link
                        :href="route('admin.orders.index')"
                        class="p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-50 transition-colors"
                    >
                        <ArrowLeft class="w-4 h-4" />
                    </Link>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-xl font-extrabold text-slate-900 dark:text-white font-mono">
                                #{{ order.order_number }}
                            </h2>
                            <span
                                class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider"
                                :class="{
                                    'bg-amber-500/10 text-amber-600': order.order_status === 'pending',
                                    'bg-sky-500/10 text-sky-600': order.order_status === 'processing',
                                    'bg-purple-500/10 text-purple-600': order.order_status === 'shipped',
                                    'bg-emerald-500/10 text-emerald-600': order.order_status === 'delivered',
                                    'bg-rose-500/10 text-rose-600': order.order_status === 'cancelled',
                                }"
                            >
                                {{ order.order_status }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Placed on {{ new Date(order.created_at).toLocaleString() }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <a
                        :href="route('admin.orders.download_invoice', order.id)"
                        class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-bold hover:bg-slate-50 transition-colors shadow-sm"
                    >
                        <Download class="w-4 h-4 text-[#730163]" /> Invoice PDF
                    </a>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <!-- Left: Line Items & Customer Details (Col 1-7) -->
                <div class="lg:col-span-7 space-y-6">
                    <!-- Customer Information Card -->
                    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-6 shadow-sm space-y-4">
                        <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-900 dark:text-white pb-3 border-b border-slate-100 dark:border-slate-800">
                            Customer & Delivery Info
                        </h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                            <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800 space-y-1">
                                <div class="text-[10px] text-slate-400 font-bold uppercase">Customer Name</div>
                                <div class="font-bold text-slate-900 dark:text-white flex items-center gap-1.5">
                                    <User class="w-3.5 h-3.5 text-[#730163]" /> {{ order.customer_name }}
                                </div>
                            </div>

                            <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800 space-y-1">
                                <div class="text-[10px] text-slate-400 font-bold uppercase">Contact Phone</div>
                                <a :href="`tel:${order.customer_phone}`" class="font-bold text-[#730163] flex items-center gap-1.5 font-mono hover:underline">
                                    <Phone class="w-3.5 h-3.5" /> {{ order.customer_phone }}
                                </a>
                            </div>

                            <div class="sm:col-span-2 p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800 space-y-1">
                                <div class="text-[10px] text-slate-400 font-bold uppercase">Shipping Address</div>
                                <div class="text-slate-700 dark:text-slate-300 flex items-start gap-1.5">
                                    <MapPin class="w-3.5 h-3.5 text-[#730163] shrink-0 mt-0.5" />
                                    <span>{{ order.customer_address }} ({{ order.delivery_zone?.replace('_', ' ') }})</span>
                                </div>
                            </div>

                            <div v-if="order.customer_note" class="sm:col-span-2 p-3.5 rounded-2xl bg-amber-50/60 text-amber-900 text-xs">
                                <strong>Customer Instruction:</strong> {{ order.customer_note }}
                            </div>
                        </div>
                    </div>

                    <!-- Ordered Items List -->
                    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-6 shadow-sm space-y-4">
                        <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-900 dark:text-white pb-3 border-b border-slate-100 dark:border-slate-800">
                            Purchased Items ({{ order.items?.length || 0 }})
                        </h3>

                        <div class="divide-y divide-slate-100 dark:divide-slate-800">
                            <div
                                v-for="item in order.items"
                                :key="item.id"
                                class="py-3.5 first:pt-0 flex items-center justify-between gap-4"
                            >
                                <div class="flex items-center gap-3 min-w-0">
                                    <img
                                        :src="item.product_thumbnail ? (item.product_thumbnail.startsWith('http') ? item.product_thumbnail : (item.product_thumbnail.startsWith('/') ? item.product_thumbnail : (item.product_thumbnail.startsWith('assets/') ? `/${item.product_thumbnail}` : `/storage/${item.product_thumbnail}`))) : '/images/placeholder.jpg'"
                                        :alt="item.product_name"
                                        class="w-12 h-14 object-cover rounded-xl border border-slate-100 dark:border-slate-800 shrink-0"
                                    />
                                    <div class="min-w-0">
                                        <h4 class="text-xs font-bold text-slate-900 dark:text-white truncate">{{ item.product_name }}</h4>
                                        <p class="text-[11px] text-slate-400">
                                            Qty: {{ item.quantity }} <span v-if="item.size">• Size: {{ item.size }}</span>
                                        </p>
                                    </div>
                                </div>

                                <div class="text-xs font-bold font-mono text-slate-900 dark:text-white shrink-0">
                                    ৳{{ Number(item.total_price).toLocaleString() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Status Management & Financials (Col 8-12) -->
                <div class="lg:col-span-5 space-y-6">
                    <!-- Courier Fulfillment & Live Tracking Card -->
                    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-6 shadow-sm space-y-4">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                            <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-900 dark:text-white flex items-center gap-2">
                                <Truck class="w-4 h-4 text-[#730163]" />
                                <span>Courier Fulfillment</span>
                            </h3>

                            <!-- Dispatched Status Badge -->
                            <span
                                v-if="order.courier_tracking_code"
                                class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider"
                                :class="order.courier_name === 'steadfast' ? 'bg-emerald-500/10 text-emerald-600 border border-emerald-500/20' : (order.courier_name === 'pathao' ? 'bg-red-500/10 text-red-600 border border-red-500/20' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300')"
                            >
                                <span class="w-1.5 h-1.5 rounded-full" :class="order.courier_name === 'steadfast' ? 'bg-emerald-500' : (order.courier_name === 'pathao' ? 'bg-red-500' : 'bg-slate-500')" />
                                {{ order.courier_label }}
                            </span>
                            <span v-else class="text-[10px] font-bold text-amber-600 bg-amber-500/10 px-2 py-0.5 rounded-full">
                                Not Dispatched
                            </span>
                        </div>

                        <!-- ALREADY DISPATCHED STATE -->
                        <div v-if="order.courier_tracking_code" class="space-y-4 text-xs">
                            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-700/60 space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-[11px] text-slate-400 font-bold uppercase">Consignment Tracking</span>
                                    <span
                                        class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase"
                                        :class="order.courier_status === 'delivered' ? 'bg-emerald-500/10 text-emerald-600' : 'bg-sky-500/10 text-sky-600'"
                                    >
                                        {{ order.courier_status || 'In Transit' }}
                                    </span>
                                </div>

                                <div class="flex items-center justify-between gap-2 p-2.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700">
                                    <div class="font-mono font-black text-sm text-slate-900 dark:text-white truncate">
                                        {{ order.courier_tracking_code }}
                                    </div>
                                    <button
                                        type="button"
                                        @click="copyTrackingCode(order.courier_tracking_code)"
                                        class="p-1.5 rounded-lg text-slate-500 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                                        title="Copy Tracking Code"
                                    >
                                        <Check v-if="copied" class="w-4 h-4 text-emerald-600" />
                                        <Copy v-else class="w-4 h-4" />
                                    </button>
                                </div>

                                <div v-if="order.courier_dispatched_at" class="text-[11px] text-slate-400">
                                    Dispatched on: {{ new Date(order.courier_dispatched_at).toLocaleString() }}
                                </div>
                            </div>

                            <!-- Actions for Dispatched Order -->
                            <div class="flex flex-col sm:flex-row gap-2">
                                <a
                                    v-if="order.courier_tracking_url"
                                    :href="order.courier_tracking_url"
                                    target="_blank"
                                    class="flex-1 py-2.5 px-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-center flex items-center justify-center gap-1.5 shadow-sm transition-all"
                                >
                                    <span>Track on {{ order.courier_name === 'steadfast' ? 'Steadfast' : (order.courier_name === 'pathao' ? 'Pathao' : 'Courier') }}</span>
                                    <ExternalLink class="w-3.5 h-3.5" />
                                </a>

                                <button
                                    type="button"
                                    @click="refreshTracking"
                                    :disabled="isTrackingLoading"
                                    class="py-2.5 px-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 font-bold text-slate-700 dark:text-slate-300 flex items-center justify-center gap-1.5 transition-all disabled:opacity-50"
                                >
                                    <RefreshCw class="w-3.5 h-3.5" :class="{ 'animate-spin': isTrackingLoading }" />
                                    <span>Refresh</span>
                                </button>
                            </div>
                        </div>

                        <!-- NOT DISPATCHED YET: 1-CLICK DISPATCH FORM -->
                        <div v-else class="space-y-4 text-xs">
                            <form @submit.prevent="dispatchCourier" class="space-y-3">
                                <!-- Courier Choice -->
                                <div>
                                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                        Select Courier Partner
                                    </label>
                                    <div class="grid grid-cols-2 gap-2">
                                        <button
                                            type="button"
                                            @click="courierForm.courier = 'steadfast'"
                                            class="p-3 rounded-2xl border text-left transition-all flex items-center gap-2.5"
                                            :class="courierForm.courier === 'steadfast' ? 'border-emerald-500 bg-emerald-500/10 ring-2 ring-emerald-500/20' : 'border-slate-200 dark:border-slate-700 hover:border-slate-300'"
                                        >
                                            <div class="w-7 h-7 rounded-lg bg-emerald-600 text-white font-black text-xs flex items-center justify-center shrink-0">
                                                ST
                                            </div>
                                            <div>
                                                <div class="font-bold text-slate-900 dark:text-white leading-tight">Steadfast</div>
                                                <div class="text-[10px] text-slate-400">Doorstep COD</div>
                                            </div>
                                        </button>

                                        <button
                                            type="button"
                                            @click="courierForm.courier = 'pathao'"
                                            class="p-3 rounded-2xl border text-left transition-all flex items-center gap-2.5"
                                            :class="courierForm.courier === 'pathao' ? 'border-red-500 bg-red-500/10 ring-2 ring-red-500/20' : 'border-slate-200 dark:border-slate-700 hover:border-slate-300'"
                                        >
                                            <div class="w-7 h-7 rounded-lg bg-red-600 text-white font-black text-xs flex items-center justify-center shrink-0">
                                                PT
                                            </div>
                                            <div>
                                                <div class="font-bold text-slate-900 dark:text-white leading-tight">Pathao</div>
                                                <div class="text-[10px] text-slate-400">Express Delivery</div>
                                            </div>
                                        </button>
                                    </div>
                                </div>

                                <!-- COD Amount -->
                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <label class="font-bold text-slate-700 dark:text-slate-300">Amount to Collect (COD)</label>
                                        <span v-if="order.payment_status === 'paid'" class="text-[10px] font-bold text-emerald-600">Paid in Advance</span>
                                    </div>
                                    <div class="relative">
                                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 font-mono font-bold text-slate-400">৳</span>
                                        <input
                                            v-model.number="courierForm.cod_amount"
                                            type="number"
                                            min="0"
                                            class="w-full pl-8 pr-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 font-mono font-bold focus:outline-none focus:ring-2 focus:ring-[#730163]/20"
                                        />
                                    </div>
                                </div>

                                <!-- Delivery Note -->
                                <div>
                                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Delivery Instruction / Note</label>
                                    <input
                                        v-model="courierForm.note"
                                        type="text"
                                        placeholder="e.g. Fragile, allow customer to check parcel"
                                        class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:outline-none"
                                    />
                                </div>

                                <!-- One-Click Dispatch Button -->
                                <button
                                    type="submit"
                                    :disabled="courierForm.processing"
                                    class="w-full py-3 rounded-xl font-bold text-white shadow-md transition-all flex items-center justify-center gap-2 disabled:opacity-50"
                                    :class="courierForm.courier === 'steadfast' ? 'bg-emerald-600 hover:bg-emerald-700 shadow-emerald-600/25' : 'bg-red-600 hover:bg-red-700 shadow-red-600/25'"
                                >
                                    <Send class="w-4 h-4" />
                                    <span>{{ courierForm.processing ? 'Dispatching to Courier...' : (courierForm.courier === 'steadfast' ? 'Dispatch to Steadfast Now' : 'Dispatch to Pathao Now') }}</span>
                                </button>
                            </form>

                            <!-- Manual Tracking Option Toggle -->
                            <div class="pt-2 border-t border-slate-100 dark:border-slate-800 text-center">
                                <button
                                    type="button"
                                    @click="isManualCourierOpen = !isManualCourierOpen"
                                    class="text-[11px] font-bold text-slate-500 hover:text-slate-800 dark:hover:text-white"
                                >
                                    {{ isManualCourierOpen ? 'Hide Manual Entry' : '+ Or Assign Manual Tracking Number' }}
                                </button>

                                <form v-if="isManualCourierOpen" @submit.prevent="submitManualCourier" class="space-y-3 pt-3 text-left">
                                    <div>
                                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Courier Name</label>
                                        <input
                                            v-model="manualForm.courier_name"
                                            type="text"
                                            placeholder="e.g. Sundarban / SA Paribahan"
                                            class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs"
                                            required
                                        />
                                    </div>
                                    <div>
                                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Tracking / CN Number</label>
                                        <input
                                            v-model="manualForm.courier_tracking_code"
                                            type="text"
                                            placeholder="e.g. CN-90218"
                                            class="w-full font-mono px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-bold"
                                            required
                                        />
                                    </div>
                                    <button
                                        type="submit"
                                        :disabled="manualForm.processing"
                                        class="w-full py-2.5 rounded-xl bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs transition-all"
                                    >
                                        Save Manual Tracking
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- SMS Customer Notifications Card -->
                    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-6 shadow-sm space-y-4">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                            <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-900 dark:text-white flex items-center gap-2">
                                <MessageSquare class="w-4 h-4 text-[#730163]" />
                                SMS Customer Notifications
                            </h3>
                            <span class="text-[10px] text-slate-400 font-mono">
                                {{ order.customer_phone }}
                            </span>
                        </div>

                        <!-- 1-Click Action Buttons -->
                        <div class="space-y-2">
                            <div class="grid grid-cols-2 gap-2">
                                <button
                                    type="button"
                                    @click="sendOrderSms('order_placed')"
                                    :disabled="smsForm.processing"
                                    class="py-2.5 px-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/80 hover:bg-slate-100 font-bold text-[11px] text-slate-700 dark:text-slate-300 transition-all flex items-center justify-center gap-1.5 disabled:opacity-50 cursor-pointer"
                                >
                                    <Send class="w-3 h-3 text-[#730163]" />
                                    <span>Send Placed SMS</span>
                                </button>

                                <button
                                    type="button"
                                    @click="sendOrderSms('order_shipped')"
                                    :disabled="smsForm.processing"
                                    class="py-2.5 px-3 rounded-xl border border-purple-200 dark:border-purple-800 bg-purple-50/50 dark:bg-purple-950/20 hover:bg-purple-100 font-bold text-[11px] text-purple-700 dark:text-purple-300 transition-all flex items-center justify-center gap-1.5 disabled:opacity-50 cursor-pointer"
                                >
                                    <Truck class="w-3 h-3 text-purple-600" />
                                    <span>Send Shipped SMS</span>
                                </button>
                            </div>

                            <button
                                type="button"
                                @click="isCustomSmsOpen = !isCustomSmsOpen"
                                class="w-full py-2 px-3 rounded-xl border border-dashed border-slate-300 dark:border-slate-700 hover:border-slate-400 font-bold text-[11px] text-slate-600 dark:text-slate-400 transition-all flex items-center justify-center gap-1.5 cursor-pointer"
                            >
                                <MessageCircle class="w-3 h-3" />
                                <span>{{ isCustomSmsOpen ? 'Cancel Custom SMS' : 'Write & Send Custom SMS' }}</span>
                            </button>

                            <!-- Custom SMS Inline Form -->
                            <div v-if="isCustomSmsOpen" class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 space-y-2.5">
                                <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300">
                                    Custom SMS Message
                                </label>
                                <textarea
                                    v-model="smsForm.custom_message"
                                    rows="3"
                                    placeholder="Type custom text to send to customer mobile..."
                                    class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-xs focus:outline-none"
                                ></textarea>
                                <button
                                    type="button"
                                    @click="submitCustomSms"
                                    :disabled="smsForm.processing || !smsForm.custom_message"
                                    class="w-full py-2 rounded-xl bg-[#730163] hover:bg-[#5b014e] text-white text-xs font-bold transition-all disabled:opacity-50 flex items-center justify-center gap-1.5 cursor-pointer"
                                >
                                    <Send class="w-3.5 h-3.5" />
                                    <span>{{ smsForm.processing ? 'Dispatching...' : 'Send to ' + order.customer_phone }}</span>
                                </button>
                            </div>
                        </div>

                        <!-- SMS Delivery Log for This Order -->
                        <div class="pt-3 border-t border-slate-100 dark:border-slate-800 space-y-2">
                            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                SMS Dispatch History
                            </div>

                            <div v-if="order.sms_logs && order.sms_logs.length > 0" class="space-y-2 max-h-48 overflow-y-auto pr-1">
                                <div
                                    v-for="log in order.sms_logs"
                                    :key="log.id"
                                    class="p-2.5 rounded-xl border text-[11px] space-y-1 bg-slate-50/50 dark:bg-slate-800/40"
                                    :class="log.status === 'failed' ? 'border-rose-200 dark:border-rose-900/40' : 'border-slate-200 dark:border-slate-700/60'"
                                >
                                    <div class="flex items-center justify-between">
                                        <span class="font-bold capitalize text-slate-800 dark:text-slate-200">
                                            {{ log.event.replace('_', ' ') }}
                                        </span>
                                        <span
                                            class="px-1.5 py-0.2 rounded text-[9px] font-bold uppercase"
                                            :class="{
                                                'bg-emerald-500/10 text-emerald-600': log.status === 'sent',
                                                'bg-amber-500/10 text-amber-600': log.status === 'simulated',
                                                'bg-rose-500/10 text-rose-600': log.status === 'failed',
                                            }"
                                        >
                                            {{ log.status }}
                                        </span>
                                    </div>
                                    <p class="text-slate-600 dark:text-slate-400 text-[10px] leading-relaxed line-clamp-2">
                                        {{ log.message }}
                                    </p>
                                    <div class="text-[9px] text-slate-400 font-mono">
                                        {{ new Date(log.created_at).toLocaleString() }} via {{ log.gateway }}
                                    </div>
                                </div>
                            </div>

                            <p v-else class="text-[11px] text-slate-400 italic">
                                No SMS notifications dispatched yet for this order.
                            </p>
                        </div>
                    </div>

                    <!-- Status Control Card -->
                    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-6 shadow-sm space-y-5">
                        <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-900 dark:text-white pb-3 border-b border-slate-100 dark:border-slate-800">
                            Order Status Management
                        </h3>

                        <form @submit.prevent="updateStatus" class="space-y-4 text-xs">
                            <div>
                                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">
                                    Delivery / Fulfillment Status
                                </label>
                                <select
                                    v-model="form.order_status"
                                    class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 font-bold capitalize focus:outline-none"
                                >
                                    <option value="pending">Pending</option>
                                    <option value="processing">Processing</option>
                                    <option value="shipped">Shipped</option>
                                    <option value="delivered">Delivered</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">
                                    Payment Status
                                </label>
                                <select
                                    v-model="form.payment_status"
                                    class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 font-bold capitalize focus:outline-none"
                                >
                                    <option value="pending">Pending (Unpaid)</option>
                                    <option value="paid">Paid (Received)</option>
                                    <option value="failed">Failed</option>
                                </select>
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">
                                    Admin Internal Notes
                                </label>
                                <textarea
                                    v-model="form.admin_notes"
                                    rows="2"
                                    placeholder="e.g. Courier tracking code #ST-88910"
                                    class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:outline-none"
                                />
                            </div>

                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="w-full py-3 rounded-xl bg-[#730163] hover:bg-[#5b014e] text-white font-bold shadow-md transition-all flex items-center justify-center gap-2"
                            >
                                <Save class="w-4 h-4" />
                                <span>{{ form.processing ? 'Updating...' : 'Save Order Status' }}</span>
                            </button>
                        </form>
                    </div>

                    <!-- Financial Summary Card -->
                    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-6 shadow-sm space-y-3 text-xs text-slate-600 dark:text-slate-400">
                        <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-900 dark:text-white pb-3 border-b border-slate-100 dark:border-slate-800">
                            Billing Breakdown
                        </h3>

                        <div class="flex justify-between">
                            <span>Items Subtotal</span>
                            <span class="font-bold text-slate-900 dark:text-white font-mono">৳{{ Number(order.subtotal).toLocaleString() }}</span>
                        </div>

                        <div v-if="order.discount > 0" class="flex justify-between text-emerald-600 font-semibold">
                            <span>Coupon Discount</span>
                            <span class="font-mono">-৳{{ Number(order.discount).toLocaleString() }}</span>
                        </div>

                        <div class="flex justify-between">
                            <span>Delivery Fee</span>
                            <span class="font-mono">{{ order.delivery_charge === 0 ? 'FREE' : '৳' + order.delivery_charge }}</span>
                        </div>

                        <div class="flex justify-between text-base font-extrabold text-slate-900 dark:text-white pt-3 border-t border-slate-100 dark:border-slate-800">
                            <span>Total Payable ({{ order.payment_method?.toUpperCase() }})</span>
                            <span class="text-[#730163] font-mono text-lg">৳{{ Number(order.total_amount).toLocaleString() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
