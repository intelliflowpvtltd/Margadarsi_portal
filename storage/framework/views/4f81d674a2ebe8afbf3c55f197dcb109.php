<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo $__env->yieldContent('title', 'Margadarsi Portal'); ?> - Real Estate Management</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo e(asset('favicon.png')); ?>">

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

    <?php echo $__env->yieldPushContent('styles'); ?>

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

        /* Sidebar Header (Fixed 70px) - Premium Branding */
        .sidebar-header {
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 var(--spacing-lg);
            border-bottom: 1px solid rgba(255, 255, 255, 0.15);
            background: linear-gradient(180deg, 
                rgba(92, 0, 17, 0.3) 0%, 
                rgba(92, 0, 17, 0) 100%
            );
            flex-shrink: 0;
            position: relative;
            overflow: hidden;
        }

        .sidebar-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at center, 
                rgba(184, 149, 106, 0.1) 0%, 
                transparent 70%
            );
            pointer-events: none;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            position: relative;
            z-index: 1;
        }

        .sidebar-brand-icon {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, 
                var(--color-gold) 0%, 
                var(--color-gold-shine) 100%
            );
            border-radius: 8px;
            color: var(--color-secondary);
            font-size: 1.5rem;
            font-weight: 700;
            box-shadow: 
                0 4px 12px rgba(184, 149, 106, 0.3),
                inset 0 1px 0 rgba(255, 255, 255, 0.3);
            position: relative;
        }

        .sidebar-brand-icon::after {
            content: '';
            position: absolute;
            inset: 2px;
            border-radius: 6px;
            background: linear-gradient(135deg, 
                transparent 0%, 
                rgba(255, 255, 255, 0.15) 50%,
                transparent 100%
            );
            pointer-events: none;
        }

        .sidebar-brand-text {
            display: flex;
            flex-direction: column;
            gap: 0.125rem;
        }

        .sidebar-brand-text h3 {
            font-family: var(--font-primary);
            color: var(--color-gold);
            margin: 0;
            font-size: 1.25rem;
            font-weight: 700;
            line-height: 1;
            letter-spacing: 0.5px;
            text-shadow: 
                0 0 20px rgba(184, 149, 106, 0.4),
                0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .sidebar-subtitle {
            font-size: 0.625rem;
            color: rgba(255, 255, 255, 0.7);
            font-weight: 500;
            letter-spacing: 1px;
            text-transform: uppercase;
            line-height: 1;
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
            background: linear-gradient(90deg, 
                rgba(184, 149, 106, 0.25) 0%, 
                rgba(184, 149, 106, 0.1) 100%
            );
            color: var(--color-gold-shine);
            border-left-color: transparent;
            box-shadow: 
                inset 3px 0 0 var(--color-gold),
                0 2px 8px rgba(184, 149, 106, 0.2);
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .sidebar-nav-link span {
            transition: all 0.2s ease;
            position: relative;
            z-index: 1;
        }

        .sidebar-nav-link:hover span {
            color: var(--color-gold-shine);
            transform: translateX(2px);
        }

        .sidebar-nav-link.active span {
            text-shadow: 0 0 12px rgba(184, 149, 106, 0.5);
        }

        .sidebar-nav-link i {
            font-size: 1.125rem;
            width: 24px;
            text-align: center;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            filter: drop-shadow(0 0 0 transparent);
            position: relative;
            z-index: 1;
        }

        .sidebar-nav-link:hover i {
            transform: scale(1.2) rotate(-8deg);
            filter: drop-shadow(0 2px 8px rgba(184, 149, 106, 0.5));
            color: var(--color-gold-shine);
        }

        .sidebar-nav-link.active i {
            transform: scale(1.15);
            filter: drop-shadow(0 2px 6px rgba(184, 149, 106, 0.4));
            color: var(--color-gold-shine);
            animation: pulse-gold 2s ease-in-out infinite;
        }

        @keyframes pulse-gold {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.8; }
        }

        /* Badge Styling */
        .sidebar-badge {
            margin-left: auto;
            background: linear-gradient(135deg, var(--color-gold), var(--color-gold-shine));
            color: var(--color-secondary);
            font-size: 0.625rem;
            font-weight: 700;
            padding: 0.125rem 0.375rem;
            border-radius: 10px;
            min-width: 18px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(184, 149, 106, 0.3);
            position: relative;
            z-index: 1;
        }

        /* Submenu Styles */
        .sidebar-submenu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
            background: rgba(0, 0, 0, 0.1);
        }

        .sidebar-submenu.active {
            max-height: 500px;
        }

        .sidebar-submenu-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.625rem 1rem 0.625rem 3.5rem;
            color: rgba(255, 255, 255, 0.75);
            text-decoration: none;
            font-size: 0.8125rem;
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
        }

        .sidebar-submenu-link:hover {
            background: rgba(184, 149, 106, 0.15);
            color: var(--color-gold-shine);
            padding-left: 3.75rem;
        }

        .sidebar-submenu-link.active {
            background: rgba(184, 149, 106, 0.2);
            color: var(--color-gold);
            border-left-color: var(--color-gold);
            font-weight: 600;
        }

        .sidebar-submenu-link i {
            font-size: 0.875rem;
            width: 18px;
            text-align: center;
        }

        .sidebar-nav-link .bi-chevron-down {
            transition: transform 0.3s ease;
        }

        .sidebar-nav-link.open .bi-chevron-down {
            transform: rotate(180deg);
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

        /* ========== PREMIUM LUXURIOUS TOPBAR ========== */
        .topbar {
            background: linear-gradient(135deg, 
                rgba(255, 255, 255, 0.95) 0%,
                rgba(250, 249, 246, 0.98) 50%,
                rgba(255, 255, 255, 0.95) 100%
            );
            backdrop-filter: blur(20px);
            height: var(--topbar-height);
            box-shadow: 
                0 2px 8px rgba(128, 0, 32, 0.04),
                0 8px 24px rgba(184, 149, 106, 0.06),
                inset 0 1px 0 rgba(255, 255, 255, 0.8);
            position: sticky;
            top: 0;
            z-index: var(--z-sticky);
            display: flex;
            align-items: center;
            padding: 0 2rem;
            justify-content: flex-end;  /* Changed from space-between to shift everything right */
            gap: 0.75rem;  /* Consistent gap between ALL components */
            position: relative;
        }

        /* Top gold accent line */
        .topbar::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg,
                transparent 0%,
                rgba(184, 149, 106, 0.3) 10%,
                var(--color-gold) 50%,
                rgba(184, 149, 106, 0.3) 90%,
                transparent 100%
            );
            background-size: 200% 100%;
            animation: shimmer-line 4s ease-in-out infinite;
        }

        @keyframes shimmer-line {
            0%, 100% {
                background-position: -200% center;
            }
            50% {
                background-position: 200% center;
            }
        }

        /* Ambient gradient orbs */
        .topbar::after {
            content: '';
            position: absolute;
            top: -50%;
            right: 20%;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, 
                rgba(184, 149, 106, 0.08) 0%, 
                transparent 70%
            );
            border-radius: 50%;
            pointer-events: none;
            animation: float-orb 8s ease-in-out infinite;
        }

        @keyframes float-orb {
            0%, 100% {
                transform: translate(0, 0);
            }
            50% {
                transform: translate(-30px, -20px);
            }
        }

        /* Right side components grouped together */
        .topbar-right-group {
            display: flex;
            align-items: center;
            gap: 0.75rem;  /* Small consistent gap between date/time, notification, and user */
        }

        /* ========== PREMIUM SEARCH BAR ========== */
        .search-wrapper-premium {
            /* Removed flex: 1 to allow shifting right */
            max-width: 420px;  /* Fixed width instead of flex */
            width: 420px;
        }

        .search-box-premium {
            position: relative;
            display: flex;
            align-items: center;
            background: rgba(255, 255, 255, 0.95);
            border: 2px solid transparent;
            border-radius: 12px;  /* Reduced from 14px */
            padding: 0.5rem 1rem;  /* Reduced from 0.75rem 1.25rem to fit in 70px */
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 
                0 2px 8px rgba(128, 0, 32, 0.04),
                0 1px 3px rgba(184, 149, 106, 0.08);
            background-clip: padding-box;
            height: 48px;  /* Fixed height to ensure it fits in topbar */
        }

        /* Gradient border shimmer */
        .search-box-premium::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 12px;  /* Fixed to match main box */
            padding: 2px;
            background: linear-gradient(135deg,
                transparent 0%,
                rgba(184, 149, 106, 0.3) 30%,
                var(--color-gold-shine) 50%,
                rgba(184, 149, 106, 0.3) 70%,
                transparent 100%
            );
            background-size: 200% 100%;
            -webkit-mask: 
                linear-gradient(#fff 0 0) content-box, 
                linear-gradient(#fff 0 0);
            mask: 
                linear-gradient(#fff 0 0) content-box, 
                linear-gradient(#fff 0 0);
            -webkit-mask-composite: exclude;
            mask-composite: exclude;
            opacity: 0;
            transition: opacity 0.3s ease;
            pointer-events: none;  /* CRITICAL: Allow clicks through to input */
        }

        .search-box-premium:focus-within {
            background: rgba(255, 255, 255, 1);
            box-shadow: 
                0 4px 20px rgba(128, 0, 32, 0.08),
                0 12px 32px rgba(184, 149, 106, 0.12),
                0 0 0 4px rgba(184, 149, 106, 0.08);
            transform: translateY(-1px);
        }

        .search-box-premium:focus-within::before {
            opacity: 1;
            animation: border-shimmer 2s ease-in-out infinite;
        }

        @keyframes border-shimmer {
            0%, 100% {
                background-position: -100% center;
            }
            50% {
                background-position: 200% center;
            }
        }

        .search-icon-premium {
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--color-text-muted);
            margin-right: 1rem;
            font-size: 1.25rem;
            transition: all 0.3s ease;
        }

        .search-box-premium:focus-within .search-icon-premium {
            color: var(--color-gold);
            transform: scale(1.1);
        }

        .search-input-premium {
            flex: 1;
            border: none;
            outline: none;
            background: transparent;
            color: var(--color-text-dark);
            font-size: 0.9375rem;
            font-family: var(--font-secondary);
            font-weight: 500;
        }

        .search-input-premium::placeholder {
            color: var(--color-text-muted);
            font-weight: 400;
        }

        .search-shortcut-premium {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            padding:0.375rem 0.625rem;
            background: linear-gradient(135deg, 
                rgba(184, 149, 106, 0.12) 0%, 
                rgba(184, 149, 106, 0.08) 100%
            );
            border: 1px solid rgba(184, 149, 106, 0.2);
            border-radius: 8px;
            font-size: 0.75rem;
            font-family: var(--font-secondary);
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .search-shortcut-premium .key {
            color: var(--color-gold-dark);
            padding: 0.125rem 0.375rem;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 4px;
            font-size: 0.6875rem;
        }

        .search-shortcut-premium .plus {
            color: var(--color-text-muted);
            font-size: 0.625rem;
        }

        .search-box-premium:focus-within .search-shortcut-premium {
            background: linear-gradient(135deg, 
                rgba(184, 149, 106, 0.18) 0%, 
                rgba(184, 149, 106, 0.12) 100%
            );
            border-color: rgba(184, 149, 106, 0.3);
            transform: scale(1.05);
        }

        /* ========== PREMIUM DATE/TIME WIDGET ========== */
        .datetime-widget-premium {
            background: linear-gradient(135deg, 
                rgba(255, 255, 255, 0.95) 0%,
                rgba(250, 249, 246, 0.98) 100%
            );
            backdrop-filter: blur(15px);
            border: 1.5px solid rgba(184, 149, 106, 0.2);
            border-radius: 12px;  /* Reduced from 14px */
            padding: 0.5rem 1rem;  /* Reduced from 0.875rem 1.5rem */
            box-shadow: 
                0 4px 16px rgba(128, 0, 32, 0.06),
                0 2px 4px rgba(184, 149, 106, 0.08),
                inset 0 1px 0 rgba(255, 255, 255, 0.8);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            height: 48px;  /* Fixed height to match search bar */
            display: flex;
            align-items: center;
        }

        .datetime-widget-premium:hover {
            box-shadow: 
                0 6px 24px rgba(128, 0, 32, 0.08),
                0 4px 8px rgba(184, 149, 106, 0.12),
                inset 0 1px 0 rgba(255, 255, 255, 0.9);
            border-color: rgba(184, 149, 106, 0.35);
            transform: translateY(-2px);
        }

        .datetime-row {
            display: flex;
            align-items: center;
            gap: 0.75rem;  /* Reduced from 0.875rem */
        }

        .datetime-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 32px;  /* Reduced from 36px */
            height: 32px;  /* Reduced from 36px */
            border-radius: 8px;  /* Reduced from 10px */
            background: linear-gradient(135deg, 
                var(--color-gold) 0%,
                var(--color-gold-shine) 100%
            );
            color: var(--color-secondary);
            font-size: 1rem;  /* Reduced from 1.125rem */
            box-shadow: 
                0 2px 8px rgba(184, 149, 106, 0.3),
                inset 0 -1px 2px rgba(0, 0, 0, 0.1),
                inset 0 1px 2px rgba(255, 255, 255, 0.3);
            transition: all 0.3s ease;
            flex-shrink: 0;
        }

        .datetime-widget-premium:hover .datetime-icon {
            transform: scale(1.05) rotate(-3deg);
            box-shadow: 
                0 4px 12px rgba(184, 149, 106, 0.4),
                inset 0 -1px 2px rgba(0, 0, 0, 0.15),
                inset 0 1px 2px rgba(255, 255, 255, 0.4);
        }

        .datetime-content {
            display: flex;
            flex-direction: column;
            gap: 0.0625rem;  /* Reduced spacing */
        }

        .date-text-premium {
            color: var(--color-text-dark);
            font-weight: 700;
            font-size: 0.8125rem;  /* Reduced from 0.875rem */
            letter-spacing: 0.3px;
            line-height: 1.1;  /* Tighter line height */
        }

        .time-text-premium {
            color: var(--color-gold-dark);
            font-weight: 700;
            font-size: 0.75rem;  /* Reduced from 0.8125rem */
            font-family: 'Courier New', monospace;
            letter-spacing: 0.5px;
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

        /* ========== PREMIUM USER DROPDOWN ========== */
        .user-dropdown {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            cursor: pointer;
            padding: 0.375rem 0.875rem;  /* Reduced from 0.5rem 1rem */
            border-radius: 12px;
            background: linear-gradient(135deg, 
                rgba(255, 255, 255, 0.9) 0%, 
                rgba(250, 249, 246, 0.95) 100%
            );
            border: 1.5px solid transparent;
            background-clip: padding-box;
            position: relative;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 
                0 2px 8px rgba(128, 0, 32, 0.05),
                0 1px 2px rgba(184, 149, 106, 0.1);
            height: 48px;  /* Fixed height to match other components */
        }

        /* Gold border gradient on hover */
        .user-dropdown::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 12px;
            padding: 1.5px;
            background: linear-gradient(135deg, 
                var(--color-gold) 0%, 
                var(--color-gold-shine) 50%,
                var(--color-gold) 100%
            );
            -webkit-mask: 
                linear-gradient(#fff 0 0) content-box, 
                linear-gradient(#fff 0 0);
            mask: 
                linear-gradient(#fff 0 0) content-box, 
                linear-gradient(#fff 0 0);
            -webkit-mask-composite: exclude;
            mask-composite: exclude;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .user-dropdown:hover {
            background: linear-gradient(135deg, 
                rgba(255, 255, 255, 1) 0%, 
                rgba(250, 249, 246, 1) 100%
            );
            box-shadow: 
                0 4px 16px rgba(128, 0, 32, 0.08),
                0 8px 24px rgba(184, 149, 106, 0.12),
                inset 0 1px 0 rgba(255, 255, 255, 0.8);
            transform: translateY(-1px);
        }

        .user-dropdown:hover::before {
            opacity: 1;
        }

        .user-dropdown:active {
            transform: translateY(0);
        }

        /* Premium Avatar with 3D Effect */
        .user-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: linear-gradient(135deg, 
                #B8956A 0%, 
                #D4B896 50%,
                #B8956A 100%
            );
            background-size: 200% 200%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--color-secondary);
            font-weight: 700;
            font-size: 1.125rem;
            letter-spacing: 0.5px;
            position: relative;
            transition: all 0.3s ease;
            box-shadow: 
                0 4px 12px rgba(184, 149, 106, 0.3),
                inset 0 -2px 4px rgba(0, 0, 0, 0.1),
                inset 0 2px 4px rgba(255, 255, 255, 0.3);
            animation: gradient-shift 3s ease-in-out infinite;
        }

        @keyframes gradient-shift {
            0%, 100% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
        }

        /* Ring animation on hover */
        .user-avatar::after {
            content: '';
            position: absolute;
            inset: -4px;
            border-radius: 50%;
            border: 2px solid var(--color-gold);
            opacity: 0;
            transform: scale(0.8);
            transition: all 0.3s ease;
        }

        .user-dropdown:hover .user-avatar {
            transform: scale(1.05) rotate(-3deg);
            box-shadow: 
                0 6px 20px rgba(184, 149, 106, 0.4),
                inset 0 -2px 4px rgba(0, 0, 0, 0.15),
                inset 0 2px 4px rgba(255, 255, 255, 0.4);
        }

        .user-dropdown:hover .user-avatar::after {
            opacity: 0.5;
            transform: scale(1);
        }

        /* User Info Styling */
        .user-dropdown .d-none {
            transition: all 0.2s ease;
        }

        .user-dropdown .d-none > div:first-child {
            font-weight: 600;
            font-size: 0.9375rem;
            color: var(--color-text-dark);
            letter-spacing: 0.2px;
        }

        .user-dropdown .d-none > div:last-child {
            font-size: 0.75rem;
            color: var(--color-gold-dark);
            font-weight: 500;
            margin-top: 0.125rem;
        }

        /* Animated Chevron */
        .user-dropdown > i {
            font-size: 0.875rem;
            color: var(--color-text-muted);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            margin-left: 0.25rem;
        }

        .user-dropdown:hover > i {
            color: var(--color-gold);
        }

        .user-dropdown[aria-expanded="true"] > i,
        .user-dropdown.show > i {
            transform: rotate(180deg);
            color: var(--color-gold);
        }

        /* ========== PREMIUM DROPDOWN MENU ========== */
        .user-dropdown + .dropdown-menu {
            border: none;
            border-radius: 16px;
            padding: 0.5rem;
            margin-top: 0.75rem !important;
            min-width: 240px;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            box-shadow: 
                0 8px 32px rgba(128, 0, 32, 0.12),
                0 4px 16px rgba(184, 149, 106, 0.08),
                inset 0 1px 0 rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(184, 149, 106, 0.15);
            animation: dropdown-slide-in 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @keyframes dropdown-slide-in {
            from {
                opacity: 0;
                transform: translateY(-10px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .user-dropdown + .dropdown-menu .dropdown-item {
            padding: 0.75rem 1rem;
            border-radius: 10px;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--color-text-dark);
            display: flex;
            align-items: center;
            transition: all 0.2s ease;
            position: relative;
            overflow: hidden;
        }

        .user-dropdown + .dropdown-menu .dropdown-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 3px;
            background: linear-gradient(180deg, 
                var(--color-gold) 0%, 
                var(--color-gold-shine) 100%
            );
            transform: scaleY(0);
            transition: transform 0.2s ease;
        }

        .user-dropdown + .dropdown-menu .dropdown-item:hover {
            background: linear-gradient(90deg, 
                rgba(184, 149, 106, 0.08) 0%, 
                rgba(184, 149, 106, 0.03) 100%
            );
            color: var(--color-gold-dark);
            transform: translateX(4px);
            padding-left: 1.25rem;
        }

        .user-dropdown + .dropdown-menu .dropdown-item:hover::before {
            transform: scaleY(1);
        }

        .user-dropdown + .dropdown-menu .dropdown-item i {
            font-size: 1.125rem;
            width: 24px;
            margin-right: 0.75rem;
            color: var(--color-text-muted);
            transition: all 0.2s ease;
        }

        .user-dropdown + .dropdown-menu .dropdown-item:hover i {
            color: var(--color-gold);
            transform: scale(1.1);
        }

        .user-dropdown + .dropdown-menu .dropdown-item.text-danger {
            color: #DC2626;
        }

        .user-dropdown + .dropdown-menu .dropdown-item.text-danger:hover {
            background: linear-gradient(90deg, 
                rgba(220, 38, 38, 0.08) 0%, 
                rgba(220, 38, 38, 0.03) 100%
            );
            color: #DC2626;
        }

        .user-dropdown + .dropdown-menu .dropdown-item.text-danger:hover i {
            color: #DC2626;
        }

        .user-dropdown + .dropdown-menu .dropdown-divider {
            margin: 0.5rem 0;
            border-top: 1px solid rgba(184, 149, 106, 0.15);
        }

        /* ========== PREMIUM NOTIFICATION SYSTEM ========== */
        .notification-trigger-premium {
            position: relative;
            width: 48px;
            height: 48px;  /* Matches other components */
            border-radius: 12px;
            border: none;
            background: linear-gradient(135deg, 
                rgba(255, 255, 255, 0.9) 0%, 
                rgba(250, 249, 246, 0.95) 100%
            );
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 
                0 2px 8px rgba(128, 0, 32, 0.05),
                0 1px 2px rgba(184, 149, 106, 0.1);
        }

        .notification-trigger-premium:hover {
            background: linear-gradient(135deg, 
                rgba(255, 255, 255, 1) 0%, 
                rgba(250, 249, 246, 1) 100%
            );
            box-shadow: 
                0 4px 16px rgba(128, 0, 32, 0.08),
                0 8px 24px rgba(184, 149, 106, 0.12);
            transform: translateY(-2px) scale(1.02);
        }

        .notification-icon-wrapper {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .bell-icon {
            font-size: 1.5rem;
            color: var(--color-text-dark);
            transition: all 0.3s ease;
        }

        .notification-trigger-premium:hover .bell-icon {
            color: var(--color-gold);
            animation: bell-ring 0.5s ease-in-out;
        }

        @keyframes bell-ring {
            0%, 100% { transform: rotate(0deg); }
            10%, 30%, 50% { transform: rotate(-10deg); }
            20%, 40% { transform: rotate(10deg); }
        }

        /* Pulsing Badge */
        .notification-badge-premium {
            position: absolute;
            top: -6px;
            right: -6px;
            min-width: 20px;
            height: 20px;
            padding: 0 6px;
            background: linear-gradient(135deg, #DC2626 0%, #EF4444 100%);
            color: white;
            border-radius: 10px;
            font-size: 0.6875rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 
                0 2px 8px rgba(220, 38, 38, 0.4),
                0 0 0 3px rgba(255, 255, 255, 0.8);
            animation: badge-pulse 2s infinite;
        }

        @keyframes badge-pulse {
            0%, 100% {
                transform: scale(1);
                box-shadow: 
                    0 2px 8px rgba(220, 38, 38, 0.4),
                    0 0 0 3px rgba(255, 255, 255, 0.8);
            }
            50% {
                transform: scale(1.1);
                box-shadow: 
                    0 4px 12px rgba(220, 38, 38, 0.6),
                    0 0 0 5px rgba(255, 255, 255, 0.6);
            }
        }

        /* Pulse ring effect */
        .notification-pulse {
            position: absolute;
            top: -6px;
            right: -6px;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: rgba(220, 38, 38, 0.3);
            animation: pulse-ring 2s infinite;
            pointer-events: none;
        }

        @keyframes pulse-ring {
            0% {
                transform: scale(1);
                opacity: 1;
            }
            100% {
                transform: scale(2);
                opacity: 0;
            }
        }

        /* Premium Notification Dropdown */
        .notification-dropdown-premium {
            width: 420px !important;
            max-width: 95vw;
            border: none;
            border-radius: 16px;
            padding: 0;
            margin-top: 0.75rem !important;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            box-shadow: 
                0 12px 48px rgba(128, 0, 32, 0.15),
                0 6px 24px rgba(184, 149, 106, 0.1),
                inset 0 1px 0 rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(184, 149, 106, 0.15);
            animation: notification-slide-in 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @keyframes notification-slide-in {
            from {
                opacity: 0;
                transform: translateY(-15px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* Notification Header */
        .notification-header-premium {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid rgba(184, 149, 106, 0.1);
   display: flex;
            align-items: center;
            justify-content: space-between;
            background: linear-gradient(135deg, 
                rgba(250, 249, 246, 0.5) 0%, 
                rgba(255, 255, 255, 0.3) 100%
            );
        }

        .notification-title {
            margin: 0;
            font-size: 1rem;
            font-weight: 700;
            color: var(--color-text-dark);
            display: flex;
            align-items: center;
        }

        .notification-title i {
            color: var(--color-gold);
            font-size: 1.125rem;
        }

        .mark-all-read-btn {
            background: none;
            border: none;
            color: var(--color-gold-dark);
            font-size: 0.8125rem;
            font-weight: 600;
            padding: 0.375rem 0.75rem;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
        }

        .mark-all-read-btn:hover {
            background: rgba(184, 149, 106, 0.1);
            color: var(--color-gold);
        }

        /* Notification List */
        .notification-list-premium {
            max-height: 420px;
            overflow-y: auto;
            padding: 0.5rem;
        }

        .notification-list-premium::-webkit-scrollbar {
            width: 6px;
        }

        .notification-list-premium::-webkit-scrollbar-track {
            background: rgba(184, 149, 106, 0.05);
            border-radius: 3px;
        }

        .notification-list-premium::-webkit-scrollbar-thumb {
            background: rgba(184, 149, 106, 0.3);
            border-radius: 3px;
        }

        .notification-list-premium::-webkit-scrollbar-thumb:hover {
            background: rgba(184, 149, 106, 0.5);
        }

        /* Notification Item */
        .notification-item-premium {
            display: flex;
            gap: 1rem;
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 0.5rem;
            text-decoration: none;
            color: inherit;
            background: rgba(255, 255, 255, 0.5);
            border: 1px solid transparent;
            position: relative;
            transition: all 0.2s ease;
        }

        .notification-item-premium:hover {
            background: rgba(250, 249, 246, 0.8);
            border-color: rgba(184, 149, 106, 0.15);
            transform: translateX(4px);
            box-shadow: 0 4px 12px rgba(128, 0, 32, 0.05);
        }

        .notification-item-premium.unread {
            background: rgba(184, 149, 106, 0.06);
        }

        .notification-icon-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            color: white;
            font-size: 1.125rem;
        }

        .notification-content {
            flex: 1;
            min-width: 0;
        }

        .notification-text strong {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--color-text-dark);
            margin-bottom: 0.25rem;
        }

        .notification-text p {
            font-size: 0.8125rem;
            color: var(--color-text-secondary);
            margin: 0;
            line-height: 1.4;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        .notification-meta {
            display: flex;
            align-items: center;
            gap: 0.375rem;
            margin-top: 0.5rem;
            font-size: 0.75rem;
            color: var(--color-text-muted);
        }

        .notification-meta i {
            font-size: 0.875rem;
        }

        .unread-indicator {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--color-gold);
            flex-shrink: 0;
            box-shadow: 0 0 8px rgba(184, 149, 106, 0.5);
        }

        .notification-item-premium:not(.unread) .unread-indicator {
            display: none;
        }

        /* Notification Footer */
        .notification-footer-premium {
            padding: 1rem 1.5rem;
            border-top: 1px solid rgba(184, 149, 106, 0.1);
            background: linear-gradient(135deg, 
                rgba(250, 249, 246, 0.5) 0%, 
                rgba(255, 255, 255, 0.3) 100%
            );
        }

        .view-all-notifications {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            color: var(--color-gold-dark);
            font-size: 0.875rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .view-all-notifications:hover {
            color: var(--color-gold);
            gap: 0.75rem;
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
        <!-- Top Header Section (Fixed 70px) - Premium Branding -->
        <div class="sidebar-header">
            <div class="sidebar-brand">
                <div class="sidebar-brand-icon">
                    <i class="bi bi-building-fill-gear"></i>
                </div>
                <div class="sidebar-brand-text">
                    <h3 class="gold-shimmer">MARGADARSI</h3>
                    <small class="sidebar-subtitle">Real Estate Portal</small>
                </div>
            </div>
        </div>

        <!-- Middle Navigation Section (Scrollable) -->
        <nav class="sidebar-nav">
            <!-- 1. Dashboard (Daily Use) -->
            <a href="<?php echo e(route('dashboard')); ?>" class="sidebar-nav-link <?php echo e(request()->routeIs('dashboard') ? 'active' : ''); ?>">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>

            <!-- 2-6. Lead Management Section (5 Items) -->
            <a href="<?php echo e(route('leads.index')); ?>" class="sidebar-nav-link <?php echo e(request()->routeIs('leads.index') ? 'active' : ''); ?>">
                <i class="bi bi-telephone"></i>
                <span>Leads</span>
            </a>

            <a href="#" class="sidebar-nav-link">
                <i class="bi bi-arrow-repeat"></i>
                <span>Followups</span>
            </a>

            <a href="#" class="sidebar-nav-link">
                <i class="bi bi-geo-alt"></i>
                <span>Site Visit</span>
            </a>

            <a href="#" class="sidebar-nav-link">
                <i class="bi bi-file-earmark-text"></i>
                <span>Reports</span>
            </a>

            <a href="#" class="sidebar-nav-link">
                <i class="bi bi-graph-up"></i>
                <span>Analytics</span>
            </a>

            <!-- Section Divider: Administration -->
            <div class="sidebar-section-divider"></div>

            <!-- 7. Masters (Frequent Configuration) -->
            <a href="#" class="sidebar-nav-link has-submenu" id="mastersToggle">
                <i class="bi bi-gear-fill"></i>
                <span>Masters</span>
                <i class="bi bi-chevron-down ms-auto" style="font-size: 0.75rem;"></i>
            </a>
            <div class="sidebar-submenu" id="mastersSubmenu">
                <!-- Location Masters -->
                <a href="/admin/masters/countries" class="sidebar-submenu-link">
                    <i class="bi bi-globe"></i>
                    <span>Countries</span>
                </a>
                <a href="/admin/masters/states" class="sidebar-submenu-link">
                    <i class="bi bi-map"></i>
                    <span>States</span>
                </a>
                <a href="/admin/masters/cities" class="sidebar-submenu-link">
                    <i class="bi bi-building"></i>
                    <span>Cities</span>
                </a>
                <!-- Property Masters -->
                <a href="/admin/masters/property-types" class="sidebar-submenu-link">
                    <i class="bi bi-house"></i>
                    <span>Property Types</span>
                </a>
                <a href="/admin/masters/property-statuses" class="sidebar-submenu-link">
                    <i class="bi bi-flag"></i>
                    <span>Property Statuses</span>
                </a>
                <!-- Amenity Masters -->
                <a href="/admin/masters/amenity-categories" class="sidebar-submenu-link">
                    <i class="bi bi-grid"></i>
                    <span>Amenity Categories</span>
                </a>
                <a href="/admin/masters/amenities" class="sidebar-submenu-link">
                    <i class="bi bi-stars"></i>
                    <span>Amenities</span>
                </a>
                <!-- Specification Masters -->
                <a href="/admin/masters/specification-categories" class="sidebar-submenu-link">
                    <i class="bi bi-list-ul"></i>
                    <span>Spec Categories</span>
                </a>
                <a href="/admin/masters/specification-types" class="sidebar-submenu-link">
                    <i class="bi bi-list-check"></i>
                    <span>Spec Types</span>
                </a>
                <!-- Lead Masters -->
                <a href="/admin/masters/lead-sources" class="sidebar-submenu-link">
                    <i class="bi bi-funnel"></i>
                    <span>Lead Sources</span>
                </a>
                <a href="/admin/masters/lead-statuses" class="sidebar-submenu-link">
                    <i class="bi bi-flag-fill"></i>
                    <span>Lead Statuses</span>
                </a>
                <a href="/admin/masters/budget-ranges" class="sidebar-submenu-link">
                    <i class="bi bi-cash-stack"></i>
                    <span>Budget Ranges</span>
                </a>
                <a href="/admin/masters/timelines" class="sidebar-submenu-link">
                    <i class="bi bi-calendar-range"></i>
                    <span>Timelines</span>
                </a>
                <!-- Generic Masters -->
                <a href="/admin/masters/generic-masters" class="sidebar-submenu-link">
                    <i class="bi bi-gear"></i>
                    <span>Generic Masters</span>
                </a>
            </div>

            <!-- Section Divider: Operations -->
            <div class="sidebar-section-divider"></div>

            <!-- 8. Users (Frequent) -->
            <a href="<?php echo e(route('users.index')); ?>" class="sidebar-nav-link <?php echo e(request()->routeIs('users.*') ? 'active' : ''); ?>">
                <i class="bi bi-people"></i>
                <span>Users</span>
            </a>

            <!-- 9. Roles (Frequent) -->
            <a href="<?php echo e(route('roles.index')); ?>" class="sidebar-nav-link <?php echo e(request()->routeIs('roles.*') ? 'active' : ''); ?>">
                <i class="bi bi-shield-check"></i>
                <span>Roles</span>
            </a>

            <!-- 10. Departments (Moderate Use) -->
            <a href="<?php echo e(route('departments.index')); ?>" class="sidebar-nav-link <?php echo e(request()->routeIs('departments.*') ? 'active' : ''); ?>">
                <i class="bi bi-diagram-3"></i>
                <span>Departments</span>
            </a>

            <!-- Section Divider: Configuration -->
            <div class="sidebar-section-divider"></div>

            <!-- 11. Projects (Less Frequent) -->
            <a href="<?php echo e(route('projects.index')); ?>" class="sidebar-nav-link <?php echo e(request()->routeIs('projects.*') ? 'active' : ''); ?>">
                <i class="bi bi-briefcase"></i>
                <span>Projects</span>
            </a>

            <!-- 12. Companies (Less Frequent) -->
            <a href="<?php echo e(route('companies.index')); ?>" class="sidebar-nav-link <?php echo e(request()->routeIs('companies.*') ? 'active' : ''); ?>">
                <i class="bi bi-building"></i>
                <span>Companies</span>
            </a>

            <!-- 13. Settings (Rare) -->
            <a href="#" class="sidebar-nav-link">
                <i class="bi bi-gear"></i>
                <span>Settings</span>
            </a>
        </nav>

        <!-- Bottom Footer Section (Fixed 70px) -->
        <div class="sidebar-footer">
            <form action="<?php echo e(route('logout')); ?>" method="POST" class="w-100">
                <?php echo csrf_field(); ?>
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
            <!-- Premium Search Bar -->
            <div class="search-wrapper-premium">
                <div class="search-box-premium">
                    <div class="search-icon-premium">
                        <i class="bi bi-search"></i>
                    </div>
                    <input type="text" class="search-input-premium" placeholder="Search projects, users, companies..." id="globalSearch">
                    <kbd class="search-shortcut-premium">
                        <span class="key">Ctrl</span>
                        <span class="plus">+</span>
                        <span class="key">K</span>
                    </kbd>
                </div>
            </div>

            <!-- Right Side Components Grouped Together -->
            <div class="topbar-right-group">
                <!-- Premium Date/Time Widget -->
                <div class="datetime-widget-premium">
                    <div class="datetime-row">
                        <div class="datetime-icon">
                            <i class="bi bi-calendar3"></i>
                        </div>
                        <div class="datetime-content">
                            <span class="date-text-premium" id="currentDate">Loading...</span>
                            <span class="time-text-premium" id="currentTime">--:--</span>
                        </div>
                    </div>
                </div>

                <!-- Premium Notifications Center -->
                <div class="dropdown notification-dropdown-wrapper">
                    <button class="notification-trigger-premium" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="notification-icon-wrapper">
                            <i class="bi bi-bell bell-icon"></i>
                            <span class="notification-badge-premium">3</span>
                            <span class="notification-pulse"></span>
                        </div>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end notification-dropdown-premium">
                        <!-- Header -->
                        <div class="notification-header-premium">
                            <h6 class="notification-title">
                                <i class="bi bi-bell-fill me-2"></i>
                                Notifications
                            </h6>
                            <button class="mark-all-read-btn" type="button">
                                <i class="bi bi-check2-all me-1"></i>
                                Mark all read
                            </button>
                        </div>

                        <!-- Notification List -->
                        <div class="notification-list-premium">
                            <!-- Notification Item 1 -->
                            <a href="#" class="notification-item-premium unread">
                                <div class="notification-icon-circle bg-primary">
                                    <i class="bi bi-person-plus"></i>
                                </div>
                                <div class="notification-content">
                                    <div class="notification-text">
                                        <strong>New user registered</strong>
                                        <p>John Doe has joined your organization</p>
                                    </div>
                                    <div class="notification-meta">
                                        <i class="bi bi-clock"></i>
                                        <span>2 minutes ago</span>
                                    </div>
                                </div>
                                <div class="unread-indicator"></div>
                            </a>

                            <!-- Notification Item 2 -->
                            <a href="#" class="notification-item-premium unread">
                                <div class="notification-icon-circle bg-success">
                                    <i class="bi bi-check-circle"></i>
                                </div>
                                <div class="notification-content">
                                    <div class="notification-text">
                                        <strong>Project approved</strong>
                                        <p>Phoenix Heights project has been approved</p>
                                    </div>
                                    <div class="notification-meta">
                                        <i class="bi bi-clock"></i>
                                        <span>1 hour ago</span>
                                    </div>
                                </div>
                                <div class="unread-indicator"></div>
                            </a>

                            <!-- Notification Item 3 -->
                            <a href="#" class="notification-item-premium unread">
                                <div class="notification-icon-circle bg-warning">
                                    <i class="bi bi-exclamation-triangle"></i>
                                </div>
                                <div class="notification-content">
                                    <div class="notification-text">
                                        <strong>System update</strong>
                                        <p>Scheduled maintenance at 2:00 AM tonight</p>
                                    </div>
                                    <div class="notification-meta">
                                        <i class="bi bi-clock"></i>
                                        <span>3 hours ago</span>
                                    </div>
                                </div>
                                <div class="unread-indicator"></div>
                            </a>
                        </div>

                        <!-- Footer -->
                        <div class="notification-footer-premium">
                            <a href="#" class="view-all-notifications">
                                View all notifications
                                <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- User Dropdown -->
                <div class="dropdown">
                    <div class="user-dropdown" data-bs-toggle="dropdown">
                        <div class="user-avatar">
                            <?php echo e(strtoupper(substr(auth()->user()->first_name, 0, 1))); ?><?php echo e(strtoupper(substr(auth()->user()->last_name, 0, 1))); ?>

                        </div>
                        <div class="d-none d-md-block">
                            <div style="font-weight: var(--fw-medium); font-size: var(--fs-small);">
                                <?php echo e(auth()->user()->first_name); ?> <?php echo e(auth()->user()->last_name); ?>

                            </div>
                            <div style="font-size: var(--fs-tiny); color: var(--color-text-muted);">
                                <?php echo e(auth()->user()->role->name); ?>

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
                            <form action="<?php echo e(route('logout')); ?>" method="POST">
                                <?php echo csrf_field(); ?>
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
            <?php if(session('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>
                <?php echo e(session('success')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <?php if(session('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle me-2"></i>
                <?php echo e(session('error')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <?php echo $__env->yieldContent('content'); ?>
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

        // Masters dropdown toggle
        const mastersToggle = document.getElementById('mastersToggle');
        const mastersSubmenu = document.getElementById('mastersSubmenu');
        
        if (mastersToggle && mastersSubmenu) {
            mastersToggle.addEventListener('click', function(e) {
                e.preventDefault();
                mastersSubmenu.classList.toggle('active');
                mastersToggle.classList.toggle('open');
            });
        }
    </script>

    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>

</html><?php /**PATH E:\Margadarsi_portal\resources\views/layouts/app.blade.php ENDPATH**/ ?>