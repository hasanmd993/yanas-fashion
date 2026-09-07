@extends('layouts.app')

@section('title', "লাইভ অর্ডার ট্র্যাকিং — Yanas Fashion")

@section('content')

    <div style="background: var(--bg-light); border-bottom: 1px solid var(--line); padding: 28px 0;">
        <div class="container" style="text-align: center; max-width: 600px;">
            <span class="eyebrow"><i class="fa-solid fa-location-crosshairs"></i> রিয়েল-টাইম ট্র্যাকিং</span>
            <h1 style="font-size: 2.2rem; margin-bottom: 8px;">আপনার অর্ডার ট্র্যাক করুন</h1>
            <p style="color: var(--text-muted); font-size: 0.95rem;">
                অর্ডার নিশ্চিতকরণের পর প্রাপ্ত অর্ডার নম্বর ও মোবাইল নম্বর দিয়ে আপনার পার্সেলের সর্বশেষ অবস্থা জানুন।
            </p>
        </div>
    </div>

    <section class="container" style="padding: 40px 16px 80px; max-width: 720px;">
        
        <!-- Search Card -->
        <div style="background: var(--white); border: 1px solid var(--line); border-radius: var(--radius-lg); padding: 28px; box-shadow: var(--shadow-sm); margin-bottom: 32px;">
            <form action="{{ route('tracking.track') }}" method="POST">
                @csrf
                <div style="display: grid; grid-template-columns: 1fr; gap: 16px;">
                    <div class="form-group">
                        <label for="order_number">অর্ডার নম্বর (Order Number) *</label>
                        <input type="text" name="order_number" id="order_number" class="form-control" placeholder="যেমন: YF-91024" required value="{{ old('order_number', request('order_number')) }}">
                    </div>

                    <div class="form-group">
                        <label for="phone">মোবাইল নম্বর (Phone Number) *</label>
                        <input type="tel" name="phone" id="phone" class="form-control" placeholder="01712345678" required value="{{ old('phone', request('phone')) }}">
                    </div>

                    <button type="submit" class="btn btn-primary btn-block" style="padding: 14px; font-size: 1rem;">
                        <i class="fa-solid fa-magnifying-glass"></i> অর্ডার ট্র্যাক করুন (Track Now)
                    </button>
                </div>
            </form>
        </div>

        <!-- Tracking Results Timeline -->
        @if(isset($order))
            <div style="background: var(--white); border: 1px solid var(--line); border-radius: var(--radius-lg); padding: 28px; box-shadow: var(--shadow-md);">
                
                <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px; border-bottom: 1px solid var(--line); padding-bottom: 16px; margin-bottom: 24px;">
                    <div>
                        <h2 style="font-size: 1.4rem; color: var(--dark);">অর্ডার #{{ $order->order_number }}</h2>
                        <span style="font-size: 0.82rem; color: var(--text-muted);">তারিখ: {{ $order->created_at->format('d M, Y h:i A') }}</span>
                    </div>

                    <div>
                        @php
                            $statusBg = match($order->order_status) {
                                'pending' => '#ff9800',
                                'processing' => '#2196f3',
                                'shipped' => '#9c27b0',
                                'delivered' => '#0db14b',
                                'cancelled' => '#f44336',
                                default => '#666',
                            };
                            $statusText = match($order->order_status) {
                                'pending' => 'অর্ডার গৃহীত হয়েছে (Pending)',
                                'processing' => 'প্যাকিং ও প্রসেসিং চলছে (Processing)',
                                'shipped' => 'কুরিয়ারে হস্তান্তর করা হয়েছে (In Transit)',
                                'delivered' => 'ডেলিভারি সম্পন্ন হয়েছে (Delivered)',
                                'cancelled' => 'অর্ডার বাতিল হয়েছে (Cancelled)',
                                default => ucfirst($order->order_status),
                            };
                        @endphp
                        <span style="background: {{ $statusBg }}; color: #fff; padding: 6px 14px; border-radius: var(--radius-full); font-size: 0.85rem; font-weight: 700;">
                            {{ $statusText }}
                        </span>
                    </div>
                </div>

                <!-- Timeline Stages -->
                @php
                    $steps = [
                        'pending' => ['title' => 'অর্ডার প্লেসড', 'desc' => 'অর্ডার সিস্টেম ভেরিফাই হয়েছে', 'icon' => 'fa-clipboard-check'],
                        'processing' => ['title' => 'প্রসেসিং ও প্যাকিং', 'desc' => 'পণ্য ওয়্যারহাউসে প্যাক করা হচ্ছে', 'icon' => 'fa-box-open'],
                        'shipped' => ['title' => 'কুরিয়ারে ডেলিভারি চলছে', 'desc' => 'পার্সেল কুরিয়ার রাইডারের কাছে', 'icon' => 'fa-truck-fast'],
                        'delivered' => ['title' => 'ডেলিভারি সম্পন্ন', 'desc' => 'গ্রাহকের হাতে পার্সেল পৌঁছেছে', 'icon' => 'fa-circle-check'],
                    ];
                    $currentIdx = match($order->order_status) {
                        'pending' => 1,
                        'processing' => 2,
                        'shipped' => 3,
                        'delivered' => 4,
                        'cancelled' => 0,
                        default => 1,
                    };
                @endphp

                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; margin: 32px 0 24px; text-align: center;">
                    @php $stepNum = 1; @endphp
                    @foreach($steps as $key => $s)
                        @php $isActive = $stepNum <= $currentIdx; @endphp
                        <div style="display: flex; flex-direction: column; align-items: center; position: relative;">
                            <div style="width: 44px; height: 44px; border-radius: 50%; background: {{ $isActive ? 'var(--primary)' : '#e0dbd5' }}; color: {{ $isActive ? '#fff' : '#888' }}; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; margin-bottom: 8px; z-index: 2; box-shadow: {{ $isActive ? '0 4px 12px rgba(115,1,99,0.3)' : 'none' }};">
                                <i class="fa-solid {{ $s['icon'] }}"></i>
                            </div>
                            <strong style="font-size: 0.8rem; color: {{ $isActive ? 'var(--dark)' : '#999' }};">{{ $s['title'] }}</strong>
                            <small style="font-size: 0.72rem; color: #888; display: none;">{{ $s['desc'] }}</small>
                        </div>
                        @php $stepNum++; @endphp
                    @endforeach
                </div>

                <!-- Order Details Brief -->
                <div style="background: var(--bg-light); border-radius: var(--radius-sm); padding: 16px; margin-top: 24px; font-size: 0.9rem;">
                    <div style="margin-bottom: 6px;"><strong>গ্রাহক:</strong> {{ $order->customer_name }} ({{ $order->customer_phone }})</div>
                    <div style="margin-bottom: 6px;"><strong>ঠিকানা:</strong> {{ $order->customer_address }}</div>
                    <div><strong>মোট মূল্য (COD):</strong> <span style="color:var(--primary); font-weight:800;">৳{{ number_format($order->total_amount) }}</span></div>
                </div>

            </div>
        @endif

    </section>

@endsection

