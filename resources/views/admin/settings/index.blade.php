@extends('admin.layouts.master')

@section('title', 'Store Settings — Admin')

@section('content')

    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-extrabold text-gray-800 dark:text-white">Store Settings</h1>
        <p class="text-xs text-gray-500 dark:text-gray-400">Configure delivery charges, hotline contacts, and notice banners</p>
    </div>

    <div class="panel max-w-4xl">
        <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-8 text-xs">
            @csrf

            <!-- Section 1: Delivery Charges -->
            <div>
                <h3 class="text-sm font-extrabold text-primary dark:text-primary-light flex items-center gap-2 border-b border-gray-100 dark:border-[#192a43] pb-2 mb-4">
                    <i class="fa-solid fa-truck-fast"></i> Delivery Charges & Policies (ডেলিভারি চার্জ)
                </h3>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div>
                        <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">ঢাকা সিটি ডেলিভারি (৳) *</label>
                        <input type="number" name="inside_dhaka_charge" required value="{{ $settings['inside_dhaka_charge'] ?? 70 }}" 
                               class="w-full rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] p-3 text-xs font-semibold focus:border-primary focus:outline-none dark:text-white">
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">ঢাকা উপশহর ডেলিভারি (৳) *</label>
                        <input type="number" name="suburbs_charge" required value="{{ $settings['suburbs_charge'] ?? 100 }}" 
                               class="w-full rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] p-3 text-xs font-semibold focus:border-primary focus:outline-none dark:text-white">
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">ঢাকার বাইরে ডেলিভারি (৳) *</label>
                        <input type="number" name="outside_dhaka_charge" required value="{{ $settings['outside_dhaka_charge'] ?? 130 }}" 
                               class="w-full rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] p-3 text-xs font-semibold focus:border-primary focus:outline-none dark:text-white">
                    </div>

                    <div class="sm:col-span-3">
                        <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">ফ্রি ডেলিভারি ন্যূনতম অর্ডার মূল্য (৳)</label>
                        <input type="number" name="free_shipping_threshold" value="{{ $settings['free_shipping_threshold'] ?? 3000 }}" 
                               class="w-full rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] p-3 text-xs font-semibold focus:border-primary focus:outline-none dark:text-white">
                        <span class="text-[11px] text-gray-400 mt-1 block">এই পরিমাণের বেশি অর্ডারে ঢাকা সিটিতে ডেলিভারি সম্পূর্ণ ফ্রি হবে।</span>
                    </div>
                </div>
            </div>

            <!-- Section 2: Contact & Hotlines -->
            <div>
                <h3 class="text-sm font-extrabold text-primary dark:text-primary-light flex items-center gap-2 border-b border-gray-100 dark:border-[#192a43] pb-2 mb-4">
                    <i class="fa-solid fa-phone"></i> Contact & Hotlines (যোগাযোগ ও সহায়তা)
                </h3>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">Hotline Phone Number *</label>
                        <input type="text" name="hotline" required value="{{ $settings['hotline'] ?? '01713580400' }}" 
                               class="w-full rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] p-3 text-xs font-semibold focus:border-primary focus:outline-none dark:text-white font-mono">
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">WhatsApp Number (Country Code সহ) *</label>
                        <input type="text" name="whatsapp_number" required value="{{ $settings['whatsapp_number'] ?? '8801713580400' }}" 
                               class="w-full rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] p-3 text-xs font-semibold focus:border-primary focus:outline-none dark:text-white font-mono">
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">Support Email</label>
                        <input type="email" name="email" value="{{ $settings['email'] ?? 'support@yanasfashion.com' }}" 
                               class="w-full rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] p-3 text-xs font-semibold focus:border-primary focus:outline-none dark:text-white">
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">Store Address</label>
                        <input type="text" name="address" value="{{ $settings['address'] ?? 'House 42, Road 11, Banani, Dhaka' }}" 
                               class="w-full rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] p-3 text-xs font-semibold focus:border-primary focus:outline-none dark:text-white">
                    </div>
                </div>
            </div>

            <!-- Section 3: Announcement & Socials -->
            <div>
                <h3 class="text-sm font-extrabold text-primary dark:text-primary-light flex items-center gap-2 border-b border-gray-100 dark:border-[#192a43] pb-2 mb-4">
                    <i class="fa-solid fa-bullhorn"></i> Announcement Banner & Socials
                </h3>

                <div class="space-y-4">
                    <div>
                        <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">Top Announcement Bar Text</label>
                        <textarea name="announcement_bar" rows="2" 
                                  class="w-full rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] p-3 text-xs font-semibold focus:border-primary focus:outline-none dark:text-white">{{ $settings['announcement_bar'] ?? '✨ Free Express Delivery in Dhaka on Orders Over ৳3,000 | 🚚 Nationwide COD Across All 64 Districts' }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <div>
                            <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">Facebook URL</label>
                            <input type="url" name="facebook_url" value="{{ $settings['facebook_url'] ?? '' }}" 
                                   class="w-full rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] p-3 text-xs font-semibold focus:border-primary focus:outline-none dark:text-white">
                        </div>
                        <div>
                            <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">Instagram URL</label>
                            <input type="url" name="instagram_url" value="{{ $settings['instagram_url'] ?? '' }}" 
                                   class="w-full rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] p-3 text-xs font-semibold focus:border-primary focus:outline-none dark:text-white">
                        </div>
                        <div>
                            <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">TikTok URL</label>
                            <input type="url" name="tiktok_url" value="{{ $settings['tiktok_url'] ?? '' }}" 
                                   class="w-full rounded-lg border border-gray-200 dark:border-[#192a43] bg-white dark:bg-[#0e1726] p-3 text-xs font-semibold focus:border-primary focus:outline-none dark:text-white">
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-100 dark:border-[#192a43] pt-6">
                <button type="submit" class="rounded-lg bg-primary px-8 py-3 text-xs font-bold text-white shadow-md hover:bg-primary-hover transition-all">
                    Save All Settings
                </button>
            </div>
        </form>
    </div>

@endsection

