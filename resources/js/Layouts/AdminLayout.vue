<script setup>
import { ref, onMounted } from 'vue';
import AdminSidebar from '@/Components/Admin/AdminSidebar.vue';
import AdminHeader from '@/Components/Admin/AdminHeader.vue';
import ToastNotifications from '@/Components/ToastNotifications.vue';

const props = defineProps({
    title: {
        type: String,
        default: 'Admin Dashboard',
    },
});

// Desktop collapsed state (false = expanded w-64, true = mini icon w-20)
const isCollapsed = ref(false);

// Mobile drawer open/close state
const isMobileOpen = ref(false);

const toggleSidebar = () => {
    if (typeof window !== 'undefined' && window.innerWidth < 1024) {
        isMobileOpen.value = !isMobileOpen.value;
    } else {
        isCollapsed.value = !isCollapsed.value;
    }
};
</script>

<template>
    <div class="min-h-screen bg-slate-100/70 dark:bg-slate-950 text-slate-900 dark:text-slate-100 font-sans selection:bg-[#730163] selection:text-white flex">
        <!-- Admin Sidebar -->
        <AdminSidebar
            :is-collapsed="isCollapsed"
            :is-mobile-open="isMobileOpen"
            @close-mobile="isMobileOpen = false"
        />

        <!-- Main Wrapper with dynamic desktop left padding (pl-64 or pl-20) -->
        <div
            class="flex-1 flex flex-col min-w-0 transition-all duration-300 ease-in-out"
            :class="isCollapsed ? 'lg:pl-20' : 'lg:pl-64'"
        >
            <!-- Admin Top Header -->
            <AdminHeader
                :title="title"
                :is-collapsed="isCollapsed"
                @toggle-sidebar="toggleSidebar"
            />

            <!-- Main Page Content -->
            <main class="flex-1 p-4 sm:p-8 max-w-7xl w-full mx-auto">
                <slot />
            </main>
        </div>

        <!-- Flash Toast Alerts -->
        <ToastNotifications />
    </div>
</template>
