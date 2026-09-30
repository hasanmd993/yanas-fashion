import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp, Head, Link } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createPinia } from 'pinia';
import { ZiggyVue, route } from 'ziggy-js';
import { Ziggy } from './ziggy';

import ErrorBoundary from './Components/ErrorBoundary.vue';

// Expose route helper globally to window for setup scripts and templates
window.Ziggy = Ziggy;
window.route = (name, params, absolute, config = Ziggy) => route(name, params, absolute, config);

const appName = import.meta.env.VITE_APP_NAME || 'Yanas Fashion';

createInertiaApp({
    title: (title) => title ? `${title} — ${appName}` : `${appName} — Contemporary & Luxury Fashion Dhaka`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const pinia = createPinia();
        const vueApp = createApp({
            render: () => h(ErrorBoundary, null, {
                default: () => h(App, props)
            })
        });

        // Global Vue error handler
        vueApp.config.errorHandler = (err, instance, info) => {
            console.error('[Vue Global Error Handler]:', err, info);
        };

        return vueApp
            .use(plugin)
            .use(pinia)
            .use(ZiggyVue, Ziggy)
            .component('Head', Head)
            .component('Link', Link)
            .component('ErrorBoundary', ErrorBoundary)
            .mount(el);
    },
    progress: {
        color: '#be185d',
        showSpinner: true,
    },
});
