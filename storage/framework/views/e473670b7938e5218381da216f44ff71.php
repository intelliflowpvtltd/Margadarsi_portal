<?php $__env->startSection('title', 'Reset Password'); ?>

<?php $__env->startSection('content'); ?>
<div class="auth-card-header">
    <h2 class="auth-card-title">Reset Password</h2>
    <p class="auth-card-subtitle">Create a strong password for your account</p>
</div>

<!-- Error Messages -->
<?php if($errors->any()): ?>
<div class="alert alert-danger" role="alert">
    <i class="bi bi-exclamation-circle me-2" aria-hidden="true"></i>
    <?php echo e($errors->first()); ?>

</div>
<?php endif; ?>

<!-- Reset Password Form -->
<form method="POST" action="<?php echo e(route('password.update')); ?>" class="auth-form" novalidate>
    <?php echo csrf_field(); ?>

    <input type="hidden" name="reset_token" value="<?php echo e($token ?? session('reset_token')); ?>">
    <input type="hidden" name="email" value="<?php echo e($email ?? session('email')); ?>">

    <!-- New Password Field -->
    <div class="form-group">
        <label for="password" class="form-label">New Password</label>
        <div class="input-icon password-wrapper">
            <i class="bi bi-lock" aria-hidden="true"></i>
            <input type="password"
                class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
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
        <a href="<?php echo e(route('login')); ?>" class="auth-link">
            <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>
            Back to Login
        </a>
    </div>
</form>

<!-- Footer -->
<div class="auth-footer">
    <p>&copy; <?php echo e(date('Y')); ?> Margadarsi Infrastructure. All rights reserved.</p>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
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
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
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
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.auth', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Margadarsi_portal\resources\views/auth/reset-password.blade.php ENDPATH**/ ?>