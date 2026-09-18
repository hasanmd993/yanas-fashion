@extends('layouts.app')

@section('title', $product->title . " — Luxury Fashion Dhaka | Yanas Fashion")

@section('content')

    <!-- Breadcrumb -->
    <div class="pdp-breadcrumb-wrap">
        <div class="container">
            <nav aria-label="breadcrumb" class="pdp-breadcrumb">
                <a href="{{ route('home') }}"><i class="fa-solid fa-house"></i> Home</a>
                <span class="sep">/</span>
                <a href="{{ route('shop.index') }}">Shop</a>
                @if($product->category)
                    @php
                        $crumbs = [];
                        $curr = $product->category;
                        while ($curr) {
                            array_unshift($crumbs, $curr);
                            $curr = $curr->parent;
                        }
                    @endphp
                    @foreach($crumbs as $crumb)
                        <span class="sep">/</span>
                        <a href="{{ route('shop.index', ['category' => $crumb->slug]) }}">{{ $crumb->name }}</a>
                    @endforeach
                @endif
                <span class="sep">/</span>
                <span class="current">{{ Str::limit($product->title, 40) }}</span>
            </nav>
        </div>
    </div>

    <!-- Main Product Section -->
    <section class="pdp-main-section">
        <div class="container">
            <div class="pdp-layout">

                <!-- Left Column: Portrait Gallery -->
                <div class="pdp-gallery-col">
                    <div class="pdp-sticky-gallery">
                        <!-- Main Image Stage (3:4 Portrait) -->
                        <div class="pdp-main-image-stage" id="pdpImageStage">
                            @if($product->badge)
                                <span class="pdp-badge pdp-badge-highlight">{{ $product->badge }}</span>
                            @endif
                            @if($product->discount_percent > 0)
                                <span class="pdp-badge pdp-badge-discount">-{{ $product->discount_percent }}% OFF</span>
                            @endif

                            <div class="pdp-zoom-container" id="pdpZoomContainer">
                                <img id="pdp-main-img" src="{{ asset($product->thumbnail) }}" alt="{{ $product->title }}"
                                    class="pdp-main-image" data-zoom="{{ asset($product->thumbnail) }}">
                            </div>

                            <button type="button" class="pdp-fullscreen-btn" id="pdpFullscreenBtn" title="View Fullscreen"
                                aria-label="View Fullscreen">
                                <i class="fa-solid fa-expand"></i>
                            </button>
                        </div>

                        <!-- Thumbnails Carousel -->
                        @if($product->gallery && count($product->gallery) > 1)
                            <div class="pdp-thumbnails-wrap">
                                @foreach($product->gallery as $index => $img)
                                    <button type="button" class="pdp-thumb-btn {{ $index === 0 ? 'active' : '' }}"
                                        data-src="{{ asset($img) }}" aria-label="View product image {{ $index + 1 }}">
                                        <img src="{{ asset($img) }}" alt="Thumbnail {{ $index + 1 }}" loading="lazy">
                                    </button>
                                @endforeach
                            </div>
                        @endif

                        <!-- Value Guarantees 4-Grid -->
                        <!-- <div class="pdp-guarantees-card">
                                    <div class="pdp-guarantee-item">
                                        <div class="guarantee-icon"><i class="fa-solid fa-truck-fast"></i></div>
                                        <div class="guarantee-text">
                                            <strong>Express Delivery</strong>
                                            <span>24-48h in Dhaka &bull; Nationwide COD</span>
                                        </div>
                                    </div>
                                    <div class="pdp-guarantee-item">
                                        <div class="guarantee-icon"><i class="fa-solid fa-hand-holding-dollar"></i></div>
                                        <div class="guarantee-text">
                                            <strong>Cash on Delivery</strong>
                                            <span>Pay upon doorstep inspection</span>
                                        </div>
                                    </div>
                                    <div class="pdp-guarantee-item">
                                        <div class="guarantee-icon"><i class="fa-solid fa-rotate-left"></i></div>
                                        <div class="guarantee-text">
                                            <strong>7-Day Easy Exchange</strong>
                                            <span>Hassle-free size & fit replacement</span>
                                        </div>
                                    </div>
                                    <div class="pdp-guarantee-item">
                                        <div class="guarantee-icon"><i class="fa-solid fa-award"></i></div>
                                        <div class="guarantee-text">
                                            <strong>100% Quality Fabric</strong>
                                            <span>Fine stitching & luxury comfort</span>
                                        </div>
                                    </div>
                                </div> -->
                    </div>
                </div>

                <!-- Right Column: Product Info & Order Form -->
                <div class="pdp-info-col">
                    <div class="pdp-header-block">
                        @if($product->category)
                            <a href="{{ route('shop.index', ['category' => $product->category->slug]) }}"
                                class="pdp-category-eyebrow">
                                @if($product->category->icon)
                                    <i class="{{ $product->category->icon }}"></i>
                                @endif
                                <span>{{ $product->category->name }}</span>
                            </a>
                        @endif

                        <h1 class="pdp-title">{{ $product->title }}</h1>

                        @if($product->title_bn)
                            <div class="pdp-title-bn">{{ $product->title_bn }}</div>
                        @endif

                        <!-- Meta Row: SKU, Rating, Stock Urgency -->
                        @php
                            $reviewCount = $product->reviews->count();
                            $avgRating = $product->reviews->isNotEmpty() ? $product->reviews->avg('rating') : 0;
                        @endphp
                        <div class="pdp-meta-row">
                            <div class="pdp-sku">SKU: <strong>{{ $product->sku }}</strong></div>
                            <span class="pdp-meta-dot">•</span>
                            <div class="pdp-rating">
                                @if($reviewCount > 0)
                                    <div class="stars">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($avgRating >= $i)
                                                <i class="fa-solid fa-star"></i>
                                            @elseif($avgRating >= ($i - 0.5))
                                                <i class="fa-solid fa-star-half-stroke"></i>
                                            @else
                                                <i class="fa-regular fa-star"></i>
                                            @endif
                                        @endfor
                                    </div>
                                    <span class="count">({{ $reviewCount }} {{ Str::plural('review', $reviewCount) }})</span>
                                @else
                                    <div class="stars stars-muted">
                                        <i class="fa-regular fa-star"></i>
                                        <i class="fa-regular fa-star"></i>
                                        <i class="fa-regular fa-star"></i>
                                        <i class="fa-regular fa-star"></i>
                                        <i class="fa-regular fa-star"></i>
                                    </div>
                                    <span class="count text-muted">(No reviews yet)</span>
                                @endif
                            </div>
                        </div>

                        <!-- Price Box -->
                        <div class="pdp-price-wrap">
                            <div class="pdp-current-price">৳{{ number_format($product->effective_price) }}</div>
                            @if($product->sale_price)
                                <div class="pdp-regular-price">৳{{ number_format($product->regular_price) }}</div>
                                <div class="pdp-save-badge">Save
                                    ৳{{ number_format($product->regular_price - $product->sale_price) }}
                                    ({{ $product->discount_percent }}% OFF)</div>
                            @endif
                        </div>

                        <!-- Stock Urgency Alert -->
                        <div class="pdp-stock-urgency">
                            <span class="stock-pulse"></span>
                            <span class="stock-text">
                                <i class="fa-solid fa-bolt-lightning text-accent"></i> <strong>In Stock & Ready to
                                    Ship</strong>
                            </span>
                        </div>

                        @if($product->short_desc)
                            <div class="pdp-short-desc">
                                {{ $product->short_desc }}
                            </div>
                        @endif
                    </div>

                    <!-- Size Selector -->
                    @if($product->sizes && count($product->sizes) > 0)
                        <div class="pdp-size-selector-block" id="pdpSizeSection">
                            <div class="pdp-size-header">
                                <span class="size-label">
                                    <i class="fa-solid fa-shirt"></i> Select Size: <strong
                                        id="selected-size-display">{{ $product->sizes[0] }}</strong>
                                </span>
                                @if($product->size_chart_html)
                                    <button type="button" class="pdp-size-guide-btn" id="open-size-chart">
                                        <i class="fa-solid fa-ruler-combined"></i> Size Chart
                                    </button>
                                @endif
                            </div>

                            <div class="pdp-size-chips">
                                @foreach($product->sizes as $idx => $s)
                                    <label class="pdp-size-chip">
                                        <input type="radio" name="product_size_radio" value="{{ $s }}" {{ $idx === 0 ? 'checked' : '' }} onchange="handlePdpSizeChange('{{ $s }}')">
                                        <span class="chip-box">{{ $s }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Quantity Stepper & Dual CTA Buttons -->
                    <div class="pdp-action-box" id="pdpBuyTriggerArea">
                        <!-- Side-by-Side: Quantity Stepper + Add to Bag -->
                        <div class="pdp-qty-bag-row">
                            <div class="pdp-qty-stepper">
                                <button type="button" class="qty-step-btn" onclick="adjustPdpQty(-1)"
                                    aria-label="Decrease quantity">-</button>
                                <input type="number" id="pdp-quantity-input" value="1" min="1" max="10" readonly>
                                <button type="button" class="qty-step-btn" onclick="adjustPdpQty(1)"
                                    aria-label="Increase quantity">+</button>
                            </div>

                            <button type="button" class="btn btn-outline pdp-btn-bag" onclick="addCurrentProductToCart()">
                                <i class="fa-solid fa-bag-shopping"></i>
                                <span>Add to Bag</span>
                            </button>
                        </div>

                        <!-- Direct Instant Order / Buy Now Button -->
                        <button type="button" class="btn btn-accent pdp-btn-buynow" onclick="buyNowDirect()">
                            <i class="fa-solid fa-bolt-lightning"></i>
                            <span>Buy Now</span>
                        </button>

                        <!-- WhatsApp Concierge Support -->
                        <div class="pdp-action-secondary-row">
                            @php
                                $waMsg = "Hello Yanas Fashion! I would like to purchase:\n• Product: {$product->title}\n• SKU: {$product->sku}\n• Price: ৳" . number_format($product->effective_price);
                            @endphp
                            <a href="https://wa.me/{{ get_whatsapp_number() }}?text={{ urlencode($waMsg) }}" target="_blank"
                                class="pdp-wa-link">
                                <i class="fa-brands fa-whatsapp"></i>
                                <span>Order via WhatsApp</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Product Tabs & Mobile Accordion Section -->
    <section class="pdp-tabs-section">
        <div class="container">
            <div class="pdp-tabs-wrapper">
                <!-- Desktop Tab Navigation (Visible on min-width: 769px) -->
                <div class="pdp-tabs-nav pdp-desktop-nav" role="tablist">
                    <button type="button" class="pdp-tab-btn active" data-tab="tab-description" role="tab">
                        <i class="fa-solid fa-align-left"></i>
                        <span>Description & Features</span>
                    </button>
                    <button type="button" class="pdp-tab-btn" data-tab="tab-fabric" role="tab">
                        <i class="fa-solid fa-shirt"></i>
                        <span>Fabric & Care</span>
                    </button>
                    <button type="button" class="pdp-tab-btn" data-tab="tab-shipping" role="tab">
                        <i class="fa-solid fa-truck"></i>
                        <span>Delivery & Returns</span>
                    </button>
                    <button type="button" class="pdp-tab-btn" data-tab="tab-reviews" role="tab">
                        <i class="fa-solid fa-star"></i>
                        <span>Reviews ({{ $reviewCount }})</span>
                    </button>
                </div>

                <!-- Tab / Accordion Content Panels -->
                <div class="pdp-tabs-content">

                    <!-- Section 1: Description -->
                    <div class="pdp-tab-panel active" id="tab-description" role="tabpanel">
                        <button type="button" class="pdp-accordion-toggle pdp-mobile-toggle active"
                            data-target="tab-description">
                            <span><i class="fa-solid fa-align-left"></i> Description & Features</span>
                            <i class="fa-solid fa-chevron-down acc-chevron"></i>
                        </button>
                        <div class="pdp-panel-body">
                            <div class="pdp-prose">
                                @if($product->description)
                                    {!! $product->description !!}
                                @else
                                    <p>Discover our exclusive luxury collection from Yanas Fashion, handcrafted with precision
                                        tailoring and premium fabrics. Perfect for festive celebrations, formal gatherings, and
                                        contemporary everyday elegance.</p>
                                    <ul>
                                        <li><strong>Fabrication:</strong> Premium Grade 100% Cotton / Luxury Twill blend</li>
                                        <li><strong>Fit:</strong> Tailored Modern Fit with ergonomic movement comfort</li>
                                        <li><strong>Stitching:</strong> Reinforced high-density dual stitching for lasting
                                            longevity</li>
                                        <li><strong>Origin:</strong> Crafted with pride in Dhaka, Bangladesh</li>
                                    </ul>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Fabric & Care -->
                    <div class="pdp-tab-panel" id="tab-fabric" role="tabpanel">
                        <button type="button" class="pdp-accordion-toggle pdp-mobile-toggle" data-target="tab-fabric">
                            <span><i class="fa-solid fa-shirt"></i> Fabric & Care</span>
                            <i class="fa-solid fa-chevron-down acc-chevron"></i>
                        </button>
                        <div class="pdp-panel-body">
                            <div class="pdp-care-grid">
                                <div class="care-card">
                                    <i class="fa-solid fa-temperature-low"></i>
                                    <h4>Washing</h4>
                                    <p>Hand wash in cold water or delicate machine wash inside-out with mild liquid
                                        detergent.</p>
                                </div>
                                <div class="care-card">
                                    <i class="fa-solid fa-ban"></i>
                                    <h4>Bleaching</h4>
                                    <p>Do not use chlorine bleach or abrasive chemical stain removers to preserve rich
                                        colors.</p>
                                </div>
                                <div class="care-card">
                                    <i class="fa-solid fa-wind"></i>
                                    <h4>Drying</h4>
                                    <p>Line dry in shaded breeze. Avoid excessive prolonged direct sunlight to prevent fiber
                                        fatigue.</p>
                                </div>
                                <div class="care-card">
                                    <i class="fa-solid fa-spray-can-sparkles"></i>
                                    <h4>Ironing</h4>
                                    <p>Warm iron with steam on reverse side for crisp finishing and smooth drape.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Delivery & Returns -->
                    <div class="pdp-tab-panel" id="tab-shipping" role="tabpanel">
                        <button type="button" class="pdp-accordion-toggle pdp-mobile-toggle" data-target="tab-shipping">
                            <span><i class="fa-solid fa-truck"></i> Delivery & Returns</span>
                            <i class="fa-solid fa-chevron-down acc-chevron"></i>
                        </button>
                        <div class="pdp-panel-body">
                            <div class="pdp-shipping-info">
                                <div class="shipping-column">
                                    <h4><i class="fa-solid fa-truck-ramp-box text-primary"></i> Shipping Timelines</h4>
                                    <ul>
                                        <li><strong>Inside Dhaka City:</strong> 24 to 48 Hours express delivery (৳70).</li>
                                        <li><strong>Dhaka Suburbs (Savar, Gazipur, Keraniganj):</strong> 24 to 48 Hours
                                            (৳100).</li>
                                        <li><strong>Outside Dhaka (64 Districts):</strong> 48 to 72 Hours door-to-door
                                            (৳130).</li>
                                        <li><strong>Free Shipping:</strong> Free delivery on all orders over ৳2,500 across
                                            Dhaka.</li>
                                    </ul>
                                </div>
                                <div class="shipping-column">
                                    <h4><i class="fa-solid fa-arrow-rotate-left text-accent"></i> 7-Day Exchange Policy</h4>
                                    <ul>
                                        <li>If size doesn't fit, request a size exchange within 7 days of delivery.</li>
                                        <li>Product must be unworn with original tags and packaging intact.</li>
                                        <li>Our dedicated support hotline (+880 1713-580400) handles quick replacements.
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 4: Dynamic Customer Reviews -->
                    <div class="pdp-tab-panel" id="tab-reviews" role="tabpanel">
                        <button type="button" class="pdp-accordion-toggle pdp-mobile-toggle" data-target="tab-reviews">
                            <span><i class="fa-solid fa-star"></i> Customer Reviews ({{ $reviewCount }})</span>
                            <i class="fa-solid fa-chevron-down acc-chevron"></i>
                        </button>
                        <div class="pdp-panel-body">

                            @if(session('success'))
                                <div class="pdp-review-alert-success">
                                    <i class="fa-solid fa-circle-check"></i>
                                    <span>{{ session('success') }}</span>
                                </div>
                            @endif

                            @if($reviewCount > 0)
                                <div class="pdp-reviews-summary">
                                    <div class="rating-overview">
                                        <div class="score">{{ number_format($avgRating, 1) }}</div>
                                        <div class="stars">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($avgRating >= $i)
                                                    <i class="fa-solid fa-star"></i>
                                                @elseif($avgRating >= ($i - 0.5))
                                                    <i class="fa-solid fa-star-half-stroke"></i>
                                                @else
                                                    <i class="fa-regular fa-star"></i>
                                                @endif
                                            @endfor
                                        </div>
                                        <span>Based on {{ $reviewCount }} {{ Str::plural('rating', $reviewCount) }}</span>
                                    </div>
                                    <div class="rating-highlight-pills">
                                        <span class="pill"><i class="fa-solid fa-check"></i> 100% Genuine Reviews</span>
                                        <span class="pill"><i class="fa-solid fa-check"></i> Verified Buyers</span>
                                        <span class="pill"><i class="fa-solid fa-check"></i> High Quality Fabric</span>
                                    </div>
                                </div>

                                <!-- Review Items List (if submitted reviews exist) -->
                                @if($product->reviews->isNotEmpty())
                                    <div class="pdp-reviews-list">
                                        @foreach($product->reviews as $rev)
                                            <div class="pdp-review-card">
                                                <div class="review-card-head">
                                                    <div class="reviewer-avatar">
                                                        {{ strtoupper(substr($rev->name, 0, 1)) }}
                                                    </div>
                                                    <div class="reviewer-meta">
                                                        <div class="reviewer-name-row">
                                                            <strong class="name">{{ $rev->name }}</strong>
                                                            @if($rev->is_verified_buyer)
                                                                <span class="verified-badge"><i class="fa-solid fa-circle-check"></i> Verified Buyer</span>
                                                            @endif
                                                        </div>
                                                        <div class="review-stars-date">
                                                            <div class="stars">
                                                                @for($s = 1; $s <= 5; $s++)
                                                                    <i class="{{ $s <= $rev->rating ? 'fa-solid' : 'fa-regular' }} fa-star"></i>
                                                                @endfor
                                                            </div>
                                                            <span class="date">{{ $rev->created_at->format('M d, Y') }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="review-comment">
                                                    {{ $rev->comment }}
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            @else
                                <!-- No Reviews Empty State -->
                                <div class="pdp-no-reviews-box">
                                    <div class="no-reviews-icon">
                                        <i class="fa-regular fa-star"></i>
                                    </div>
                                    <h4>No Reviews Yet</h4>
                                    <p>There are currently no reviews for this product. Be the first to share your experience with other shoppers!</p>
                                </div>
                            @endif

                            <!-- Write a Review Form Card -->
                            <div class="pdp-write-review-card">
                                <div class="card-head">
                                    <h4><i class="fa-regular fa-pen-to-square"></i> Write a Customer Review</h4>
                                    <p>Share your honest thoughts about fabric, fit, and stitching</p>
                                </div>
                                <form action="{{ route('product.review.store', $product->id) }}" method="POST" class="pdp-review-form">
                                    @csrf
                                    
                                    <!-- Star Rating Picker -->
                                    <div class="form-group rating-picker-group">
                                        <label>Overall Rating <span class="req">*</span></label>
                                        <div class="star-rating-selector" id="starRatingSelector">
                                            <input type="radio" id="star5" name="rating" value="5" checked>
                                            <label for="star5" title="5 stars"><i class="fa-solid fa-star"></i></label>
                                            
                                            <input type="radio" id="star4" name="rating" value="4">
                                            <label for="star4" title="4 stars"><i class="fa-solid fa-star"></i></label>
                                            
                                            <input type="radio" id="star3" name="rating" value="3">
                                            <label for="star3" title="3 stars"><i class="fa-solid fa-star"></i></label>
                                            
                                            <input type="radio" id="star2" name="rating" value="2">
                                            <label for="star2" title="2 stars"><i class="fa-solid fa-star"></i></label>
                                            
                                            <input type="radio" id="star1" name="rating" value="1">
                                            <label for="star1" title="1 star"><i class="fa-solid fa-star"></i></label>
                                        </div>
                                    </div>

                                    <div class="form-grid-2">
                                        <div class="form-group">
                                            <label for="rev_name">Your Name <span class="req">*</span></label>
                                            <input type="text" name="name" id="rev_name" class="form-control" placeholder="e.g. Fahim Rahman" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="rev_email">Email Address <span class="opt">(Optional)</span></label>
                                            <input type="email" name="email" id="rev_email" class="form-control" placeholder="fahim@example.com">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="rev_comment">Your Review <span class="req">*</span></label>
                                        <textarea name="comment" id="rev_comment" class="form-control" rows="3" placeholder="Describe the fabric quality, color accuracy, and fit..." required></textarea>
                                    </div>

                                    <button type="submit" class="btn btn-primary pdp-btn-submit-review">
                                        <i class="fa-solid fa-paper-plane"></i>
                                        <span>Submit Review</span>
                                    </button>
                                </form>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <!-- Related Products -->
    @if($relatedProducts->isNotEmpty())
        <section class="pdp-related-section">
            <div class="container">
                <div class="section-head" style="text-align:left; margin-bottom: 24px;">
                    <span class="eyebrow">Complete Your Ensemble</span>
                    <h2 class="section-title">You May Also Like</h2>
                </div>
                <div class="product-grid">
                    @foreach($relatedProducts as $rel)
                        @php
                            $relEffectivePrice = $rel->sale_price ?? $rel->regular_price;
                            $relHasDiscount = $rel->sale_price && $rel->sale_price < $rel->regular_price;
                            $relDiscountPercent = $relHasDiscount ? round((($rel->regular_price - $rel->sale_price) / $rel->regular_price) * 100) : 0;
                            $relSavings = $relHasDiscount ? ($rel->regular_price - $rel->sale_price) : 0;
                        @endphp
                        <div class="product-card">
                            <div class="product-thumb">
                                @if($rel->badge)
                                    <span class="product-badge">{{ $rel->badge }}</span>
                                @endif
                                @if($relDiscountPercent > 0)
                                    <span class="discount-tag">-{{ $relDiscountPercent }}%</span>
                                @endif

                                <a href="{{ route('product.show', $rel->slug) }}" class="product-thumb-link">
                                    <img src="{{ asset($rel->thumbnail) }}" alt="{{ $rel->title }}" loading="lazy">
                                </a>

                                <div class="product-thumb-actions">
                                    <a href="{{ route('product.show', $rel->slug) }}" class="thumb-action-btn" title="View Details">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                </div>
                            </div>

                            <div class="product-info">
                                @if($rel->category)
                                    <a href="{{ route('shop.index', ['category' => $rel->category->slug]) }}" class="product-cat">
                                        {{ $rel->category->name }}
                                    </a>
                                @else
                                    <span class="product-cat">Collection</span>
                                @endif

                                <h3 class="product-title">
                                    <a href="{{ route('product.show', $rel->slug) }}">{{ $rel->title }}</a>
                                </h3>

                                <div class="product-price-row">
                                    <span class="price-current">৳{{ number_format($relEffectivePrice) }}</span>
                                    @if($relHasDiscount)
                                        <span class="price-old">৳{{ number_format($rel->regular_price) }}</span>
                                        <span class="price-save">Save ৳{{ number_format($relSavings) }}</span>
                                    @endif
                                </div>

                                <div class="product-card-btns">
                                    <button type="button" class="btn-card-add" onclick="addToCartAjax({{ $rel->id }})">
                                        <i class="fa-solid fa-bag-shopping"></i>
                                        <span>Add</span>
                                    </button>
                                    <a href="{{ route('checkout.index', ['buy_now_product_id' => $rel->id]) }}" class="btn-card-buy">
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
    @endif

    <!-- Size Chart Modal -->
    @if($product->size_chart_html)
        <div id="size-chart-modal" class="pdp-modal-overlay">
            <div class="pdp-modal-card">
                <button type="button" id="close-size-chart" class="pdp-modal-close"
                    aria-label="Close size chart">&times;</button>
                <div class="pdp-modal-head">
                    <i class="fa-solid fa-ruler-combined"></i>
                    <h3>Size Measurement Guide</h3>
                </div>
                <div class="pdp-modal-body">
                    {!! $product->size_chart_html !!}
                </div>
            </div>
        </div>
    @endif

    <!-- Fullscreen Image Lightbox Modal -->
    <div id="pdp-lightbox-modal" class="pdp-lightbox-overlay">
        <button type="button" id="pdp-lightbox-close" class="pdp-lightbox-close"
            aria-label="Close fullscreen">&times;</button>
        <img id="pdp-lightbox-img" src="" alt="Fullscreen view">
    </div>

    <!-- Mobile Floating Sticky CTA Bar (Visible on scroll) -->
    <div class="pdp-mobile-sticky-bar" id="pdpMobileStickyBar">
        <div class="sticky-thumb-info">
            <img src="{{ asset($product->thumbnail) }}" alt="{{ $product->title }}">
            <div>
                <strong>৳{{ number_format($product->effective_price) }}</strong>
                <small id="sticky-size-badge">{{ $product->sizes ? 'Size: ' . $product->sizes[0] : 'Standard' }}</small>
            </div>
        </div>
        <div class="sticky-actions">
            <button type="button" class="btn btn-outline btn-sm" onclick="addCurrentProductToCart()">
                <i class="fa-solid fa-bag-shopping"></i> Bag
            </button>
            <button type="button" class="btn btn-accent btn-sm" onclick="buyNowDirect()">
                <i class="fa-solid fa-bolt-lightning"></i> Buy Now
            </button>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        const pdpProductPrice = {{ $product->effective_price }};
        let pdpSelectedSize = '{{ $product->sizes ? $product->sizes[0] : "" }}';
        let pdpQuantity = 1;

        function handlePdpSizeChange(size) {
            pdpSelectedSize = size;
            const display = document.getElementById('selected-size-display');
            const stickyBadge = document.getElementById('sticky-size-badge');

            if (display) display.innerText = size;
            if (stickyBadge) stickyBadge.innerText = 'Size: ' + size;
        }

        function adjustPdpQty(delta) {
            const input = document.getElementById('pdp-quantity-input');
            if (!input) return;

            let val = parseInt(input.value) || 1;
            val = Math.max(1, Math.min(10, val + delta));
            input.value = val;
            pdpQuantity = val;
        }

        function addCurrentProductToCart() {
            addToCartAjax({{ $product->id }}, pdpQuantity, pdpSelectedSize);
        }

        function buyNowDirect() {
            let url = '{{ route("checkout.index") }}?buy_now={{ $product->id }}&quantity=' + pdpQuantity;
            if (pdpSelectedSize) {
                url += '&size=' + encodeURIComponent(pdpSelectedSize);
            }
            window.location.href = url;
        }
    </script>
@endpush