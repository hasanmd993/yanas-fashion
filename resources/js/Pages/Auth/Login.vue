<script setup>
import { ref } from "vue";
import { Head, Link, useForm, usePage } from "@inertiajs/vue3";
import {
    Lock,
    Mail,
    Eye,
    EyeOff,
    ArrowRight,
    ShieldCheck,
    Sparkles,
    ArrowLeft,
    CheckCircle2,
} from "lucide-vue-next";

const page = usePage();
const settings = page.props.settings || {};

const showPassword = ref(false);

const form = useForm({
    email: "",
    password: "",
    remember: true,
});

const submit = () => {
    form.post(route("login.submit"), {
        onFinish: () => form.reset("password"),
    });
};
</script>

<template>
    <Head title="Admin & Portal Login — Yanas Fashion" />

    <div
        class="min-h-screen bg-[#fafafa] dark:bg-slate-950 text-slate-900 dark:text-slate-100 flex flex-col justify-between font-sans selection:bg-[#730163] selection:text-white relative overflow-hidden"
    >
        <!-- Ambient Glowing Background Accents -->
        <div
            class="absolute -top-40 -left-40 w-96 h-96 bg-[#730163]/10 dark:bg-[#730163]/20 rounded-full blur-3xl pointer-events-none"
        ></div>
        <div
            class="absolute -bottom-40 -right-40 w-96 h-96 bg-[#F68625]/10 dark:bg-[#F68625]/20 rounded-full blur-3xl pointer-events-none"
        ></div>

        <!-- Top Header Navigation -->
        <header
            class="w-full max-w-7xl mx-auto px-6 py-6 flex items-center justify-between z-10"
        >
            <Link
                :href="route('home')"
                class="flex items-center gap-2 text-xs font-bold text-slate-500 hover:text-[#730163] dark:hover:text-white transition-colors"
            >
                <ArrowLeft class="w-4 h-4" />
                <span>Return to Storefront</span>
            </Link>
        </header>

        <!-- Main Card Container -->
        <main class="w-full max-w-md mx-auto px-6 py-8 z-10">
            <div
                class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 p-8 sm:p-10 shadow-2xl shadow-slate-200/50 dark:shadow-none relative"
            >
                <!-- Brand Logo / Title Header -->
                <div class="text-center mb-8">
                    <div
                        class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-[#730163] to-[#990284] text-white flex items-center justify-center mx-auto shadow-lg shadow-[#730163]/30 mb-4 p-3"
                    >
                        <img
                            v-if="settings.site_logo"
                            :src="settings.site_logo"
                            alt="Yanas Fashion"
                            class="max-h-full max-w-full object-contain filter brightness-0 invert"
                        />
                        <Sparkles v-else class="w-8 h-8" />
                    </div>

                    <h1
                        class="text-2xl font-black text-slate-900 dark:text-white font-serif tracking-tight"
                    >
                        {{ settings.site_name || "Yanas Fashion" }}
                    </h1>
                    <p
                        class="text-xs text-slate-500 dark:text-slate-400 mt-1 font-medium"
                    >
                        Sign in to access your dashboard & order management
                    </p>
                </div>

                <!-- Form -->
                <form @submit.prevent="submit" class="space-y-5">
                    <!-- Email Input -->
                    <div class="space-y-1.5">
                        <label
                            class="block text-xs font-bold text-slate-700 dark:text-slate-300"
                        >
                            Email Address <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <Mail
                                class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2"
                            />
                            <input
                                v-model="form.email"
                                type="email"
                                required
                                placeholder="admin@yanasfashion.com"
                                autocomplete="email"
                                autofocus
                                class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950 text-xs font-semibold text-slate-900 dark:text-white focus:outline-none focus:border-[#730163] focus:bg-white dark:focus:bg-slate-900 transition-all"
                                :class="{
                                    'border-rose-500': form.errors.email,
                                }"
                            />
                        </div>
                        <p
                            v-if="form.errors.email"
                            class="text-[11px] text-rose-500 font-semibold mt-1"
                        >
                            {{ form.errors.email }}
                        </p>
                    </div>

                    <!-- Password Input -->
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label
                                class="block text-xs font-bold text-slate-700 dark:text-slate-300"
                            >
                                Password <span class="text-rose-500">*</span>
                            </label>
                        </div>
                        <div class="relative">
                            <Lock
                                class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2"
                            />
                            <input
                                v-model="form.password"
                                :type="showPassword ? 'text' : 'password'"
                                required
                                placeholder="••••••••••••"
                                autocomplete="current-password"
                                class="w-full pl-10 pr-11 py-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950 text-xs font-semibold text-slate-900 dark:text-white focus:outline-none focus:border-[#730163] focus:bg-white dark:focus:bg-slate-900 transition-all"
                                :class="{
                                    'border-rose-500': form.errors.password,
                                }"
                            />
                            <button
                                type="button"
                                @click="showPassword = !showPassword"
                                class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors p-1"
                                tabindex="-1"
                            >
                                <EyeOff v-if="showPassword" class="w-4 h-4" />
                                <Eye v-else class="w-4 h-4" />
                            </button>
                        </div>
                        <p
                            v-if="form.errors.password"
                            class="text-[11px] text-rose-500 font-semibold mt-1"
                        >
                            {{ form.errors.password }}
                        </p>
                    </div>

                    <!-- Remember Me & Forgot Password Row -->
                    <div class="flex items-center justify-between pt-1">
                        <label
                            class="flex items-center gap-2 cursor-pointer select-none"
                        >
                            <input
                                v-model="form.remember"
                                type="checkbox"
                                class="w-4 h-4 rounded text-[#730163] focus:ring-[#730163] border-slate-300 dark:border-slate-700 dark:bg-slate-800"
                            />
                            <span
                                class="text-xs font-semibold text-slate-600 dark:text-slate-400"
                                >Remember me</span
                            >
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full py-3.5 px-6 rounded-xl bg-gradient-to-r from-[#730163] to-[#8c0278] hover:from-[#5e0151] hover:to-[#730163] text-white text-xs font-extrabold uppercase tracking-wider shadow-xl shadow-[#730163]/25 transition-all flex items-center justify-center gap-2 disabled:opacity-50 hover:scale-[1.01] cursor-pointer"
                    >
                        <Lock class="w-4 h-4" />
                        <span>{{
                            form.processing
                                ? "Signing In..."
                                : "Sign In to Dashboard"
                        }}</span>
                    </button>
                </form>
            </div>
        </main>

        <!-- Footer -->
        <footer
            class="w-full max-w-7xl mx-auto px-6 py-6 text-center text-xs text-slate-400 z-10"
        >
            <p>
                © {{ new Date().getFullYear() }}
                {{ settings.site_name || "Yanas Fashion" }}. All rights
                reserved.
            </p>
        </footer>
    </div>
</template>
