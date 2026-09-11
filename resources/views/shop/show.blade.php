@extends('layouts.app')

@section('title', $product->title . " - Yanas Fashion")

@section('content')

    <!-- Breadcrumb -->
    <div style="background: var(--bg-light); border-bottom: 1px solid var(--line); padding: 16px 0;">
        <div class="container">
            <div style="display:flex; align-items:center; gap:8px; font-size:0.85rem; color:var(--text-muted);">
                <a href="{{ route('home') }}">Home</a> <span>/</span>
                <a href="{{ route('shop.index') }}">Shop</a> <span>/</span>
                <a href="{{ route('shop.index', ['category' => $product->category->slug]) }}">{{ $product->category->name }}</a> <span>/</span>
                <strong style="color:var(--dark);">{{ $product->title }}</strong>
            </div>
        </div>
    </div>

    <!-- Product Details Layout -->
    <section class="container">
        <div class="product-detail-layout">
            
            <!-- Left: Gallery Media -->
            <div>
                <div class="gallery-main">
                    @if($product->badge)
                        <span class="product-badge">{{ $product->badge }}</span>
                    @endif
                    @if($product->discount_percent > 0)
                        <span class="discount-tag">-{{ $product->discount_percent }}%</span>
                    @endif
                    <img id="product-main-image" src="{{ asset($product->thumbnail) }}" alt="{{ $product->title }}">
                </div>

                @if($product->gallery && count($product->gallery) > 1)
                    <div class="gallery-thumbs">
                        @foreach($product->gallery as $index => $img)
                            <button type="button" class="thumb-btn {{ $index === 0 ? 'active' : '' }}" data-src="{{ asset($img) }}">
                                <img src="{{ asset($img) }}" alt="Thumbnail {{ $index + 1 }}">
                            </button>
                        @endforeach
                    </div>
                @endif

                <!-- Value Guarantees Box -->
                <div style="margin-top: 24px; padding: 18px; background: var(--white); border: 1px solid var(--line); border-radius: var(--radius-md);">
                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                        <div style="display:flex; align-items:center; gap:10px; font-size:0.85rem; font-weight:600;">
                            <i class="fa-solid fa-truck" style="color:var(--primary); font-size:1.1rem;"></i>
                            <span>Nationwide Home Delivery</span>
                        </div>
                        <div style="display:flex; align-items:center; gap:10px; font-size:0.85rem; font-weight:600;">
                            <i class="fa-solid fa-hand-holding-dollar" style="color:var(--primary); font-size:1.1rem;"></i>
                            <span>Cash on Delivery (COD)</span>
                        </div>
                        <div style="display:flex; align-items:center; gap:10px; font-size:0.85rem; font-weight:600;">
                            <i class="fa-solid fa-rotate-left" style="color:var(--primary); font-size:1.1rem;"></i>
                            <span>7-Day Easy Exchange</span>
                        </div>
                        <div style="display:flex; align-items:center; gap:10px; font-size:0.85rem; font-weight:600;">
                            <i class="fa-solid fa-medal" style="color:var(--primary); font-size:1.1rem;"></i>
                            <span>100% Authentic Quality</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Product Info & Order Form -->
            <div class="product-detail-info">
                <span class="eyebrow">{{ $product->category->name }}</span>
                <h1>{{ $product->title }}</h1>
                @if($product->title_bn)
                    <div class="product-detail-bn">{{ $product->title_bn }}</div>
                @endif

                <div class="sku-stock-row">
                    <span>SKU: <strong>{{ $product->sku }}</strong></span>
                    <span>•</span>
                    <span class="in-stock-tag"><i class="fa-solid fa-circle-check"></i> In Stock</span>
                    <span>•</span>
                    <div class="rating-stars" style="margin-bottom:0;">★★★★★ ({{ $product->reviews_count }} reviews)</div>
                </div>

                <!-- Price Box -->
                <div class="detail-price-box">
                    <span class="price">৳{{ number_format($product->effective_price) }}</span>
                    @if($product->sale_price)
                        <span class="old">৳{{ number_format($product->regular_price) }}</span>
                        <span style="background:var(--accent); color:#fff; font-size:0.8rem; font-weight:800; padding:4px 8px; border-radius:var(--radius-sm);">
                            {{ $product->discount_percent }}% OFF
                        </span>
                    @endif
                </div>

                @if($product->short_desc)
                    <p style="color: var(--text-muted); font-size: 0.95rem; margin-bottom: 20px; line-height: 1.6;">
                        {{ $product->short_desc }}
                    </p>
                @endif

                <!-- Sizing Selector -->
                @if($product->sizes && count($product->sizes) > 0)
                    <div class="size-selector-wrap">
                        <div class="size-header">
                            <label>Select Size:</label>
                            @if($product->size_chart_html)
                                <button type="button" class="size-chart-trigger" id="open-size-chart">
                                    <i class="fa-solid fa-ruler-combined"></i> Size Guide
                                </button>
                            @endif
                        </div>
                        <div class="size-pills">
                            @foreach($product->sizes as $idx => $s)
                                <label class="size-pill-label">
                                    <input type="radio" name="product_size_select" value="{{ $s }}" {{ $idx === 0 ? 'checked' : '' }} onchange="updateSelectedSize('{{ $s }}')">
                                    <div class="size-pill-box">{{ $s }}</div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Cart & WhatsApp Direct Action Buttons -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 28px;">
                    <button type="button" class="btn btn-primary" onclick="addCurrentProductToCart()">
                        <i class="fa-solid fa-cart-plus"></i> Add to Cart
                    </button>
                    
                    @php
                        $waText = urlencode("Hello Yanas Fashion, I want to order '{$product->title}' (SKU: {$product->sku}) at ৳" . number_format($product->effective_price) . ".");
                    @endphp
                    <a href="https://wa.me/8801713580400?text={{ $waText }}" target="_blank" class="btn btn-whatsapp">
                        <i class="fa-brands fa-whatsapp"></i> WhatsApp Order
                    </a>
                </div>

                <!-- Embedded 1-Click Fast Direct Order Form -->
                <div class="direct-order-box">
                    <div class="direct-order-head">
                        <h3><i class="fa-solid fa-bolt" style="color:var(--accent);"></i> Fill in the form to order quickly</h3>
                    </div>

                    <form action="{{ route('checkout.store') }}" method="POST" id="direct-order-form">
                        @csrf
                        <input type="hidden" name="buy_now_product_id" value="{{ $product->id }}">
                        <input type="hidden" name="buy_now_size" id="direct-order-size" value="{{ $product->sizes ? $product->sizes[0] : '' }}">
                        <input type="hidden" name="payment_method" value="cod">

                        <!-- Name -->
                        <div class="form-group">
                            <label for="direct_name">Your Name *</label>
                            <input type="text" name="customer_name" id="direct_name" class="form-control" placeholder="Enter your full name" required value="{{ old('customer_name') }}">
                        </div>

                        <!-- Phone -->
                        <div class="form-group">
                            <label for="direct_phone">Mobile Number * (11 digits)</label>
                            <input type="tel" name="customer_phone" id="direct_phone" class="form-control" placeholder="01712345678" required pattern="^(?:\+88|88)?(01[3-9]\d{8})$" value="{{ old('customer_phone') }}">
                        </div>

                        <!-- Address -->
                        <div class="form-group">
                            <label for="direct_address">Full Address * (District, Thana, Road, House No)</label>
                            <textarea name="customer_address" id="direct_address" class="form-control" placeholder="Enter your detailed address..." required>{{ old('customer_address') }}</textarea>
                        </div>

                        <!-- Delivery Zone -->
                        <div class="form-group">
                            <label>Select Delivery Zone *</label>
                            <div class="delivery-zones-grid">
                                <label class="zone-option-label">
                                    <div>
                                        <input type="radio" name="delivery_zone" value="inside_dhaka" checked onchange="updateDirectTotal(70)">
                                        <span class="zone-title">Inside Dhaka</span>
                                    </div>
                                    <span class="zone-fee">৳70</span>
                                </label>
                                <label class="zone-option-label">
                                    <div>
                                        <input type="radio" name="delivery_zone" value="dhaka_suburbs" onchange="updateDirectTotal(100)">
                                        <span class="zone-title">Dhaka Suburbs (Savar, Gazipur, Keraniganj)</span>
                                    </div>
                                    <span class="zone-fee">৳100</span>
                                </label>
                                <label class="zone-option-label">
                                    <div>
                                        <input type="radio" name="delivery_zone" value="outside_dhaka" onchange="updateDirectTotal(130)">
                                        <span class="zone-title">Outside Dhaka</span>
                                    </div>
                                    <span class="zone-fee">৳130</span>
                                </label>
                            </div>
                        </div>

                        <!-- Direct Calculation Summary -->
                        <div style="background:var(--bg-light); border-radius:var(--radius-sm); padding:14px; margin:16px 0;">
                            <div style="display:flex; justify-content:space-between; font-size:0.9rem; margin-bottom:6px;">
                                <span>Product Price:</span>
                                <span>৳{{ number_format($product->effective_price) }}</span>
                            </div>
                            <div style="display:flex; justify-content:space-between; font-size:0.9rem; margin-bottom:6px;">
                                <span>Delivery Charge:</span>
                                <span id="direct-delivery-text">৳70</span>
                            </div>
                            <div style="display:flex; justify-content:space-between; font-size:1.15rem; font-weight:800; color:var(--primary); border-top:1px dashed var(--line); padding-top:8px;">
                                <span>Total Payable (Cash On Delivery):</span>
                                <span id="direct-grand-total">৳{{ number_format($product->effective_price + 70) }}</span>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-accent btn-block" style="font-size:1.05rem; padding:14px;">
                            <i class="fa-solid fa-lock"></i> Confirm Order
                        </button>
                    </form>
                </div>

                <!-- Product Description & Specifications -->
                @if($product->description)
                    <div style="margin-top: 36px; padding-top: 24px; border-top: 1px solid var(--line);">
                        <h3 style="font-size: 1.35rem; margin-bottom: 12px;">Product Description</h3>
                        <div style="font-size: 0.95rem; line-height: 1.7; color: var(--text);">
                            {!! $product->description !!}
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </section>

    <!-- Size Chart Modal -->
    @if($product->size_chart_html)
        <div id="size-chart-modal" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.6); z-index:3000; align-items:center; justify-content:center; padding:16px;">
            <div style="background:#fff; border-radius:var(--radius-lg); max-width:600px; width:100%; padding:28px; position:relative; box-shadow:var(--shadow-lg); max-height:90vh; overflow-y:auto;">
                <button type="button" id="close-size-chart" style="position:absolute; top:16px; right:16px; background:none; border:none; font-size:1.5rem; cursor:pointer; color:#888;">&times;</button>
                <h3 style="font-size:1.5rem; color:var(--primary); margin-bottom:14px; border-bottom:1px solid var(--line); padding-bottom:8px;">
                    <i class="fa-solid fa-ruler-combined"></i> Size Measurement Guide
                </h3>
                <div style="font-size:0.92rem; line-height:1.6;">
                    {!! $product->size_chart_html !!}
                </div>
            </div>
        </div>
    @endif

    <!-- Related Products -->
    @if($relatedProducts->isNotEmpty())
        <section style="padding: 48px 0; background: var(--white); border-top: 1px solid var(--line);">
            <div class="container">
                <div class="section-head" style="text-align:left;">
                    <span class="eyebrow">Related Products</span>
                    <h2 class="section-title">You May Also Like</h2>
                </div>
                <div class="product-grid">
                    @foreach($relatedProducts as $rel)
                        <div class="product-card">
                            <div class="product-media">
                                <a href="{{ route('product.show', $rel->slug) }}">
                                    <img src="{{ asset($rel->thumbnail) }}" alt="{{ $rel->title }}" loading="lazy">
                                </a>
                            </div>
                            <div class="product-body">
                                <a href="{{ route('product.show', $rel->slug) }}" class="product-title">{{ $rel->title }}</a>
                                <div class="price-box">
                                    <span class="current-price">৳{{ number_format($rel->effective_price) }}</span>
                                    @if($rel->sale_price)
                                        <span class="old-price">৳{{ number_format($rel->regular_price) }}</span>
                                    @endif
                                </div>
                                <div class="card-actions">
                                    <a href="{{ route('checkout.index', ['buy_now' => $rel->id]) }}" class="btn-order-now">Order Now</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

@endsection

@push('scripts')
<script>
    let currentSelectedSize = '{{ $product->sizes ? $product->sizes[0] : "" }}';
    const productPrice = {{ $product->effective_price }};

    function updateSelectedSize(size) {
        currentSelectedSize = size;
        document.getElementById('direct-order-size').value = size;
    }

    function addCurrentProductToCart() {
        addToCartAjax({{ $product->id }}, 1, currentSelectedSize);
    }

    function updateDirectTotal(fee) {
        document.getElementById('direct-delivery-text').innerText = '৳' + fee;
        document.getElementById('direct-grand-total').innerText = '৳' + (productPrice + fee).toLocaleString();
    }
</script>
@endpush
