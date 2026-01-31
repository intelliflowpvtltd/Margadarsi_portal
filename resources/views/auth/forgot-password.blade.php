@extends('layouts.auth')

@section('title', 'Forgot Password')

@section('content')
<div class="auth-card-header">
    <h2 class="auth-card-title">Forgot Password?</h2>
    <p class="auth-card-subtitle">Enter your email and we'll send you an OTP to reset your password</p>
</div>

<!-- Success Message -->
@if (session('status'))
<div class="alert alert-success">
    <i class="bi bi-check-circle me-2"></i>
    {{ session('status') }}
</div>
@endif

<!-- Error Messages -->
@if ($errors->any())
<div class="alert alert-danger">
    <i class="bi bi-exclamation-circle me-2"></i>
    {{ $errors->first() }}
</div>
@endif

<!-- Forgot Password Form -->
<form method="POST" action="{{ route('password.email') }}" class="auth-form">
    @csrf

    <!-- Email Field -->
    <div class="form-group">
        <label for="email" class="form-label">Email Address</label>
        <div class="input-icon">
            <i class="bi bi-envelope"></i>
            <input type="email"
                class="form-control"
                id="email"
                name="email"
                placeholder="Enter your registered email"
                value="{{ old('email') }}"
                required
                autofocus>
        </div>
    </div>

    <!-- Send OTP Button -->
    <button type="submit" class="btn btn-primary mb-3">
        <i class="bi bi-send"></i>
        Send OTP
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
        const form = document.querySelector('.auth-form');
        form.addEventListener('submit', function(e) {
            const button = form.querySelector('button[type="submit"]');
            button.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Sending OTP...';
            button.disabled = true;
        });
    });
</script>
@endpush