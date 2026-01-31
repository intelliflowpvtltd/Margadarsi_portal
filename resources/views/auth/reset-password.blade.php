@extends('layouts.auth')

@section('title', 'Reset Password')

@section('content')
<div class="auth-card-header">
    <h2 class="auth-card-title">Reset Password</h2>
    <p class="auth-card-subtitle">Create a strong password for your account</p>
</div>

<!-- Error Messages -->
@if ($errors->any())
<div class="alert alert-danger" role="alert">
    <i class="bi bi-exclamation-circle me-2" aria-hidden="true"></i>
    {{ $errors->first() }}
</div>
@endif

<!-- Reset Password Form -->
<form method="POST" action="{{ route('password.update') }}" class="auth-form" novalidate>
    @csrf

    <input type="hidden" name="reset_token" value="{{ $token ?? session('reset_token') }}">
    <input type="hidden" name="email" value="{{ $email ?? session('email') }}">

    <!-- New Password Field -->
    <div class="form-group">
        <label for="password" class="form-label">New Password</label>
        <div class="input-icon password-wrapper">
            <i class="bi bi-lock" aria-hidden="true"></i>
            <input type="password"
                class="form-control @error('password') is-invalid @enderror"
                id="password"
                name="password"
                placeholder="Enter new password"
                autocomplete="new-password"
                aria-label="New password"
                minlength="8"
                maxlength="128"
                required
                autofocus>
            <button type="button" 
                class="password-toggle" 
                onclick="togglePassword('password', this)"
                aria-label="Toggle password visibility">
                <i class="bi bi-eye"></i>
            </button>
        </div>
        <small class="text-muted">At least 8 characters with uppercase, lowercase, number & symbol</small>
    </div>

    <!-- Confirm Password Field -->
    <div class="form-group">
        <label for="password_confirmation" class="form-label">Confirm Password</label>
        <div class="input-icon password-wrapper">
            <i class="bi bi-lock-fill" aria-hidden="true"></i>
            <input type="password"
                class="form-control"
                id="password_confirmation"
                name="password_confirmation"
                placeholder="Re-enter new password"
                autocomplete="new-password"
                aria-label="Confirm new password"
                required>
            <button type="button" 
                class="password-toggle" 
                onclick="togglePassword('password_confirmation', this)"
                aria-label="Toggle password confirmation visibility">
                <i class="bi bi-eye"></i>
            </button>
        </div>
    </div>

    <!-- Reset Password Button -->
    <button type="submit" class="btn btn-primary mb-3" id="reset-btn">
        <i class="bi bi-shield-check" aria-hidden="true"></i>
        <span class="btn-text">Reset Password</span>
    </button>

    <!-- Back to Login Link -->
    <div class="text-center">
        <a href="{{ route('login') }}" class="auth-link">
            <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>
            Back to Login
        </a>
    </div>
</form>

<!-- Footer -->
<div class="auth-footer">
    <p>&copy; {{ date('Y') }} Margadarsi Infrastructure. All rights reserved.</p>
</div>
@endsection

@push('styles')
<style>
    /* Compact card padding for reset password */
    .auth-login-card {
        padding: 0.5rem 2rem !important;  /* Reduced vertical padding */
    }
    
    .password-wrapper {
        position: relative;
    }
    
    /* Password toggle button - properly positioned inside input */
    .password-toggle {
        position: absolute;
        right: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: var(--color-text-muted);
        cursor: pointer;
        padding: 0.5rem;
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
        padding-right: 3rem !important;
    }
    
    /* Keep the left padding from .input-icon */
    .password-wrapper.input-icon .form-control {
        padding-left: 3rem !important;
        padding-right: 3rem !important;
    }
    
    /* Compact spacing for reset password */
    .auth-form .form-group {
        margin-bottom: 0.875rem;
    }
    
    .auth-form .form-group small {
        display: block;
        margin-top: 0.25rem;
   }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('.auth-form');
        const resetBtn = document.getElementById('reset-btn');

        // Form submission with loading state
        form.addEventListener('submit', function(e) {
            const btnText = resetBtn.querySelector('.btn-text');
            resetBtn.disabled = true;
            btnText.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Resetting...';
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