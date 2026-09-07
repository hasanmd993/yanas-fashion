@extends('layouts.app')

@section('title', "চেকআউট ও অর্ডার কনফার্মেশন — Yanas Fashion")

@section('content')

    <!-- Checkout Header -->
    <div style="background: var(--bg-light); border-bottom: 1px solid var(--line); padding: 24px 0;">
        <div class="container">
            <h1 style="font-size: 2rem; margin-bottom: 4px;">
                <i class="fa-solid fa-lock" style="color:var(--primary); font-size:1.6rem; margin-right:8px;"></i>
                নিরাপদ চেকআউট (Secure Fast Checkout)
            </h1>
            <p style="color:var(--text-muted); font-size:0.92rem;">
                অর্ডার নিশ্চিত করতে অনুগ্রহ করে নিচের তথ্যগুলো সঠিকভাবে প্রদান করুন।
            </p>
        </div>
    </div>

    <!-- Main Checkout Section -->
    <section class="container">
        <form action="{{ route('checkout.store') }}" method="POST" id="main-checkout-form">
            @csrf
            
            <div class="checkout-layout">
                
                <!-- Left: Customer Shipping & Payment Info -->
                <div>
                    <div class="checkout-card">
                        <h2><i class="fa-solid fa-user-pen"></i> ১. আপনার বিস্তারিত তথ্য</h2>

                        <!-- Full Name -->
                        <div class="form-group">
                            <label for="customer_name">আপনার নাম (Full Name) *</label>
                            <input type="text" name="customer_name" id="customer_name" class="form-control" placeholder="আপনার সম্পূর্ণ নাম লিখুন" required value="{{ old('customer_name') }}">
                            @error('customer_name')
                                <small style="color:var(--danger); font-weight:600;">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Phone Number -->
                        <div class="form-group">
                            <label for="customer_phone">মোবাইল নম্বর (Phone Number) *</label>
                            <input type="tel" name="customer_phone" id="customer_phone" class="form-control" placeholder="01712345678 (১১ ডিজিট)" required pattern="^(?:\+88|88)?(01[3-9]\d{8})$" value="{{ old('customer_phone') }}">
                            <small style="color:var(--text-muted); font-size:0.8rem;">অর্ডার ভেরিফিকেশন ও ডেলিভারির জন্য সঠিক মোবাইল নম্বর দিন</small>
                            @error('customer_phone')
                                <div style="color:var(--danger); font-weight:600;">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Full Address -->
                        <div class="form-group">
                            <label for="customer_address">সম্পূর্ণ ডেলিভারি ঠিকানা (Full Delivery Address) *</label>
                            <textarea name="customer_address" id="customer_address" class="form-control" placeholder="হাউস নং, রোড নং, এলাকা, থানা ও জেলা লিখুন..." required>{{ old('customer_address') }}</textarea>
                            @error('customer_address')
                                <small style="color:var(--danger); font-weight:600;">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Delivery Zone Selection -->
                        <div class="form-group" style="margin-top: 24px;">
                            <label style="font-size: 1.05rem; font-weight: 700; color: var(--primary);">
                                <i class="fa-solid fa-map-location-dot"></i> ২. ডেলিভারি এলাকা নির্বাচন করুন *
                            </label>
                            
                            <div class="delivery-zones-grid">
                                <label class="zone-option-label">
                                    <div>
                                        <input type="radio" name="delivery_zone" value="inside_dhaka" checked>
                                        <span class="zone-title">ঢাকা সিটি (Inside Dhaka)</span>
                                    </div>
                                    <span class="zone-fee">৳{{ number_format($insideDhaka) }}</span>
                                </label>

                                <label class="zone-option-label">
                                    <div>
                                        <input type="radio" name="delivery_zone" value="dhaka_suburbs" {{ old('delivery_zone') == 'dhaka_suburbs' ? 'checked' : '' }}>
                                        <span class="zone-title">ঢাকা উপশহর (সাভার, গাজীপুর, কেরানীগঞ্জ)</span>
                                    </div>
                                    <span class="zone-fee">৳{{ number_format($suburbs) }}</span>
                                </label>

                                <label class="zone-option-label">
                                    <div>
                                        <input type="radio" name="delivery_zone" value="outside_dhaka" {{ old('delivery_zone') == 'outside_dhaka' ? 'checked' : '' }}>
                                        <span class="zone-title">ঢাকার বাইরে সারা বাংলাদেশ (Outside Dhaka)</span>
                                    </div>
                                    <span class="zone-fee">৳{{ number_format($outsideDhaka) }}</span>
                                </label>
                            </div>
                            @error('delivery_zone')
                                <small style="color:var(--danger); font-weight:600;">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Payment Method -->
                        <div class="form-group" style="margin-top: 24px;">
                            <label style="font-size: 1.05rem; font-weight: 700; color: var(--primary);">
                                <i class="fa-solid fa-credit-card"></i> ৩. পেমেন্ট মেথড (Payment Method)
                            </label>

                            <div style="display:grid; grid-template-columns:1fr; gap:10px; margin-top:8px;">
                                <label class="zone-option-label">
                                    <div>
                                        <input type="radio" name="payment_method" value="cod" checked>
                                        <span class="zone-title">ক্যাশ অন ডেলিভারি (Cash On Delivery)</span>
                                    </div>
                                    <span style="font-size:0.82rem; font-weight:700; color:var(--success);">হাতে পেয়ে মূল্য পরিশোধ</span>
                                </label>

                                <label class="zone-option-label">
                                    <div>
                                        <input type="radio" name="payment_method" value="bkash">
                                        <span class="zone-title">বিকাশ / নগদ পেমেন্ট (bKash / Nagad)</span>
                                    </div>
                                    <span style="font-size:0.82rem; font-weight:700; color:#e2136e;">অর্ডার পরবর্তী পেমেন্ট</span>
                                </label>
                            </div>
                        </div>

                        <!-- Order Notes -->
                        <div class="form-group" style="margin-top: 16px;">
                            <label for="customer_note">অর্ডার সংক্রান্ত বিশেষ কোনো নোট (Optional)</label>
                            <input type="text" name="customer_note" id="customer_note" class="form-control" placeholder="যেমন: কোনো নির্দিষ্ট সময়ে ডেলিভারি প্রয়োজন হলে লিখুন" value="{{ old('customer_note') }}">
                        </div>

                    </div>
                </div>

                <!-- Right: Order Items & Subtotal Calculation -->
                <div>
                    <div class="order-summary-box">
                        <h3 style="font-size: 1.35rem; color: var(--dark); border-bottom: 1px solid var(--line); padding-bottom: 10px; margin-bottom: 16px;">
                            অর্ডার সারসংক্ষেপ (Order Summary)
                        </h3>

                        <!-- Items List -->
                        <div style="max-height: 280px; overflow-y: auto;">
                            @foreach($cart as $item)
                                <div class="summary-item-row">
                                    <img src="{{ asset($item['thumbnail']) }}" alt="{{ $item['title'] }}" class="summary-item-img">
                                    <div class="summary-item-info">
                                        <div class="summary-item-title">{{ $item['title'] }}</div>
                                        <div class="summary-item-meta">
                                            @if(!empty($item['size'])) সাইজ: {{ $item['size'] }} | @endif
                                            পরিমাণ: {{ $item['quantity'] }} টি
                                        </div>
                                    </div>
                                    <div class="summary-item-total">
                                        ৳{{ number_format($item['price'] * $item['quantity']) }}
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Coupon Code Apply Box -->
                        <div style="margin-top: 18px; padding-top: 14px; border-top: 1px solid var(--line);">
                            <label style="font-size: 0.85rem; font-weight: 700; color: var(--text);">কুপন কোড থাকলে লিখুন (Coupon Code):</label>
                            <div class="coupon-input-group">
                                <input type="text" id="coupon-code-input" placeholder="যেমন: YANA10" value="{{ $appliedCoupon ? $appliedCoupon['code'] : '' }}">
                                <button type="button" class="btn btn-primary btn-sm" onclick="applyCouponCode()">প্রয়োগ করুন</button>
                            </div>
                            <div id="coupon-status-msg" style="margin-top: 6px; font-size: 0.82rem;"></div>
                        </div>

                        <!-- Calculation Totals -->
                        <div class="summary-totals-list">
                            <div class="summary-row">
                                <span style="color:var(--text-muted);">পণ্যের মোট মূল্য (Subtotal):</span>
                                <span id="checkout-subtotal" data-amount="{{ $subtotal }}" style="font-weight:700;">৳{{ number_format($subtotal) }}</span>
                            </div>

                            <div class="summary-row">
                                <span style="color:var(--text-muted);">ডেলিভারি চার্জ (Delivery Charge):</span>
                                <span id="checkout-delivery-fee" style="font-weight:700;">৳{{ number_format($insideDhaka) }}</span>
                            </div>

                            @if($discount > 0)
                                <div class="summary-row" style="color:var(--success);">
                                    <span>কুপন ছাড় (Discount):</span>
                                    <span id="checkout-discount-amount" data-amount="{{ $discount }}" style="font-weight:700;">-৳{{ number_format($discount) }}</span>
                                </div>
                            @endif

                            <div class="summary-row grand-total">
                                <span>সর্বমোট প্রদেয় বিল:</span>
                                <span id="checkout-grand-total">৳{{ number_format(max(0, $subtotal + $insideDhaka - $discount)) }}</span>
                            </div>
                        </div>

                        <!-- Submit Order Button -->
                        <button type="submit" class="btn btn-accent btn-block" style="padding: 16px; font-size: 1.15rem; font-weight: 800; border-radius: var(--radius-sm);">
                            <i class="fa-solid fa-lock"></i> অর্ডার কনফার্ম করুন (Confirm Order)
                        </button>

                        <div style="text-align: center; margin-top: 14px; font-size: 0.8rem; color: var(--text-muted);">
                            <i class="fa-solid fa-shield-halved" style="color:var(--success);"></i> ১০০% নিরাপদ ক্যাশ অন ডেলিভারি সার্ভিস
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </section>

@endsection

