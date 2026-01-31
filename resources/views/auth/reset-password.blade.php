@extends('layouts.auth')

@section('title', 'Reset Password')

@section('content')
<div class="auth-card-header">
    <h2 class="auth-card-title">Reset Password</h2>
    <p class="auth-card-subtitle">Create a strong password for your account</p>
</div>

<!-- Error Messages -->
@if ($errors->any())
<div class="alert alert-danger">
    <i class="bi bi-exclamation-circle me-2"></i>
    {{ $errors->first() }}
</div>
@endif

<!-- Reset Password Form -->
<form method="POST" action="{{ route('password.update') }}" class="auth-form">
    @csrf

    <input type="hidden" name="token" value="{{ $token ?? session('reset_token') }}">
    <input type="hidden" name="email" value="{{ $email ?? session('email') }}">

    <!-- New Password Field -->
    <div class="form-group">
        <label for="password" class="form-label">New Password</label>
        <div class="input-icon">
            <i class="bi bi-lock"></i>
            <input type="password"
                class="form-control"
                id="password"
                name="password"
                placeholder="Enter new password"
                required
                autofocus>
        </div>
        <small class="text-muted">Minimum 8 characters required</small>
    </div>

    <!-- Confirm Password Field -->
    <div class="form-group">
        <label for="password_confirmation" class="form-label">Confirm Password</label>
        <div class="input-icon">
            <i class="bi bi-lock-fill"></i>
            <input type="password"
                class="form-control"
                id="password_confirmation"
                name="password_confirmation"
                placeholder="Re-enter new password"
                required>
        </div>
    </div>

    <!-- Password Strength Indicator -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <small class="text-muted">Password Strength:</small>
            <small id="strength-text" class="fw-medium">-</small>
        </div>
        <div class="progress" style="height: 6px; border-radius: 3px;">
            <div id="strength-bar"
                class="progress-bar"
                role="progressbar"
                style="width: 0%; transition: all 0.3s ease;"></div>
        </div>
    </div>

    <!-- Reset Password Button -->
    <button type="submit" class="btn btn-primary mb-3">
        <i class="bi bi-shield-check"></i>
        Reset Password
    </button>

    <!-- Back to Login Link -->
    <div class="text-center">
        <a href="{{ route('login') }}" class="auth-link">
            <i class="bi bi-arrow-left me-1"></i>
            Back to Login
        </a>
    </div>
</form>

<!-- Footer -->
<div class="auth-footer">
    <p>&copy; {{ date('Y') }} Margadarsi Infrastructure. All rights reserved.</p>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const passwordInput = document.getElementById('password');
        const confirmInput = document.getElementById('password_confirmation');
        const strengthBar = document.getElementById('strength-bar');
        const strengthText = document.getElementById('strength-text');
        const form = document.querySelector('.auth-form');

        // Password strength checker
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;

            if (password.length >= 8) strength += 25;
            if (password.length >= 12) strength += 25;
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength += 25;
            if (/[0-9]/.test(password)) strength += 15;
            if (/[^a-zA-Z0-9]/.test(password)) strength += 10;

            strengthBar.style.width = strength + '%';

            if (strength < 30) {
                strengthBar.className = 'progress-bar bg-danger';
                strengthText.textContent = 'Weak';
                strengthText.style.color = '#EF4444';
            } else if (strength < 60) {
                strengthBar.className = 'progress-bar bg-warning';
                strengthText.textContent = 'Fair';
                strengthText.style.color = '#F59E0B';
            } else if (strength < 90) {
                strengthBar.className = 'progress-bar';
                strengthBar.style.backgroundColor = 'var(--color-coffee-gold)';
                strengthText.textContent = 'Good';
                strengthText.style.color = 'var(--color-coffee-gold-dark)';
            } else {
                strengthBar.className = 'progress-bar bg-success';
                strengthText.textContent = 'Strong';
                strengthText.style.color = '#10B981';
            }
        });

        // Password match validation
        confirmInput.addEventListener('input', function() {
            if (this.value && this.value !== passwordInput.value) {
                this.setCustomValidity('Passwords do not match');
                this.classList.add('is-invalid');
            } else {
                this.setCustomValidity('');
                this.classList.remove('is-invalid');
            }
        });

        // Form submission
        form.addEventListener('submit', function(e) {
            if (confirmInput.value !== passwordInput.value) {
                e.preventDefault();
                confirmInput.classList.add('is-invalid');
                return false;
            }

            const button = form.querySelector('button[type="submit"]');
            button.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Resetting...';
            button.disabled = true;
        });
    });
</script>
@endpush