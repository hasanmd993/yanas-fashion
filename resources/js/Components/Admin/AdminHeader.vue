<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import { usePage, router, Link } from "@inertiajs/vue3";
import {
    onClickOutside,
    useDark,
    useToggle,
} from "@vueuse/core";
import {
    Menu,
    ChevronDown,
    ExternalLink,
    LogOut,
    ShieldCheck,
    Bell,
    Sun,
    Moon,
    Maximize2,
    Minimize2,
    Search,
    ShoppingBag,
    ShoppingCart,
    AlertTriangle,
    CheckCheck,
    Clock,
    X,
    Sparkles,
} from "lucide-vue-next";

const props = defineProps({
    title: {
        type: String,
        default: "Dashboard",
    },
    isCollapsed: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["toggle-sidebar"]);

const page = usePage();
const authUser = computed(
    () =>
        page.props.auth?.user || {
            name: "Admin Manager",
            email: "admin@yanasfashion.com",
        },
);

// 1. Dark Mode State & Toggle
const isDark = useDark({
    selector: "html",
    attribute: "class",
    valueDark: "dark",
    valueLight: "",
});
const toggleDark = useToggle(isDark);

// 2. Fullscreen State & Cross-Browser Toggle
const isFullscreen = ref(false);

const checkFullscreen = () => {
    if (typeof document !== "undefined") {
        isFullscreen.value = Boolean(
            document.fullscreenElement ||
            document.webkitFullscreenElement ||
            document.mozFullScreenElement ||
            document.msFullscreenElement
        );
    }
};

onMounted(() => {
    if (typeof document !== "undefined") {
        document.addEventListener("fullscreenchange", checkFullscreen);
        document.addEventListener("webkitfullscreenchange", checkFullscreen);
        document.addEventListener("mozfullscreenchange", checkFullscreen);
        document.addEventListener("MSFullscreenChange", checkFullscreen);
    }
});

onUnmounted(() => {
    if (typeof document !== "undefined") {
        document.removeEventListener("fullscreenchange", checkFullscreen);
        document.removeEventListener("webkitfullscreenchange", checkFullscreen);
        document.removeEventListener("mozfullscreenchange", checkFullscreen);
        document.removeEventListener("MSFullscreenChange", checkFullscreen);
    }
});

const toggleFullscreen = async () => {
    try {
        if (typeof document === "undefined") return;

        const isCurrentlyFull = Boolean(
            document.fullscreenElement ||
            document.webkitFullscreenElement ||
            document.mozFullScreenElement ||
            document.msFullscreenElement
        );

        if (!isCurrentlyFull) {
            const docEl = document.documentElement;
            if (docEl.requestFullscreen) {
                await docEl.requestFullscreen();
            } else if (docEl.webkitRequestFullscreen) {
                await docEl.webkitRequestFullscreen();
            } else if (docEl.mozRequestFullScreen) {
                await docEl.mozRequestFullScreen();
            } else if (docEl.msRequestFullscreen) {
                await docEl.msRequestFullscreen();
            }
        } else {
            if (document.exitFullscreen) {
                await document.exitFullscreen();
            } else if (document.webkitExitFullscreen) {
                await document.webkitExitFullscreen();
            } else if (document.mozCancelFullScreen) {
                await document.mozCancelFullScreen();
            } else if (document.msExitFullscreen) {
                await document.msExitFullscreen();
            }
        }
    } catch (err) {
        console.warn("Fullscreen toggle warning:", err);
    }
};

// 3. User Menu Dropdown State
const isUserMenuOpen = ref(false);
const userDropdownRef = ref(null);

onClickOutside(userDropdownRef, () => {
    isUserMenuOpen.value = false;
});

const handleLogout = () => {
    isUserMenuOpen.value = false;
    router.post(route("logout"));
};

// 4. Notifications Center State
const isNotificationOpen = ref(false);
const notificationDropdownRef = ref(null);

onClickOutside(notificationDropdownRef, () => {
    isNotificationOpen.value = false;
});

// Dynamic Notification Feed (derived from Inertia shared backend data or live fallback)
const getIconForType = (type) => {
    if (type === 'order') return ShoppingCart;
    if (type === 'stock') return AlertTriangle;
    return Sparkles;
};

const getIconBgForType = (type) => {
    if (type === 'order') return 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400';
    if (type === 'stock') return 'bg-amber-500/10 text-amber-600 dark:text-amber-400';
    return 'bg-purple-500/10 text-[#730163] dark:text-purple-400';
};

const notifications = ref([]);

const initNotifications = () => {
    const raw = page.props.admin_notifications || [];
    if (Array.isArray(raw) && raw.length > 0) {
        notifications.value = raw.map(n => ({
            ...n,
            icon: getIconForType(n.type),
            iconBg: getIconBgForType(n.type),
        }));
    } else {
        notifications.value = [
            {
                id: 'sys-1',
                type: 'system',
                title: 'System Active & Secure',
                description: 'Yanas Fashion admin portal is online and responsive',
                time: 'Just now',
                isRead: true,
                icon: Sparkles,
                iconBg: 'bg-purple-500/10 text-[#730163] dark:text-purple-400',
                route: 'admin.dashboard',
            }
        ];
    }
};

initNotifications();

const unreadCount = computed(
    () => notifications.value.filter((n) => !n.isRead).length,
);

const markAllAsRead = () => {
    notifications.value.forEach((n) => (n.isRead = true));
};

const removeNotification = (id) => {
    notifications.value = notifications.value.filter((n) => n.id !== id);
};

const handleNotificationClick = (item) => {
    item.isRead = true;
    isNotificationOpen.value = false;
    if (item.route) {
        router.visit(route(item.route));
    }
};

// 5. Quick Search State
const searchQuery = ref("");
const handleHeaderSearch = () => {
    if (!searchQuery.value.trim()) return;
    router.get(route("admin.products.index"), { search: searchQuery.value });
};
</script>

<template>
    <header
        class="h-20 bg-white dark:bg-slate-900 border-b border-slate-200/80 dark:border-slate-800 px-4 sm:px-8 flex items-center justify-between sticky top-0 z-30 transition-colors"
    >
        <!-- Left Side: Sidebar Toggle & Title & Global Search -->
        <div class="flex items-center gap-3 sm:gap-6 flex-1 min-w-0 mr-4">
            <button
                @click="emit('toggle-sidebar')"
                class="p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 transition-colors cursor-pointer shrink-0"
                aria-label="Toggle Navigation Sidebar"
                title="Toggle Sidebar"
            >
                <Menu class="w-5 h-5" />
            </button>

            <!-- Page Title -->
            <div class="min-w-0 hidden md:block">
                <h1
                    class="text-base sm:text-lg font-extrabold text-slate-900 dark:text-white leading-none font-serif truncate"
                >
                    {{ title }}
                </h1>
                <span
                    class="text-[11px] text-slate-400 font-medium truncate block mt-0.5"
                    >Yanas Fashion Administration</span
                >
            </div>

            <!-- Header Quick Search -->
            <div class="relative max-w-xs w-full hidden lg:block ml-2">
                <input
                    v-model="searchQuery"
                    @keyup.enter="handleHeaderSearch"
                    type="text"
                    placeholder="Quick search products, orders..."
                    class="w-full pl-9 pr-12 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/80 text-xs text-slate-800 dark:text-slate-200 focus:bg-white dark:focus:bg-slate-900 focus:outline-none focus:ring-2 focus:ring-[#730163]/20 transition-all placeholder:text-slate-400"
                />
                <Search
                    class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"
                />
                <span
                    class="absolute right-2.5 top-1/2 -translate-y-1/2 text-[10px] font-mono text-slate-400 bg-slate-200/60 dark:bg-slate-700/60 px-1.5 py-0.5 rounded"
                >
                    ↵
                </span>
            </div>
        </div>

        <!-- Right Side: Utility Action Controls -->
        <div class="flex items-center gap-2 sm:gap-3 shrink-0">
            <!-- 1. Dark / Light Mode Switcher -->
            <button
                type="button"
                @click="toggleDark()"
                class="p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/80 dark:bg-slate-800/60 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 transition-all cursor-pointer"
                :title="isDark ? 'Switch to Light Mode' : 'Switch to Dark Mode'"
            >
                <Sun
                    v-if="isDark"
                    class="w-4 h-4 text-amber-400 animate-spin-slow"
                />
                <Moon v-else class="w-4 h-4 text-slate-600" />
            </button>

            <!-- 2. Fullscreen Toggle Button -->
            <button
                type="button"
                @click="toggleFullscreen"
                class="p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/80 dark:bg-slate-800/60 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 transition-all cursor-pointer hidden sm:inline-flex"
                :title="
                    isFullscreen
                        ? 'Exit Fullscreen'
                        : 'Enter Fullscreen Monitor Mode'
                "
            >
                <Minimize2
                    v-if="isFullscreen"
                    class="w-4 h-4 text-[#730163] dark:text-purple-400"
                />
                <Maximize2 v-else class="w-4 h-4" />
            </button>

            <!-- 3. Notifications Center Dropdown -->
            <div ref="notificationDropdownRef" class="relative">
                <button
                    type="button"
                    @click="isNotificationOpen = !isNotificationOpen"
                    class="relative p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/80 dark:bg-slate-800/60 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 transition-all cursor-pointer"
                    :class="{
                        'ring-2 ring-[#730163]/30 bg-slate-100 dark:bg-slate-800':
                            isNotificationOpen,
                    }"
                    title="Notifications"
                >
                    <Bell class="w-4 h-4" />

                    <!-- Pulsing Unread Badge Counter -->
                    <span
                        v-if="unreadCount > 0"
                        class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 rounded-full bg-gradient-to-r from-rose-500 to-[#730163] text-white text-[10px] font-extrabold flex items-center justify-center shadow-md animate-pulse"
                    >
                        {{ unreadCount }}
                    </span>
                </button>

                <!-- Notifications Dropdown Panel -->
                <transition
                    enter-active-class="transition duration-150 ease-out"
                    enter-from-class="transform scale-95 opacity-0 -translate-y-2"
                    enter-to-class="transform scale-100 opacity-100 translate-y-0"
                    leave-active-class="transition duration-100 ease-in"
                    leave-from-class="transform scale-100 opacity-100 translate-y-0"
                    leave-to-class="transform scale-95 opacity-0 -translate-y-2"
                >
                    <div
                        v-if="isNotificationOpen"
                        class="absolute right-0 mt-2 w-80 sm:w-96 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-2xl overflow-hidden z-50 divide-y divide-slate-100 dark:divide-slate-800/80 animate-in"
                    >
                        <!-- Notifications Header -->
                        <div
                            class="p-4 bg-slate-50/70 dark:bg-slate-800/50 flex items-center justify-between"
                        >
                            <div class="flex items-center gap-2">
                                <h3
                                    class="text-xs font-extrabold text-slate-900 dark:text-white uppercase tracking-wider"
                                >
                                    Notifications
                                </h3>
                                <span
                                    v-if="unreadCount > 0"
                                    class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#730163]/10 text-[#730163] dark:text-purple-300"
                                >
                                    {{ unreadCount }} new
                                </span>
                            </div>

                            <button
                                v-if="unreadCount > 0"
                                type="button"
                                @click="markAllAsRead"
                                class="text-[11px] font-bold text-[#730163] dark:text-purple-400 hover:underline flex items-center gap-1"
                            >
                                <CheckCheck class="w-3.5 h-3.5" />
                                <span>Mark all read</span>
                            </button>
                        </div>

                        <!-- Notifications Feed List -->
                        <div
                            class="max-h-80 overflow-y-auto divide-y divide-slate-100 dark:divide-slate-800/60"
                        >
                            <div
                                v-for="item in notifications"
                                :key="item.id"
                                @click="handleNotificationClick(item)"
                                class="p-3.5 flex items-start gap-3 hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-colors cursor-pointer group relative"
                                :class="{
                                    'bg-purple-50/40 dark:bg-purple-950/20':
                                        !item.isRead,
                                }"
                            >
                                <div
                                    class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 mt-0.5"
                                    :class="item.iconBg"
                                >
                                    <component
                                        :is="item.icon"
                                        class="w-4 h-4"
                                    />
                                </div>

                                <div class="flex-1 min-w-0 pr-4">
                                    <div
                                        class="flex items-center justify-between gap-1"
                                    >
                                        <h4
                                            class="text-xs font-bold text-slate-900 dark:text-white truncate"
                                        >
                                            {{ item.title }}
                                        </h4>
                                        <span
                                            class="text-[10px] text-slate-400 shrink-0 font-mono"
                                        >
                                            {{ item.time }}
                                        </span>
                                    </div>
                                    <p
                                        class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 line-clamp-2"
                                    >
                                        {{ item.description }}
                                    </p>
                                </div>

                                <button
                                    type="button"
                                    @click.stop="removeNotification(item.id)"
                                    class="absolute right-2 top-3 opacity-0 group-hover:opacity-100 p-1 rounded-lg text-slate-400 hover:text-rose-500 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all"
                                    title="Dismiss"
                                >
                                    <X class="w-3.5 h-3.5" />
                                </button>
                            </div>

                            <div
                                v-if="notifications.length === 0"
                                class="py-8 px-4 text-center"
                            >
                                <div
                                    class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 mx-auto flex items-center justify-center mb-2"
                                >
                                    <Bell class="w-6 h-6 opacity-60" />
                                </div>
                                <p
                                    class="text-xs font-bold text-slate-700 dark:text-slate-300"
                                >
                                    All caught up!
                                </p>
                                <p class="text-[11px] text-slate-400 mt-0.5">
                                    No unread alerts at this time
                                </p>
                            </div>
                        </div>

                        <!-- Notifications Footer -->
                        <div
                            class="p-2.5 bg-slate-50/50 dark:bg-slate-800/30 text-center"
                        >
                            <Link
                                :href="route('admin.orders.index')"
                                @click="isNotificationOpen = false"
                                class="text-[11px] font-bold text-[#730163] dark:text-purple-400 hover:underline"
                            >
                                View All Store Orders →
                            </Link>
                        </div>
                    </div>
                </transition>
            </div>

            <!-- 5. User Menu Dropdown Container -->
            <div ref="userDropdownRef" class="relative pl-1">
                <!-- Dropdown Trigger Button -->
                <button
                    type="button"
                    @click="isUserMenuOpen = !isUserMenuOpen"
                    class="flex items-center gap-2.5 p-1 sm:px-2.5 sm:py-1.5 rounded-2xl border border-slate-200 dark:border-slate-700/80 bg-slate-50/80 dark:bg-slate-800/60 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all cursor-pointer focus:outline-none focus:ring-2 focus:ring-[#730163]/20"
                    :class="{
                        'ring-2 ring-[#730163]/30 bg-slate-100 dark:bg-slate-800':
                            isUserMenuOpen,
                    }"
                >
                    <!-- User Avatar -->
                    <div
                        class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-gradient-to-br from-[#730163] to-[#F68625] text-white flex items-center justify-center font-extrabold text-xs shadow-md shadow-[#730163]/20 shrink-0"
                    >
                        {{
                            authUser.name
                                ? authUser.name.charAt(0).toUpperCase()
                                : "A"
                        }}
                    </div>

                    <!-- User Name -->
                    <div class="hidden sm:block text-left pr-1">
                        <div
                            class="text-xs font-bold text-slate-900 dark:text-white leading-tight"
                        >
                            {{ authUser.name || "Admin" }}
                        </div>
                    </div>

                    <!-- Dropdown Arrow -->
                    <ChevronDown
                        class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200 shrink-0"
                        :class="{ 'rotate-180 text-[#730163]': isUserMenuOpen }"
                    />
                </button>

                <!-- Dropdown Menu Card -->
                <transition
                    enter-active-class="transition duration-150 ease-out"
                    enter-from-class="transform scale-95 opacity-0 -translate-y-2"
                    enter-to-class="transform scale-100 opacity-100 translate-y-0"
                    leave-active-class="transition duration-100 ease-in"
                    leave-from-class="transform scale-100 opacity-100 translate-y-0"
                    leave-to-class="transform scale-95 opacity-0 -translate-y-2"
                >
                    <div
                        v-if="isUserMenuOpen"
                        class="absolute right-0 mt-2 w-64 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-2xl overflow-hidden z-50 divide-y divide-slate-100 dark:divide-slate-800/80 animate-in"
                    >
                        <!-- User Summary Header -->
                        <div class="p-4 bg-slate-50/60 dark:bg-slate-800/40">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 rounded-xl bg-[#730163] text-white flex items-center justify-center font-bold text-sm shadow-sm shrink-0"
                                >
                                    {{
                                        authUser.name
                                            ? authUser.name
                                                  .charAt(0)
                                                  .toUpperCase()
                                            : "A"
                                    }}
                                </div>
                                <div class="min-w-0">
                                    <div
                                        class="text-xs font-extrabold text-slate-900 dark:text-white truncate"
                                    >
                                        {{ authUser.name || "Admin Manager" }}
                                    </div>
                                    <div
                                        class="text-[11px] text-slate-400 truncate font-mono mt-0.5"
                                    >
                                        {{
                                            authUser.email ||
                                            "admin@yanasfashion.com"
                                        }}
                                    </div>
                                </div>
                            </div>

                            <div
                                class="mt-3 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-purple-50 dark:bg-purple-950/40 text-[#730163] dark:text-purple-300 text-[10px] font-bold uppercase tracking-wider"
                            >
                                <ShieldCheck class="w-3.5 h-3.5" />
                                <span>Super Administrator</span>
                            </div>
                        </div>

                        <!-- Sign Out Button -->
                        <div class="p-2">
                            <button
                                type="button"
                                @click="handleLogout"
                                class="w-full flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-bold text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition-colors cursor-pointer"
                            >
                                <LogOut class="w-4 h-4" />
                                <span>Sign Out of Portal</span>
                            </button>
                        </div>
                    </div>
                </transition>
            </div>
        </div>
    </header>
</template>
