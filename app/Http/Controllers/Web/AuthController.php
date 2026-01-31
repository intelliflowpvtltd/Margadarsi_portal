<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->filled('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $user = Auth::user();
            return redirect()->intended(route('dashboard'))
                ->with('success', 'Welcome back, ' . $user->first_name . '!');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
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
     * Send password reset OTP
     */
    public function sendResetOTP(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'User not found.']);
        }

        // Generate 6-digit OTP
        $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store OTP in password_resets table (hashed for security)
        PasswordReset::updateOrCreate(
            ['email' => $request->email],
            [
                'token' => Hash::make($otp), // Store hashed for security
                'otp' => null, // Never store plain OTP
                'created_at' => now(),
            ]
        );

        // Send OTP via email
        Mail::to($user->email)->send(new \App\Mail\OtpMail($otp, $user->full_name));

        return redirect()->route('password.verify-otp-form')
            ->with('email', $request->email)
            ->with('status', 'OTP sent to your email. Please check your inbox.');
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
     * Verify OTP and generate reset token
     */
    public function verifyOTP(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:6',
        ]);

        $passwordReset = PasswordReset::where('email', $request->email)->first();

        if (!$passwordReset) {
            return back()->withErrors(['otp' => 'Invalid or expired OTP.']);
        }

        // Check if OTP is expired (10 minutes)
        if (Carbon::parse($passwordReset->created_at)->addMinutes(10)->isPast()) {
            $passwordReset->delete();
            return back()->withErrors(['otp' => 'OTP has expired. Please request a new one.']);
        }

        // Verify OTP using hash comparison (secure)
        if (!Hash::check($request->otp, $passwordReset->token)) {
            return back()->withErrors(['otp' => 'Invalid OTP. Please try again.']);
        }

        // Generate reset token
        $resetToken = Str::random(64);
        $passwordReset->update([
            'token' => Hash::make($resetToken),
            'reset_token' => $resetToken, // Store plain token for URL
        ]);

        return redirect()->route('password.reset', ['token' => $resetToken])
            ->with('email', $request->email)
            ->with('status', 'OTP verified! You can now reset your password.');
    }

    /**
     * Show reset password form
     */
    public function showResetPasswordForm($token)
    {
        $email = session('email');

        if (!$email) {
            return redirect()->route('password.request')
                ->withErrors(['token' => 'Invalid or expired reset link.']);
        }

        return view('auth.reset-password', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    /**
     * Reset password
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'token' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $passwordReset = PasswordReset::where('email', $request->email)->first();

        if (!$passwordReset || $passwordReset->reset_token !== $request->token) {
            return back()->withErrors(['token' => 'Invalid or expired reset token.']);
        }

        // Update user password
        $user = User::where('email', $request->email)->first();
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // Revoke all existing API tokens
        $user->tokens()->delete();

        // Delete password reset record
        $passwordReset->delete();

        return redirect()->route('login')
            ->with('status', 'Password reset successfully! Please login with your new password.');
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('status', 'You have been logged out successfully.');
    }
}
