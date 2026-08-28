<div :class="{'dark text-white-dark' : $store.app.semidark}">
    <nav class="sidebar fixed bottom-0 top-0 z-50 h-full min-h-screen w-[260px] -left-[260px] lg:left-0 shadow-[5px_0_25px_0_rgba(94,92,154,0.1)] transition-all duration-300 bg-white dark:bg-[#0e1726]" :class="{'!left-0': $store.app.sidebar}">
        
        <!-- Sidebar Brand Header -->
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 dark:border-[#192a43]">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
                <div class="h-9 w-9 rounded-lg bg-primary flex items-center justify-center text-white font-serif font-black text-xl shadow-md">
                    Y
                </div>
                <div>
                    <span class="text-xl font-bold font-serif tracking-tight text-primary dark:text-white">Yana's<span class="text-secondary">.</span></span>
                    <span class="block text-[10px] uppercase font-bold tracking-widest text-gray-400 dark:text-gray-500">Admin Control</span>
                </div>
            </a>
            
            <button type="button" class="lg:hidden text-gray-500 hover:text-primary dark:text-gray-400" @click="$store.app.toggleSidebar()">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <!-- Sidebar Navigation Links -->
        <div class="h-[calc(100vh-80px)] overflow-y-auto px-4 py-4">
            <ul class="space-y-1 font-semibold text-gray-600 dark:text-gray-400">
                
                <!-- Section: Main -->
                <li class="px-3 pt-2 pb-1 text-[11px] font-extrabold uppercase tracking-wider text-gray-400 dark:text-gray-500">
                    Main Overview
                </li>

                <!-- Dashboard -->
                <li>
                    <a href="{{ route('admin.dashboard') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-primary text-white shadow-md font-bold' : 'hover:bg-primary-light hover:text-primary dark:hover:bg-[#1b2e4b] dark:hover:text-white' }}">
                        <i class="fa-solid fa-chart-pie w-5 text-center text-base"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <!-- Section: E-Commerce Operations -->
                <li class="px-3 pt-5 pb-1 text-[11px] font-extrabold uppercase tracking-wider text-gray-400 dark:text-gray-500">
                    E-Commerce Operations
                </li>

                <!-- Orders -->
                <li>
                    <a href="{{ route('admin.orders.index') }}" 
                       class="flex items-center justify-between px-3.5 py-2.5 rounded-lg transition-all {{ request()->routeIs('admin.orders.*') ? 'bg-primary text-white shadow-md font-bold' : 'hover:bg-primary-light hover:text-primary dark:hover:bg-[#1b2e4b] dark:hover:text-white' }}">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-bag-shopping w-5 text-center text-base"></i>
                            <span>Orders (অর্ডারসমূহ)</span>
                        </div>
                        @php $pendingCount = \App\Models\Order::where('order_status', 'pending')->count(); @endphp
                        @if($pendingCount > 0)
                            <span class="badge badge-secondary text-[10px] px-2 py-0.5">{{ $pendingCount }}</span>
                        @endif
                    </a>
                </li>

                <!-- Products -->
                <li>
                    <a href="{{ route('admin.products.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg transition-all {{ request()->routeIs('admin.products.*') ? 'bg-primary text-white shadow-md font-bold' : 'hover:bg-primary-light hover:text-primary dark:hover:bg-[#1b2e4b] dark:hover:text-white' }}">
                        <i class="fa-solid fa-shirt w-5 text-center text-base"></i>
                        <span>Products (পণ্য তালিকা)</span>
                    </a>
                </li>

                <!-- Categories -->
                <li>
                    <a href="{{ route('admin.categories.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg transition-all {{ request()->routeIs('admin.categories.*') ? 'bg-primary text-white shadow-md font-bold' : 'hover:bg-primary-light hover:text-primary dark:hover:bg-[#1b2e4b] dark:hover:text-white' }}">
                        <i class="fa-solid fa-folder-tree w-5 text-center text-base"></i>
                        <span>Categories (ক্যাটাগরি)</span>
                    </a>
                </li>

                <!-- Hero Sliders -->
                <li>
                    <a href="{{ route('admin.sliders.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg transition-all {{ request()->routeIs('admin.sliders.*') ? 'bg-primary text-white shadow-md font-bold' : 'hover:bg-primary-light hover:text-primary dark:hover:bg-[#1b2e4b] dark:hover:text-white' }}">
                        <i class="fa-solid fa-images w-5 text-center text-base"></i>
                        <span>Hero Sliders (স্লাইডার)</span>
                    </a>
                </li>

                <!-- Coupons & Discounts -->
                <li>
                    <a href="{{ route('admin.coupons.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg transition-all {{ request()->routeIs('admin.coupons.*') ? 'bg-primary text-white shadow-md font-bold' : 'hover:bg-primary-light hover:text-primary dark:hover:bg-[#1b2e4b] dark:hover:text-white' }}">
                        <i class="fa-solid fa-ticket w-5 text-center text-base"></i>
                        <span>Coupons (কুপন ও অফার)</span>
                    </a>
                </li>

                <!-- Section: Management & Settings -->
                <li class="px-3 pt-5 pb-1 text-[11px] font-extrabold uppercase tracking-wider text-gray-400 dark:text-gray-500">
                    Configuration
                </li>

                <!-- Settings -->
                <li>
                    <a href="{{ route('admin.settings.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg transition-all {{ request()->routeIs('admin.settings.*') ? 'bg-primary text-white shadow-md font-bold' : 'hover:bg-primary-light hover:text-primary dark:hover:bg-[#1b2e4b] dark:hover:text-white' }}">
                        <i class="fa-solid fa-sliders w-5 text-center text-base"></i>
                        <span>Store Settings</span>
                    </a>
                </li>

                <!-- Backups -->
                <li>
                    <a href="{{ route('admin.backups.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg transition-all {{ request()->routeIs('admin.backups.*') ? 'bg-primary text-white shadow-md font-bold' : 'hover:bg-primary-light hover:text-primary dark:hover:bg-[#1b2e4b] dark:hover:text-white' }}">
                        <i class="fa-solid fa-shield-halved w-5 text-center text-base"></i>
                        <span>System Backups (ব্যাকআপ)</span>
                    </a>
                </li>

                <!-- Live Storefront Link -->
                <li class="pt-4 mt-4 border-t border-gray-200 dark:border-[#192a43]">
                    <a href="{{ route('home') }}" target="_blank" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-secondary font-bold hover:bg-secondary-light dark:hover:bg-[#1b2e4b] transition-all">
                        <i class="fa-solid fa-arrow-up-right-from-square w-5 text-center text-base"></i>
                        <span>View Live Store</span>
                    </a>
                </li>

            </ul>
        </div>

    </nav>
</div>
