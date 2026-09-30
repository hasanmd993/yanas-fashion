@extends(request()->is('admin*') ? 'admin.layouts.master' : 'layouts.app')

@section('title', request()->is('admin*') ? '401 - Admin Login Required' : '401 - Unauthorized | ' . get_setting('site_name', 'Yanas Fashion'))

@section('content')

@if(request()->is('admin*'))
    <!-- ==========================================
         ADMIN PANEL 401 VIEW (Vristo Dashboard Theme)
         ========================================== -->
    <div class="panel border-0 shadow-lg rounded-2xl p-10 max-w-2xl mx-auto my-12 text-center bg-white dark:bg-[#1b2e4b]">
        <!-- Watermark -->
        <div class="text-7xl font-black text-warning/10 dark:text-warning/20 mb-2 font-mono select-none">401</div>

        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-warning/10 text-warning dark:bg-warning/20 text-2xl mb-4">
            <i class="fa-solid fa-user-lock"></i>
        </div>

        <h1 class="text-xl font-extrabold text-gray-800 dark:text-white mb-2">
            Admin Authentication Required
        </h1>
        <p class="text-xs text-gray-500 dark:text-gray-400 mb-8 max-w-md mx-auto leading-relaxed">
            You must be logged in as an authorized administrator to access the dashboard and store management panels.
        </p>

        <!-- Admin Quick Shortcuts -->
        <div class="flex flex-wrap items-center justify-center gap-3">
            <a href="{{ route('login') }}" class="inline-flex items-center gap-2 rounded-lg bg-primary px-5 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-primary-hover transition-all">
                <i class="fa-solid fa-right-to-bracket"></i> Login to Admin
            </a>
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] px-5 py-2.5 text-xs font-bold text-gray-700 dark:text-gray-300 hover:border-primary hover:text-primary transition-all">
                <i class="fa-solid fa-house"></i> Storefront Home
            </a>
        </div>
    </div>

@else
    <!-- ==========================================
         STOREFRONT PUBLIC 401 VIEW (Luxury Brand)
         ========================================== -->
    <section class="error-page-section">
        <div class="error-page-container">
            <div class="error-card">
                <!-- Large Watermark -->
                <div class="error-watermark-code">401</div>

                <div class="error-content-body">
                    <!-- Floating Icon Badge -->
                    <div class="error-icon-badge badge-warning-glow">
                        <i class="fa-solid fa-user-lock"></i>
                    </div>

                    <!-- Tagline & Heading -->
                    <div class="error-tagline" style="background: rgba(226, 160, 63, 0.12); color: #c4801e;">
                        <i class="fa-solid fa-key"></i> Authentication Required
                    </div>
                    <h1 class="error-title">Please Log In to Access</h1>
                    <p class="error-subtitle">
                        This area requires authorized credentials. Please log into your account to continue.
                    </p>

                    <!-- Action Buttons -->
                    <div class="error-actions-group">
                        <a href="{{ route('login') }}" class="btn-error-primary">
                            <i class="fa-solid fa-arrow-right-to-bracket"></i> Login Now
                        </a>
                        <a href="{{ route('home') }}" class="btn-error-secondary">
                            <i class="fa-solid fa-house"></i> Return to Homepage
                        </a>
                    </div>

                    <!-- Support Footer -->
                    <div class="error-support-footer">
                        Need help logging in? Call us: 
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
