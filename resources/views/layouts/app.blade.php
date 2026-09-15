<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', "Yanas Fashion — Contemporary & Luxury Fashion Dhaka")</title>

    <!-- Meta & SEO Tags -->
    <meta name="description"
        content="Shop luxury festive wear, tailored panjabis, twel-stitch cargo trousers, and contemporary outfits with Cash on Delivery across Bangladesh.">
    <meta name="keywords"
        content="Yanas Fashion, Wasitex, Luxury Fashion, Men panjabi, Cargo pants BD, Bangladeshi fashion, online shop Dhaka">

    <!-- FontAwesome 6 Icons CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Dynamic Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ get_favicon_url() }}">

    <!-- Custom Design System -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')
</head>

<body>

    <!-- Top Announcement Bar -->
    <div class="announcement-bar">
        <div class="announcement-promo">
            ✨ <span class="highlight">Free Delivery in Dhaka City</span> on orders over ৳2,500 &nbsp;|&nbsp; 🚚 Express
            Delivery across 64 Districts
        </div>
        <div class="announcement-nav">
            <a href="{{ route('tracking.index') }}"><i class="fa-solid fa-location-dot"></i> Order Tracking</a>
            <a href="tel:01713580400"><i class="fa-solid fa-phone"></i> Helpline: 01713-580400</a>
            @auth
                <a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-gauge"></i> Admin Dashboard</a>
            @else
                <a href="{{ route('tyro-login.login') }}"><i class="fa-solid fa-lock"></i> Login</a>
            @endauth
        </div>
    </div>

    <!-- Main Header -->
    <header class="site-header">
        <div class="container">
            <div class="header-inner">
                <!-- Left Header Group (Mobile Hamburger & Brand Logo) -->
                <div class="header-left-group">
                    <button type="button" class="mobile-menu-toggle-btn" id="mobileMenuToggle"
                        aria-label="Open Navigation Menu" title="Open Menu">
                        <i class="fa-solid fa-bars-staggered"></i>
                    </button>
                    <div class="logo-wrapper">
                        <a href="{{ route('home') }}"
                            style="display:inline-flex; align-items:center; gap:10px; text-decoration:none;">
                            <img src="{{ get_logo_url() }}" alt="{{ get_setting('site_name', 'Yanas Fashion') }}"
                                style="height:38px; width:auto; max-width:44px; object-fit:contain; border-radius:6px;">
                            <div class="brand-logo">{{ get_setting('site_name', 'Yanas Fashion') }}</div>
                        </a>
                    </div>
                </div>

                <!-- Live Search Bar (Desktop) -->
                <div class="search-container">
                    <form action="{{ route('shop.index') }}" method="GET" class="desktop-search-form">
                        <div class="search-input-wrap">
                            <input type="text" name="q" id="global-search-input" class="live-search-input"
                                placeholder="Search shirts, panjabi, trousers, jackets..."
                                value="{{ request('q') }}" autocomplete="off">
                            <button type="submit" class="search-btn" aria-label="Search">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </button>
                        </div>
                        <div class="search-results-dropdown" id="search-results-dropdown"></div>
                    </form>
                </div>

                <!-- Actions / Hotline -->
                <div class="header-actions">
                    <a href="tel:01713580400" class="hotline-pill">
                        <div class="hotline-icon"><i class="fa-solid fa-phone"></i></div>
                        <div class="hotline-text">
                            <small>Call Us Directly</small>
                            <strong>01713580400</strong>
                        </div>
                    </a>

                    <a href="{{ route('tracking.index') }}" class="header-icon-btn" title="Order Tracking">
                        <i class="fa-solid fa-truck-fast"></i>
                    </a>

                    <!-- Trigger Slide-out Cart Drawer -->
                    <button type="button" class="header-icon-btn trigger-cart-drawer" title="Shopping Bag"
                        aria-label="Shopping Bag">
                        <i class="fa-solid fa-bag-shopping"></i>
                        <span class="badge-count cart-counter-badge">
                            {{ count(session('cart', [])) }}
                        </span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Search Bar (Displayed on Mobile instead of Category Nav) -->
        <div class="mobile-search-bar-wrap">
            <div class="container">
                <form action="{{ route('shop.index') }}" method="GET" class="mobile-search-form">
                    <div class="search-input-wrap">
                        <input type="text" name="q" id="mobile-search-input" class="live-search-input"
                            placeholder="Search shirts, panjabi, trousers, jackets..." value="{{ request('q') }}"
                            autocomplete="off">
                        <button type="submit" class="search-btn" aria-label="Search">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                    </div>
                    <div class="search-results-dropdown" id="mobile-search-results-dropdown"></div>
                </form>
            </div>
        </div>

        <!-- Secondary Categories Nav Bar (Dynamic Mega-Menu on Desktop) -->
        <nav class="main-nav">
            <div class="container main-nav-inner">
                <ul class="main-nav-list">
                    <li>
                        <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">
                            <i class="fa-solid fa-house"></i>
                            <span>Home</span>
                        </a>
                    </li>

                    @php
                        $navParents = get_nav_categories();
                        $currentCatSlug = request('category');
                    @endphp

                    @foreach($navParents as $parent)
                        @php
                            $subSlugs = $parent->activeChildren->pluck('slug')->toArray();
                            $childSlugs = $parent->activeChildren->flatMap->activeChildren->pluck('slug')->toArray();
                            $allFamilySlugs = array_merge([$parent->slug], $subSlugs, $childSlugs);
                            $isFamilyActive = in_array($currentCatSlug, $allFamilySlugs);
                            $hasSubcategories = $parent->activeChildren->count() > 0;
                        @endphp
                        <li class="nav-item-dropdown {{ $hasSubcategories ? 'has-dropdown' : '' }}">
                            <a href="{{ route('shop.index', ['category' => $parent->slug]) }}"
                                class="{{ $isFamilyActive ? 'active' : '' }}">
                                @if($parent->icon)
                                    <i class="{{ $parent->icon }}"></i>
                                @endif
                                <span>{{ $parent->name }}</span>
                                @if($hasSubcategories)
                                    <i class="fa-solid fa-chevron-down nav-dropdown-arrow"></i>
                                @endif
                            </a>

                            @if($hasSubcategories)
                                <div class="nav-mega-dropdown">
                                    <div class="nav-mega-grid">
                                        @foreach($parent->activeChildren as $sub)
                                            <div class="nav-mega-col">
                                                <a href="{{ route('shop.index', ['category' => $sub->slug]) }}"
                                                    class="nav-mega-heading {{ $currentCatSlug === $sub->slug ? 'active' : '' }}">
                                                    @if($sub->icon)
                                                        <i class="{{ $sub->icon }}"></i>
                                                    @endif
                                                    <span>{{ $sub->name }}</span>
                                                </a>

                                                @if($sub->activeChildren->count() > 0)
                                                    <ul class="nav-mega-sublist">
                                                        @foreach($sub->activeChildren as $child)
                                                            <li>
                                                                <a href="{{ route('shop.index', ['category' => $child->slug]) }}"
                                                                    class="{{ $currentCatSlug === $child->slug ? 'active' : '' }}">
                                                                    {{ $child->name }}
                                                                </a>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="nav-mega-footer">
                                        <a href="{{ route('shop.index', ['category' => $parent->slug]) }}">
                                            <span>Explore All {{ $parent->name }}</span>
                                            <i class="fa-solid fa-arrow-right"></i>
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </li>
                    @endforeach

                    <li>
                        <a href="{{ route('tracking.index') }}"
                            class="{{ request()->routeIs('tracking.index') ? 'active' : '' }}">
                            <i class="fa-solid fa-truck-fast"></i>
                            <span>Order Tracking</span>
                        </a>
                    </li>
                </ul>


            </div>
        </nav>
    </header>

    <!-- Flash Alerts / Messages -->
    @if(session('success'))
        <div class="container" style="margin-top: 16px;">
            <div
                style="background: #e8f7ed; border-left: 4px solid var(--success); padding: 12px 18px; border-radius: var(--radius-sm); color: #0a632b; font-weight: 600;">
                <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
            </div>
        </div>
    @endif
    @if(session('error'))
        <div class="container" style="margin-top: 16px;">
            <div
                style="background: #fdeeed; border-left: 4px solid var(--danger); padding: 12px 18px; border-radius: var(--radius-sm); color: #a51d24; font-weight: 600;">
                <i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}
            </div>
        </div>
    @endif

    <!-- Main Body Content -->
    <main>
        @yield('content')
    </main>

    <!-- Slide-out Mobile Navigation Drawer -->
    <div class="mobile-nav-overlay" id="mobileNavOverlay"></div>
    <div class="mobile-nav-drawer" id="mobileNavDrawer" aria-label="Mobile Navigation Menu">
        <!-- Drawer Header -->
        <div class="mobile-nav-header">
            <a href="{{ route('home') }}" class="mobile-nav-brand">
                <img src="{{ get_logo_url() }}" alt="{{ get_setting('site_name', 'Yanas Fashion') }}">
                <span>{{ get_setting('site_name', 'Yanas Fashion') }}</span>
            </a>
            <button type="button" class="mobile-nav-close-btn" id="closeMobileNav"
                aria-label="Close Navigation">&times;</button>
        </div>

        <!-- Quick Live Search Wrap inside Drawer -->
        <div class="mobile-nav-search-wrap">
            <form action="{{ route('shop.index') }}" method="GET" class="mobile-nav-search-form">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" name="q" placeholder="Search luxury panjabi, shirts..." value="{{ request('q') }}" autocomplete="off">
                <button type="submit">Search</button>
            </form>
        </div>

        <!-- Scrollable Category & Navigation Body -->
        <div class="mobile-nav-body">
            <div class="mobile-nav-section-title">Collections & Categories</div>
            <ul class="mobile-nav-menu">
                <!-- Home -->
                <li class="mobile-nav-li">
                    <a href="{{ route('home') }}"
                        class="mobile-nav-link {{ request()->routeIs('home') ? 'active' : '' }}">
                        <span class="nav-icon-box"><i class="fa-solid fa-house"></i></span>
                        <span class="nav-text">Home</span>
                    </a>
                </li>

                <!-- Dynamic 3-Tier Category Accordions -->
                @foreach(get_nav_categories() as $parent)
                    @php
                        $hasSubs = $parent->activeChildren->count() > 0;
                        $subSlugs = $parent->activeChildren->pluck('slug')->toArray();
                        $childSlugs = $parent->activeChildren->flatMap->activeChildren->pluck('slug')->toArray();
                        $allFamilySlugs = array_merge([$parent->slug], $subSlugs, $childSlugs);
                        $isFamilyActive = in_array(request('category'), $allFamilySlugs);
                    @endphp
                    <li class="mobile-nav-li {{ $hasSubs ? 'has-accordion' : '' }} {{ $isFamilyActive ? 'open' : '' }}">
                        <div class="mobile-nav-row">
                            <a href="{{ route('shop.index', ['category' => $parent->slug]) }}"
                                class="mobile-nav-link {{ $isFamilyActive ? 'active' : '' }}">
                                <span class="nav-icon-box">
                                    <i class="{{ $parent->icon ?: 'fa-solid fa-folder' }}"></i>
                                </span>
                                <span class="nav-text">{{ $parent->name }}</span>
                            </a>
                            @if($hasSubs)
                                <button type="button" class="mobile-accordion-btn"
                                    aria-label="Toggle {{ $parent->name }} subcategories">
                                    <i class="fa-solid fa-chevron-down"></i>
                                </button>
                            @endif
                        </div>

                        @if($hasSubs)
                            <div class="mobile-accordion-content" style="{{ $isFamilyActive ? 'display: block;' : '' }}">
                                <a href="{{ route('shop.index', ['category' => $parent->slug]) }}" class="mobile-sub-explore">
                                    <span>Explore All {{ $parent->name }}</span>
                                    <i class="fa-solid fa-arrow-right"></i>
                                </a>

                                <ul class="mobile-sub-list">
                                    @foreach($parent->activeChildren as $sub)
                                        @php
                                            $hasChildren = $sub->activeChildren->count() > 0;
                                            $subChildSlugs = $sub->activeChildren->pluck('slug')->toArray();
                                            $isSubActive = request('category') === $sub->slug || in_array(request('category'), $subChildSlugs);
                                        @endphp
                                        <li
                                            class="mobile-sub-li {{ $hasChildren ? 'has-child-accordion' : '' }} {{ $isSubActive ? 'open' : '' }}">
                                            <div class="mobile-sub-row">
                                                <a href="{{ route('shop.index', ['category' => $sub->slug]) }}"
                                                    class="mobile-sub-link {{ request('category') === $sub->slug ? 'active' : '' }}">
                                                    @if($sub->icon)
                                                        <i class="{{ $sub->icon }}"></i>
                                                    @endif
                                                    <span>{{ $sub->name }}</span>
                                                </a>
                                                @if($hasChildren)
                                                    <button type="button" class="mobile-child-accordion-btn"
                                                        aria-label="Toggle {{ $sub->name }} child categories">
                                                        <i class="fa-solid fa-plus"></i>
                                                    </button>
                                                @endif
                                            </div>

                                            @if($hasChildren)
                                                <ul class="mobile-child-list" style="{{ $isSubActive ? 'display: block;' : '' }}">
                                                    @foreach($sub->activeChildren as $child)
                                                        <li>
                                                            <a href="{{ route('shop.index', ['category' => $child->slug]) }}"
                                                                class="mobile-child-link {{ request('category') === $child->slug ? 'active' : '' }}">
                                                                <i class="fa-solid fa-angle-right"></i>
                                                                <span>{{ $child->name }}</span>
                                                            </a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </li>
                @endforeach

                <!-- Order Tracking -->
                <li class="mobile-nav-li">
                    <a href="{{ route('tracking.index') }}"
                        class="mobile-nav-link {{ request()->routeIs('tracking.index') ? 'active' : '' }}">
                        <span class="nav-icon-box"><i class="fa-solid fa-truck-fast"></i></span>
                        <span class="nav-text">Order Tracking</span>
                    </a>
                </li>
            </ul>

            <!-- Direct Hotline & Support -->
            <div class="mobile-nav-section-title" style="margin-top: 24px;">Support & Hotline</div>
            <div class="mobile-nav-support-box">
                <a href="tel:01713580400" class="mobile-support-btn phone">
                    <i class="fa-solid fa-phone"></i>
                    <div>
                        <small>Customer Hotline</small>
                        <strong>01713-580400</strong>
                    </div>
                </a>
                <a href="https://wa.me/8801713580400?text={{ urlencode('Hello Yana\'s Fashion, I would like to inquire about an order.') }}"
                    target="_blank" class="mobile-support-btn whatsapp">
                    <i class="fa-brands fa-whatsapp"></i>
                    <div>
                        <small>WhatsApp Orders</small>
                        <strong>Chat Directly</strong>
                    </div>
                </a>
            </div>
        </div>

        <!-- Drawer Footer -->
        <div class="mobile-nav-footer">
            <div class="mobile-nav-socials">
                <a href="https://facebook.com" target="_blank" aria-label="Facebook"><i
                        class="fa-brands fa-facebook"></i></a>
                <a href="https://instagram.com" target="_blank" aria-label="Instagram"><i
                        class="fa-brands fa-instagram"></i></a>
                <a href="https://tiktok.com" target="_blank" aria-label="TikTok"><i class="fa-brands fa-tiktok"></i></a>
                <a href="https://wa.me/8801713580400" target="_blank" aria-label="WhatsApp"><i
                        class="fa-brands fa-whatsapp"></i></a>
            </div>
            <p class="mobile-nav-copyright">© 2026 Yanas Fashion. Dhaka, Bangladesh.</p>
        </div>
    </div>

    <!-- Slide-out Cart Drawer -->
    <div class="cart-drawer-overlay" id="cart-drawer-overlay"></div>
    <div class="cart-drawer" id="cart-drawer">
        <div class="drawer-header">
            <h3><i class="fa-solid fa-bag-shopping" style="color:var(--primary); margin-right:8px;"></i> Your Shopping
                Bag</h3>
            <button type="button" class="drawer-close-btn" id="close-cart-drawer">&times;</button>
        </div>
        <div class="drawer-body" id="drawer-cart-items">
            <!-- Populated via AJAX -->
        </div>
        <div class="drawer-footer">
            <div class="drawer-subtotal">
                <span>Subtotal:</span>
                <span id="drawer-subtotal-amount" style="color:var(--primary);">৳0</span>
            </div>
            <a href="{{ route('checkout.index') }}" class="btn btn-primary btn-block">
                Proceed to Checkout <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>
    </div>

    <!-- Floating Sticky Cart Pill -->
    <div class="floating-cart-pill trigger-cart-drawer">
        <i class="fa-solid fa-bag-shopping" style="font-size:1.3rem;"></i>
        <span class="cart-counter-badge"
            style="font-size:0.85rem; font-weight:800;">{{ count(session('cart', [])) }}</span>
        <span id="floating-cart-total" style="font-size:0.75rem; font-weight:700;">Cart</span>
    </div>

    <!-- Expandable Floating 3-in-1 Communication Widget -->
    <div class="floating-communication-widget" id="floatingCommunicationWidget">
        <div class="communication-channels">
            <a href="https://wa.me/8801713580400?text={{ urlencode('Hello Yana\'s Fashion, I would like to inquire about an order.') }}"
                target="_blank" class="channel-btn whatsapp-channel" title="WhatsApp Chat">
                <i class="fa-brands fa-whatsapp"></i>
                <span class="channel-tooltip">Chat on WhatsApp</span>
            </a>
            <a href="tel:01713580400" class="channel-btn phone-channel" title="Call Us">
                <i class="fa-solid fa-phone"></i>
                <span class="channel-tooltip">Call Us Directly</span>
            </a>
            <a href="https://m.me/yanasfashionbd" target="_blank" class="channel-btn messenger-channel"
                title="Facebook Messenger">
                <i class="fa-brands fa-facebook-messenger"></i>
                <span class="channel-tooltip">Message on Messenger</span>
            </a>
        </div>
        <button type="button" class="widget-toggle-btn" id="widgetToggleBtn" aria-label="Customer Support">
            <i class="fa-solid fa-comments icon-open"></i>
            <i class="fa-solid fa-xmark icon-close"></i>
        </button>
    </div>

    <!-- Mobile Bottom Sticky Navigation -->
    <nav class="mobile-bottom-bar">
        <a href="{{ route('home') }}" class="mobile-nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
            <i class="fa-solid fa-house"></i>
            <span>Home</span>
        </a>
        <button type="button" class="mobile-nav-item" id="mobileBottomMenuToggle"
            style="background:none; border:none; cursor:pointer;">
            <i class="fa-solid fa-bars-staggered"></i>
            <span>Menu</span>
        </button>
        <a href="{{ route('shop.index') }}"
            class="mobile-nav-item {{ request()->routeIs('shop.index') && !request('category') ? 'active' : '' }}">
            <i class="fa-solid fa-border-all"></i>
            <span>Shop</span>
        </a>
        <button type="button" class="mobile-nav-item trigger-cart-drawer"
            style="background:none; border:none; cursor:pointer;">
            <i class="fa-solid fa-bag-shopping"></i>
            <span class="badge-count cart-counter-badge">{{ count(session('cart', [])) }}</span>
            <span>Cart</span>
        </button>
        <a href="{{ route('tracking.index') }}"
            class="mobile-nav-item {{ request()->routeIs('tracking.index') ? 'active' : '' }}">
            <i class="fa-solid fa-truck-fast"></i>
            <span>Tracking</span>
        </a>
    </nav>

    <!-- Footer -->
    <footer class="site-footer">
        <div class="container">
            <div class="footer-grid">
                <!-- Brand Info -->
                <div class="footer-brand">
                    <div style="display:flex; align-items:center; gap:10px; margin-bottom:12px;">
                        <img src="{{ get_logo_url() }}" alt="{{ get_setting('site_name', 'Yanas Fashion') }}"
                            style="height:36px; width:auto; max-width:40px; object-fit:contain; border-radius:6px;">
                        <div class="brand-logo" style="color:#ffffff;">{{ get_setting('site_name', 'Yanas Fashion') }}
                        </div>
                    </div>
                    <p>Premium contemporary fashion crafted with fine tailoring, luxury fabrics, and modern aesthetics.
                        Fast express delivery with Cash on Delivery nationwide across Bangladesh.</p>
                    <div style="display:flex; gap:12px; font-size:1.2rem;">
                        <a href="https://facebook.com" target="_blank" style="color:#fff;"><i
                                class="fa-brands fa-facebook"></i></a>
                        <a href="https://instagram.com" target="_blank" style="color:#fff;"><i
                                class="fa-brands fa-instagram"></i></a>
                        <a href="https://tiktok.com" target="_blank" style="color:#fff;"><i
                                class="fa-brands fa-tiktok"></i></a>
                        <a href="https://wa.me/8801713580400" target="_blank" style="color:#25D366;"><i
                                class="fa-brands fa-whatsapp"></i></a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div>
                    <h4 class="footer-title">Collections</h4>
                    <ul class="footer-links">
                        <li><a href="{{ route('home') }}">Home</a></li>
                        <li><a href="{{ route('shop.index') }}">Shop All</a></li>
                        @foreach(get_nav_categories() as $pCat)
                            <li><a href="{{ route('shop.index', ['category' => $pCat->slug]) }}">{{ $pCat->name }}</a></li>
                        @endforeach
                        <li><a href="{{ route('tracking.index') }}">Order Tracking</a></li>
                    </ul>
                </div>

                <!-- Customer Care -->
                <div>
                    <h4 class="footer-title">Customer Care</h4>
                    <ul class="footer-links">
                        <li><a href="{{ route('tracking.index') }}">Track Your Order</a></li>
                        <li><a href="{{ route('checkout.index') }}">Checkout</a></li>
                        <li><a href="tel:01713580400">Hotline Support</a></li>
                        <li><a href="https://wa.me/8801713580400">WhatsApp Order</a></li>
                        <!-- <li><a href="{{ route('admin.dashboard') }}">Admin Portal</a></li> -->
                    </ul>
                </div>

                <!-- Contact & Payments -->
                <div>
                    <h4 class="footer-title">Contact & Support</h4>
                    <p style="font-size:0.88rem; color:#b7ab9c; margin-bottom:8px;">
                        <i class="fa-solid fa-location-dot" style="color:var(--accent); margin-right:6px;"></i> House
                        42, Road 11, Banani, Dhaka-1213
                    </p>
                    <p style="font-size:0.88rem; color:#b7ab9c; margin-bottom:8px;">
                        <i class="fa-solid fa-phone" style="color:var(--accent); margin-right:6px;"></i> +880
                        1713-580400
                    </p>
                    <p style="font-size:0.88rem; color:#b7ab9c;">
                        <i class="fa-solid fa-envelope" style="color:var(--accent); margin-right:6px;"></i>
                        support@yanasfashion.com
                    </p>
                </div>
            </div>

            <div class="footer-bottom">
                <p>© 2026 Yanas Fashion. All Rights Reserved. Crafted with passion in Bangladesh.</p>
            </div>
        </div>
    </footer>

    <!-- Client Script -->
    <script src="{{ asset('js/app.js') }}"></script>
    @stack('scripts')
</body>

</html>