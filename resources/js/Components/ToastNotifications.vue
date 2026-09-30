<script setup>
import { ref, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { CheckCircle2, AlertCircle, AlertTriangle, Info, X } from 'lucide-vue-next';

const page = usePage();
const toasts = ref([]);

const addToast = (message, type = 'success') => {
    if (!message) return;
    const id = Date.now() + Math.random();
    toasts.value.push({ id, message, type });

    setTimeout(() => {
        removeToast(id);
    }, 3500);
};

const removeToast = (id) => {
    toasts.value = toasts.value.filter(t => t.id !== id);
};

// Listen to flash messages from Inertia page props
watch(
    () => page.props.flash,
    (flash) => {
        if (flash?.success) addToast(flash.success, 'success');
        if (flash?.error) addToast(flash.error, 'error');
        if (flash?.warning) addToast(flash.warning, 'warning');
        if (flash?.info) addToast(flash.info, 'info');
    },
    { deep: true, immediate: true }
);

defineExpose({ addToast });
</script>

<template>
    <div class="fixed bottom-5 right-5 z-[9999] flex flex-col gap-2.5 max-w-sm w-full pointer-events-none px-4 sm:px-0">
        <TransitionGroup
            enter-active-class="transform ease-out duration-300 transition"
            enter-from-class="translate-y-4 opacity-0 scale-95"
            enter-to-class="translate-y-0 opacity-100 scale-100"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95"
        >
            <div
                v-for="toast in toasts"
                :key="toast.id"
                class="pointer-events-auto flex items-start gap-3 p-4 rounded-xl shadow-xl border backdrop-blur-md transition-all text-sm font-medium"
                :class="{
                    'bg-emerald-950/90 text-emerald-100 border-emerald-500/30': toast.type === 'success',
                    'bg-rose-950/90 text-rose-100 border-rose-500/30': toast.type === 'error',
                    'bg-amber-950/90 text-amber-100 border-amber-500/30': toast.type === 'warning',
                    'bg-slate-900/90 text-slate-100 border-slate-700/50': toast.type === 'info',
                }"
            >
                <div class="shrink-0 mt-0.5">
                    <CheckCircle2 v-if="toast.type === 'success'" class="w-5 h-5 text-emerald-400" />
                    <AlertCircle v-else-if="toast.type === 'error'" class="w-5 h-5 text-rose-400" />
                    <AlertTriangle v-else-if="toast.type === 'warning'" class="w-5 h-5 text-amber-400" />
                    <Info v-else class="w-5 h-5 text-sky-400" />
                </div>

                <div class="flex-1 pr-2 leading-relaxed">
                    {{ toast.message }}
                </div>

                <button
                    @click="removeToast(toast.id)"
                    class="shrink-0 opacity-60 hover:opacity-100 transition-opacity p-0.5 rounded-md"
                >
                    <X class="w-4 h-4" />
                </button>
            </div>
        </TransitionGroup>
    </div>
</template>
