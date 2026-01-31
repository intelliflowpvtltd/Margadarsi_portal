@extends('layouts.auth')

@section('title', 'Login')

@section('content')
<div class="auth-card-header">
    <h2 class="auth-card-title">Welcome Back</h2>
    <p class="auth-card-subtitle">Please login to your account to continue</p>
</div>

<!-- Success/Status Messages -->
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
    {{ $errors->first('email') ?? $errors->first() }}
</div>
@endif

<!-- Login Form -->
<form method="POST" action="{{ route('login') }}" class="auth-form">
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
                placeholder="Enter your email"
                value="{{ old('email') }}"
                required
                autofocus>
        </div>
    </div>

    <!-- Password Field -->
    <div class="form-group">
        <label for="password" class="form-label">Password</label>
        <div class="input-icon">
            <i class="bi bi-lock"></i>
            <input type="password"
                class="form-control"
                id="password"
                name="password"
                placeholder="Enter your password"
                required>
        </div>
    </div>

    <!-- Remember Me & Forgot Password Row -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="remember" id="remember">
            <label class="form-check-label" for="remember">
                Remember me
            </label>
        </div>
        <a href="{{ route('password.request') }}" class="auth-link">Forgot password?</a>
    </div>

    <!-- Login Button -->
    <button type="submit" class="btn btn-primary">
        <i class="bi bi-box-arrow-in-right"></i>
        Sign In
    </button>
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
            button.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Signing in...';
            button.disabled = true;
        });
    });
</script>
@endpush