@extends(request()->is('admin*') ? 'admin.layouts.master' : 'layouts.app')

@section('title', request()->is('admin*') ? '500 - Admin System Error' : '500 - Server Error | ' . get_setting('site_name', 'Yanas Fashion'))

@section('content')

@if(request()->is('admin*'))
    <!-- ==========================================
         ADMIN PANEL 500 VIEW (Vristo Dashboard Theme)
         ========================================== -->
    <div class="mb-6 flex items-center justify-between">
        <ul class="flex items-center space-x-2 rtl:space-x-reverse text-xs text-gray-500 dark:text-gray-400 font-semibold">
            <li><a href="{{ route('admin.dashboard') }}" class="text-primary hover:underline">Dashboard</a></li>
            <li class="before:content-['/'] ltr:before:mr-2 rtl:before:ml-2"><span>Error 500</span></li>
        </ul>
        <button type="button" onclick="window.location.reload();" class="btn inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-xs font-bold text-white shadow-md hover:bg-primary-hover transition-all">
            <i class="fa-solid fa-arrows-rotate text-[11px]"></i> Reload Page
        </button>
    </div>

    <div class="panel border-0 shadow-lg rounded-2xl p-10 max-w-2xl mx-auto my-8 text-center bg-white dark:bg-[#1b2e4b]">
        <!-- Watermark -->
        <div class="text-7xl font-black text-danger/10 dark:text-danger/20 mb-2 font-mono select-none">500</div>

        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-danger/10 text-danger dark:bg-danger/20 text-2xl mb-4">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>

        <h1 class="text-xl font-extrabold text-gray-800 dark:text-white mb-2">
            Admin Server or Controller Error
        </h1>
        <p class="text-xs text-gray-500 dark:text-gray-400 mb-8 max-w-md mx-auto leading-relaxed">
            An unexpected error occurred while executing this administrative action. Check your server logs or try refreshing the request.
        </p>

        <!-- Admin Quick Shortcuts -->
        <div class="flex flex-wrap items-center justify-center gap-3">
            <button type="button" onclick="window.location.reload();" class="inline-flex items-center gap-2 rounded-lg bg-danger px-5 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-danger/90 transition-all">
                <i class="fa-solid fa-arrows-rotate"></i> Reload Page
            </button>
            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 rounded-lg bg-primary px-5 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-primary-hover transition-all">
                <i class="fa-solid fa-gauge"></i> Admin Dashboard
            </a>
            <a href="{{ route('admin.products.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] px-5 py-2.5 text-xs font-bold text-gray-700 dark:text-gray-300 hover:border-primary hover:text-primary transition-all">
                <i class="fa-solid fa-boxes-stacked"></i> Products
            </a>
            <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] px-5 py-2.5 text-xs font-bold text-gray-700 dark:text-gray-300 hover:border-primary hover:text-primary transition-all">
                <i class="fa-solid fa-cart-shopping"></i> Orders
            </a>
        </div>
    </div>

@else
    <!-- ==========================================
         STOREFRONT PUBLIC 500 VIEW (Luxury Brand)
         ========================================== -->
    <section class="error-page-section">
        <div class="error-page-container">
            <div class="error-card">
                <!-- Large Watermark -->
                <div class="error-watermark-code">500</div>

                <div class="error-content-body">
                    <!-- Floating Icon Badge -->
                    <div class="error-icon-badge badge-danger-glow">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>

                    <!-- Tagline & Heading -->
                    <div class="error-tagline" style="background: rgba(231, 81, 90, 0.1); color: #e7515a;">
                        <i class="fa-solid fa-server"></i> Server Error
                    </div>
                    <h1 class="error-title">Something Went Wrong On Our End</h1>
                    <p class="error-subtitle">
                        Our technical team has been notified and is working swiftly to resolve this. Please try refreshing the page or check back shortly.
                    </p>

                    <!-- Action Buttons -->
                    <div class="error-actions-group">
                        <button type="button" onclick="window.location.reload();" class="btn-error-primary">
                            <i class="fa-solid fa-arrows-rotate"></i> Reload Page
                        </button>
                        <a href="{{ route('home') }}" class="btn-error-secondary">
                            <i class="fa-solid fa-house"></i> Return to Homepage
                        </a>
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', get_setting('whatsapp_number', '8801713580400')) }}?text={{ urlencode('Hi Yanas Fashion, I encountered a server error on your website.') }}" target="_blank" class="btn-error-whatsapp">
                            <i class="fa-brands fa-whatsapp"></i> Report on WhatsApp
                        </a>
                    </div>

                    <!-- Support Footer -->
                    <div class="error-support-footer">
                        Need instant order assistance? Call our hotline: 
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
