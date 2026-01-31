@extends('layouts.auth')

@section('title', 'Verify OTP')

@section('content')
<div class="auth-card-header">
    <h2 class="auth-card-title">Verify OTP</h2>
    <p class="auth-card-subtitle">Enter the 6-digit code sent to {{ session('email') ?? 'your email' }}</p>
</div>

<!-- Error Messages -->
@if ($errors->any())
<div class="alert alert-danger">
    <i class="bi bi-exclamation-circle me-2"></i>
    {{ $errors->first() }}
</div>
@endif

<!-- OTP Verification Form -->
<form method="POST" action="{{ route('password.verify-otp') }}" class="auth-form">
    @csrf

    <input type="hidden" name="email" value="{{ session('email') ?? old('email') }}">

    <!-- OTP Input -->
    <div class="form-group">
        <label class="form-label">Enter OTP Code</label>
        <div class="d-flex justify-content-between gap-2 mb-3" id="otp-inputs">
            <input type="text" class="form-control text-center otp-box" maxlength="1" pattern="[0-9]" data-index="0" required>
            <input type="text" class="form-control text-center otp-box" maxlength="1" pattern="[0-9]" data-index="1" required>
            <input type="text" class="form-control text-center otp-box" maxlength="1" pattern="[0-9]" data-index="2" required>
            <input type="text" class="form-control text-center otp-box" maxlength="1" pattern="[0-9]" data-index="3" required>
            <input type="text" class="form-control text-center otp-box" maxlength="1" pattern="[0-9]" data-index="4" required>
            <input type="text" class="form-control text-center otp-box" maxlength="1" pattern="[0-9]" data-index="5" required>
        </div>
        <!-- Hidden input for full OTP -->
        <input type="hidden" name="otp" id="otp-value">
    </div>

    <!-- Timer -->
    <div class="text-center mb-4">
        <small class="text-muted">
            Code expires in: <span id="timer" class="fw-bold" style="color: var(--color-coffee-gold-dark);">10:00</span>
        </small>
    </div>

    <!-- Verify Button -->
    <button type="submit" class="btn btn-primary mb-3" id="verify-btn">
        <i class="bi bi-check-circle"></i>
        Verify OTP
    </button>

    <!-- Resend OTP -->
    <div class="text-center">
        <a href="{{ route('password.request') }}" class="auth-link">
            Didn't receive code? <strong>Resend OTP</strong>
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
    .otp-box {
        width: 50px !important;
        height: 60px;
        font-size: 1.5rem;
        font-weight: 700;
        padding: 0;
    }

    .otp-box:focus {
        border-color: var(--color-coffee-gold);
        box-shadow: 0 0 0 3px rgba(184, 149, 106, 0.1);
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const otpInputs = document.querySelectorAll('.otp-box');
        const otpValue = document.getElementById('otp-value');
        const form = document.querySelector('.auth-form');

        // OTP Input Handling
        otpInputs.forEach((input, index) => {
            input.addEventListener('input', function(e) {
                const value = e.target.value;

                // Only allow numbers
                if (!/^[0-9]$/.test(value)) {
                    e.target.value = '';
                    return;
                }

                // Move to next input
                if (value && index < otpInputs.length - 1) {
                    otpInputs[index + 1].focus();
                }

                updateOTPValue();
            });

            // Handle backspace
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && !e.target.value && index > 0) {
                    otpInputs[index - 1].focus();
                }
            });

            // Handle paste
            input.addEventListener('paste', function(e) {
                e.preventDefault();
                const pastedData = e.clipboardData.getData('text').replace(/\D/g, '');

                if (pastedData.length === 6) {
                    pastedData.split('').forEach((char, i) => {
                        if (otpInputs[i]) {
                            otpInputs[i].value = char;
                        }
                    });
                    otpInputs[5].focus();
                    updateOTPValue();
                }
            });
        });

        function updateOTPValue() {
            const otp = Array.from(otpInputs).map(input => input.value).join('');
            otpValue.value = otp;
        }

        // Timer countdown (10 minutes)
        let timeLeft = 600;
        const timerElement = document.getElementById('timer');

        const countdown = setInterval(function() {
            timeLeft--;
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            timerElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;

            if (timeLeft <= 0) {
                clearInterval(countdown);
                timerElement.textContent = 'Expired';
                timerElement.style.color = '#EF4444';
                document.getElementById('verify-btn').disabled = true;
            }
        }, 1000);

        // Form submission
        form.addEventListener('submit', function(e) {
            const button = form.querySelector('button[type="submit"]');
            button.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Verifying...';
            button.disabled = true;
        });

        // Focus first input
        otpInputs[0].focus();
    });
</script>
@endpush