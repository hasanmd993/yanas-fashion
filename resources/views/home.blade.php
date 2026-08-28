@extends('layouts.app')

@section('title', "Yana's Fashion — Bangladeshi Luxury & Contemporary Ethnic Wear")

@section('content')

    <!-- Interactive Luxury Hero Slider -->
    <section class="hero-slider-section" id="heroSlider">
        
        @forelse($sliders as $idx => $slide)
            <div class="hero-slide {{ $idx === 0 ? 'active' : '' }}" data-slide="{{ $idx }}">
                <img src="{{ asset($slide->image) }}" alt="{{ $slide->title }}" class="hero-slide-bg">
                <div class="hero-slide-overlay"></div>
                <div class="container">
                    <div class="hero-content">
                        @if($slide->tag)
                            <div class="hero-tag">
                                <i class="fa-solid fa-sparkles"></i> {{ $slide->tag }}
                            </div>
                        @endif
                        <h1 class="hero-title">
                            {!! $slide->title !!}
                        </h1>
                        @if($slide->subtitle)
                            <p class="hero-subtitle">
                                {!! $slide->subtitle !!}
                            </p>
                        @endif
                        <div class="hero-cta-group">
                            <a href="{{ url($slide->button_link) }}" class="btn btn-accent btn-lg">
                                {{ $slide->button_text }} <i class="fa-solid fa-arrow-right"></i>
                            </a>
                            @if($slide->secondary_button_text && $slide->secondary_button_link)
                                <a href="{{ url($slide->secondary_button_link) }}" class="btn btn-outline" style="color:#fff; border-color:rgba(255,255,255,0.4);">
                                    {{ $slide->secondary_button_text }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <!-- Fallback Static Slide if DB is empty -->
            <div class="hero-slide active" data-slide="0">
                <img src="{{ asset('assets/hero.jpg') }}" alt="Bangladeshi Festive Fashion" class="hero-slide-bg">
                <div class="hero-slide-overlay"></div>
                <div class="container">
                    <div class="hero-content">
                        <div class="hero-tag">
                            <i class="fa-solid fa-sparkles"></i> Pohela Boishakh & Festive Edit 2026
                        </div>
                        <h1 class="hero-title">
                            বাংলার ঐতিহ্য, <span>আধুনিক আভিজাত্য</span>
                        </h1>
                        <p class="hero-subtitle">
                            খাঁটি হাতে বোনা ঢাকাই জামদানি, প্রিমিয়াম রাজমহলী সিল্ক ও নিখুঁত ফেস্টিভ পাঞ্জাবি।
                        </p>
                        <div class="hero-cta-group">
                            <a href="{{ route('shop.index') }}" class="btn btn-accent btn-lg">
                                কালেকশন দেখুন <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforelse

        @if($sliders->count() > 1)
            <!-- Navigation Arrows -->
            <button type="button" class="hero-slider-nav prev" id="heroPrevBtn" aria-label="Previous Slide">
                <i class="fa-solid fa-chevron-left"></i>
            </button>
            <button type="button" class="hero-slider-nav next" id="heroNextBtn" aria-label="Next Slide">
                <i class="fa-solid fa-chevron-right"></i>
            </button>

            <!-- Indicator Progress Dots -->
            <div class="hero-slider-dots" id="heroDotsContainer">
                @foreach($sliders as $idx => $s)
                    <span class="hero-dot {{ $idx === 0 ? 'active' : '' }}" data-index="{{ $idx }}"></span>
                @endforeach
            </div>
        @endif

    </section>

    <!-- Categories Shelf Section -->
    <section class="categories-section">
        <div class="container">
            <div class="section-head">
                <span class="eyebrow">কিউরেটেড কালেকশন</span>
                <h2 class="section-title">পছন্দের ক্যাটাগরি বাছাই করুন</h2>
                <p class="section-subtitle">সেরা কারিগরদের নিখুঁত বুনন ও আধুনিক ডিজাইনের অনন্য সমাহার</p>
            </div>

            <div class="category-grid">
                @foreach($categories as $category)
                    <a href="{{ route('shop.index', ['category' => $category->slug]) }}" class="category-card">
                        <div class="cat-img-wrap">
                            <img src="{{ asset($category->image ?? 'assets/category-men.jpg') }}" alt="{{ $category->name }}">
                        </div>
                        <h3>{{ $category->name }}</h3>
                        <span class="cat-bn">{{ $category->name_bn }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Flash Deals & Hot Offers Section -->
    <section style="padding: 48px 0; background: var(--white); border-top: 1px solid var(--line); border-bottom: 1px solid var(--line);">
        <div class="container">
            <div class="section-head">
                <span class="eyebrow" style="color:var(--accent);"><i class="fa-solid fa-bolt"></i> স্পেশাল অফার</span>
                <h2 class="section-title">হট ডিল ও বেস্টসেলার</h2>
                <p class="section-subtitle">সর্বাধিক বিক্রিত ও জনপ্রিয় পোশাকসমূহ সীমিত সময়ের বিশেষ মূল্যে</p>
            </div>

            <div class="product-grid">
                @foreach($featuredProducts as $product)
                    <div class="product-card">
                        <div class="product-media">
                            @if($product->badge)
                                <span class="product-badge">{{ $product->badge }}</span>
                            @endif
                            @if($product->discount_percent > 0)
                                <span class="discount-tag">-{{ $product->discount_percent }}%</span>
                            @endif
                            <a href="{{ route('product.show', $product->slug) }}">
                                <img src="{{ asset($product->thumbnail) }}" alt="{{ $product->title }}" loading="lazy">
                            </a>
                        </div>
                        <div class="product-body">
                            <span class="product-cat-name">{{ $product->category->name ?? 'Collection' }}</span>
                            <a href="{{ route('product.show', $product->slug) }}" class="product-title">
                                {{ $product->title }}
                            </a>
                            
                            <div class="rating-stars">
                                ★★★★★ <span>({{ $product->reviews_count }})</span>
                            </div>

                            <div class="price-box">
                                <span class="current-price">৳{{ number_format($product->effective_price) }}</span>
                                @if($product->sale_price)
                                    <span class="old-price">৳{{ number_format($product->regular_price) }}</span>
                                @endif
                            </div>

                            <!-- Dual Actions -->
                            <div class="card-actions">
                                <a href="{{ route('checkout.index', ['buy_now' => $product->id]) }}" class="btn-order-now">
                                    <i class="fa-solid fa-bolt"></i> অর্ডার করুন (Order Now)
                                </a>
                                <button type="button" class="btn-add-cart" onclick="addToCartAjax({{ $product->id }})">
                                    <i class="fa-solid fa-cart-plus"></i> কার্ট-এ যোগ করুন
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div style="text-align: center; margin-top: 36px;">
                <a href="{{ route('shop.index') }}" class="btn btn-outline">
                    সম্পূর্ণ কালেকশন দেখুন <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Artisan Heritage Banner -->
    <section style="padding: 64px 0;">
        <div class="container">
            <div style="display: grid; grid-template-columns: 1fr; gap: 36px; align-items: center; background: #201915; color: #fff; border-radius: var(--radius-lg); overflow: hidden; box-shadow: var(--shadow-lg);">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));">
                    <div style="height: 380px;">
                        <img src="{{ asset('assets/feature-sustainable.jpg') }}" alt="Master Artisan" style="width:100%; height:100%; object-fit:cover;">
                    </div>
                    <div style="padding: 40px 32px; display: flex; flex-direction: column; justify-content: center;">
                        <span class="eyebrow" style="color: #ffd875;">হাতে বোনা ঐতিহ্যের অহংকার</span>
                        <h2 style="color: #fff; font-size: clamp(1.8rem, 3.5vw, 2.5rem); margin-bottom: 16px;">
                            বাংলার সেরা তাঁতিদের নিখুঁত সৃষ্টি
                        </h2>
                        <p style="color: #c9bea9; font-size: 0.95rem; line-height: 1.7; margin-bottom: 24px;">
                            নারায়ণগঞ্জের রূপগঞ্জের ঐতিহ্যবাহী ইউনেস্কো স্বীকৃত জামদানি তাঁতশিল্পী থেকে রাজশাহীর তসর রেশমশিল্পী — প্রতিটি পোশাক দেশীয় তাঁতিদের ন্যায্য পারিশ্রমিক নিশ্চিত করে গভীর যত্ন ও শিল্পে তৈরি।
                        </p>
                        <div style="display: flex; gap: 20px; flex-wrap: wrap;">
                            <div style="display:flex; align-items:center; gap:8px; font-weight:600; color:#f0d5b5; font-size:0.9rem;">
                                <i class="fa-solid fa-check-circle" style="color:var(--accent);"></i> ১০০% খাঁটি তাঁতের কাপড়
                            </div>
                            <div style="display:flex; align-items:center; gap:8px; font-weight:600; color:#f0d5b5; font-size:0.9rem;">
                                <i class="fa-solid fa-check-circle" style="color:var(--accent);"></i> কোয়ালিটি নিশ্চিতকরণ
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Trending Now Shelf -->
    <section style="padding: 16px 0 64px;">
        <div class="container">
            <div class="section-head">
                <span class="eyebrow">জনপ্রিয় পছন্দ</span>
                <h2 class="section-title">ট্রেন্ডিং কালেকশন (Trending Now)</h2>
                <p class="section-subtitle">আমাদের সবচেয়ে জনপ্রিয় ট্রেন্ডি পোশাকের লেটেস্ট কালেকশন</p>
            </div>

            <div class="product-grid">
                @foreach($trendingProducts as $product)
                    <div class="product-card">
                        <div class="product-media">
                            @if($product->badge)
                                <span class="product-badge">{{ $product->badge }}</span>
                            @endif
                            @if($product->discount_percent > 0)
                                <span class="discount-tag">-{{ $product->discount_percent }}%</span>
                            @endif
                            <a href="{{ route('product.show', $product->slug) }}">
                                <img src="{{ asset($product->thumbnail) }}" alt="{{ $product->title }}" loading="lazy">
                            </a>
                        </div>
                        <div class="product-body">
                            <span class="product-cat-name">{{ $product->category->name ?? 'Collection' }}</span>
                            <a href="{{ route('product.show', $product->slug) }}" class="product-title">
                                {{ $product->title }}
                            </a>
                            
                            <div class="rating-stars">
                                ★★★★★ <span>({{ $product->reviews_count }})</span>
                            </div>

                            <div class="price-box">
                                <span class="current-price">৳{{ number_format($product->effective_price) }}</span>
                                @if($product->sale_price)
                                    <span class="old-price">৳{{ number_format($product->regular_price) }}</span>
                                @endif
                            </div>

                            <div class="card-actions">
                                <a href="{{ route('checkout.index', ['buy_now' => $product->id]) }}" class="btn-order-now">
                                    <i class="fa-solid fa-bolt"></i> অর্ডার করুন (Order Now)
                                </a>
                                <button type="button" class="btn-add-cart" onclick="addToCartAjax({{ $product->id }})">
                                    <i class="fa-solid fa-cart-plus"></i> কার্ট-এ যোগ করুন
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Trust / Value Perks Strip -->
    <section class="perks-section">
        <div class="container">
            <div class="perks-grid">
                <div class="perk-item">
                    <div class="perk-icon"><i class="fa-solid fa-truck-fast"></i></div>
                    <div class="perk-info">
                        <h4>দ্রুততম ডেলিভারি</h4>
                        <p>ঢাকা সিটিতে ২৪-৪৮ ঘণ্টা, ৬৪ জেলায় ৩-৫ দিন</p>
                    </div>
                </div>
                <div class="perk-item">
                    <div class="perk-icon"><i class="fa-solid fa-rotate-left"></i></div>
                    <div class="perk-info">
                        <h4>৭ দিনের সহজ এক্সচেঞ্জ</h4>
                        <p>সাইজ ও পণ্যে যে কোনো সমস্যায় সহজ পরিবর্তন</p>
                    </div>
                </div>
                <div class="perk-item">
                    <div class="perk-icon"><i class="fa-solid fa-hand-holding-dollar"></i></div>
                    <div class="perk-info">
                        <h4>ক্যাশ অন ডেলিভারি</h4>
                        <p>পণ্য হাতে পেয়ে মূল্য পরিশোধের সুবিধা</p>
                    </div>
                </div>
                <div class="perk-item">
                    <div class="perk-icon"><i class="fa-solid fa-shield-halved"></i></div>
                    <div class="perk-info">
                        <h4>১০০% প্রিমিয়াম কোয়ালিটি</h4>
                        <p>প্রতিটি পোশাক কোয়ালিটি চেক করে প্রেরিত</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
