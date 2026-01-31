<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

/**
 * Authentication Service
 * 
 * Centralized authentication business logic for production-grade security.
 * Handles login attempts, lockouts, and security logging.
 */
class AuthService
{
    /**
     * Maximum login attempts before lockout.
     */
    public const MAX_ATTEMPTS = 5;

    /**
     * Lockout duration in minutes.
     */
    public const LOCKOUT_MINUTES = 15;

    /**
     * Maximum OTP verification attempts.
     */
    public const MAX_OTP_ATTEMPTS = 3;

    /**
     * OTP expiration in minutes.
     */
    public const OTP_EXPIRY_MINUTES = 10;

    /**
     * Check if account is locked out.
     */
    public function isLockedOut(string $email, ?string $ip = null): bool
    {
        $emailLockout = Cache::has($this->getEmailLockoutKey($email));
        $ipLockout = $ip ? Cache::has($this->getIpLockoutKey($ip)) : false;

        return $emailLockout || $ipLockout;
    }

    /**
     * Get remaining lockout time in minutes.
     */
    public function getRemainingLockoutMinutes(string $email, ?string $ip = null): int
    {
        $emailExpiry = Cache::get($this->getEmailLockoutKey($email), 0);
        $ipExpiry = $ip ? Cache::get($this->getIpLockoutKey($ip), 0) : 0;

        $maxExpiry = max($emailExpiry, $ipExpiry);
        if ($maxExpiry <= 0) {
            return 0;
        }

        return (int) ceil(($maxExpiry - time()) / 60);
    }

    /**
     * Record a failed login attempt.
     * 
     * @return array{attempts: int, locked: bool}
     */
    public function recordFailedAttempt(string $email, string $ip): array
    {
        $emailAttemptsKey = $this->getEmailAttemptsKey($email);
        $ipAttemptsKey = $this->getIpAttemptsKey($ip);

        // Increment both email and IP attempt counters
        $emailAttempts = Cache::get($emailAttemptsKey, 0) + 1;
        $ipAttempts = Cache::get($ipAttemptsKey, 0) + 1;

        Cache::put($emailAttemptsKey, $emailAttempts, now()->addMinutes(self::LOCKOUT_MINUTES));
        Cache::put($ipAttemptsKey, $ipAttempts, now()->addMinutes(self::LOCKOUT_MINUTES));

        $maxAttempts = max($emailAttempts, $ipAttempts);
        $locked = false;

        // Check if lockout threshold reached
        if ($emailAttempts >= self::MAX_ATTEMPTS) {
            $this->lockAccount($email, $ip, 'email');
            $locked = true;
        }

        if ($ipAttempts >= self::MAX_ATTEMPTS * 2) { // IP gets more attempts (could be shared)
            $this->lockAccount($email, $ip, 'ip');
            $locked = true;
        }

        // Log failed attempt
        Log::warning('Failed login attempt', [
            'email' => $this->maskEmail($email),
            'ip' => $ip,
            'email_attempts' => $emailAttempts,
            'ip_attempts' => $ipAttempts,
            'locked' => $locked,
        ]);

        return [
            'attempts' => $maxAttempts,
            'locked' => $locked,
        ];
    }

    /**
     * Lock an account.
     */
    protected function lockAccount(string $email, string $ip, string $reason): void
    {
        $lockoutExpiry = time() + (self::LOCKOUT_MINUTES * 60);

        if ($reason === 'email') {
            Cache::put($this->getEmailLockoutKey($email), $lockoutExpiry, now()->addMinutes(self::LOCKOUT_MINUTES));
            Cache::forget($this->getEmailAttemptsKey($email));
        }

        if ($reason === 'ip') {
            Cache::put($this->getIpLockoutKey($ip), $lockoutExpiry, now()->addMinutes(self::LOCKOUT_MINUTES));
            Cache::forget($this->getIpAttemptsKey($ip));
        }

        Log::warning('Account locked', [
            'email' => $this->maskEmail($email),
            'ip' => $ip,
            'reason' => $reason,
            'duration_minutes' => self::LOCKOUT_MINUTES,
        ]);
    }

    /**
     * Clear failed attempts on successful login.
     */
    public function clearFailedAttempts(string $email, string $ip): void
    {
        Cache::forget($this->getEmailAttemptsKey($email));
        Cache::forget($this->getIpAttemptsKey($ip));
        Cache::forget($this->getEmailLockoutKey($email));
        Cache::forget($this->getIpLockoutKey($ip));
    }

    /**
     * Validate user credentials.
     */
    public function validateCredentials(string $email, string $password): ?User
    {
        $user = User::where('email', $email)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return null;
        }

        return $user;
    }

    /**
     * Check if user can login (active, not deleted, etc).
     */
    public function canUserLogin(User $user): array
    {
        if (!$user->is_active) {
            return [
                'allowed' => false,
                'reason' => 'Your account has been deactivated. Please contact administrator.',
                'code' => 403,
            ];
        }

        if ($user->deleted_at) {
            return [
                'allowed' => false,
                'reason' => 'Account not found.',
                'code' => 401,
            ];
        }

        return ['allowed' => true];
    }

    /**
     * Record successful login.
     */
    public function recordSuccessfulLogin(User $user, Request $request): void
    {
        $user->update([
            'last_login_at' => now(),
        ]);

        Log::info('Successful login', [
            'user_id' => $user->id,
            'email' => $this->maskEmail($user->email),
            'ip' => $request->ip(),
            'user_agent' => substr($request->userAgent() ?? '', 0, 100),
        ]);
    }

    /**
     * Generate cryptographically secure OTP.
     */
    public function generateSecureOtp(): string
    {
        return str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Check and increment OTP attempts.
     * 
     * @return array{allowed: bool, attempts: int}
     */
    public function checkOtpAttempts(string $email): array
    {
        $key = 'otp_attempts_' . md5($email);
        $attempts = Cache::get($key, 0);

        if ($attempts >= self::MAX_OTP_ATTEMPTS) {
            return ['allowed' => false, 'attempts' => $attempts];
        }

        return ['allowed' => true, 'attempts' => $attempts];
    }

    /**
     * Record failed OTP attempt.
     */
    public function recordFailedOtpAttempt(string $email): int
    {
        $key = 'otp_attempts_' . md5($email);
        $attempts = Cache::get($key, 0) + 1;
        Cache::put($key, $attempts, now()->addMinutes(self::OTP_EXPIRY_MINUTES));

        Log::warning('Failed OTP attempt', [
            'email' => $this->maskEmail($email),
            'attempts' => $attempts,
        ]);

        return $attempts;
    }

    /**
     * Clear OTP attempts on success.
     */
    public function clearOtpAttempts(string $email): void
    {
        Cache::forget('otp_attempts_' . md5($email));
    }

    /**
     * Mask email for logging (privacy).
     */
    protected function maskEmail(string $email): string
    {
        $parts = explode('@', $email);
        if (count($parts) !== 2) {
            return '***';
        }

        $name = $parts[0];
        $domain = $parts[1];

        $maskedName = strlen($name) > 2
            ? substr($name, 0, 2) . str_repeat('*', min(strlen($name) - 2, 5))
            : str_repeat('*', strlen($name));

        return $maskedName . '@' . $domain;
    }

    /**
     * Cache key generators.
     */
    protected function getEmailAttemptsKey(string $email): string
    {
        return 'login_attempts_email_' . md5($email);
    }

    protected function getIpAttemptsKey(string $ip): string
    {
        return 'login_attempts_ip_' . md5($ip);
    }

    protected function getEmailLockoutKey(string $email): string
    {
        return 'login_lockout_email_' . md5($email);
    }

    protected function getIpLockoutKey(string $ip): string
    {
        return 'login_lockout_ip_' . md5($ip);
    }
}
