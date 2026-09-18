@extends('layouts.app')

@section('title', "Yanas Fashion — Contemporary & Luxury Fashion Dhaka")

@section('content')

    {{-- ============================================================
    HERO SLIDER
    ============================================================ --}}
    <section class="hp-hero" id="heroSlider">

        @forelse($sliders as $idx => $slide)
            <div class="hero-slide {{ $idx === 0 ? 'active' : '' }}" data-slide="{{ $idx }}">
                <img src="{{ asset($slide->image) }}" alt="{{ $slide->title }}" class="hero-slide-bg">
                <div class="hp-hero-overlay"></div>
                <div class="container">
                    <div class="hp-hero-content">
                        @if($slide->tag)
                            <div class="hp-hero-tag">
                                <i class="fa-solid fa-sparkles"></i> {{ $slide->tag }}
                            </div>
                        @endif
                        <h1 class="hp-hero-title">{!! $slide->title !!}</h1>
                        @if($slide->subtitle)
                            <p class="hp-hero-sub">{!! $slide->subtitle !!}</p>
                        @endif
                        <div class="hp-hero-actions">
                            <a href="{{ url($slide->button_link ?? '/shop') }}" class="hp-btn-primary">
                                {{ $slide->button_text ?? 'Shop Collection' }} <i class="fa-solid fa-arrow-right"></i>
                            </a>
                            @if($slide->secondary_button_text && $slide->secondary_button_link)
                                <a href="{{ url($slide->secondary_button_link) }}" class="hp-btn-ghost">
                                    {{ $slide->secondary_button_text }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        @empty
            <div class="hero-slide active" data-slide="0">
                <img src="{{ asset('assets/hero.jpg') }}" alt="Yanas Fashion" class="hero-slide-bg">
                <div class="hp-hero-overlay"></div>
                <div class="container">
                    <div class="hp-hero-content">
                        <div class="hp-hero-tag"><i class="fa-solid fa-sparkles"></i> Festive & Luxury Edit 2026</div>
                        <h1 class="hp-hero-title">Bangladeshi Heritage, <span>Modern Luxury</span></h1>
                        <p class="hp-hero-sub">Pure handcrafted luxury fabrics, premium festive panjabis, and contemporary
                            silhouettes. Nationwide Home Delivery with Cash on Delivery.</p>
                        <div class="hp-hero-actions">
                            <a href="{{ route('shop.index') }}" class="hp-btn-primary">Explore Collection <i
                                    class="fa-solid fa-arrow-right"></i></a>
                            <a href="{{ route('shop.index') }}" class="hp-btn-ghost">Shop All</a>
                        </div>
                    </div>
                </div>

            </div>
        @endforelse

        @if($sliders->count() > 1)
            <button type="button" class="hero-slider-nav prev" id="heroPrevBtn" aria-label="Previous">
                <i class="fa-solid fa-chevron-left"></i>
            </button>
            <button type="button" class="hero-slider-nav next" id="heroNextBtn" aria-label="Next">
                <i class="fa-solid fa-chevron-right"></i>
            </button>
            <div class="hp-dots" id="heroDotsContainer">
                @foreach($sliders as $idx => $s)
                    <button class="hp-dot {{ $idx === 0 ? 'active' : '' }}" data-index="{{ $idx }}"
                        aria-label="Slide {{ $idx + 1 }}"></button>
                @endforeach
            </div>
        @endif
    </section>

    {{-- ============================================================
    PERKS / TRUST STRIP (moved just below hero for conversion)
    ============================================================ --}}
    <section class="hp-perks">
        <div class="container">
            <div class="hp-perks-row">
                <div class="hp-perk">
                    <i class="fa-solid fa-truck-fast hp-perk-icon"></i>
                    <div>
                        <strong>Express Delivery</strong>
                        <span>24-48 hrs Dhaka · 3-5 days nationwide</span>
                    </div>
                </div>
                <div class="hp-perk-divider"></div>
                <div class="hp-perk">
                    <i class="fa-solid fa-hand-holding-dollar hp-perk-icon"></i>
                    <div>
                        <strong>Cash on Delivery</strong>
                        <span>Pay safely at your doorstep</span>
                    </div>
                </div>
                <div class="hp-perk-divider"></div>
                <div class="hp-perk">
                    <i class="fa-solid fa-rotate-left hp-perk-icon"></i>
                    <div>
                        <strong>7-Day Easy Exchange</strong>
                        <span>Hassle-free size replacement</span>
                    </div>
                </div>
                <div class="hp-perk-divider"></div>
                <div class="hp-perk">
                    <i class="fa-solid fa-shield-halved hp-perk-icon"></i>
                    <div>
                        <strong>100% Quality Checked</strong>
                        <span>Every item inspected before dispatch</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================================
    CATEGORIES
    ============================================================ --}}
    <section class="hp-cats">
        <div class="container">
            <div class="hp-section-head">
                <div>
                    <span class="eyebrow">CURATED SELECTION</span>
                    <h2 class="hp-section-title">Shop by Category</h2>
                </div>
                <div class="hp-cats-nav-wrap">
                    <button type="button" class="hp-cat-nav-btn prev" id="hpCatPrev" aria-label="Previous Categories">
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>
                    <button type="button" class="hp-cat-nav-btn next" id="hpCatNext" aria-label="Next Categories">
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>
                    <a href="{{ route('shop.index') }}" class="hp-view-all">View All <i class="fa-solid fa-arrow-right"></i></a>
                </div>
            </div>

            <div class="hp-cat-carousel-wrapper" id="hpCatCarouselWrapper">
                <div class="hp-cat-carousel-track" id="hpCatTrack">
                    @foreach($categories as $category)
                        <div class="hp-cat-slide">
                            <a href="{{ route('shop.index', ['category' => $category->slug]) }}" class="hp-cat-card">
                                <div class="hp-cat-img">
                                    <img src="{{ asset($category->image ?? 'assets/category-men.jpg') }}" alt="{{ $category->name }}">
                                </div>
                                <span class="hp-cat-name">{{ $category->name }}</span>
                                <span class="hp-cat-cta">Shop <i class="fa-solid fa-arrow-right"></i></span>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================================
    HOT DEALS & BESTSELLERS
    ============================================================ --}}
    <section class="hp-products-section hp-deals">
        <div class="container">
            <div class="hp-section-head">
                <div>
                    <span class="eyebrow eyebrow-accent"><i class="fa-solid fa-bolt"></i> LIMITED OFFERS</span>
                    <h2 class="hp-section-title">Hot Deals & Bestsellers</h2>
                </div>
                <a href="{{ route('shop.index') }}" class="hp-view-all">View All <i class="fa-solid fa-arrow-right"></i></a>
            </div>

            <div class="product-grid">
                @foreach($featuredProducts as $product)
                    @php
                        $effectivePrice = $product->sale_price ?? $product->regular_price;
                        $hasDiscount = $product->sale_price && $product->sale_price < $product->regular_price;
                        $discountPercent = $hasDiscount ? round((($product->regular_price - $product->sale_price) / $product->regular_price) * 100) : 0;
                        $savings = $hasDiscount ? ($product->regular_price - $product->sale_price) : 0;
                    @endphp
                    <div class="product-card">
                        <div class="product-thumb">
                            @if($product->badge)
                                <span class="product-badge">{{ $product->badge }}</span>
                            @endif
                            @if($discountPercent > 0)
                                <span class="discount-tag">-{{ $discountPercent }}%</span>
                            @endif

                            <a href="{{ route('product.show', $product->slug) }}" class="product-thumb-link">
                                <img src="{{ asset($product->thumbnail) }}" alt="{{ $product->title }}" loading="lazy">
                            </a>

                            <div class="product-thumb-actions">
                                <a href="{{ route('product.show', $product->slug) }}" class="thumb-action-btn" title="View Details">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                            </div>
                        </div>

                        <div class="product-info">
                            @if($product->category)
                                <a href="{{ route('shop.index', ['category' => $product->category->slug]) }}" class="product-cat">
                                    {{ $product->category->name }}
                                </a>
                            @else
                                <span class="product-cat">Collection</span>
                            @endif

                            <h3 class="product-title">
                                <a href="{{ route('product.show', $product->slug) }}">{{ $product->title }}</a>
                            </h3>

                            <div class="product-price-row">
                                <span class="price-current">৳{{ number_format($effectivePrice) }}</span>
                                @if($hasDiscount)
                                    <span class="price-old">৳{{ number_format($product->regular_price) }}</span>
                                    <span class="price-save">Save ৳{{ number_format($savings) }}</span>
                                @endif
                            </div>

                            <div class="product-card-btns">
                                <button type="button" class="btn-card-add" onclick="addToCartAjax({{ $product->id }})">
                                    <i class="fa-solid fa-bag-shopping"></i>
                                    <span>Add</span>
                                </button>
                                <a href="{{ route('checkout.index', ['buy_now_product_id' => $product->id]) }}" class="btn-card-buy">
                                    <i class="fa-solid fa-bolt"></i>
                                    <span>Buy Now</span>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============================================================
    TRENDING NOW
    ============================================================ --}}
    <section class="hp-products-section hp-trending">
        <div class="container">
            <div class="hp-section-head">
                <div>
                    <span class="eyebrow eyebrow-accent"><i class="fa-solid fa-fire"></i> HIGH DEMAND</span>
                    <h2 class="hp-section-title">Trending Now</h2>
                </div>
                <a href="{{ route('shop.index') }}" class="hp-view-all">View All <i class="fa-solid fa-arrow-right"></i></a>
            </div>

            <div class="product-grid">
                @foreach($trendingProducts as $product)
                    @php
                        $effectivePrice = $product->sale_price ?? $product->regular_price;
                        $hasDiscount = $product->sale_price && $product->sale_price < $product->regular_price;
                        $discountPercent = $hasDiscount ? round((($product->regular_price - $product->sale_price) / $product->regular_price) * 100) : 0;
                        $savings = $hasDiscount ? ($product->regular_price - $product->sale_price) : 0;
                    @endphp
                    <div class="product-card">
                        <div class="product-thumb">
                            @if($product->badge)
                                <span class="product-badge">{{ $product->badge }}</span>
                            @endif
                            @if($discountPercent > 0)
                                <span class="discount-tag">-{{ $discountPercent }}%</span>
                            @endif

                            <a href="{{ route('product.show', $product->slug) }}" class="product-thumb-link">
                                <img src="{{ asset($product->thumbnail) }}" alt="{{ $product->title }}" loading="lazy">
                            </a>

                            <div class="product-thumb-actions">
                                <a href="{{ route('product.show', $product->slug) }}" class="thumb-action-btn" title="View Details">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                            </div>
                        </div>

                        <div class="product-info">
                            @if($product->category)
                                <a href="{{ route('shop.index', ['category' => $product->category->slug]) }}" class="product-cat">
                                    {{ $product->category->name }}
                                </a>
                            @else
                                <span class="product-cat">Collection</span>
                            @endif

                            <h3 class="product-title">
                                <a href="{{ route('product.show', $product->slug) }}">{{ $product->title }}</a>
                            </h3>

                            <div class="product-price-row">
                                <span class="price-current">৳{{ number_format($effectivePrice) }}</span>
                                @if($hasDiscount)
                                    <span class="price-old">৳{{ number_format($product->regular_price) }}</span>
                                    <span class="price-save">Save ৳{{ number_format($savings) }}</span>
                                @endif
                            </div>

                            <div class="product-card-btns">
                                <button type="button" class="btn-card-add" onclick="addToCartAjax({{ $product->id }})">
                                    <i class="fa-solid fa-bag-shopping"></i>
                                    <span>Add</span>
                                </button>
                                <a href="{{ route('checkout.index', ['buy_now_product_id' => $product->id]) }}" class="btn-card-buy">
                                    <i class="fa-solid fa-bolt"></i>
                                    <span>Buy Now</span>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

@endsection
