<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PasswordReset;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Show login form
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login request with brute force protection.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:6',
        ]);

        $email = strtolower(trim($request->email));
        $ip = $request->ip();
        $remember = $request->filled('remember');

        // Check if account is locked out
        if ($this->authService->isLockedOut($email, $ip)) {
            $remainingMinutes = $this->authService->getRemainingLockoutMinutes($email, $ip);
            
            Log::warning('Web login attempt on locked account', [
                'email' => $email,
                'ip' => $ip,
            ]);

            return back()->withErrors([
                'email' => "Too many failed attempts. Please try again in {$remainingMinutes} minute(s).",
            ])->onlyInput('email');
        }

        $credentials = ['email' => $email, 'password' => $request->password];

        if (Auth::attempt($credentials, $remember)) {
            // Clear failed attempts on success
            $this->authService->clearFailedAttempts($email, $ip);

            // Regenerate session for security
            $request->session()->regenerate();

            // Record successful login
            $user = Auth::user();
            $this->authService->recordSuccessfulLogin($user, $request);

            return redirect()->intended(route('dashboard'))
                ->with('success', 'Welcome back, ' . $user->first_name . '!');
        }

        // Record failed attempt
        $result = $this->authService->recordFailedAttempt($email, $ip);

        if ($result['locked']) {
            return back()->withErrors([
                'email' => 'Too many failed attempts. Account locked for ' . AuthService::LOCKOUT_MINUTES . ' minutes.',
            ])->onlyInput('email');
        }

        $remaining = AuthService::MAX_ATTEMPTS - $result['attempts'];

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.' . 
                       ($remaining <= 2 ? " {$remaining} attempt(s) remaining." : ''),
        ])->onlyInput('email');
    }

    /**
     * Show forgot password form
     */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Send password reset OTP with security protections.
     */
    public function sendResetOTP(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
        ]);

        $email = strtolower(trim($request->email));
        $user = User::where('email', $email)->first();

        // Always show success to prevent email enumeration
        if ($user) {
            // Generate secure OTP
            $otp = $this->authService->generateSecureOtp();

            // Store OTP in password_resets table (hashed for security)
            PasswordReset::updateOrCreate(
                ['email' => $email],
                [
                    'token' => Hash::make($otp),
                    'otp' => null, // Never store plain OTP
                    'created_at' => now(),
                ]
            );

            // Send OTP via email
            try {
                Mail::to($user->email)->send(new \App\Mail\OtpMail($otp, $user->full_name));
                Log::info('Web password reset OTP sent', ['email' => $email]);
            } catch (\Exception $e) {
                Log::error('Failed to send OTP email', [
                    'email' => $email,
                    'error' => $e->getMessage(),
                ]);
                
                return back()->withErrors([
                    'email' => 'Failed to send OTP. Please try again later.',
                ]);
            }
        }

        // Always redirect to prevent email enumeration
        return redirect()->route('password.verify-otp-form')
            ->with('email', $email)
            ->with('status', 'If an account exists with this email, an OTP has been sent.');
    }

    /**
     * Show OTP verification form
     */
    public function showVerifyOTPForm()
    {
        if (!session('email')) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Please request a password reset first.']);
        }

        return view('auth.verify-otp');
    }

    /**
     * Verify OTP with attempt limiting.
     */
    public function verifyOTP(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
            'otp' => 'required|digits:6',
        ]);

        $email = strtolower(trim($request->email));

        // Check OTP attempts
        $otpCheck = $this->authService->checkOtpAttempts($email);
        if (!$otpCheck['allowed']) {
            return back()->withErrors([
                'otp' => 'Too many failed attempts. Please request a new OTP.',
            ]);
        }

        $passwordReset = PasswordReset::where('email', $email)->first();

        if (!$passwordReset) {
            return back()->withErrors(['otp' => 'Invalid or expired OTP.']);
        }

        // Check if OTP is expired (10 minutes)
        if (Carbon::parse($passwordReset->created_at)->addMinutes(AuthService::OTP_EXPIRY_MINUTES)->isPast()) {
            $passwordReset->delete();
            $this->authService->clearOtpAttempts($email);
            return back()->withErrors(['otp' => 'OTP has expired. Please request a new one.']);
        }

        // Verify OTP using hash comparison (secure)
        if (!Hash::check($request->otp, $passwordReset->token)) {
            $attempts = $this->authService->recordFailedOtpAttempt($email);
            $remaining = AuthService::MAX_OTP_ATTEMPTS - $attempts;

            return back()->withErrors([
                'otp' => 'Invalid OTP.' . ($remaining <= 1 ? " {$remaining} attempt(s) remaining." : ''),
            ]);
        }

        // Clear OTP attempts on success
        $this->authService->clearOtpAttempts($email);

        // Generate reset token
        $resetToken = Str::random(64);
        $passwordReset->update([
            'token' => Hash::make($resetToken),
            'reset_token' => $resetToken, // Store for URL (will be compared via hash)
        ]);

        Log::info('Web OTP verified successfully', ['email' => $email]);

        // Store email and reset_token in PERSISTENT session (not flash)
        session()->put('reset_email', $email);
        session()->put('reset_token', $resetToken);

        return redirect()->route('password.reset', ['token' => $resetToken])
            ->with('status', 'OTP verified! You can now reset your password.');
    }

    /**
     * Show reset password form
     */
    public function showResetPasswordForm($token)
    {
        // Get email and verify token from persistent session
        $email = session('reset_email');
        $sessionToken = session('reset_token');

        if (!$email || !$sessionToken || $sessionToken !== $token) {
            // Clear invalid session data
            session()->forget(['reset_email', 'reset_token']);
            
            return redirect()->route('password.request')
                ->withErrors(['token' => 'Invalid or expired reset link.']);
        }

        return view('auth.reset-password', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    /**
     * Reset password with token verification.
     */
    public function resetPassword(Request $request)
    {
        // Get email and reset_token from persistent session
        $email = session('reset_email');
        $sessionToken = session('reset_token');

        // Validate request
        $request->validate([
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/[a-z]/',      // at least one lowercase
                'regex:/[A-Z]/',      // at least one uppercase
                'regex:/[0-9]/',      // at least one number
            ],
        ], [
            'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, and one number.',
        ]);

        // Verify session data exists
        if (!$email || !$sessionToken) {
            session()->forget(['reset_email', 'reset_token']);
            return redirect()->route('password.request')
                ->withErrors(['token' => 'Session expired. Please start the password reset process again.']);
        }

        $email = strtolower(trim($email));
        $passwordReset = PasswordReset::where('email', $email)->first();

        // Verify reset token matches
        if (!$passwordReset || $passwordReset->reset_token !== $sessionToken || $passwordReset->reset_token !== $request->reset_token) {
            session()->forget(['reset_email', 'reset_token']);
            return back()->withErrors(['token' => 'Invalid or expired reset token.']);
        }

        // Update user password
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            session()->forget(['reset_email', 'reset_token']);
            return back()->withErrors(['email' => 'User not found.']);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // Revoke all existing API tokens
        $user->tokens()->delete();

        // Delete password reset record
        $passwordReset->delete();

        // Clear session data
        session()->forget(['reset_email', 'reset_token']);

        Log::info('Web password reset successful', ['email' => $email]);

        return redirect()->route('login')
            ->with('status', 'Password reset successfully! Please login with your new password.');
    }

    /**
     * Handle logout with session cleanup.
     */
    public function logout(Request $request)
    {
        $userId = Auth::id();

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Log::info('User logged out', ['user_id' => $userId]);

        return redirect()->route('login')
            ->with('status', 'You have been logged out successfully.');
    }
}
