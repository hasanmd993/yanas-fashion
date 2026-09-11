@extends('layouts.app')

@section('title', ($currentCategory ? $currentCategory->name . " - " : "All Collections - ") . "Yanas Fashion")

@section('content')

    <!-- Catalog Header & Breadcrumb -->
    <div style="background: var(--bg-light); border-bottom: 1px solid var(--line); padding: 24px 0;">
        <div class="container">
            <div style="display:flex; align-items:center; gap:8px; font-size:0.85rem; color:var(--text-muted); margin-bottom:8px;">
                <a href="{{ route('home') }}">Home</a> <span>/</span>
                <span>Shop</span>
                @if($currentCategory)
                    <span>/</span> <strong style="color:var(--dark);">{{ $currentCategory->name }}</strong>
                @endif
            </div>
            <h1 style="font-size: 2rem;">
                {{ $currentCategory ? $currentCategory->name : 'All Collections' }}
            </h1>
            <p style="color:var(--text-muted); font-size:0.92rem;">
                {{ $currentCategory ? $currentCategory->description : 'Shop our exclusive clothing collections crafted with heritage and modern elegance.' }}
            </p>
        </div>
    </div>

    <!-- Shop Content Section -->
    <section style="padding: 32px 0 64px;">
        <div class="container">
            <!-- Filter & Sort Bar -->
            <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 1px solid var(--line);">
                
                <!-- Category Pills -->
                <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                    <a href="{{ route('shop.index') }}" 
                       style="padding: 6px 14px; border-radius: var(--radius-full); font-size: 0.85rem; font-weight: 600; border: 1px solid {{ !request('category') ? 'var(--primary)' : 'var(--line)' }}; background: {{ !request('category') ? 'var(--primary)' : 'var(--white)' }}; color: {{ !request('category') ? '#fff' : 'var(--text)' }};">
                        All
                    </a>
                    @foreach($categories as $cat)
                        <a href="{{ route('shop.index', ['category' => $cat->slug]) }}" 
                           style="padding: 6px 14px; border-radius: var(--radius-full); font-size: 0.85rem; font-weight: 600; border: 1px solid {{ request('category') == $cat->slug ? 'var(--primary)' : 'var(--line)' }}; background: {{ request('category') == $cat->slug ? 'var(--primary)' : 'var(--white)' }}; color: {{ request('category') == $cat->slug ? '#fff' : 'var(--text)' }};">
                            {{ $cat->name }}
                        </a>
                    @endforeach
                </div>

                <!-- Sort Control -->
                <form action="{{ route('shop.index') }}" method="GET" style="display: flex; align-items: center; gap: 8px;">
                    @if(request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                    @if(request('q'))
                        <input type="hidden" name="q" value="{{ request('q') }}">
                    @endif
                    <label for="sort-select" style="font-size: 0.85rem; font-weight: 600; color: var(--text-muted);">Sort by:</label>
                    <select name="sort" id="sort-select" onchange="this.form.submit()" 
                            style="padding: 6px 12px; border: 1px solid var(--line); border-radius: var(--radius-sm); font-size: 0.88rem; outline: none; background: #fff;">
                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest</option>
                        <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price Low to High</option>
                        <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price High to Low</option>
                        <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Popular</option>
                    </select>
                </form>
            </div>

            <!-- Product Grid -->
            @if($products->isEmpty())
                <div style="text-align: center; padding: 64px 16px; background: var(--white); border-radius: var(--radius-md); border: 1px solid var(--line);">
                    <i class="fa-solid fa-box-open" style="font-size: 3rem; color: #ccc; margin-bottom: 16px;"></i>
                    <h3 style="margin-bottom: 8px;">No products found</h3>
                    <p style="color: var(--text-muted); margin-bottom: 16px;">Please try a different category or filter.</p>
                    <a href="{{ route('shop.index') }}" class="btn btn-primary btn-sm">View All Products</a>
                </div>
            @else
                <div class="product-grid">
                    @foreach($products as $product)
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
                                        <i class="fa-solid fa-bolt"></i> Order Now
                                    </a>
                                    <button type="button" class="btn-add-cart" onclick="addToCartAjax({{ $product->id }})">
                                        <i class="fa-solid fa-cart-plus"></i> Add to Cart
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div style="margin-top: 40px; display: flex; justify-content: center;">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </section>

@endsection
