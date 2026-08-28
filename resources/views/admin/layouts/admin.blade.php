<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard — Yana\'s Fashion')</title>
    
    <!-- FontAwesome 6 CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    
    <style>
        .admin-layout {
            display: flex;
            min-height: 100vh;
            background: #f4f6f9;
        }
        .admin-sidebar {
            width: 260px;
            background: #191417;
            color: #d1c8be;
            padding: 24px 0;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
        }
        .admin-brand {
            padding: 0 24px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 16px;
        }
        .admin-brand h2 {
            font-size: 1.8rem;
            color: #fff;
        }
        .admin-brand h2 span { color: var(--accent); }
        .admin-menu {
            list-style: none;
            padding: 0 12px;
            flex: 1;
        }
        .admin-menu li {
            margin-bottom: 4px;
        }
        .admin-menu a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-radius: var(--radius-sm);
            color: #d1c8be;
            font-size: 0.92rem;
            font-weight: 600;
            transition: var(--transition);
        }
        .admin-menu a:hover,
        .admin-menu a.active {
            background: var(--primary);
            color: #ffffff;
        }
        .admin-main {
            flex: 1;
            padding: 24px 32px;
            overflow-y: auto;
        }
        .admin-topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 28px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--line);
        }
        .stat-card {
            background: var(--white);
            border-radius: var(--radius-md);
            border: 1px solid var(--line);
            padding: 20px;
            box-shadow: var(--shadow-sm);
        }
        .stat-title {
            font-size: 0.82rem;
            text-transform: uppercase;
            font-weight: 700;
            color: var(--text-muted);
            margin-bottom: 6px;
        }
        .stat-value {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--dark);
        }
        .admin-table-card {
            background: var(--white);
            border-radius: var(--radius-md);
            border: 1px solid var(--line);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }
        .admin-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9rem;
        }
        .admin-table th {
            background: #faf8f5;
            padding: 12px 16px;
            text-align: left;
            font-weight: 700;
            color: var(--dark);
            border-bottom: 1px solid var(--line);
        }
        .admin-table td {
            padding: 14px 16px;
            border-bottom: 1px solid var(--line);
            color: var(--text);
        }
        .badge-status {
            padding: 4px 10px;
            border-radius: var(--radius-full);
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
        }
        .badge-pending { background: #fff3cd; color: #856404; }
        .badge-processing { background: #cce5ff; color: #004085; }
        .badge-shipped { background: #e2e3e5; color: #383d41; }
        .badge-delivered { background: #d4edda; color: #155724; }
        .badge-cancelled { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>

    <div class="admin-layout">
        <!-- Sidebar Navigation -->
        <aside class="admin-sidebar">
            <div class="admin-brand">
                <h2>Yana's<span>.</span> Admin</h2>
                <small style="color:var(--text-muted); font-size:0.75rem;">E-Commerce Control Panel</small>
            </div>

            <ul class="admin-menu">
                <li>
                    <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="fa-solid fa-chart-pie"></i> Dashboard
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.orders.index') }}" class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-box"></i> Orders (অর্ডারসমূহ)
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.products.index') }}" class="{{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-shirt"></i> Products (পণ্য তালিকা)
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.categories.index') }}" class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-folder-tree"></i> Categories (ক্যাটাগরি)
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.settings.index') }}" class="{{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-sliders"></i> Store Settings
                    </a>
                </li>
                <li style="margin-top: 32px; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 16px;">
                    <a href="{{ route('home') }}" target="_blank">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i> View Storefront
                    </a>
                </li>
            </ul>
        </aside>

        <!-- Main Content Area -->
        <main class="admin-main">
            <!-- Flash Message Alerts -->
            @if(session('success'))
                <div style="background: #e8f7ed; border-left: 4px solid var(--success); padding: 12px 18px; border-radius: var(--radius-sm); color: #0a632b; font-weight: 600; margin-bottom: 20px;">
                    <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                </div>
            @endif

            @yield('admin_content')
        </main>
    </div>

</body>
</html>
