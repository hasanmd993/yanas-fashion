<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import Navbar from '@/Components/Navbar.vue';
import Footer from '@/Components/Footer.vue';
import CartDrawer from '@/Components/CartDrawer.vue';
import ToastNotifications from '@/Components/ToastNotifications.vue';
import { MessageCircle, ArrowUp } from 'lucide-vue-next';

const page = usePage();
const settings = computed(() => page.props.settings || {});

const showScrollTop = ref(false);

const handleScroll = () => {
    showScrollTop.value = window.scrollY > 400;
};

const scrollToTop = () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
};

onMounted(() => {
    window.addEventListener('scroll', handleScroll, { passive: true });
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
});
</script>

<template>
    <div class="min-h-screen flex flex-col bg-[#fafafa] dark:bg-slate-950 text-slate-900 dark:text-slate-100 font-sans selection:bg-rose-500 selection:text-white">
        <!-- Site Navbar -->
        <Navbar />

        <!-- Main Content Area -->
        <main class="flex-1">
            <slot />
        </main>

        <!-- Site Footer -->
        <Footer />

        <!-- Slide-over Cart Drawer -->
        <CartDrawer />

        <!-- Flash Toast Notifications -->
        <ToastNotifications />

        <!-- Floating WhatsApp Live Chat Button -->
        <a
            :href="`https://wa.me/${String(settings.whatsapp || '8801713580400').replace(/[^0-9]/g, '')}?text=${encodeURIComponent('Hello Yanas Fashion! I would like to inquire about an order.')}`"
            target="_blank"
            rel="noopener noreferrer"
            class="fixed bottom-6 left-6 z-40 group flex items-center gap-2.5 bg-[#25D366] hover:bg-[#20bd5a] text-white px-4 py-3 rounded-full shadow-xl shadow-emerald-600/30 font-extrabold text-xs transition-all hover:scale-105 active:scale-95"
            title="Chat on WhatsApp"
        >
            <div class="relative">
                <MessageCircle class="w-5 h-5 fill-current" />
                <span class="absolute -top-1 -right-1 flex h-2.5 w-2.5">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-200"></span>
                </span>
            </div>
            <span class="hidden sm:inline font-bold">Order / Support on WhatsApp</span>
        </a>

        <!-- Scroll To Top Button -->
        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0 translate-y-4"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100 translate-y-0"
            leave-to-class="opacity-0 translate-y-4"
        >
            <button
                v-if="showScrollTop"
                @click="scrollToTop"
                class="fixed bottom-6 right-6 z-40 p-3 rounded-full bg-slate-900 dark:bg-white text-white dark:text-slate-900 shadow-xl hover:opacity-90 transition-all hover:scale-110 active:scale-95"
                aria-label="Scroll to top"
            >
                <ArrowUp class="w-4 h-4" />
            </button>
        </Transition>
    </div>
</template>
