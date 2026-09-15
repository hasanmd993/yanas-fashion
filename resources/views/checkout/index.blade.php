@extends('layouts.app')

@section('title', "Secure Checkout — Luxury Fashion Dhaka | Yanas Fashion")

@section('content')

    <!-- Breadcrumb & Security Bar -->
    <div class="checkout-header-bar">
        <div class="container">
            <div class="checkout-header-inner">
                <div>
                    <nav class="checkout-breadcrumb" aria-label="breadcrumb">
                        <a href="{{ route('home') }}"><i class="fa-solid fa-house"></i> Home</a>
                        <span class="sep">/</span>
                        <a href="{{ route('cart.index') }}">Shopping Bag</a>
                        <span class="sep">/</span>
                        <span class="current">Checkout</span>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Checkout Section -->
    <section class="checkout-section">
        <div class="container">
            <form action="{{ route('checkout.store') }}" method="POST" id="main-checkout-form" class="checkout-form">
                @csrf

                @if(request()->filled('buy_now'))
                    <input type="hidden" name="buy_now_product_id" value="{{ request()->query('buy_now') }}">
                    <input type="hidden" name="buy_now_size" value="{{ request()->query('size', '') }}">
                    <input type="hidden" name="quantity" value="{{ request()->query('quantity', 1) }}">
                @endif

                <div class="checkout-layout-grid">

                    <!-- Left Column: 3 Step Form Cards -->
                    <div class="checkout-main-col">

                        <!-- Step 1: Customer Shipping Information -->
                        <div class="checkout-step-card">
                            <div class="step-card-head">
                                <div class="step-badge">1</div>
                                <div>
                                    <h2>Delivery Address & Contact</h2>
                                    <p>Please provide your shipping and contact information</p>
                                </div>
                            </div>

                            <div class="step-card-body">
                                <div class="checkout-form-grid">
                                    <!-- Name -->
                                    <div class="form-group">
                                        <label for="customer_name">Full Name <span class="req">*</span></label>
                                        <div class="input-with-icon">
                                            <i class="fa-regular fa-user"></i>
                                            <input type="text" name="customer_name" id="customer_name" class="form-control"
                                                placeholder="e.g. Tanvir Ahmed" required value="{{ old('customer_name') }}">
                                        </div>
                                        @error('customer_name')
                                            <small class="form-error-msg">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <!-- Phone -->
                                    <div class="form-group">
                                        <label for="customer_phone">Mobile Number <span class="req">*</span> (11
                                            digits)</label>
                                        <div class="input-with-icon">
                                            <i class="fa-solid fa-phone"></i>
                                            <input type="tel" name="customer_phone" id="customer_phone" class="form-control"
                                                placeholder="017XXXXXXXX" required pattern="^(?:\+88|88)?(01[3-9]\d{8})$"
                                                value="{{ old('customer_phone') }}">
                                        </div>
                                        <small class="input-hint">For order verification and courier delivery
                                            updates</small>
                                        @error('customer_phone')
                                            <small class="form-error-msg">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Full Address -->
                                <div class="form-group">
                                    <label for="customer_address">Complete Delivery Address <span
                                            class="req">*</span></label>
                                    <div class="input-with-icon textarea-icon">
                                        <i class="fa-solid fa-location-dot"></i>
                                        <textarea name="customer_address" id="customer_address" class="form-control"
                                            rows="2"
                                            placeholder="House No, Road No, Sector/Block, Area, Thana and District..."
                                            required>{{ old('customer_address') }}</textarea>
                                    </div>
                                    @error('customer_address')
                                        <small class="form-error-msg">{{ $message }}</small>
                                    @enderror
                                </div>

                                <!-- Delivery Note -->
                                <div class="form-group" style="margin-bottom: 0;">
                                    <label for="customer_note">Order Instructions <span
                                            class="opt">(Optional)</span></label>
                                    <div class="input-with-icon">
                                        <i class="fa-regular fa-message"></i>
                                        <input type="text" name="customer_note" id="customer_note" class="form-control"
                                            placeholder="e.g. Gate code, preferred delivery time or call before delivery"
                                            value="{{ old('customer_note') }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: Delivery Zone Selection -->
                        <div class="checkout-step-card">
                            <div class="step-card-head">
                                <div class="step-badge">2</div>
                                <div>
                                    <h2>Select Delivery Location</h2>
                                    <p>Choose your area for exact shipping rates and timelines</p>
                                </div>
                            </div>

                            <div class="step-card-body">
                                <div class="checkout-zones-grid">
                                    <!-- Inside Dhaka -->
                                    <label class="checkout-zone-card">
                                        <input type="radio" name="delivery_zone" value="inside_dhaka" checked
                                            onchange="updateCheckoutZoneFee('inside_dhaka')">
                                        <div class="zone-card-box">
                                            <div class="zone-radio-indicator"></div>
                                            <div class="zone-details">
                                                <div class="zone-name">
                                                    <strong>Inside Dhaka City</strong>
                                                    <span class="zone-time-badge">⚡ 24–48 Hours</span>
                                                </div>
                                                <p>All areas within Dhaka metropolitan area</p>
                                            </div>
                                            <div class="zone-price-tag">৳{{ number_format($insideDhaka) }}</div>
                                        </div>
                                    </label>

                                    <!-- Dhaka Suburbs -->
                                    <label class="checkout-zone-card">
                                        <input type="radio" name="delivery_zone" value="dhaka_suburbs" {{ old('delivery_zone') == 'dhaka_suburbs' ? 'checked' : '' }}
                                            onchange="updateCheckoutZoneFee('dhaka_suburbs')">
                                        <div class="zone-card-box">
                                            <div class="zone-radio-indicator"></div>
                                            <div class="zone-details">
                                                <div class="zone-name">
                                                    <strong>Dhaka Suburbs</strong>
                                                    <span class="zone-time-badge">🚚 24–48 Hours</span>
                                                </div>
                                                <p>Savar, Gazipur, Keraniganj, Narayanganj</p>
                                            </div>
                                            <div class="zone-price-tag">৳{{ number_format($suburbs) }}</div>
                                        </div>
                                    </label>

                                    <!-- Outside Dhaka -->
                                    <label class="checkout-zone-card">
                                        <input type="radio" name="delivery_zone" value="outside_dhaka" {{ old('delivery_zone') == 'outside_dhaka' ? 'checked' : '' }}
                                            onchange="updateCheckoutZoneFee('outside_dhaka')">
                                        <div class="zone-card-box">
                                            <div class="zone-radio-indicator"></div>
                                            <div class="zone-details">
                                                <div class="zone-name">
                                                    <strong>Outside Dhaka (Nationwide)</strong>
                                                    <span class="zone-time-badge">📦 48–72 Hours</span>
                                                </div>
                                                <p>All other 63 districts across Bangladesh</p>
                                            </div>
                                            <div class="zone-price-tag">৳{{ number_format($outsideDhaka) }}</div>
                                        </div>
                                    </label>
                                </div>
                                @error('delivery_zone')
                                    <small class="form-error-msg">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <!-- Step 3: Payment Method Selection -->
                        <div class="checkout-step-card">
                            <div class="step-card-head">
                                <div class="step-badge">3</div>
                                <div>
                                    <h2>Payment Method</h2>
                                    <p>Select your preferred method to complete your purchase</p>
                                </div>
                            </div>

                            <div class="step-card-body">
                                <div class="checkout-payment-grid">
                                    <!-- Cash on Delivery -->
                                    <label class="checkout-payment-card">
                                        <input type="radio" name="payment_method" value="cod" checked>
                                        <div class="payment-card-box">
                                            <div class="payment-card-left">
                                                <div class="zone-radio-indicator"></div>
                                                <div class="payment-icon-wrap icon-cod">
                                                    <i class="fa-solid fa-hand-holding-dollar"></i>
                                                </div>
                                                <div>
                                                    <div class="payment-title">
                                                        <strong>Cash on Delivery (COD)</strong>
                                                        <span class="rec-badge">Recommended</span>
                                                    </div>
                                                    <p>Pay cash upon doorstep inspection of your package</p>
                                                </div>
                                            </div>
                                            <i class="fa-solid fa-circle-check payment-check-icon"></i>
                                        </div>
                                    </label>

                                    <!-- bKash / Nagad / Online -->
                                    <label class="checkout-payment-card">
                                        <input type="radio" name="payment_method" value="bkash">
                                        <div class="payment-card-box">
                                            <div class="payment-card-left">
                                                <div class="zone-radio-indicator"></div>
                                                <div class="payment-icon-wrap icon-bkash">
                                                    <i class="fa-solid fa-mobile-screen-button"></i>
                                                </div>
                                                <div>
                                                    <div class="payment-title">
                                                        <strong>bKash / Nagad / Mobile Banking</strong>
                                                    </div>
                                                    <p>Pay after order placement via bKash / Nagad account</p>
                                                </div>
                                            </div>
                                            <i class="fa-solid fa-circle-check payment-check-icon"></i>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Right Column: Sticky Order Summary -->
                    <div class="checkout-sidebar-col">
                        <div class="checkout-sticky-summary">
                            <div class="checkout-summary-card">
                                <div class="summary-head">
                                    <h3>Order Summary</h3>
                                    <span class="summary-count-badge">{{ count($cart) }}
                                        {{ count($cart) === 1 ? 'item' : 'items' }}</span>
                                </div>

                                <!-- Items List -->
                                <div class="checkout-items-scroll">
                                    @foreach($cart as $item)
                                        <div class="checkout-item-row">
                                            <div class="item-img-wrap">
                                                <img src="{{ asset($item['thumbnail']) }}" alt="{{ $item['title'] }}">
                                                <span class="item-qty-badge">{{ $item['quantity'] }}</span>
                                            </div>
                                            <div class="item-info">
                                                <div class="item-title">{{ $item['title'] }}</div>
                                                @if(!empty($item['size']))
                                                    <div class="item-size-chip">Size: <strong>{{ $item['size'] }}</strong></div>
                                                @endif
                                                <div class="item-unit-price">৳{{ number_format($item['price']) }} &times;
                                                    {{ $item['quantity'] }}
                                                </div>
                                            </div>
                                            <div class="item-total-price">
                                                ৳{{ number_format($item['price'] * $item['quantity']) }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Coupon Code Box -->
                                <div class="checkout-coupon-box">
                                    <label for="coupon-code-input"><i class="fa-solid fa-tag"></i> Have a Promo
                                        Code?</label>
                                    <div class="coupon-input-wrap">
                                        <input type="text" id="coupon-code-input" placeholder="e.g. YANAS10"
                                            value="{{ $appliedCoupon ? $appliedCoupon['code'] : '' }}">
                                        <button type="button" class="btn-coupon-apply"
                                            onclick="applyCouponCode()">Apply</button>
                                    </div>
                                    <div id="coupon-status-msg" class="coupon-msg"></div>
                                </div>

                                <!-- Totals Breakdown -->
                                <div class="checkout-totals-box">
                                    <div class="totals-row">
                                        <span>Bag Subtotal:</span>
                                        <strong id="checkout-subtotal"
                                            data-amount="{{ $subtotal }}">৳{{ number_format($subtotal) }}</strong>
                                    </div>

                                    <div class="totals-row">
                                        <span>Delivery Fee:</span>
                                        <strong id="checkout-delivery-fee">৳{{ number_format($insideDhaka) }}</strong>
                                    </div>

                                    @if($discount > 0)
                                        <div class="totals-row discount-row">
                                            <span>Coupon Discount:</span>
                                            <strong id="checkout-discount-amount"
                                                data-amount="{{ $discount }}">-৳{{ number_format($discount) }}</strong>
                                        </div>
                                    @endif

                                    <div class="totals-grand-row">
                                        <div class="grand-label">
                                            <strong>Total Amount Payable:</strong>
                                            <small>(Cash on Delivery)</small>
                                        </div>
                                        <div class="grand-amount" id="checkout-grand-total">
                                            ৳{{ number_format(max(0, $subtotal + $insideDhaka - $discount)) }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <button type="submit" class="btn btn-accent btn-checkout-submit" id="btnConfirmOrder">
                                    <i class="fa-solid fa-lock"></i>
                                    <span>Confirm Order</span>
                                    <i class="fa-solid fa-arrow-right"></i>
                                </button>

                                <!-- Trust Guarantees 3-Grid -->
                                <div class="checkout-trust-grid">
                                    <div class="trust-item">
                                        <i class="fa-solid fa-hand-holding-dollar text-primary"></i>
                                        <span>Doorstep Inspection</span>
                                    </div>
                                    <div class="trust-item">
                                        <i class="fa-solid fa-rotate-left text-accent"></i>
                                        <span>7-Day Easy Exchange</span>
                                    </div>
                                    <div class="trust-item">
                                        <i class="fa-solid fa-shield-halved text-success"></i>
                                        <span>100% Genuine Quality</span>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </section>

@endsection

@push('scripts')
    <script>
        const subtotalAmount = {{ $subtotal }};
        const freeShippingThreshold = {{ $freeShippingThreshold ?? 3000 }};
        let discountAmount = {{ $discount ?? 0 }};

        function updateCheckoutZoneFee(zone) {
            let fee = 70;
            if (zone === 'inside_dhaka') fee = 70;
            else if (zone === 'dhaka_suburbs') fee = 100;
            else if (zone === 'outside_dhaka') fee = 130;

            const feeEl = document.getElementById('checkout-delivery-fee');
            const grandTotalEl = document.getElementById('checkout-grand-total');

            if (subtotalAmount >= freeShippingThreshold && zone === 'inside_dhaka') {
                fee = 0;
                if (feeEl) feeEl.innerHTML = '<span style="color:#0b6832; font-weight:800;">FREE</span>';
            } else {
                if (feeEl) feeEl.innerText = '৳' + fee;
            }

            const grandTotal = Math.max(0, subtotalAmount + fee - discountAmount);
            if (grandTotalEl) grandTotalEl.innerText = '৳' + grandTotal.toLocaleString();
        }

        // Anti-double-click on order submission
        const checkoutForm = document.getElementById('main-checkout-form');
        const submitBtn = document.getElementById('btnConfirmOrder');
        if (checkoutForm && submitBtn) {
            checkoutForm.addEventListener('submit', function () {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Processing Order...';
            });
        }
    </script>
@endpush