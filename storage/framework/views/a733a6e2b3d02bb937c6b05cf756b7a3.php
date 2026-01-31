<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    
    <!-- Security: Prevent indexing of auth pages -->
    <meta name="robots" content="noindex, nofollow">
    
    <!-- Security: CSP fallback (primary CSP is in SecurityHeaders middleware) -->
    <meta http-equiv="Content-Security-Policy" content="default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com https://cdn.jsdelivr.net; img-src 'self' data:; connect-src 'self' https://cdn.jsdelivr.net;">

    <title><?php echo $__env->yieldContent('title', 'Login'); ?> - Margadarsi Portal</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo e(asset('favicon.png')); ?>?v=2">

    <!-- Fonts (with crossorigin for SRI) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS with SRI -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" 
          rel="stylesheet" 
          integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" 
          crossorigin="anonymous">

    <!-- Bootstrap Icons (no SRI - hashes change between minor versions) -->
    <link rel="stylesheet" 
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">

    <?php echo $__env->yieldPushContent('styles'); ?>

    <style>
        /* ========== CSS VARIABLES ========== */
        :root {
            /* Colors */
            --color-off-white: #FAF9F6;
            --color-dark-maroon: #800020;
            --color-maroon-light: #A0152E;
            --color-coffee-gold: #B8956A;
            --color-coffee-gold-dark: #9C7A54;
            --color-coffee-gold-light: #D4B896;
            --color-white: #FFFFFF;
            --color-text-dark: #2C2C2C;
            --color-text-muted: #6C757D;

            /* Fonts */
            --font-display: 'Playfair Display', Georgia, serif;
            --font-body: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;

            /* Spacing */
            --spacing-xs: 0.5rem;
            --spacing-sm: 0.75rem;
            --spacing-md: 1rem;
            --spacing-lg: 1.5rem;
            --spacing-xl: 2rem;
            --spacing-2xl: 3rem;
            --spacing-3xl: 4rem;

            /* Other */
            --border-radius: 20px;
            --border-radius-xl: 16px;

            /* Enhanced Shadows */
            --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.08);
            --shadow-lg: 0 8px 24px rgba(0, 0, 0, 0.12);
            --shadow-xl: 0 12px 36px rgba(128, 0, 32, 0.2);
            --shadow-2xl: 0 20px 60px rgba(0, 0, 0, 0.15);
            --shadow-gold: 0 4px 16px rgba(184, 149, 106, 0.3);
            --shadow-gold-lg: 0 8px 24px rgba(184, 149, 106, 0.4);

            /* Smooth Transitions */
            --transition-smooth: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            --transition-fast: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            --transition-slow: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* ========== RESET & BASE ========== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            height: 100%;
            width: 100%;
            overflow-x: hidden;
        }

        body {
            font-family: var(--font-body);
            font-size: 16px;
            line-height: 1.6;
            color: var(--color-text-dark);
            background-color: var(--color-off-white);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* ========== MAIN CONTAINER ========== */
        .auth-main-container {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            width: 100%;
            padding: 2rem;
            background: linear-gradient(135deg, #FAF9F6 0%, #F5F1ED 50%, #FAF9F6 100%);
            background-size: 400% 400%;
            animation: subtleGradient 20s ease infinite;
            position: relative;
            overflow: hidden;
        }

        /* Animated Background Gradient */
        @keyframes subtleGradient {

            0%,
            100% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }
        }

        /* Floating Ambient Orb 1 */
        .auth-main-container::before {
            content: '';
            position: absolute;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(184, 149, 106, 0.08) 0%, transparent 70%);
            border-radius: 50%;
            top: -200px;
            left: -200px;
            animation: floatAmbient1 25s ease-in-out infinite;
            pointer-events: none;
            z-index: 0;
        }

        /* Floating Ambient Orb 2 */
        .auth-main-container::after {
            content: '';
            position: absolute;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(128, 0, 32, 0.04) 0%, transparent 70%);
            border-radius: 50%;
            bottom: -150px;
            right: -150px;
            animation: floatAmbient2 30s ease-in-out infinite;
            pointer-events: none;
            z-index: 0;
        }

        /* Ambient Orb Animations */
        @keyframes floatAmbient1 {

            0%,
            100% {
                transform: translate(0, 0) scale(1);
                opacity: 0.6;
            }

            33% {
                transform: translate(50px, 50px) scale(1.1);
                opacity: 0.8;
            }

            66% {
                transform: translate(-30px, 80px) scale(0.95);
                opacity: 0.7;
            }
        }

        @keyframes floatAmbient2 {

            0%,
            100% {
                transform: translate(0, 0) scale(1);
                opacity: 0.5;
            }

            25% {
                transform: translate(-60px, -40px) scale(1.15);
                opacity: 0.65;
            }

            50% {
                transform: translate(-30px, 30px) scale(0.9);
                opacity: 0.55;
            }

            75% {
                transform: translate(40px, -20px) scale(1.05);
                opacity: 0.6;
            }
        }

        /* ========== SPLIT RECTANGLE CONTAINER ========== */
        .auth-split-container {
            display: flex;
            width: 100%;
            max-width: 1000px !important;
            height: 580px !important;
            background-color: var(--color-white);
            border-radius: 20px !important;
            overflow: hidden !important;
            position: relative;
            z-index: 1;
            box-shadow:
                0 20px 60px rgba(128, 0, 32, 0.15),
                0 8px 24px rgba(0, 0, 0, 0.1),
                0 2px 8px rgba(0, 0, 0, 0.05);
        }

        /* ========== LEFT PANEL (Dark Maroon) ========== */
        .auth-left-panel {
            flex: 1;
            background: linear-gradient(135deg, #800020 0%, #5C0011 100%);
            padding: 2.5rem var(--spacing-3xl);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: var(--color-white);
            position: relative;
            overflow: hidden;
            height: 580px !important;
        }

        /* Animated Background Orbs */
        .auth-left-panel::before {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(184, 149, 106, 0.3) 0%, transparent 70%);
            border-radius: 50%;
            top: -100px;
            right: -100px;
            animation: floatOrb1 20s ease-in-out infinite;
        }

        .auth-left-panel::after {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(212, 184, 150, 0.2) 0%, transparent 70%);
            border-radius: 50%;
            bottom: -80px;
            left: -80px;
            animation: floatOrb2 15s ease-in-out infinite;
        }

        /* Orb Animations */
        @keyframes floatOrb1 {

            0%,
            100% {
                transform: translate(0, 0) scale(1);
                opacity: 0.3;
            }

            25% {
                transform: translate(-30px, 30px) scale(1.1);
                opacity: 0.4;
            }

            50% {
                transform: translate(-50px, 0px) scale(0.9);
                opacity: 0.35;
            }

            75% {
                transform: translate(-20px, -30px) scale(1.05);
                opacity: 0.38;
            }
        }

        @keyframes floatOrb2 {

            0%,
            100% {
                transform: translate(0, 0) scale(1);
                opacity: 0.2;
            }

            33% {
                transform: translate(40px, -25px) scale(1.15);
                opacity: 0.25;
            }

            66% {
                transform: translate(20px, 20px) scale(0.95);
                opacity: 0.22;
            }
        }


        .auth-left-content {
            position: relative;
            z-index: 2;
        }

        .auth-logo {
            margin-bottom: var(--spacing-2xl);
        }

        .auth-logo h1 {
            font-family: var(--font-display);
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: var(--spacing-sm);
            letter-spacing: 2px;
            color: var(--color-coffee-gold-light);
        }

        .auth-logo p {
            font-size: 1.125rem;
            font-weight: 300;
            color: rgba(255, 255, 255, 0.9);
            letter-spacing: 0.5px;
        }

        .auth-features {
            margin-top: var(--spacing-2xl);
            text-align: left;
        }

        .auth-feature-item {
            display: flex;
            align-items: center;
            margin-bottom: var(--spacing-lg);
        }

        .auth-feature-icon {
            width: 48px;
            height: 48px;
            background-color: rgba(184, 149, 106, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: var(--spacing-md);
            flex-shrink: 0;
        }

        .auth-feature-icon i {
            font-size: 1.5rem;
            color: var(--color-coffee-gold-light);
        }

        .auth-feature-text h3 {
            font-size: 1.125rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
            color: var(--color-white);
        }

        .auth-feature-text p {
            font-size: 0.875rem;
            color: rgba(255, 255, 255, 0.8);
            margin: 0;
        }

        /* ========== RIGHT PANEL (White with Login Card) ========== */
        .auth-right-panel {
            flex: 1;
            background-color: var(--color-white);
            padding: 2.5rem var(--spacing-3xl);
            display: flex;
            align-items: center;
            justify-content: center;
            height: 580px !important;
            overflow-y: auto;
        }

        .auth-login-card {
            width: 100%;
            max-width: 420px;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 16px;
            padding: 0.75rem 2rem;
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow:
                0 8px 32px rgba(128, 0, 32, 0.1),
                0 4px 16px rgba(0, 0, 0, 0.05),
                0 2px 8px rgba(0, 0, 0, 0.03);
            transition: var(--transition-smooth);
        }

        /* Glassmorphism Fallback for unsupported browsers */
        @supports not (backdrop-filter: blur(20px)) {
            .auth-login-card {
                background: rgba(255, 255, 255, 0.95);
            }
        }

        /* Subtle hover effect */
        .auth-login-card:hover {
            box-shadow:
                0 12px 40px rgba(128, 0, 32, 0.15),
                0 6px 20px rgba(0, 0, 0, 0.08),
                0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .auth-card-header {
            margin-bottom: var(--spacing-sm);
            text-align: center;
        }

        .auth-card-title {
            font-family: var(--font-display);
            font-size: 2rem;
            font-weight: 700;
            color: var(--color-dark-maroon);
            margin-bottom: var(--spacing-sm);
            line-height: 1.2;
        }

        .auth-card-subtitle {
            font-size: 0.9375rem;
            color: var(--color-text-muted);
            margin-bottom: 0;
        }

        /* ========== FORM STYLES ========== */
        .auth-form {
            width: 100%;
        }

        .form-group {
            margin-bottom: var(--spacing-sm);
        }

        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--color-text-dark);
            margin-bottom: var(--spacing-xs);
        }

        .form-control {
            width: 100%;
            padding: 0.65rem 1rem;
            font-size: 0.9375rem;
            font-family: var(--font-body);
            color: var(--color-text-dark);
            background-color: var(--color-white);
            border: 2px solid #E5E7EB;
            border-radius: 8px;
            transition: var(--transition-smooth);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--color-coffee-gold);
            box-shadow:
                0 0 0 3px rgba(184, 149, 106, 0.1),
                0 4px 12px rgba(184, 149, 106, 0.15);
            transform: translateY(-1px);
        }

        .form-control::placeholder {
            color: #9CA3AF;
        }

        .input-icon {
            position: relative;
        }

        .input-icon i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--color-text-muted);
            font-size: 1.125rem;
        }

        .input-icon .form-control {
            padding-left: 3rem;
        }

        .form-check {
            display: flex;
            align-items: center;
            margin-bottom: var(--spacing-lg);
        }

        .form-check-input {
            width: 1.125rem;
            height: 1.125rem;
            margin-right: var(--spacing-sm);
            border: 2px solid #E5E7EB;
            border-radius: 4px;
            cursor: pointer;
        }

        .form-check-input:checked {
            background-color: var(--color-coffee-gold);
            border-color: var(--color-coffee-gold);
        }

        .form-check-label {
            font-size: 0.875rem;
            color: var(--color-text-dark);
            cursor: pointer;
        }

        /* ========== BUTTON STYLES ========== */
        .btn {
            width: 100%;
            padding: 1rem 1.5rem;
            font-size: 0.9375rem;
            font-weight: 600;
            font-family: var(--font-body);
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: var(--transition-smooth);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            position: relative;
            overflow: hidden;
        }

        .btn-primary {
            background: linear-gradient(135deg, #B8956A 0%, #D4B896 50%, #B8956A 100%);
            background-size: 200% 200%;
            color: var(--color-white);
            box-shadow:
                0 4px 15px rgba(184, 149, 106, 0.3),
                0 2px 5px rgba(184, 149, 106, 0.2),
                inset 0 1px 1px rgba(255, 255, 255, 0.2);
        }

        .btn-primary:hover {
            background-position: 100% 0;
            box-shadow:
                0 6px 20px rgba(184, 149, 106, 0.4),
                0 4px 10px rgba(184, 149, 106, 0.3),
                inset 0 1px 1px rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }

        .btn-primary:active {
            transform: translateY(1px) scale(0.98);
            box-shadow:
                0 2px 8px rgba(184, 149, 106, 0.3),
                inset 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        /* Button Loading State */
        .btn-loading {
            color: transparent;
            pointer-events: none;
        }

        .btn-loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            top: 50%;
            left: 50%;
            margin-left: -10px;
            margin-top: -10px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .btn i {
            margin-right: var(--spacing-xs);
        }

        /* ========== LINKS ========== */
        .auth-link {
            display: inline-block;
            font-size: 0.875rem;
            color: var(--color-coffee-gold-dark);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition-smooth);
        }

        .auth-link:hover {
            color: var(--color-coffee-gold);
            text-decoration: underline;
        }

        .auth-footer {
            margin-top: var(--spacing-xs);
            padding-top: 0;
            border-top: 1px solid #E5E7EB;
            text-align: center;
        }

        .auth-footer p {
            font-size: 0.875rem;
            color: var(--color-text-muted);
            margin-bottom: 0;
        }

        /* ========== ALERTS ========== */
        .alert {
            padding: 0.875rem 1rem;
            margin-bottom: var(--spacing-lg);
            border-radius: 8px;
            font-size: 0.875rem;
        }

        .alert-success {
            background-color: #D1FAE5;
            color: #065F46;
            border-left: 4px solid #10B981;
        }

        .alert-danger {
            background-color: #FEE2E2;
            color: #991B1B;
            border-left: 4px solid #EF4444;
        }

        /* ========== RESPONSIVE DESIGN ========== */
        @media (max-width: 992px) {
            .auth-split-container {
                flex-direction: column;
                height: auto;
                max-width: 540px;
            }

            .auth-left-panel {
                padding: var(--spacing-2xl);
                min-height: 300px;
            }

            .auth-logo h1 {
                font-size: 2.5rem;
            }

            .auth-features {
                display: none;
            }

            .auth-right-panel {
                padding: var(--spacing-2xl);
            }
        }

        @media (max-width: 576px) {
            .auth-main-container {
                padding: var(--spacing-md);
            }

            .auth-left-panel,
            .auth-right-panel {
                padding: var(--spacing-lg);
            }

            .auth-logo h1 {
                font-size: 2rem;
            }

            .auth-card-title {
                font-size: 1.75rem;
            }
        }

        /* ========== ACCESSIBILITY: REDUCED MOTION ========== */
        @media (prefers-reduced-motion: reduce) {

            *,
            *::before,
            *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }
    </style>
</head>

<body>
    <!-- Main Container -->
    <div class="auth-main-container">
        <!-- Split Rectangle Container -->
        <div class="auth-split-container">

            <!-- LEFT PANEL: Dark Maroon with Sliding Content -->
            <div class="auth-left-panel">
                <div class="auth-left-content">
                    <div class="auth-logo">
                        <h1>MARGADARSI</h1>
                        <p>Real Estate Management Portal</p>
                    </div>

                    <div class="auth-features">
                        <div class="auth-feature-item">
                            <div class="auth-feature-icon">
                                <i class="bi bi-shield-check"></i>
                            </div>
                            <div class="auth-feature-text">
                                <h3>Secure Access</h3>
                                <p>Bank-grade encryption & security</p>
                            </div>
                        </div>

                        <div class="auth-feature-item">
                            <div class="auth-feature-icon">
                                <i class="bi bi-building"></i>
                            </div>
                            <div class="auth-feature-text">
                                <h3>Property Management</h3>
                                <p>Manage all your projects effortlessly</p>
                            </div>
                        </div>

                        <div class="auth-feature-item">
                            <div class="auth-feature-icon">
                                <i class="bi bi-graph-up-arrow"></i>
                            </div>
                            <div class="auth-feature-text">
                                <h3>Real-time Analytics</h3>
                                <p>Track performance & insights</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT PANEL: White with Login Card -->
            <div class="auth-right-panel">
                <div class="auth-login-card">
                    <?php echo $__env->yieldContent('content'); ?>
                </div>
            </div>

        </div>
    </div>

    <!-- Noscript Fallback -->
    <noscript>
        <div style="background: #FEE2E2; color: #991B1B; padding: 20px; text-align: center; position: fixed; top: 0; left: 0; right: 0; z-index: 9999;">
            <strong>JavaScript Required</strong> - Please enable JavaScript in your browser to use this application.
        </div>
    </noscript>

    <!-- Bootstrap JS with SRI -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
            crossorigin="anonymous"></script>

    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>

</html><?php /**PATH E:\Margadarsi_portal\resources\views/layouts/auth.blade.php ENDPATH**/ ?>