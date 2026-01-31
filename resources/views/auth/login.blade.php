@extends('layouts.auth')

@section('title', 'Login')

@section('content')
<div class="auth-card-header">
    <h2 class="auth-card-title">Welcome Back</h2>
    <p class="auth-card-subtitle">Please login to your account to continue</p>
</div>

<!-- Success/Status Messages -->
@if (session('status'))
<div class="alert alert-success" role="alert">
    <i class="bi bi-check-circle me-2" aria-hidden="true"></i>
    {{ session('status') }}
</div>
@endif

<!-- Error Messages -->
@if ($errors->any())
<div class="alert alert-danger" role="alert">
    <i class="bi bi-exclamation-circle me-2" aria-hidden="true"></i>
    {{ $errors->first('email') ?? $errors->first() }}
</div>
@endif

<!-- Login Form -->
<form method="POST" action="{{ route('login') }}" class="auth-form" novalidate>
    @csrf

    <!-- Email Field -->
    <div class="form-group">
        <label for="email" class="form-label">Email Address</label>
        <div class="input-icon">
            <i class="bi bi-envelope" aria-hidden="true"></i>
            <input type="email"
                class="form-control @error('email') is-invalid @enderror"
                id="email"
                name="email"
                placeholder="Enter your email"
                value="{{ old('email') }}"
                autocomplete="email"
                inputmode="email"
                aria-label="Email address"
                aria-describedby="email-error"
                required
                autofocus>
        </div>
        @error('email')
        <div id="email-error" class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <!-- Password Field -->
    <div class="form-group">
        <label for="password" class="form-label">Password</label>
        <div class="input-icon password-wrapper">
            <i class="bi bi-lock" aria-hidden="true"></i>
            <input type="password"
                class="form-control @error('password') is-invalid @enderror"
                id="password"
                name="password"
                placeholder="Enter your password"
                autocomplete="current-password"
                aria-label="Password"
                minlength="8"
                maxlength="128"
                required>
            <button type="button" 
                class="password-toggle" 
                onclick="togglePassword('password', this)"
                aria-label="Toggle password visibility">
                <i class="bi bi-eye"></i>
            </button>
        </div>
    </div>

    <!-- Remember Me & Forgot Password Row -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="form-check">
            <input class="form-check-input" 
                type="checkbox" 
                name="remember" 
                id="remember"
                {{ old('remember') ? 'checked' : '' }}>
            <label class="form-check-label" for="remember">
                Remember me
            </label>
        </div>
        <a href="{{ route('password.request') }}" class="auth-link">Forgot password?</a>
    </div>

    <!-- Login Button -->
    <button type="submit" class="btn btn-primary" id="login-btn">
        <i class="bi bi-box-arrow-in-right" aria-hidden="true"></i>
        <span class="btn-text">Sign In</span>
    </button>
</form>

<!-- Footer -->
<div class="auth-footer">
    <p>&copy; {{ date('Y') }} Margadarsi Infrastructure. All rights reserved.</p>
</div>
@endsection

@push('styles')
<style>
    .password-wrapper {
        position: relative;
    }
    
    /* Password toggle button - properly positioned inside input */
    .password-toggle {
        position: absolute;
        right: 0.75rem;  /* Slightly closer to edge for better containment */
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: var(--color-text-muted);
        cursor: pointer;
        padding: 0.5rem;  /* Increased for better touch target */
        transition: color 0.2s ease;
        z-index: 10;
        display: flex;
        align-items: center;
        justify-content: center;
        height: 2rem;
        width: 2rem;
    }
    
    .password-toggle:hover {
        color: var(--color-coffee-gold);
    }
    
    .password-toggle:focus {
        outline: 2px solid var(--color-coffee-gold);
        outline-offset: 2px;
        border-radius: 4px;
    }
    
    .password-toggle i {
        font-size: 1.125rem;
        line-height: 1;
    }
    
    /* Ensure password input has enough padding for both icon and toggle */
    .password-wrapper .form-control {
        padding-right: 3rem !important;  /* Space for toggle button */
    }
    
    /* Keep the left padding from .input-icon */
    .password-wrapper.input-icon .form-control {
        padding-left: 3rem !important;   /* Space for lock icon */
        padding-right: 3rem !important;  /* Space for toggle button */
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('.auth-form');
        const emailInput = document.getElementById('email');

        // Trim email whitespace on blur
        emailInput.addEventListener('blur', function() {
            this.value = this.value.trim().toLowerCase();
        });

        // Form submission with loading state
        form.addEventListener('submit', function(e) {
            const button = document.getElementById('login-btn');
            const btnText = button.querySelector('.btn-text');
            
            // Trim email before submit
            emailInput.value = emailInput.value.trim().toLowerCase();
            
            button.disabled = true;
            btnText.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Signing in...';
        });
    });

    // Password visibility toggle
    function togglePassword(inputId, button) {
        const input = document.getElementById(inputId);
        const icon = button.querySelector('i');
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'bi bi-eye-slash';
            button.setAttribute('aria-label', 'Hide password');
        } else {
            input.type = 'password';
            icon.className = 'bi bi-eye';
            button.setAttribute('aria-label', 'Show password');
        }
    }
</script>
@endpush