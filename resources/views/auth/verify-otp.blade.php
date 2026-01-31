@extends('layouts.auth')

@section('title', 'Verify OTP')

@php
    // Mask email for privacy (e.g., j***@example.com)
    $email = session('email') ?? old('email') ?? '';
    $maskedEmail = '';
    if ($email) {
        $parts = explode('@', $email);
        if (count($parts) === 2) {
            $local = $parts[0];
            $domain = $parts[1];
            $maskedLocal = strlen($local) > 2 
                ? substr($local, 0, 1) . str_repeat('*', strlen($local) - 2) . substr($local, -1)
                : $local[0] . '*';
            $maskedEmail = $maskedLocal . '@' . $domain;
        }
    }
@endphp

@section('content')
<div class="auth-card-header">
    <h2 class="auth-card-title">Verify OTP</h2>
    <p class="auth-card-subtitle">Enter the 6-digit code sent to <strong>{{ $maskedEmail ?: 'your email' }}</strong></p>
</div>

<!-- Error Messages -->
@if ($errors->any())
<div class="alert alert-danger" role="alert">
    <i class="bi bi-exclamation-circle me-2" aria-hidden="true"></i>
    {{ $errors->first() }}
</div>
@endif

<!-- Attempts Warning -->
@if (session('attempts_remaining'))
<div class="alert alert-warning" role="alert">
    <i class="bi bi-exclamation-triangle me-2" aria-hidden="true"></i>
    {{ session('attempts_remaining') }} attempts remaining
</div>
@endif

<!-- OTP Verification Form -->
<form method="POST" action="{{ route('password.verify-otp') }}" class="auth-form" novalidate>
    @csrf

    <input type="hidden" name="email" value="{{ session('email') ?? old('email') }}">

    <!-- OTP Input -->
    <div class="form-group">
        <label class="form-label" id="otp-label">Enter OTP Code</label>
        <div class="d-flex justify-content-center gap-2 mb-3" 
             id="otp-inputs" 
             role="group" 
             aria-labelledby="otp-label">
            @for ($i = 0; $i < 6; $i++)
            <input type="text" 
                class="form-control text-center otp-box" 
                maxlength="1" 
                pattern="[0-9]" 
                inputmode="numeric"
                autocomplete="one-time-code"
                aria-label="Digit {{ $i + 1 }} of 6"
                data-index="{{ $i }}" 
                required>
            @endfor
        </div>
        <!-- Hidden input for full OTP -->
        <input type="hidden" name="otp" id="otp-value">
    </div>

    <!-- Timer -->
    <div class="text-center mb-4">
        <small class="text-muted">
            Code expires in: <span id="timer" class="fw-bold" style="color: var(--color-coffee-gold-dark);" aria-live="polite">10:00</span>
        </small>
    </div>

    <!-- Verify Button -->
    <button type="submit" class="btn btn-primary mb-3" id="verify-btn">
        <i class="bi bi-check-circle" aria-hidden="true"></i>
        <span class="btn-text">Verify OTP</span>
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
    /* OTP Input Container */
    #otp-inputs {
        max-width: 380px;  /* Constrain total width for better centering */
        margin: 0 auto;    /* Center the container */
    }
    
    /* Individual OTP Boxes */
    .otp-box {
        width: 55px !important;   /* Slightly larger for better visibility */
        height: 65px;
        font-size: 1.75rem;       /* Larger text for readability */
        font-weight: 700;
        padding: 0;
        border: 2px solid #E5E7EB;
        border-radius: 10px;      /* Rounded corners for modern look */
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        background-color: var(--color-white);
    }

    .otp-box:focus {
        border-color: var(--color-coffee-gold);
        box-shadow: 0 0 0 4px rgba(184, 149, 106, 0.15);
        transform: scale(1.05);   /* Slight scale on focus */
        outline: none;
    }
    
    .otp-box.filled {
        background-color: rgba(184, 149, 106, 0.08);
        border-color: var(--color-coffee-gold);
        border-width: 2px;
    }
    
    /* Hover effect for better UX */
    .otp-box:hover:not(:focus) {
        border-color: var(--color-coffee-gold-light);
    }
    
    .alert-warning {
        background-color: #FEF3C7;
        color: #92400E;
        border-left: 4px solid #F59E0B;
    }
    
    /* Responsive adjustments for smaller screens */
    @media (max-width: 576px) {
        #otp-inputs {
            max-width: 100%;
            gap: 0.5rem !important;  /* Smaller gap on mobile */
        }
        
        .otp-box {
            width: 45px !important;
            height: 55px;
            font-size: 1.5rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const otpInputs = document.querySelectorAll('.otp-box');
        const otpValue = document.getElementById('otp-value');
        const form = document.querySelector('.auth-form');
        const verifyBtn = document.getElementById('verify-btn');

        // OTP Input Handling
        otpInputs.forEach((input, index) => {
            input.addEventListener('input', function(e) {
                const value = e.target.value;

                // Only allow numbers
                if (!/^[0-9]$/.test(value)) {
                    e.target.value = '';
                    e.target.classList.remove('filled');
                    return;
                }

                e.target.classList.add('filled');

                // Move to next input
                if (value && index < otpInputs.length - 1) {
                    otpInputs[index + 1].focus();
                }

                updateOTPValue();
            });

            // Handle backspace
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace') {
                    if (!e.target.value && index > 0) {
                        otpInputs[index - 1].focus();
                        otpInputs[index - 1].value = '';
                        otpInputs[index - 1].classList.remove('filled');
                    } else {
                        e.target.classList.remove('filled');
                    }
                    updateOTPValue();
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
                            otpInputs[i].classList.add('filled');
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

            if (timeLeft <= 60) {
                timerElement.style.color = '#EF4444';
            }

            if (timeLeft <= 0) {
                clearInterval(countdown);
                timerElement.textContent = 'Expired';
                verifyBtn.disabled = true;
                verifyBtn.innerHTML = '<i class="bi bi-x-circle" aria-hidden="true"></i> OTP Expired';
            }
        }, 1000);

        // Form submission
        form.addEventListener('submit', function(e) {
            const btnText = verifyBtn.querySelector('.btn-text');
            verifyBtn.disabled = true;
            btnText.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Verifying...';
        });

        // Focus first input
        otpInputs[0].focus();
    });
</script>
@endpush