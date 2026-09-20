@extends(request()->is('admin*') ? 'admin.layouts.master' : 'layouts.app')

@section('title', request()->is('admin*') ? '403 - Permission Denied' : '403 - Access Forbidden | ' . get_setting('site_name', 'Yanas Fashion'))

@section('content')

@if(request()->is('admin*'))
    <!-- ==========================================
         ADMIN PANEL 403 VIEW (Vristo Dashboard Theme)
         ========================================== -->
    <div class="mb-6 flex items-center justify-between">
        <ul class="flex items-center space-x-2 rtl:space-x-reverse text-xs text-gray-500 dark:text-gray-400 font-semibold">
            <li><a href="{{ route('admin.dashboard') }}" class="text-primary hover:underline">Dashboard</a></li>
            <li class="before:content-['/'] ltr:before:mr-2 rtl:before:ml-2"><span>Error 403</span></li>
        </ul>
        <a href="{{ route('admin.dashboard') }}" class="btn inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-xs font-bold text-white shadow-md hover:bg-primary-hover transition-all">
            <i class="fa-solid fa-gauge text-[11px]"></i> Admin Dashboard
        </a>
    </div>

    <div class="panel border-0 shadow-lg rounded-2xl p-10 max-w-2xl mx-auto my-8 text-center bg-white dark:bg-[#1b2e4b]">
        <!-- Watermark -->
        <div class="text-7xl font-black text-danger/10 dark:text-danger/20 mb-2 font-mono select-none">403</div>

        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-danger/10 text-danger dark:bg-danger/20 text-2xl mb-4">
            <i class="fa-solid fa-shield-halved"></i>
        </div>

        <h1 class="text-xl font-extrabold text-gray-800 dark:text-white mb-2">
            Admin Permission Denied
        </h1>
        <p class="text-xs text-gray-500 dark:text-gray-400 mb-8 max-w-md mx-auto leading-relaxed">
            Your administrator account does not possess the permissions required to access or execute actions on this resource.
        </p>

        <!-- Admin Quick Shortcuts -->
        <div class="flex flex-wrap items-center justify-center gap-3">
            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 rounded-lg bg-primary px-5 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-primary-hover transition-all">
                <i class="fa-solid fa-gauge"></i> Return to Dashboard
            </a>
            <a href="{{ route('tyro-login.logout') }}" onclick="event.preventDefault(); document.getElementById('admin-logout-form-err').submit();" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] px-5 py-2.5 text-xs font-bold text-danger hover:bg-danger/10 transition-all">
                <i class="fa-solid fa-arrow-right-from-bracket"></i> Switch Account
            </a>
            <form id="admin-logout-form-err" action="{{ route('tyro-login.logout') }}" method="POST" class="hidden">@csrf</form>
        </div>
    </div>

@else
    <!-- ==========================================
         STOREFRONT PUBLIC 403 VIEW (Luxury Brand)
         ========================================== -->
    <section class="error-page-section">
        <div class="error-page-container">
            <div class="error-card">
                <!-- Large Watermark -->
                <div class="error-watermark-code">403</div>

                <div class="error-content-body">
                    <!-- Floating Icon Badge -->
                    <div class="error-icon-badge badge-danger-glow">
                        <i class="fa-solid fa-lock"></i>
                    </div>

                    <!-- Tagline & Heading -->
                    <div class="error-tagline" style="background: rgba(231, 81, 90, 0.1); color: #e7515a;">
                        <i class="fa-solid fa-shield-halved"></i> Access Restricted
                    </div>
                    <h1 class="error-title">Access Forbidden</h1>
                    <p class="error-subtitle">
                        You do not have permission to view or modify this resource. If you believe this is an error, please log in with the appropriate credentials.
                    </p>

                    <!-- Action Buttons -->
                    <div class="error-actions-group">
                        <a href="{{ route('home') }}" class="btn-error-primary">
                            <i class="fa-solid fa-house"></i> Return to Homepage
                        </a>
                        <a href="{{ route('tyro-login.login') }}" class="btn-error-secondary">
                            <i class="fa-solid fa-arrow-right-to-bracket"></i> Login
                        </a>
                    </div>

                    <!-- Support Footer -->
                    <div class="error-support-footer">
                        Need administrative support? Contact: 
                        <a href="mailto:{{ get_setting('email', 'info@yanasfashion.com') }}">
                            <i class="fa-solid fa-envelope"></i> {{ get_setting('email', 'info@yanasfashion.com') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif

@endsection
