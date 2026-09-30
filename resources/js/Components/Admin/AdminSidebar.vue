<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import {
    LayoutDashboard,
    ShoppingBag,
    Layers,
    ShoppingCart,
    Tag,
    Image,
    Settings,
    Database,
    ExternalLink,
    X,
    ChevronRight
} from 'lucide-vue-next';

const props = defineProps({
    isCollapsed: {
        type: Boolean,
        default: false,
    },
    isMobileOpen: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['close-mobile']);

const page = usePage();
const settings = computed(() => page.props.settings || {});

const navItems = [
    { name: 'Dashboard', route: 'admin.dashboard', icon: LayoutDashboard, activePrefix: 'admin.dashboard' },
    { name: 'Products', route: 'admin.products.index', icon: ShoppingBag, activePrefix: 'admin.products' },
    { name: 'Categories', route: 'admin.categories.index', icon: Layers, activePrefix: 'admin.categories' },
    { name: 'Orders', route: 'admin.orders.index', icon: ShoppingCart, activePrefix: 'admin.orders' },
    { name: 'Coupons', route: 'admin.coupons.index', icon: Tag, activePrefix: 'admin.coupons' },
    { name: 'Hero Sliders', route: 'admin.sliders.index', icon: Image, activePrefix: 'admin.sliders' },
    { name: 'Store Settings', route: 'admin.settings.index', icon: Settings, activePrefix: 'admin.settings' },
    { name: 'System Backups', route: 'admin.backups.index', icon: Database, activePrefix: 'admin.backups' },
];

const isRouteActive = (prefix) => {
    return route().current(prefix + '.*') || route().current(prefix);
};

const handleNavClick = () => {
    if (typeof window !== 'undefined' && window.innerWidth < 1024) {
        emit('close-mobile');
    }
};
</script>

<template>
    <div>
        <!-- Mobile Backdrop Overlay -->
        <div
            v-if="isMobileOpen"
            @click="emit('close-mobile')"
            class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm z-40 lg:hidden transition-opacity"
        />

        <!-- Sidebar Container (Handles both desktop mini-icon / full width and mobile drawer) -->
        <aside
            class="fixed inset-y-0 left-0 z-50 bg-slate-900 text-slate-300 flex flex-col justify-between border-r border-slate-800 transition-all duration-300 ease-in-out shadow-2xl lg:shadow-none"
            :class="[
                // Desktop sizing: collapsed (w-20) vs expanded (w-64)
                isCollapsed ? 'lg:w-20' : 'lg:w-64',
                // Mobile drawer translation
                isMobileOpen ? 'w-64 translate-x-0' : 'w-64 -translate-x-full lg:translate-x-0'
            ]"
        >
            <!-- Brand Header -->
            <div
                class="h-20 flex items-center border-b border-slate-800/80 transition-all"
                :class="isCollapsed ? 'lg:px-0 lg:justify-center px-6 justify-between' : 'px-6 justify-between'"
            >
                <Link
                    :href="route('admin.dashboard')"
                    class="flex items-center gap-3 group"
                    :title="settings.site_name || 'Yanas Fashion'"
                >
                    <img
                        :src="settings.site_logo"
                        :alt="settings.site_name"
                        class="h-9 w-9 object-contain rounded-lg bg-white p-0.5 shrink-0 shadow-sm transition-transform group-hover:scale-105"
                    />
                    <div v-if="!isCollapsed" class="min-w-0 transition-opacity duration-200">
                        <span class="font-extrabold text-sm tracking-wide text-white block leading-none font-serif truncate">
                            YANAS
                        </span>
                        <span class="text-[9px] tracking-[0.2em] text-[#F68625] font-bold uppercase block mt-0.5 truncate">
                            ADMIN PORTAL
                        </span>
                    </div>
                </Link>

                <!-- Mobile Close Button -->
                <button
                    @click="emit('close-mobile')"
                    class="lg:hidden p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800"
                >
                    <X class="w-5 h-5" />
                </button>
            </div>

            <!-- Navigation Links -->
            <div
                class="flex-1 py-6 overflow-y-auto overflow-x-hidden space-y-1.5"
                :class="isCollapsed ? 'lg:px-2 px-4' : 'px-4'"
            >
                <!-- Section Header (hidden or condensed when collapsed) -->
                <div
                    v-if="!isCollapsed"
                    class="px-3 pb-2 text-[10px] font-extrabold uppercase tracking-widest text-slate-500"
                >
                    Management
                </div>
                <div v-else class="hidden lg:block my-2 border-b border-slate-800/60 mx-2" />

                <!-- Menu Item Links -->
                <Link
                    v-for="item in navItems"
                    :key="item.name"
                    :href="route(item.route)"
                    @click="handleNavClick"
                    class="relative group flex items-center rounded-xl text-xs font-bold transition-all"
                    :class="[
                        isCollapsed 
                            ? 'lg:justify-center lg:p-3 p-3.5 justify-start gap-3' 
                            : 'justify-between px-3.5 py-2.5',
                        isRouteActive(item.activePrefix)
                            ? 'bg-[#730163] text-white shadow-md shadow-[#730163]/30 font-extrabold'
                            : 'text-slate-400 hover:text-white hover:bg-slate-800/60'
                    ]"
                >
                    <!-- Nav Icon + Label -->
                    <div
                        class="flex items-center"
                        :class="isCollapsed ? 'lg:justify-center gap-3' : 'gap-3'"
                    >
                        <component
                            :is="item.icon"
                            class="w-5 h-5 shrink-0 transition-transform group-hover:scale-110"
                            :class="isRouteActive(item.activePrefix) ? 'text-white' : 'text-slate-400 group-hover:text-white'"
                        />
                        <span v-if="!isCollapsed" class="truncate font-sans text-xs">
                            {{ item.name }}
                        </span>
                    </div>

                    <ChevronRight
                        v-if="!isCollapsed && isRouteActive(item.activePrefix)"
                        class="w-3.5 h-3.5 opacity-60 shrink-0"
                    />

                    <!-- Floating Tooltip Pill in Mini-Icon Mode (Desktop only) -->
                    <div
                        v-if="isCollapsed"
                        class="hidden lg:block absolute left-full ml-3 px-3 py-1.5 rounded-xl bg-slate-950 text-white text-xs font-bold whitespace-nowrap shadow-2xl border border-slate-700 pointer-events-none opacity-0 group-hover:opacity-100 transition-all duration-200 translate-x-1 group-hover:translate-x-0 z-50"
                    >
                        {{ item.name }}
                    </div>
                </Link>
            </div>

            <!-- Footer: Live Store Shortcut -->
            <div
                class="border-t border-slate-800/80 bg-slate-950/40 transition-all"
                :class="isCollapsed ? 'lg:p-2 p-4' : 'p-4'"
            >
                <a
                    :href="route('home')"
                    target="_blank"
                    class="group relative flex items-center rounded-xl bg-slate-800/80 hover:bg-slate-800 text-slate-300 hover:text-white text-xs font-semibold transition-colors"
                    :class="isCollapsed ? 'lg:justify-center lg:p-3 p-3.5 justify-between' : 'justify-between px-3.5 py-2.5'"
                    title="View Live Storefront"
                >
                    <span class="flex items-center gap-2">
                        <ExternalLink class="w-4 h-4 text-[#F68625] shrink-0" />
                        <span v-if="!isCollapsed">Live Store</span>
                    </span>
                    <span
                        v-if="!isCollapsed"
                        class="text-[10px] bg-slate-700 px-1.5 py-0.5 rounded font-mono"
                    >
                        Site
                    </span>

                    <!-- Tooltip when in mini icon mode -->
                    <div
                        v-if="isCollapsed"
                        class="hidden lg:block absolute left-full ml-3 px-3 py-1.5 rounded-xl bg-slate-950 text-white text-xs font-bold whitespace-nowrap shadow-2xl border border-slate-700 pointer-events-none opacity-0 group-hover:opacity-100 transition-all duration-200 translate-x-1 group-hover:translate-x-0 z-50"
                    >
                        View Live Store
                    </div>
                </a>
            </div>
        </aside>
    </div>
</template>
