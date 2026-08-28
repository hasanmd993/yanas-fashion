<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', "Yana's Fashion — Bangladeshi Luxury & Contemporary Ethnic Wear")</title>
    
    <!-- Meta & SEO Tags -->
    <meta name="description" content="Shop luxury Dhakai Jamdani sarees, tailored festive panjabis, twel-stitch cargo trousers, and silk kurtis with Cash on Delivery across Bangladesh.">
    <meta name="keywords" content="Yanas Fashion, Wasitex, Luxury Look, Jamdani saree, Men panjabi, Cargo pants BD, Bangladeshi fashion, online dress shop Dhaka">
    
    <!-- FontAwesome 6 Icons CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Custom Design System -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')
</head>
<body>

    <!-- Top Announcement Bar -->
    <div class="announcement-bar">
        <div>
            ✨ <span class="highlight">ঢাকা সিটিতে ফ্রি ডেলিভারি</span> ৳৩,০০০+ অর্ডারে &nbsp;|&nbsp; 🚚 সারা বাংলাদেশে ৬৪ জেলায় হোম ডেলিভারি &nbsp;|&nbsp; 💳 বিকাশ, নগদ ও ক্যাশ অন ডেলিভারি
        </div>
        <div class="announcement-nav">
            <a href="{{ route('tracking.index') }}"><i class="fa-solid fa-location-dot"></i> অর্ডার ট্র্যাক করুন</a>
            <a href="tel:01713580400"><i class="fa-solid fa-phone"></i> হেল্পলাইন: 01713580400</a>
            @auth
                <a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-gauge"></i> অ্যাডমিন ড্যাশবোর্ড</a>
            @else
                <a href="{{ route('tyro-login.login') }}"><i class="fa-solid fa-lock"></i> লগইন</a>
            @endauth
        </div>
    </div>

    <!-- Main Header -->
    <header class="site-header">
        <div class="container">
            <div class="header-inner">
                <!-- Brand Logo -->
                <div class="logo-wrapper">
                    <a href="{{ route('home') }}">
                        <div class="brand-logo">Yana's<span>.</span></div>
                        <span class="brand-subtitle">Dhaka · Luxury Fashion</span>
                    </a>
                </div>

                <!-- Live Search Bar -->
                <div class="search-container">
                    <div class="search-input-wrap">
                        <input type="text" id="global-search-input" placeholder="শার্ট, পাঞ্জাবি, জামদানি শাড়ি, কার্গো প্যান্ট খুঁজুন..." autocomplete="off">
                        <button class="search-btn" aria-label="Search">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                    </div>
                    <div class="search-results-dropdown" id="search-results-dropdown"></div>
                </div>

                <!-- Actions / Hotline -->
                <div class="header-actions">
                    <a href="tel:01713580400" class="hotline-pill">
                        <div class="hotline-icon"><i class="fa-solid fa-phone"></i></div>
                        <div class="hotline-text">
                            <small>সরাসরি কল করুন</small>
                            <strong>01713580400</strong>
                        </div>
                    </a>

                    <a href="{{ route('tracking.index') }}" class="header-icon-btn" title="অর্ডার ট্র্যাকিং">
                        <i class="fa-solid fa-truck-fast"></i>
                    </a>

                    <!-- Trigger Slide-out Cart Drawer -->
                    <button type="button" class="header-icon-btn trigger-cart-drawer" title="শপিং ব্যাগ" aria-label="Shopping Bag">
                        <i class="fa-solid fa-bag-shopping"></i>
                        <span class="badge-count cart-counter-badge">
                            {{ count(session('cart', [])) }}
                        </span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Secondary Categories Nav Bar (Desktop) -->
        <nav class="main-nav">
            <div class="container">
                <ul>
                    <li><a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">হোম (Home)</a></li>
                    <li><a href="{{ route('shop.index') }}" class="{{ request()->routeIs('shop.index') && !request('category') ? 'active' : '' }}">সব কালেকশন (All Shop)</a></li>
                    <li><a href="{{ route('shop.index', ['category' => 'mens-fashion']) }}">মেনস ফ্যাশন</a></li>
                    <li><a href="{{ route('shop.index', ['category' => 'womens-ethnic']) }}">ওমেন্স এথনিক</a></li>
                    <li><a href="{{ route('shop.index', ['category' => 'festive-panjabi']) }}">ফেস্টিভ পাঞ্জাবি</a></li>
                    <li><a href="{{ route('shop.index', ['category' => 'cargo-trousers']) }}">কার্গো ও ট্রাউজার</a></li>
                    <li><a href="{{ route('shop.index', ['category' => 'artisanal-accessories']) }}">অ্যাক্সেসরিজ</a></li>
                    <li><a href="{{ route('tracking.index') }}">অর্ডার ট্র্যাকিং</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <!-- Flash Alerts / Messages -->
    @if(session('success'))
        <div class="container" style="margin-top: 16px;">
            <div style="background: #e8f7ed; border-left: 4px solid var(--success); padding: 12px 18px; border-radius: var(--radius-sm); color: #0a632b; font-weight: 600;">
                <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
            </div>
        </div>
    @endif
    @if(session('error'))
        <div class="container" style="margin-top: 16px;">
            <div style="background: #fdeeed; border-left: 4px solid var(--danger); padding: 12px 18px; border-radius: var(--radius-sm); color: #a51d24; font-weight: 600;">
                <i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}
            </div>
        </div>
    @endif

    <!-- Main Body Content -->
    <main>
        @yield('content')
    </main>

    <!-- Slide-out Cart Drawer -->
    <div class="cart-drawer-overlay" id="cart-drawer-overlay"></div>
    <div class="cart-drawer" id="cart-drawer">
        <div class="drawer-header">
            <h3><i class="fa-solid fa-bag-shopping" style="color:var(--primary); margin-right:8px;"></i> আপনার শপিং ব্যাগ</h3>
            <button type="button" class="drawer-close-btn" id="close-cart-drawer">&times;</button>
        </div>
        <div class="drawer-body" id="drawer-cart-items">
            <!-- Populated via AJAX -->
        </div>
        <div class="drawer-footer">
            <div class="drawer-subtotal">
                <span>মোট (Subtotal):</span>
                <span id="drawer-subtotal-amount" style="color:var(--primary);">৳0</span>
            </div>
            <a href="{{ route('checkout.index') }}" class="btn btn-primary btn-block">
                অর্ডার সম্পন্ন করুন (Proceed to Checkout) <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>
    </div>

    <!-- Floating Sticky Cart Pill -->
    <div class="floating-cart-pill trigger-cart-drawer">
        <i class="fa-solid fa-bag-shopping" style="font-size:1.3rem;"></i>
        <span class="cart-counter-badge" style="font-size:0.85rem; font-weight:800;">{{ count(session('cart', [])) }}</span>
        <span id="floating-cart-total" style="font-size:0.75rem; font-weight:700;">কার্ট</span>
    </div>

    <!-- Floating WhatsApp Action -->
    <a href="https://wa.me/8801713580400?text=Hello%20Yana's%20Fashion,%20I%20need%20help%20with%20an%20order." target="_blank" class="floating-whatsapp" title="WhatsApp Chat">
        <i class="fa-brands fa-whatsapp"></i>
    </a>

    <!-- Mobile Bottom Sticky Navigation -->
    <nav class="mobile-bottom-bar">
        <a href="{{ route('home') }}" class="mobile-nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
            <i class="fa-solid fa-house"></i>
            <span>হোম</span>
        </a>
        <a href="{{ route('shop.index') }}" class="mobile-nav-item {{ request()->routeIs('shop.index') ? 'active' : '' }}">
            <i class="fa-solid fa-border-all"></i>
            <span>কালেকশন</span>
        </a>
        <button type="button" class="mobile-nav-item trigger-cart-drawer" style="background:none; border:none; cursor:pointer;">
            <i class="fa-solid fa-bag-shopping"></i>
            <span class="badge-count cart-counter-badge">{{ count(session('cart', [])) }}</span>
            <span>কার্ট</span>
        </button>
        <a href="{{ route('tracking.index') }}" class="mobile-nav-item {{ request()->routeIs('tracking.index') ? 'active' : '' }}">
            <i class="fa-solid fa-truck-fast"></i>
            <span>ট্র্যাকিং</span>
        </a>
        <a href="tel:01713580400" class="mobile-nav-item">
            <i class="fa-solid fa-phone"></i>
            <span>কল করুন</span>
        </a>
    </nav>

    <!-- Footer -->
    <footer class="site-footer">
        <div class="container">
            <div class="footer-grid">
                <!-- Brand Info -->
                <div class="footer-brand">
                    <div class="brand-logo" style="color:#ffffff;">Yana's<span>.</span></div>
                    <p>ঐতিহ্যবাহী ঢাকাই জামদানি, রাজমহলী সিল্ক ও আধুনিক আভিজাত্যের সমন্বয়ে তৈরি প্রিমিয়াম ফ্যাশন আউটফিট। সারা বাংলাদেশে ক্যাশ অন ডেলিভারি সহ দ্রুততম ডেলিভারি সেবা।</p>
                    <div style="display:flex; gap:12px; font-size:1.2rem;">
                        <a href="https://facebook.com" target="_blank" style="color:#fff;"><i class="fa-brands fa-facebook"></i></a>
                        <a href="https://instagram.com" target="_blank" style="color:#fff;"><i class="fa-brands fa-instagram"></i></a>
                        <a href="https://tiktok.com" target="_blank" style="color:#fff;"><i class="fa-brands fa-tiktok"></i></a>
                        <a href="https://wa.me/8801713580400" target="_blank" style="color:#25D366;"><i class="fa-brands fa-whatsapp"></i></a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div>
                    <h4 class="footer-title">কুইক মেনু</h4>
                    <ul class="footer-links">
                        <li><a href="{{ route('home') }}">হোম পেজ</a></li>
                        <li><a href="{{ route('shop.index') }}">সব কালেকশন</a></li>
                        <li><a href="{{ route('shop.index', ['category' => 'mens-fashion']) }}">মেনস ফ্যাশন</a></li>
                        <li><a href="{{ route('shop.index', ['category' => 'womens-ethnic']) }}">ওমেন্স এথনিক</a></li>
                        <li><a href="{{ route('tracking.index') }}">অর্ডার ট্র্যাকিং</a></li>
                    </ul>
                </div>

                <!-- Customer Care -->
                <div>
                    <h4 class="footer-title">কাস্টমার সার্ভিস</h4>
                    <ul class="footer-links">
                        <li><a href="{{ route('tracking.index') }}">অর্ডার ট্র্যাক করুন</a></li>
                        <li><a href="{{ route('checkout.index') }}">চেকআউট</a></li>
                        <li><a href="tel:01713580400">হটলাইন সাপোর্ট</a></li>
                        <li><a href="https://wa.me/8801713580400">হোয়াটসঅ্যাপ অর্ডার</a></li>
                        <li><a href="{{ route('admin.dashboard') }}">অ্যাডমিন পোর্টাল</a></li>
                    </ul>
                </div>

                <!-- Contact & Payments -->
                <div>
                    <h4 class="footer-title">যোগাযোগ ও পেমেন্ট</h4>
                    <p style="font-size:0.88rem; color:#b7ab9c; margin-bottom:8px;">
                        <i class="fa-solid fa-location-dot" style="color:var(--accent); margin-right:6px;"></i> বাড়ি ৪২, রোড ১১, বনানী, ঢাকা-১২১৩
                    </p>
                    <p style="font-size:0.88rem; color:#b7ab9c; margin-bottom:8px;">
                        <i class="fa-solid fa-phone" style="color:var(--accent); margin-right:6px;"></i> ০১৭১৩-৫৮০৪০০
                    </p>
                    <p style="font-size:0.88rem; color:#b7ab9c;">
                        <i class="fa-solid fa-envelope" style="color:var(--accent); margin-right:6px;"></i> support@yanasfashion.com
                    </p>

                    <div class="payment-badges-row">
                        <div class="pay-badge" style="background:#e2136e;">bKash</div>
                        <div class="pay-badge" style="background:#f7941d;">Nagad</div>
                        <div class="pay-badge">Rocket</div>
                        <div class="pay-badge">Visa</div>
                        <div class="pay-badge">Mastercard</div>
                        <div class="pay-badge" style="background:var(--primary);">Cash On Delivery (COD)</div>
                    </div>
                </div>
            </div>

            <div class="footer-bottom">
                <p>© 2026 Yana's Fashion. All Rights Reserved. Crafted with passion in Bangladesh.</p>
            </div>
        </div>
    </footer>

    <!-- Client Script -->
    <script src="{{ asset('js/app.js') }}"></script>
    @stack('scripts')
</body>
</html>
