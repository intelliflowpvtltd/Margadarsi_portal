<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Margadarsi Portal') - Real Estate Management</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700;900&family=Inter:wght@300;400;500;600;700&family=Cormorant+Garamond:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">

    <style>
        :root {
            --color-off-white: #FAF9F6;
            --color-dark-maroon: #800020;
            --color-maroon-light: #A0152E;
            --color-coffee-gold: #B8956A;
            --color-coffee-gold-dark: #9C7A54;
            --color-white: #FFFFFF;
            --color-text-dark: #2C2C2C;
            --color-text-secondary: #6C757D;
            --color-text-muted: #9CA3AF;
            --color-text-white: #FFFFFF;
            --color-bg-body: #FAF9F6;
            --color-bg-card: #FFFFFF;
            --color-bg-hover: #F3F4F6;
            --color-border-light: #E5E7EB;
            --font-primary: 'Playfair Display', Georgia, serif;
            --font-secondary: 'Inter', sans-serif;
            --fs-h1: 2.5rem;
            --fs-h3: 1.5rem;
            --fs-small: 0.875rem;
            --fs-tiny: 0.75rem;
            --fw-medium: 500;
            --fw-semibold: 600;
            --fw-bold: 700;
            --spacing-sm: 0.5rem;
            --spacing-md: 1rem;
            --spacing-lg: 1.5rem;
            --spacing-xl: 2rem;
            --spacing-2xl: 3rem;
            --border-radius: 8px;
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.1);
            --sidebar-width: 260px;
            --topbar-height: 70px;
            --transition-fast: all 0.2s ease;
            --transition-base: all 0.3s ease;
            --z-sticky: 100;
            --z-fixed: 200;
            --color-gold: #B8956A;
            --color-gold-shine: #D4B896;
            --color-gold-rgb: 184, 149, 106;
            --color-secondary: #800020;
            --color-secondary-dark: #5C0011;
        }

        .gold-shimmer {
            background: linear-gradient(90deg, #D4B896 0%, #B8956A 50%, #D4B896 100%);
            background-size: 200% 100%;
            animation: shimmer 3s linear infinite;
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        @keyframes shimmer {
            0% {
                background-position: -100% 0;
            }

            100% {
                background-position: 200% 0;
            }
        }
    </style>

    @stack('styles')

    <style>
        /* Layout Structure */
        body {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(180deg, var(--color-secondary) 0%, var(--color-secondary-dark) 100%);
            color: var(--color-text-white);
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            z-index: var(--z-fixed);
            transition: transform var(--transition-base);
            display: flex;
            flex-direction: column;
        }

        /* Sidebar Header (Fixed 70px) */
        .sidebar-header {
            height: 70px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 0 var(--spacing-lg);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            flex-shrink: 0;
        }

        .sidebar-header h3 {
            font-family: var(--font-primary);
            color: var(--color-gold);
            margin: 0;
            font-size: 1.25rem;
            line-height: 1.2;
        }

        .sidebar-subtitle {
            font-size: 0.65rem;
            color: rgba(255, 255, 255, 0.6);
            margin-top: 0.125rem;
        }

        /* Sidebar Navigation (Scrollable Middle) */
        .sidebar-nav {
            flex: 1;
            padding: var(--spacing-lg) 0;
            overflow-y: auto;
            overflow-x: hidden;
        }

        /* Custom Scrollbar for Sidebar Nav */
        .sidebar-nav::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar-nav::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
        }

        .sidebar-nav::-webkit-scrollbar-thumb {
            background: rgba(184, 149, 106, 0.3);
            border-radius: 2px;
        }

        .sidebar-nav::-webkit-scrollbar-thumb:hover {
            background: rgba(184, 149, 106, 0.5);
        }

        .sidebar-nav-link {
            display: flex;
            align-items: center;
            gap: var(--spacing-md);
            padding: 0.875rem var(--spacing-xl);
            color: rgba(255, 255, 255, 0.75);
            text-decoration: none;
            transition: all var(--transition-fast);
            border-left: 3px solid transparent;
            margin: 0.25rem 0;
            position: relative;
            font-family: var(--font-secondary);
            font-size: 0.9375rem;
            font-weight: 500;
            letter-spacing: 0.3px;
        }

        .sidebar-nav-link::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 0;
            background: linear-gradient(90deg, rgba(184, 149, 106, 0.15) 0%, transparent 100%);
            transition: width 0.3s ease;
        }

        .sidebar-nav-link:hover {
            background-color: rgba(255, 255, 255, 0.08);
            color: var(--color-gold);
            border-left-color: var(--color-gold);
            padding-left: calc(var(--spacing-xl) + 0.25rem);
            letter-spacing: 0.5px;
        }

        .sidebar-nav-link:hover::before {
            width: 100%;
        }

        .sidebar-nav-link.active {
            background: linear-gradient(90deg, rgba(184, 149, 106, 0.2) 0%, rgba(184, 149, 106, 0.05) 100%);
            color: var(--color-gold);
            border-left-color: var(--color-gold);
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .sidebar-nav-link span {
            transition: all 0.2s ease;
        }

        .sidebar-nav-link:hover span,
        .sidebar-nav-link.active span {
            text-shadow: 0 0 8px rgba(184, 149, 106, 0.3);
        }

        .sidebar-nav-link i {
            font-size: 1.375rem;
            width: 28px;
            text-align: center;
            transition: all 0.3s ease;
            filter: drop-shadow(0 0 0 transparent);
        }

        .sidebar-nav-link:hover i {
            transform: scale(1.15) rotate(-5deg);
            filter: drop-shadow(0 2px 6px rgba(184, 149, 106, 0.4));
            color: var(--color-gold-shine);
        }

        .sidebar-nav-link.active i {
            transform: scale(1.1);
            filter: drop-shadow(0 2px 4px rgba(184, 149, 106, 0.3));
            color: var(--color-gold-shine);
        }

        /* Sidebar Footer (Fixed 70px) */
        .sidebar-footer {
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 var(--spacing-lg);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            background: linear-gradient(180deg, rgba(92, 0, 17, 0) 0%, rgba(92, 0, 17, 0.5) 100%);
            flex-shrink: 0;
        }

        .logout-btn {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: var(--spacing-sm);
            padding: 0.75rem var(--spacing-md);
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 10px;
            color: rgba(255, 255, 255, 0.9);
            font-weight: 600;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all var(--transition-fast);
        }

        .logout-btn:hover {
            background: rgba(184, 149, 106, 0.2);
            border-color: var(--color-gold);
            color: var(--color-gold);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(184, 149, 106, 0.2);
        }

        .logout-btn:active {
            transform: translateY(0);
        }

        .logout-btn i {
            font-size: 1.1rem;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .topbar {
            background-color: var(--color-bg-card);
            height: var(--topbar-height);
            box-shadow: var(--shadow-sm);
            position: sticky;
            top: 0;
            z-index: var(--z-sticky);
            display: flex;
            align-items: center;
            padding: 0 var(--spacing-xl);
            justify-content: space-between;
            gap: var(--spacing-xl);
        }

        /* Date/Time Widget */
        .datetime-widget {
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid rgba(184, 149, 106, 0.2);
            border-radius: 12px;
            padding: 0.75rem 1.5rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .datetime-widget:hover {
            box-shadow: 0 6px 16px rgba(184, 149, 106, 0.15);
            border-color: rgba(184, 149, 106, 0.3);
        }

        .datetime-display {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            white-space: nowrap;
        }

        .date-text {
            color: var(--color-text-dark);
            font-weight: 600;
            font-size: 0.875rem;
        }

        .time-separator {
            color: var(--color-coffee-gold);
            opacity: 0.5;
            font-weight: 300;
        }

        .time-text {
            color: var(--color-coffee-gold);
            font-weight: 700;
            font-size: 0.875rem;
        }

        /* Search Bar */
        .search-wrapper {
            flex: 1;
            max-width: 400px;
        }

        .search-box {
            position: relative;
            display: flex;
            align-items: center;
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid rgba(184, 149, 106, 0.15);
            border-radius: 12px;
            padding: 0.5rem 1rem;
            transition: all 0.3s ease;
        }

        .search-box:focus-within {
            background: rgba(255, 255, 255, 0.95);
            border-color: var(--color-coffee-gold);
            box-shadow: 0 4px 16px rgba(184, 149, 106, 0.2);
        }

        .search-icon {
            color: var(--color-text-muted);
            margin-right: 0.75rem;
            font-size: 1rem;
        }

        .search-input {
            flex: 1;
            border: none;
            outline: none;
            background: transparent;
            color: var(--color-text-dark);
            font-size: 0.875rem;
            font-family: var(--font-secondary);
        }

        .search-input::placeholder {
            color: var(--color-text-muted);
        }

        .search-shortcut {
            background: rgba(184, 149, 106, 0.1);
            color: var(--color-coffee-gold-dark);
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.7rem;
            font-family: monospace;
            border: 1px solid rgba(184, 149, 106, 0.2);
        }

        .content-wrapper {
            flex: 1;
            padding: var(--spacing-xl);
        }

        .page-header {
            margin-bottom: var(--spacing-2xl);
        }

        .page-title {
            font-family: var(--font-primary);
            font-size: var(--fs-h1);
            color: var(--color-secondary);
            margin-bottom: var(--spacing-sm);
        }

        .breadcrumb {
            background: transparent;
            padding: 0;
            margin: 0;
        }

        .breadcrumb-item a {
            color: var(--color-text-secondary);
            text-decoration: none;
        }

        .breadcrumb-item.active {
            color: var(--color-gold-dark);
        }

        /* User Dropdown */
        .user-dropdown {
            display: flex;
            align-items: center;
            gap: var(--spacing-sm);
            cursor: pointer;
            padding: var(--spacing-sm) var(--spacing-md);
            border-radius: var(--border-radius);
            transition: background-color var(--transition-fast);
        }

        .user-dropdown:hover {
            background-color: var(--color-bg-hover);
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--color-gold), var(--color-gold-shine));
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--color-secondary);
            font-weight: var(--fw-bold);
        }

        /* ========== HELPER CLASSES ========== */
        .text-gold {
            color: var(--color-gold) !important;
        }

        .text-maroon {
            color: var(--color-secondary) !important;
        }

        .bg-gold {
            background-color: var(--color-gold) !important;
        }

        .btn-outline-gold {
            color: var(--color-gold);
            border-color: var(--color-gold);
        }

        .btn-outline-gold:hover {
            background-color: var(--color-gold);
            color: var(--color-white);
        }

        /* ========== LUXURY CARD ========== */
        .luxury-card {
            background: var(--color-bg-card);
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            /* Soft shadow, NO BLUR */
            border: 1px solid rgba(0, 0, 0, 0.03);
            transition: all 0.3s ease;
            height: 100%;
            position: relative;
            overflow: hidden;
        }

        .luxury-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(184, 149, 106, 0.15);
            /* Gold tint shadow on hover */
            border-color: rgba(184, 149, 106, 0.2);
        }

        .luxury-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .luxury-card-title {
            font-family: var(--font-primary);
            font-size: 1.25rem;
            color: var(--color-secondary);
            margin: 0;
            font-weight: 600;
        }

        /* ========== ANIMATIONS ========== */
        .fade-in {
            animation: fadeIn 0.5s ease-out forwards;
            opacity: 0;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* User Avatar in Card */
        .luxury-card .user-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--color-gold), var(--color-gold-shine));
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--color-secondary);
            font-weight: 700;
            font-size: 2.5rem;
            margin: 0 auto 1rem;
            box-shadow: 0 4px 15px rgba(184, 149, 106, 0.3);
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <!-- Top Header Section (Fixed 70px) -->
        <div class="sidebar-header">
            <h3 class="gold-shimmer">MARGADARSI</h3>
            <small class="sidebar-subtitle">Real Estate Portal</small>
        </div>

        <!-- Middle Navigation Section (Scrollable) -->
        <nav class="sidebar-nav">
            <a href="{{ route('dashboard') }}" class="sidebar-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('companies.index') }}" class="sidebar-nav-link {{ request()->routeIs('companies.*') ? 'active' : '' }}">
                <i class="bi bi-building"></i>
                <span>Companies</span>
            </a>

            <a href="{{ route('projects.index') }}" class="sidebar-nav-link {{ request()->routeIs('projects.*') ? 'active' : '' }}">
                <i class="bi bi-briefcase"></i>
                <span>Projects</span>
            </a>

            <a href="{{ route('roles.index') }}" class="sidebar-nav-link {{ request()->routeIs('roles.*') ? 'active' : '' }}">
                <i class="bi bi-shield-check"></i>
                <span>Roles</span>
            </a>

            <a href="{{ route('users.index') }}" class="sidebar-nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i>
                <span>Users</span>
            </a>

            <a href="{{ route('leads.index') }}" class="sidebar-nav-link {{ request()->routeIs('leads.*') ? 'active' : '' }}">
                <i class="bi bi-telephone"></i>
                <span>Leads</span>
            </a>

            <a href="#" class="sidebar-nav-link">
                <i class="bi bi-gear"></i>
                <span>Settings</span>
            </a>
        </nav>

        <!-- Bottom Footer Section (Fixed 70px) -->
        <div class="sidebar-footer">
            <form action="{{ route('logout') }}" method="POST" class="w-100">
                @csrf
                <button type="submit" class="logout-btn">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Bar -->
        <header class="topbar">
            <!-- Left: Search Bar -->
            <div class="search-wrapper">
                <div class="search-box">
                    <i class="bi bi-search search-icon"></i>
                    <input type="text" class="search-input" placeholder="Search anything..." id="globalSearch">
                    <kbd class="search-shortcut">Ctrl+K</kbd>
                </div>
            </div>

            <!-- Center-Right: Date/Time Widget -->
            <div class="datetime-widget">
                <div class="datetime-display">
                    <span class="date-text" id="currentDate">Loading...</span>
                    <span class="time-separator">|</span>
                    <span class="time-text" id="currentTime">--:--</span>
                </div>
            </div>

            <!-- Right: Notifications & User Profile -->
            <div class="d-flex align-items-center gap-3">
                <!-- Notifications -->
                <div class="dropdown">
                    <button class="btn btn-link text-secondary position-relative" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-bell" style="font-size: 1.25rem;"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            3
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#">New notification 1</a></li>
                        <li><a class="dropdown-item" href="#">New notification 2</a></li>
                        <li><a class="dropdown-item" href="#">New notification 3</a></li>
                    </ul>
                </div>

                <!-- User Dropdown -->
                <div class="dropdown">
                    <div class="user-dropdown" data-bs-toggle="dropdown">
                        <div class="user-avatar">
                            {{ strtoupper(substr(auth()->user()->first_name, 0, 1)) }}{{ strtoupper(substr(auth()->user()->last_name, 0, 1)) }}
                        </div>
                        <div class="d-none d-md-block">
                            <div style="font-weight: var(--fw-medium); font-size: var(--fs-small);">
                                {{ auth()->user()->first_name }} {{ auth()->user()->last_name }}
                            </div>
                            <div style="font-size: var(--fs-tiny); color: var(--color-text-muted);">
                                {{ auth()->user()->role->name }}
                            </div>
                        </div>
                        <i class="bi bi-chevron-down"></i>
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i> Profile</a></li>
                        <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i> Settings</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="content-wrapper">
            <!-- Success/Error Messages -->
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Live Date/Time Clock -->
    <script>
        function updateDateTime() {
            const now = new Date();

            // Format date: "Thursday, 30 Jan 2026"
            const dateOptions = {
                timeZone: 'Asia/Kolkata',
                weekday: 'long',
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            };
            const dateStr = now.toLocaleDateString('en-IN', dateOptions);

            // Format time: "03:27 AM"
            const timeOptions = {
                timeZone: 'Asia/Kolkata',
                hour: '2-digit',
                minute: '2-digit',
                hour12: true
            };
            const timeStr = now.toLocaleTimeString('en-IN', timeOptions);

            // Update DOM elements
            const dateElement = document.getElementById('currentDate');
            const timeElement = document.getElementById('currentTime');

            if (dateElement) dateElement.textContent = dateStr;
            if (timeElement) timeElement.textContent = timeStr;
        }

        // Update immediately and then every second
        updateDateTime();
        setInterval(updateDateTime, 1000);
    </script>

    @stack('scripts')
</body>

</html>