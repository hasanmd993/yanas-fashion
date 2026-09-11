<!-- Start Vristo Admin Header -->
<header class="z-40 sticky top-0 bg-white dark:bg-[#0e1726] border-b border-gray-200 dark:border-[#192a43] shadow-sm">
    <div class="flex items-center justify-between px-4 sm:px-6 py-3">
        
        <!-- Left: Toggle Sidebar & Page Context -->
        <div class="flex items-center gap-3 sm:gap-4">
            <button type="button" 
                    class="flex h-9 w-9 sm:h-10 sm:w-10 items-center justify-center rounded-lg bg-gray-100 hover:bg-gray-200 dark:bg-[#1b2e4b] dark:hover:bg-[#253d63] text-gray-600 dark:text-gray-300 transition-all"
                    @click="$store.app.toggleSidebar()">
                <i class="fa-solid fa-bars-staggered text-lg"></i>
            </button>

            <div class="hidden sm:flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                <span class="font-bold text-gray-800 dark:text-white">Admin Control Center</span>
                <span>/</span>
                <span>Yanas Fashion</span>
            </div>
        </div>

        <!-- Right: Actions, Theme Mode, Notifications & Profile -->
        <div class="flex items-center gap-2 sm:gap-3">
            
            <!-- Fullscreen Screen Mode Toggle -->
            <div x-data="{ isFullScreen: false }">
                <button type="button" 
                        class="flex h-9 w-9 sm:h-10 sm:w-10 items-center justify-center rounded-full bg-gray-100 hover:bg-gray-200 text-gray-700 dark:bg-[#1b2e4b] dark:hover:bg-[#253d63] dark:text-gray-300 transition-all"
                        @click="if (document.fullscreenElement) { document.exitFullscreen(); isFullScreen = false; } else { document.documentElement.requestFullscreen().catch(()=>{}); isFullScreen = true; }" 
                        title="Toggle Fullscreen Screen Mode">
                    <i x-show="!isFullScreen" class="fa-solid fa-expand text-sm sm:text-base"></i>
                    <i x-show="isFullScreen" class="fa-solid fa-compress text-sm sm:text-base text-primary"></i>
                </button>
            </div>

            <!-- Dark / Light Mode Switcher -->
            <div>
                <!-- When in Light mode, click to go Dark -->
                <button type="button" x-show="!$store.app.isDarkMode" 
                        class="flex h-9 w-9 sm:h-10 sm:w-10 items-center justify-center rounded-full bg-gray-100 hover:bg-gray-200 text-gray-700 dark:bg-[#1b2e4b] dark:hover:bg-[#253d63] dark:text-gray-300 transition-all"
                        @click="$store.app.toggleTheme('dark')" title="Switch to Dark Mode">
                    <i class="fa-solid fa-moon text-sm sm:text-base text-gray-700"></i>
                </button>

                <!-- When in Dark mode, click to go Light -->
                <button type="button" x-show="$store.app.isDarkMode" 
                        class="flex h-9 w-9 sm:h-10 sm:w-10 items-center justify-center rounded-full bg-gray-100 hover:bg-gray-200 text-gray-700 dark:bg-[#1b2e4b] dark:hover:bg-[#253d63] dark:text-gray-300 transition-all"
                        @click="$store.app.toggleTheme('light')" title="Switch to Light Mode">
                    <i class="fa-solid fa-sun text-sm sm:text-base text-amber-400"></i>
                </button>
            </div>

            <!-- Notifications Dropdown with Number Badge -->
            @php 
                $pendingOrders = \App\Models\Order::where('order_status', 'pending')->latest()->take(6)->get(); 
            @endphp
            <div class="relative" x-data="{ open: false }">
                <button type="button" 
                        class="relative flex h-9 w-9 sm:h-10 sm:w-10 items-center justify-center rounded-full bg-gray-100 hover:bg-gray-200 dark:bg-[#1b2e4b] dark:hover:bg-[#253d63] text-gray-700 dark:text-gray-300 transition-all"
                        @click="open = !open" @click.outside="open = false" title="Notifications">
                    <i class="fa-regular fa-bell text-sm sm:text-base"></i>
                    @if($pendingOrders->count() > 0)
                        <span class="absolute -top-1 -right-1 flex h-[18px] min-w-[18px] items-center justify-center rounded-full bg-secondary px-1 text-[10px] font-black text-white shadow-sm ring-2 ring-white dark:ring-[#0e1726]">
                            {{ $pendingOrders->count() }}
                        </span>
                        <span class="absolute -top-1 -right-1 h-[18px] min-w-[18px] rounded-full bg-secondary animate-ping opacity-75"></span>
                    @endif
                </button>

                <!-- Dropdown Menu -->
                <div x-cloak x-show="open" 
                     class="absolute right-0 mt-2 w-[calc(100vw-2rem)] max-w-sm sm:w-96 rounded-2xl bg-white dark:bg-[#1b2e4b] shadow-2xl border border-gray-100 dark:border-[#192a43] py-2 z-50 animate__animated animate__fadeIn">
                    
                    <div class="px-4 py-3 border-b border-gray-100 dark:border-[#192a43] flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-bell text-secondary text-sm"></i>
                            <span class="font-extrabold text-xs text-gray-800 dark:text-white uppercase tracking-wider">Order Notifications</span>
                        </div>
                        <span class="badge badge-secondary text-[10px]">{{ $pendingOrders->count() }} Pending</span>
                    </div>
                    
                    <div class="max-h-72 overflow-y-auto divide-y divide-gray-100 dark:divide-[#192a43]">
                        @forelse($pendingOrders as $po)
                            <a href="{{ route('admin.orders.show', $po->id) }}" class="block px-4 py-3 hover:bg-gray-50 dark:hover:bg-[#14233c] transition-all">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-xs text-primary dark:text-primary-light">#{{ $po->order_number }}</span>
                                    <span class="text-[10px] text-gray-400">{{ $po->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="text-xs font-semibold text-gray-800 dark:text-gray-200 mt-1">{{ $po->customer_name }}</div>
                                <div class="text-[11px] text-gray-500 dark:text-gray-400 font-bold mt-0.5">৳{{ number_format($po->total_amount) }} • {{ $po->zone_label }}</div>
                            </a>
                        @empty
                            <div class="px-4 py-8 text-center text-xs text-gray-400">
                                <i class="fa-solid fa-bell-slash text-2xl mb-2 block opacity-40"></i>
                                No pending notifications right now.
                            </div>
                        @endforelse
                    </div>

                    <div class="p-2 border-t border-gray-100 dark:border-[#192a43] text-center bg-gray-50/50 dark:bg-[#14233c]/50">
                        <a href="{{ route('admin.orders.index') }}" class="text-xs font-bold text-primary dark:text-primary-light hover:underline inline-flex items-center gap-1">
                            <span>Manage All Orders</span>
                            <i class="fa-solid fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Storefront Quick Link -->
            <a href="{{ route('home') }}" target="_blank" class="hidden sm:inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-primary-light text-primary font-bold text-xs hover:bg-primary hover:text-white transition-all">
                <i class="fa-solid fa-store"></i>
                <span>Store</span>
            </a>

            <!-- User Profile & Logout Dropdown -->
            <div class="relative" x-data="{ open: false }">
                <button type="button" 
                        class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-[#1b2e4b] transition-all"
                        @click="open = !open" @click.outside="open = false">
                    <div class="h-8 w-8 rounded-full bg-primary text-white flex items-center justify-center font-bold text-xs">
                        {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                    </div>
                    <div class="hidden md:block text-left">
                        <span class="block text-xs font-bold text-gray-800 dark:text-white leading-tight">{{ auth()->user()->name ?? 'Admin Manager' }}</span>
                        <span class="block text-[10px] text-gray-400 leading-tight">{{ auth()->user()->email ?? 'admin@yanasfashion.com' }}</span>
                    </div>
                    <i class="fa-solid fa-chevron-down text-[10px] text-gray-400"></i>
                </button>

                <!-- Profile Dropdown -->
                <div x-cloak x-show="open" 
                     class="absolute right-0 mt-2 w-52 rounded-xl bg-white dark:bg-[#1b2e4b] shadow-xl border border-gray-100 dark:border-[#192a43] py-2 z-50 animate__animated animate__fadeIn">
                    <div class="px-4 py-2 border-b border-gray-100 dark:border-[#192a43]">
                        <span class="block text-xs font-bold text-gray-800 dark:text-white">{{ auth()->user()->name ?? 'Admin' }}</span>
                        <span class="block text-[11px] text-gray-400 font-mono">{{ auth()->user()->email ?? '' }}</span>
                    </div>

                    <a href="{{ route('admin.settings.index') }}" class="flex items-center gap-2 px-4 py-2.5 text-xs text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-[#14233c] transition-all">
                        <i class="fa-solid fa-sliders text-xs text-gray-400"></i>
                        <span>Settings</span>
                    </a>

                    <div class="border-t border-gray-100 dark:border-[#192a43] my-1"></div>

                    <!-- Logout Button -->
                    <form action="{{ route('tyro-login.logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-2 px-4 py-2.5 text-xs text-danger hover:bg-danger-light dark:hover:bg-danger-dark-light transition-all text-left font-bold">
                            <i class="fa-solid fa-right-from-bracket text-xs"></i>
                            <span>Sign Out</span>
                        </button>
                    </form>
                </div>
            </div>

        </div>

    </div>
</header>

