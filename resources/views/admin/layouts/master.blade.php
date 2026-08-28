<!DOCTYPE html>
<html lang="en" dir="ltr" x-data="main" :class="[$store.app.theme === 'dark' || $store.app.isDarkMode ? 'dark' : '']">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>@yield('title', 'Admin Dashboard') | {{ get_setting('app_name', "Yana's Fashion Admin") }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Google Font: Nunito -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- FontAwesome 6 Icons CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    <!-- Tailwind CSS CDN with Vristo Configuration -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        nunito: ['Nunito', 'sans-serif'],
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            DEFAULT: '#730163',
                            light: '#fbeaf7',
                            'dark-light': 'rgba(115, 1, 99, 0.15)',
                        },
                        secondary: {
                            DEFAULT: '#F68625',
                            light: '#fff4ea',
                            'dark-light': 'rgba(246, 134, 37, 0.15)',
                        },
                        success: {
                            DEFAULT: '#00ab55',
                            light: '#ddf5f0',
                            'dark-light': 'rgba(0, 171, 85, 0.15)',
                        },
                        danger: {
                            DEFAULT: '#e7515a',
                            light: '#fdeeed',
                            'dark-light': 'rgba(231, 81, 90, 0.15)',
                        },
                        warning: {
                            DEFAULT: '#e2a03f',
                            light: '#fff9ed',
                            'dark-light': 'rgba(226, 160, 63, 0.15)',
                        },
                        info: {
                            DEFAULT: '#2196f3',
                            light: '#e7f7ff',
                            'dark-light': 'rgba(33, 150, 243, 0.15)',
                        },
                        dark: {
                            DEFAULT: '#0e1726',
                            light: '#eaeaec',
                            'dark-light': 'rgba(14, 23, 38, 0.15)',
                        },
                        black: {
                            DEFAULT: '#0e1726',
                            light: '#e3e4eb',
                            'dark-light': 'rgba(14,23,38,0.1)',
                        },
                        white: {
                            DEFAULT: '#ffffff',
                            light: '#e0e6ed',
                            dark: '#888ea8',
                        },
                    },
                },
            },
        }
    </script>

    <!-- ApexCharts CDN -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <!-- Alpine.js Plugins & Core -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/persist@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/focus@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        .panel {
            background-color: #ffffff;
            border-radius: 0.75rem;
            padding: 1.25rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.07), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
            border: 1px solid #eef0f3;
        }
        .dark .panel {
            background-color: #1b2e4b;
            border-color: #192a43;
            color: #e0e6ed;
        }
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.625rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 700;
            line-height: 1;
            text-transform: uppercase;
        }
        .badge-primary { background-color: #fbeaf7; color: #730163; }
        .badge-success { background-color: #ddf5f0; color: #00ab55; }
        .badge-warning { background-color: #fff9ed; color: #e2a03f; }
        .badge-danger { background-color: #fdeeed; color: #e7515a; }
        .badge-info { background-color: #e7f7ff; color: #2196f3; }
        .badge-secondary { background-color: #fff4ea; color: #F68625; }
    </style>
    @stack('styles')
</head>

<body class="relative overflow-x-hidden font-nunito text-sm font-normal antialiased bg-[#fafafa] text-[#0e1726] dark:bg-[#060818] dark:text-[#888ea8]">

    <!-- Sidebar Overlay for Mobile -->
    <div x-cloak class="fixed inset-0 z-50 bg-[black]/60 lg:hidden" :class="{'hidden' : !$store.app.sidebar}" @click="$store.app.toggleSidebar()"></div>

    <div class="main-container min-h-screen">
        <!-- Sidebar Navigation -->
        @include('admin.layouts.sidebar')

        <!-- Main Content Area -->
        <div class="main-content flex min-h-screen flex-col transition-all duration-300" :class="[$store.app.sidebar ? 'lg:ltr:ml-0 lg:rtl:mr-0' : 'lg:ltr:ml-[260px] lg:rtl:mr-[260px]']">
            <!-- Header Top Bar -->
            @include('admin.layouts.header')

            <!-- Main Dynamic Content -->
            <div class="p-4 sm:p-6 flex-1">
                @if(session('success'))
                    <div class="mb-5 flex items-center rounded bg-success-light p-3.5 text-success dark:bg-success-dark-light">
                        <span class="ltr:pr-2 rtl:pl-2 text-lg"><i class="fa-solid fa-circle-check"></i></span>
                        <span class="font-semibold">{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-5 flex items-center rounded bg-danger-light p-3.5 text-danger dark:bg-danger-dark-light">
                        <span class="ltr:pr-2 rtl:pl-2 text-lg"><i class="fa-solid fa-circle-xmark"></i></span>
                        <span class="font-semibold">{{ session('error') }}</span>
                    </div>
                @endif

                @yield('content')
            </div>

            <!-- Footer -->
            @include('admin.layouts.footer')
        </div>
    </div>

    <!-- Alpine Store Initialization -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('app', {
                theme: localStorage.getItem('theme') || 'light',
                isDarkMode: localStorage.getItem('theme') === 'dark',
                sidebar: false,
                semidark: false,
                toggleTheme(val) {
                    this.theme = val;
                    if (val === 'dark') {
                        this.isDarkMode = true;
                        document.documentElement.classList.add('dark');
                        localStorage.setItem('theme', 'dark');
                    } else {
                        this.isDarkMode = false;
                        document.documentElement.classList.remove('dark');
                        localStorage.setItem('theme', 'light');
                    }
                },
                toggleSidebar() {
                    this.sidebar = !this.sidebar;
                }
            });

            // Initial theme setup
            if (Alpine.store('app').theme === 'dark') {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }

            Alpine.data('main', () => ({}));
            Alpine.data('dropdown', () => ({
                open: false,
                toggle() {
                    this.open = !this.open;
                },
            }));
        });
    </script>
    @stack('scripts')
</body>
</html>
