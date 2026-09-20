@extends(request()->is('admin*') ? 'admin.layouts.master' : 'layouts.app')

@section('title', request()->is('admin*') ? '503 - Maintenance Active' : '503 - Under Maintenance | ' . get_setting('site_name', 'Yanas Fashion'))

@section('content')

@if(request()->is('admin*'))
    <!-- ==========================================
         ADMIN PANEL 503 VIEW (Vristo Dashboard Theme)
         ========================================== -->
    <div class="panel border-0 shadow-lg rounded-2xl p-10 max-w-2xl mx-auto my-12 text-center bg-white dark:bg-[#1b2e4b]">
        <!-- Watermark -->
        <div class="text-7xl font-black text-warning/10 dark:text-warning/20 mb-2 font-mono select-none">503</div>

        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-warning/10 text-warning dark:bg-warning/20 text-2xl mb-4">
            <i class="fa-solid fa-screwdriver-wrench"></i>
        </div>

        <h1 class="text-xl font-extrabold text-gray-800 dark:text-white mb-2">
            Store Maintenance Mode is Currently Active
        </h1>
        <p class="text-xs text-gray-500 dark:text-gray-400 mb-6 max-w-md mx-auto leading-relaxed">
            The storefront is temporarily paused for maintenance. To bring the application back online for customers, execute the artisan command below in terminal:
        </p>

        <div class="bg-gray-100 dark:bg-[#0e1726] rounded-xl p-3 font-mono text-xs font-bold text-gray-700 dark:text-gray-300 max-w-xs mx-auto mb-8 border border-gray-200 dark:border-[#192a43]">
            php artisan up
        </div>

        <!-- Admin Quick Shortcuts -->
        <div class="flex flex-wrap items-center justify-center gap-3">
            <button type="button" onclick="window.location.reload();" class="inline-flex items-center gap-2 rounded-lg bg-primary px-5 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-primary-hover transition-all">
                <i class="fa-solid fa-arrows-rotate"></i> Check Status
            </button>
            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] px-5 py-2.5 text-xs font-bold text-gray-700 dark:text-gray-300 hover:border-primary hover:text-primary transition-all">
                <i class="fa-solid fa-gauge"></i> Admin Dashboard
            </a>
            <a href="{{ route('admin.settings.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] px-5 py-2.5 text-xs font-bold text-gray-700 dark:text-gray-300 hover:border-primary hover:text-primary transition-all">
                <i class="fa-solid fa-sliders"></i> Store Settings
            </a>
        </div>
    </div>

@else
    <!-- ==========================================
         STOREFRONT PUBLIC 503 VIEW (Luxury Brand)
         ========================================== -->
    <section class="error-page-section">
        <div class="error-page-container">
            <div class="error-card">
                <!-- Large Watermark -->
                <div class="error-watermark-code">503</div>

                <div class="error-content-body">
                    <!-- Floating Icon Badge -->
                    <div class="error-icon-badge">
                        <i class="fa-solid fa-wand-magic-sparkles"></i>
                    </div>

                    <!-- Tagline & Heading -->
                    <div class="error-tagline">
                        <i class="fa-solid fa-gears"></i> Upgrades in Progress
                    </div>
                    <h1 class="error-title">Enhancing Your Fashion Experience</h1>
                    <p class="error-subtitle">
                        We are currently conducting routine performance upgrades to bring you an even smoother luxury shopping experience. We'll be back online in a few minutes!
                    </p>

                    <!-- Action Buttons -->
                    <div class="error-actions-group">
                        <button type="button" onclick="window.location.reload();" class="btn-error-primary">
                            <i class="fa-solid fa-arrows-rotate"></i> Check Again
                        </button>
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', get_setting('whatsapp_number', '8801713580400')) }}?text={{ urlencode('Hi Yanas Fashion, is the website currently under maintenance?') }}" target="_blank" class="btn-error-whatsapp">
                            <i class="fa-brands fa-whatsapp"></i> WhatsApp Urgent Inquiries
                        </a>
                    </div>

                    <!-- Support Footer -->
                    <div class="error-support-footer">
                        For direct phone orders during maintenance, reach our helpline: 
                        <a href="tel:{{ preg_replace('/[^0-9+]/', '', get_setting('hotline', '01713-580400')) }}">
                            <i class="fa-solid fa-phone"></i> {{ get_setting('hotline', '01713-580400') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif

@endsection
