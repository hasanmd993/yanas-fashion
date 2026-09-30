<script setup>
import { ref, onErrorCaptured } from 'vue';
import { AlertTriangle, RefreshCw, Home, MessageCircle, ChevronDown } from 'lucide-vue-next';

const hasError = ref(false);
const errorDetails = ref(null);
const showTechDetails = ref(false);

onErrorCaptured((err, instance, info) => {
    console.error('[Vue Error Boundary Captured]:', err, info);
    hasError.value = true;
    errorDetails.value = {
        message: err?.message || 'An unexpected rendering error occurred.',
        stack: err?.stack || '',
        info: info || '',
    };
    // Return false to prevent error from bubbling further and crashing the root app
    return false;
});

const reloadPage = () => {
    window.location.reload();
};

const goHome = () => {
    window.location.href = '/';
};
</script>

<template>
    <div v-if="hasError" class="min-h-[70vh] flex items-center justify-center p-6 bg-slate-50 dark:bg-slate-950 font-sans text-slate-900 dark:text-white">
        <div class="max-w-lg w-full bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-8 shadow-2xl text-center space-y-6">
            <!-- Icon -->
            <div class="w-16 h-16 rounded-2xl bg-rose-50 dark:bg-rose-950/50 text-rose-600 flex items-center justify-center mx-auto shadow-sm">
                <AlertTriangle class="w-8 h-8" />
            </div>

            <!-- Title & Notice -->
            <div class="space-y-2">
                <h2 class="text-xl font-black text-slate-900 dark:text-white font-serif">
                    Something went wrong
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto leading-relaxed">
                    We encountered an unexpected issue while displaying this page. Please try refreshing or return to the home catalog.
                </p>
            </div>

            <!-- Error Message Preview -->
            <div v-if="errorDetails" class="p-3.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-left border border-slate-200/70 dark:border-slate-700/60">
                <div class="text-[11px] font-mono font-semibold text-rose-600 dark:text-rose-400 break-words">
                    {{ errorDetails.message }}
                </div>

                <div v-if="errorDetails.stack" class="mt-2 pt-2 border-t border-slate-200 dark:border-slate-700">
                    <button
                        type="button"
                        @click="showTechDetails = !showTechDetails"
                        class="text-[10px] text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 flex items-center gap-1 font-mono font-bold cursor-pointer"
                    >
                        <span>{{ showTechDetails ? 'Hide technical trace' : 'Show technical trace' }}</span>
                        <ChevronDown class="w-3 h-3 transition-transform" :class="{ 'rotate-180': showTechDetails }" />
                    </button>

                    <pre
                        v-if="showTechDetails"
                        class="mt-2 text-[10px] text-slate-600 dark:text-slate-400 max-h-36 overflow-auto font-mono whitespace-pre-wrap bg-white dark:bg-slate-900 p-2 rounded-lg border border-slate-200 dark:border-slate-800"
                    >{{ errorDetails.stack }}</pre>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3 pt-2">
                <button
                    type="button"
                    @click="reloadPage"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-slate-900 dark:bg-white text-white dark:text-slate-900 text-xs font-bold shadow-md hover:opacity-90 transition-all cursor-pointer"
                >
                    <RefreshCw class="w-4 h-4" />
                    <span>Refresh Page</span>
                </button>

                <button
                    type="button"
                    @click="goHome"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer"
                >
                    <Home class="w-4 h-4" />
                    <span>Back to Home</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Normal Slot Render -->
    <slot v-else />
</template>
