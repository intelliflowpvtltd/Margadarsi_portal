@extends('layouts.auth')

@section('title', 'Forgot Password')

@section('content')
<div class="auth-card-header">
    <h2 class="auth-card-title">Forgot Password?</h2>
    <p class="auth-card-subtitle">Enter your email and we'll send you an OTP to reset your password</p>
</div>

<!-- Success Message -->
@if (session('status'))
<div class="alert alert-success" role="alert">
    <i class="bi bi-check-circle me-2" aria-hidden="true"></i>
    {{ session('status') }}
</div>
@endif

<!-- Rate Limit Warning -->
@if (session('throttle'))
<div class="alert alert-warning" role="alert">
    <i class="bi bi-clock me-2" aria-hidden="true"></i>
    {{ session('throttle') }}
</div>
@endif

<!-- Error Messages -->
@if ($errors->any())
<div class="alert alert-danger" role="alert">
    <i class="bi bi-exclamation-circle me-2" aria-hidden="true"></i>
    {{ $errors->first() }}
</div>
@endif

<!-- Forgot Password Form -->
<form method="POST" action="{{ route('password.email') }}" class="auth-form" novalidate>
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
                placeholder="Enter your registered email"
                value="{{ old('email') }}"
                autocomplete="email"
                inputmode="email"
                aria-label="Email address"
                aria-describedby="email-help"
                required
                autofocus>
        </div>
        <small id="email-help" class="text-muted">We'll send a 6-digit code to this email</small>
    </div>

    <!-- Send OTP Button -->
    <button type="submit" class="btn btn-primary mb-3" id="send-otp-btn">
        <i class="bi bi-send" aria-hidden="true"></i>
        <span class="btn-text">Send OTP</span>
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
    .alert-warning {
        background-color: #FEF3C7;
        color: #92400E;
        border-left: 4px solid #F59E0B;
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
            const button = document.getElementById('send-otp-btn');
            const btnText = button.querySelector('.btn-text');
            
            // Trim email before submit
            emailInput.value = emailInput.value.trim().toLowerCase();
            
            button.disabled = true;
            btnText.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Sending OTP...';
        });
    });
</script>
@endpush