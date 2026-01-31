

<?php $__env->startSection('title', 'Login'); ?>

<?php $__env->startSection('content'); ?>
<div class="auth-card-header">
    <h2 class="auth-card-title">Welcome Back</h2>
    <p class="auth-card-subtitle">Please login to your account to continue</p>
</div>

<!-- Success/Status Messages -->
<?php if(session('status')): ?>
<div class="alert alert-success">
    <i class="bi bi-check-circle me-2"></i>
    <?php echo e(session('status')); ?>

</div>
<?php endif; ?>

<!-- Error Messages -->
<?php if($errors->any()): ?>
<div class="alert alert-danger">
    <i class="bi bi-exclamation-circle me-2"></i>
    <?php echo e($errors->first('email') ?? $errors->first()); ?>

</div>
<?php endif; ?>

<!-- Login Form -->
<form method="POST" action="<?php echo e(route('login')); ?>" class="auth-form">
    <?php echo csrf_field(); ?>

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
                value="<?php echo e(old('email')); ?>"
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
        <a href="<?php echo e(route('password.request')); ?>" class="auth-link">Forgot password?</a>
    </div>

    <!-- Login Button -->
    <button type="submit" class="btn btn-primary">
        <i class="bi bi-box-arrow-in-right"></i>
        Sign In
    </button>
</form>

<!-- Footer -->
<div class="auth-footer">
    <p>&copy; <?php echo e(date('Y')); ?> Margadarsi Infrastructure. All rights reserved.</p>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
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
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.auth', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Margadarsi Portal\Backend\resources\views/auth/login.blade.php ENDPATH**/ ?>