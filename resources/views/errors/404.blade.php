@extends(request()->is('admin*') ? 'admin.layouts.master' : 'layouts.app')

@section('title', request()->is('admin*') ? '404 - Admin Page Not Found' : '404 - Page Not Found | ' . get_setting('site_name', 'Yanas Fashion'))

@section('content')

@if(request()->is('admin*'))
    <!-- ==========================================
         ADMIN PANEL 404 VIEW (Vristo Dashboard Theme)
         ========================================== -->
    <div class="mb-6 flex items-center justify-between">
        <ul class="flex items-center space-x-2 rtl:space-x-reverse text-xs text-gray-500 dark:text-gray-400 font-semibold">
            <li><a href="{{ route('admin.dashboard') }}" class="text-primary hover:underline">Dashboard</a></li>
            <li class="before:content-['/'] ltr:before:mr-2 rtl:before:ml-2"><span>Error 404</span></li>
        </ul>
        <a href="{{ route('admin.dashboard') }}" class="btn inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-xs font-bold text-white shadow-md hover:bg-primary-hover transition-all">
            <i class="fa-solid fa-house text-[11px]"></i> Admin Dashboard
        </a>
    </div>

    <div class="panel border-0 shadow-lg rounded-2xl p-10 max-w-2xl mx-auto my-8 text-center bg-white dark:bg-[#1b2e4b]">
        <!-- Watermark -->
        <div class="text-7xl font-black text-primary/10 dark:text-primary/20 mb-2 font-mono select-none">404</div>

        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-primary/10 text-primary dark:bg-primary/20 text-2xl mb-4">
            <i class="fa-solid fa-compass-drafting"></i>
        </div>

        <h1 class="text-xl font-extrabold text-gray-800 dark:text-white mb-2">
            Admin Page or Record Not Found
        </h1>
        <p class="text-xs text-gray-500 dark:text-gray-400 mb-8 max-w-md mx-auto leading-relaxed">
            The admin resource, product record, or management section you requested does not exist or may have been deleted.
        </p>

        <!-- Admin Quick Shortcuts -->
        <div class="flex flex-wrap items-center justify-center gap-3">
            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 rounded-lg bg-primary px-5 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-primary-hover transition-all">
                <i class="fa-solid fa-gauge"></i> Dashboard
            </a>
            <a href="{{ route('admin.products.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] px-5 py-2.5 text-xs font-bold text-gray-700 dark:text-gray-300 hover:border-primary hover:text-primary transition-all">
                <i class="fa-solid fa-boxes-stacked"></i> Products List
            </a>
            <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] px-5 py-2.5 text-xs font-bold text-gray-700 dark:text-gray-300 hover:border-primary hover:text-primary transition-all">
                <i class="fa-solid fa-cart-shopping"></i> Orders List
            </a>
            <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] px-5 py-2.5 text-xs font-bold text-gray-700 dark:text-gray-300 hover:border-primary hover:text-primary transition-all">
                <i class="fa-solid fa-layer-group"></i> Categories
            </a>
        </div>
    </div>

@else
    <!-- ==========================================
         STOREFRONT PUBLIC 404 VIEW (Luxury Brand)
         ========================================== -->
    <section class="error-page-section">
        <div class="error-page-container">
            <div class="error-card">
                <!-- Large Watermark -->
                <div class="error-watermark-code">404</div>

                <div class="error-content-body">
                    <!-- Floating Icon Badge -->
                    <div class="error-icon-badge">
                        <i class="fa-solid fa-shirt"></i>
                    </div>

                    <!-- Tagline & Heading -->
                    <div class="error-tagline">
                        <i class="fa-solid fa-compass"></i> Page Not Found
                    </div>
                    <h1 class="error-title">Oops! This Look is Out of Stock or Moved</h1>
                    <p class="error-subtitle">
                        The page or product you are searching for might have been renamed, moved to a new collection, or is temporarily unavailable.
                    </p>

                    <!-- Search Form -->
                    <div class="error-search-wrap">
                        <form action="{{ route('shop.index') }}" method="GET" class="error-search-form">
                            <input type="text" name="search" placeholder="Search Panjabis, Shirts, Cargo Pants..." class="error-search-input" required autocomplete="off">
                            <button type="submit" class="error-search-btn">
                                <i class="fa-solid fa-magnifying-glass"></i> Search
                            </button>
                        </form>
                    </div>

                    <!-- Action Buttons -->
                    <div class="error-actions-group">
                        <a href="{{ route('home') }}" class="btn-error-primary">
                            <i class="fa-solid fa-house"></i> Back to Home
                        </a>
                        <a href="{{ route('shop.index') }}" class="btn-error-secondary">
                            <i class="fa-solid fa-bag-shopping"></i> Explore Collection
                        </a>
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', get_setting('whatsapp_number', '8801713580400')) }}?text={{ urlencode('Hi Yanas Fashion, I need help finding an item.') }}" target="_blank" class="btn-error-whatsapp">
                            <i class="fa-brands fa-whatsapp"></i> Chat on WhatsApp
                        </a>
                    </div>

                    <!-- Support Footer -->
                    <div class="error-support-footer">
                        Need instant order assistance? Call our direct hotline: 
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
