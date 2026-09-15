@extends('layouts.app')

@section('title', ($currentCategory ? $currentCategory->name . " - " : "All Collections - ") . "Yanas Fashion")

@section('content')

    <!-- Catalog Header & Hierarchical Breadcrumb -->
    <div style="background: var(--bg-light); border-bottom: 1px solid var(--line); padding: 24px 0;">
        <div class="container">
            <nav aria-label="breadcrumb"
                style="display:flex; flex-wrap:wrap; align-items:center; gap:8px; font-size:0.82rem; color:var(--text-muted); margin-bottom:10px;">
                <a href="{{ route('home') }}" style="color:var(--text-muted); text-decoration:none;">Home</a>
                <span>/</span>
                <a href="{{ route('shop.index') }}" style="color:var(--text-muted); text-decoration:none;">Shop</a>

                @if($currentCategory)
                    @php
                        // Build breadcrumb chain from root to current
                        $crumbs = [];
                        $curr = $currentCategory;
                        while ($curr) {
                            array_unshift($crumbs, $curr);
                            $curr = $curr->parent;
                        }
                    @endphp

                    @foreach($crumbs as $index => $crumb)
                        <span>/</span>
                        @if($loop->last)
                            <strong style="color:var(--dark);">{{ $crumb->name }}</strong>
                        @else
                            <a href="{{ route('shop.index', ['category' => $crumb->slug]) }}"
                                style="color:var(--text-muted); text-decoration:none;">
                                {{ $crumb->name }}
                            </a>
                        @endif
                    @endforeach
                @endif
            </nav>

            <div style="display:flex; flex-wrap:wrap; align-items:flex-end; justify-content:space-between; gap:16px;">
                <div>
                    <h1 style="font-size: 2rem; margin-bottom: 4px;">
                        {{ $currentCategory ? $currentCategory->name : 'All Collections' }}
                    </h1>
                    <p style="color:var(--text-muted); font-size:0.92rem; margin:0;">
                        {{ $currentCategory && $currentCategory->description ? $currentCategory->description : 'Explore premium men\'s & women\'s handcrafted fashion, luxury ethnic wear, and modern collections.' }}
                    </p>
                </div>
                <div>
                    <span style="font-size:0.85rem; font-weight:600; color:var(--text-muted);">
                        Showing <strong>{{ $products->total() }}</strong> {{ Str::plural('item', $products->total()) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Shop Content Section -->
    <section style="padding: 32px 0 64px;">
        <div class="container">

            <!-- Tier 1: Parent Category Pills & Sorting Bar -->
            <div
                style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 16px; padding-bottom: 16px; border-bottom: 1px solid var(--line);">

                <!-- Main Category Pills (Tier 1) -->
                <div style="display: flex; flex-wrap: wrap; gap: 8px; align-items: center;">
                    <a href="{{ route('shop.index') }}"
                        style="display:inline-flex; align-items:center; gap:6px; padding: 7px 16px; border-radius: var(--radius-full); font-size: 0.85rem; font-weight: 700; text-decoration:none; transition:var(--transition); border: 1px solid {{ !request('category') ? 'var(--primary)' : 'var(--line)' }}; background: {{ !request('category') ? 'var(--primary)' : 'var(--white)' }}; color: {{ !request('category') ? '#fff' : 'var(--text)' }};">
                        <i class="fa-solid fa-layer-group"></i>
                        <span>All</span>
                    </a>

                    @foreach($parentCategories as $parent)
                        @php
                            $subSlugs = $parent->activeChildren->pluck('slug')->toArray();
                            $childSlugs = $parent->activeChildren->flatMap->activeChildren->pluck('slug')->toArray();
                            $allFamilySlugs = array_merge([$parent->slug], $subSlugs, $childSlugs);
                            $isParentFamilyActive = in_array(request('category'), $allFamilySlugs);
                        @endphp
                        <a href="{{ route('shop.index', ['category' => $parent->slug]) }}"
                            style="display:inline-flex; align-items:center; gap:6px; padding: 7px 16px; border-radius: var(--radius-full); font-size: 0.85rem; font-weight: 700; text-decoration:none; transition:var(--transition); border: 1px solid {{ $isParentFamilyActive ? 'var(--primary)' : 'var(--line)' }}; background: {{ $isParentFamilyActive ? 'var(--primary)' : 'var(--white)' }}; color: {{ $isParentFamilyActive ? '#fff' : 'var(--text)' }};">
                            @if($parent->icon)
                                <i class="{{ $parent->icon }}"></i>
                            @endif
                            <span>{{ $parent->name }}</span>
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
                    <label for="sort-select" style="font-size: 0.85rem; font-weight: 600; color: var(--text-muted);">Sort
                        by:</label>
                    <select name="sort" id="sort-select" onchange="this.form.submit()"
                        style="padding: 6px 12px; border: 1px solid var(--line); border-radius: var(--radius-sm); font-size: 0.88rem; outline: none; background: #fff; cursor:pointer;">
                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest</option>
                        <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price Low to High
                        </option>
                        <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price High to Low
                        </option>
                        <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Popular</option>
                    </select>
                </form>
            </div>

            <!-- Tier 2: Subcategories & Child Categories Bar (Contextual) -->
            @if($activeParentCategory && $activeParentCategory->activeChildren->count() > 0)
                <div
                    style="background: var(--bg-light); border: 1px solid var(--line); border-radius: var(--radius-md); padding: 14px 18px; margin-bottom: 28px;">
                    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:10px;">
                        <span
                            style="font-size:0.75rem; text-transform:uppercase; font-weight:800; letter-spacing:0.06em; color:var(--text-muted);">
                            Subcategories in {{ $activeParentCategory->name }}:
                        </span>
                        <a href="{{ route('shop.index', ['category' => $activeParentCategory->slug]) }}"
                            style="font-size:0.8rem; font-weight:700; color:var(--accent); text-decoration:none;">
                            View All {{ $activeParentCategory->name }} &rarr;
                        </a>
                    </div>

                    <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                        @foreach($activeParentCategory->activeChildren as $sub)
                            @php
                                $subChildSlugs = $sub->activeChildren->pluck('slug')->toArray();
                                $subFamilySlugs = array_merge([$sub->slug], $subChildSlugs);
                                $isSubActive = in_array(request('category'), $subFamilySlugs);
                            @endphp
                            <a href="{{ route('shop.index', ['category' => $sub->slug]) }}"
                                style="display:inline-flex; align-items:center; gap:5px; padding: 5px 12px; border-radius: var(--radius-sm); font-size: 0.82rem; font-weight: 600; text-decoration:none; transition:var(--transition); border: 1px solid {{ $isSubActive ? 'var(--accent)' : 'var(--line)' }}; background: {{ $isSubActive ? 'var(--accent)' : '#fff' }}; color: {{ $isSubActive ? '#fff' : 'var(--text)' }};">
                                <span>{{ $sub->name }}</span>
                                @if($sub->activeChildren->count() > 0)
                                    <span style="font-size:0.65rem; opacity:0.8;">({{ $sub->activeChildren->count() }})</span>
                                @endif
                            </a>

                            <!-- Child Category Chips if subcategory is active -->
                            @if($isSubActive && $sub->activeChildren->count() > 0)
                                @foreach($sub->activeChildren as $ch)
                                    <a href="{{ route('shop.index', ['category' => $ch->slug]) }}"
                                        style="display:inline-flex; align-items:center; gap:4px; padding: 5px 10px; border-radius: var(--radius-sm); font-size: 0.78rem; font-weight: 600; text-decoration:none; transition:var(--transition); border: 1px dashed {{ request('category') === $ch->slug ? 'var(--primary)' : 'var(--line)' }}; background: {{ request('category') === $ch->slug ? 'var(--primary-light)' : '#fff' }}; color: {{ request('category') === $ch->slug ? 'var(--primary)' : 'var(--text-muted)' }};">
                                        <span style="font-size:0.7rem;">&rdsh;</span>
                                        <span>{{ $ch->name }}</span>
                                    </a>
                                @endforeach
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Product Grid -->
            @if($products->isEmpty())
                <div
                    style="text-align: center; padding: 64px 16px; background: var(--white); border-radius: var(--radius-md); border: 1px solid var(--line);">
                    <i class="fa-solid fa-box-open" style="font-size: 3rem; color: #ccc; margin-bottom: 16px;"></i>
                    <h3 style="margin-bottom: 8px;">No products found in this category</h3>
                    <p style="color: var(--text-muted); margin-bottom: 16px;">Try selecting a parent category or browse all
                        collections.</p>
                    <a href="{{ route('shop.index') }}" class="btn btn-primary btn-sm">View All Products</a>
                </div>
            @else
                <div class="product-grid">
                    @foreach($products as $product)
                        <div class="product-card">
                            <div class="product-thumb">
                                <a href="{{ route('product.show', $product->slug) }}">
                                    <img src="{{ asset($product->thumbnail) }}" alt="{{ $product->title }}" loading="lazy">
                                </a>
                                @if($product->badge)
                                    <span class="product-badge">{{ $product->badge }}</span>
                                @endif
                                <div class="product-actions">
                                    <a href="{{ route('product.show', $product->slug) }}" class="btn-action" title="View Details">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="product-info">
                                <div class="product-cat">
                                    {{ $product->category ? $product->category->name : 'Uncategorized' }}
                                </div>
                                <h3 class="product-title">
                                    <a href="{{ route('product.show', $product->slug) }}">{{ $product->title }}</a>
                                </h3>
                                <div class="product-price">
                                    @if($product->sale_price)
                                        <span class="price-current">৳{{ number_format($product->sale_price) }}</span>
                                        <span class="price-old">৳{{ number_format($product->regular_price) }}</span>
                                    @else
                                        <span class="price-current">৳{{ number_format($product->regular_price) }}</span>
                                    @endif
                                </div>

                                <div class="product-card-btns"
                                    style="margin-top: 12px; display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                                    <button type="button" class="btn btn-outline btn-sm quick-add-cart" data-id="{{ $product->id }}"
                                        data-title="{{ $product->title }}"
                                        data-price="{{ $product->sale_price ?? $product->regular_price }}"
                                        data-thumb="{{ asset($product->thumbnail) }}">
                                        <i class="fa-solid fa-bag-shopping"></i> Add
                                    </button>
                                    <a href="{{ route('checkout.index', ['buy_now_product_id' => $product->id]) }}"
                                        class="btn btn-primary btn-sm" style="text-align: center;">
                                        Buy Now
                                    </a>
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