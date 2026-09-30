@extends(request()->is('admin*') ? 'admin.layouts.master' : 'layouts.app')

@section('title', request()->is('admin*') ? '419 - Admin Session Expired' : '419 - Session Expired | ' . get_setting('site_name', 'Yanas Fashion'))

@section('content')

@if(request()->is('admin*'))
    <!-- ==========================================
         ADMIN PANEL 419 VIEW (Vristo Dashboard Theme)
         ========================================== -->
    <div class="mb-6 flex items-center justify-between">
        <ul class="flex items-center space-x-2 rtl:space-x-reverse text-xs text-gray-500 dark:text-gray-400 font-semibold">
            <li><a href="{{ route('admin.dashboard') }}" class="text-primary hover:underline">Dashboard</a></li>
            <li class="before:content-['/'] ltr:before:mr-2 rtl:before:ml-2"><span>Error 419</span></li>
        </ul>
        <a href="{{ route('login') }}" class="btn inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-xs font-bold text-white shadow-md hover:bg-primary-hover transition-all">
            <i class="fa-solid fa-lock text-[11px]"></i> Log In
        </a>
    </div>

    <div class="panel border-0 shadow-lg rounded-2xl p-10 max-w-2xl mx-auto my-8 text-center bg-white dark:bg-[#1b2e4b]">
        <!-- Watermark -->
        <div class="text-7xl font-black text-warning/10 dark:text-warning/20 mb-2 font-mono select-none">419</div>

        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-warning/10 text-warning dark:bg-warning/20 text-2xl mb-4">
            <i class="fa-solid fa-clock-rotate-left"></i>
        </div>

        <h1 class="text-xl font-extrabold text-gray-800 dark:text-white mb-2">
            Admin Session or Security Token Expired
        </h1>
        <p class="text-xs text-gray-500 dark:text-gray-400 mb-8 max-w-md mx-auto leading-relaxed">
            Your administrative CSRF token has timed out due to a period of inactivity. Please reload the page or log in again to continue your session.
        </p>

        <!-- Admin Quick Shortcuts -->
        <div class="flex flex-wrap items-center justify-center gap-3">
            <button type="button" onclick="window.location.reload();" class="inline-flex items-center gap-2 rounded-lg bg-primary px-5 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-primary-hover transition-all">
                <i class="fa-solid fa-arrows-rotate"></i> Refresh Page
            </button>
            <a href="{{ route('login') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] px-5 py-2.5 text-xs font-bold text-gray-700 dark:text-gray-300 hover:border-primary hover:text-primary transition-all">
                <i class="fa-solid fa-right-to-bracket"></i> Log In Again
            </a>
            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] px-5 py-2.5 text-xs font-bold text-gray-700 dark:text-gray-300 hover:border-primary hover:text-primary transition-all">
                <i class="fa-solid fa-gauge"></i> Dashboard
            </a>
        </div>
    </div>

@else
    <!-- ==========================================
         STOREFRONT PUBLIC 419 VIEW (Luxury Brand)
         ========================================== -->
    <section class="error-page-section">
        <div class="error-page-container">
            <div class="error-card">
                <!-- Large Watermark -->
                <div class="error-watermark-code">419</div>

                <div class="error-content-body">
                    <!-- Floating Icon Badge -->
                    <div class="error-icon-badge badge-warning-glow">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                    </div>

                    <!-- Tagline & Heading -->
                    <div class="error-tagline" style="background: rgba(226, 160, 63, 0.12); color: #c4801e;">
                        <i class="fa-solid fa-hourglass-end"></i> Page Expired
                    </div>
                    <h1 class="error-title">Your Security Session Expired</h1>
                    <p class="error-subtitle">
                        For your security, checkout and form sessions automatically expire after a period of inactivity. Please refresh the page to continue seamlessly.
                    </p>

                    <!-- Action Buttons -->
                    <div class="error-actions-group">
                        <button type="button" onclick="window.location.reload();" class="btn-error-primary">
                            <i class="fa-solid fa-arrows-rotate"></i> Refresh Page Now
                        </button>
                        <a href="{{ route('shop.index') }}" class="btn-error-secondary">
                            <i class="fa-solid fa-bag-shopping"></i> Continue Shopping
                        </a>
                        <a href="{{ route('home') }}" class="btn-error-secondary">
                            <i class="fa-solid fa-house"></i> Home
                        </a>
                    </div>

                    <!-- Support Footer -->
                    <div class="error-support-footer">
                        Having trouble with an order? Call us: 
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
