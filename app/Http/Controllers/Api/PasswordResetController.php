<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\VerifyOtpRequest;
use App\Mail\OtpMail;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Send OTP to email for password reset.
     * 
     * Security features:
     * - Cryptographically secure OTP generation
     * - OTP is hashed before storage
     * - 10-minute expiration
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $email = strtolower(trim($request->email));
        $user = User::where('email', $email)->first();

        // Always return success to prevent email enumeration
        // But only send email if user exists
        if ($user) {
            // Generate secure OTP
            $otp = $this->authService->generateSecureOtp();

            // Store in database with 10-minute expiration
            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $email],
                [
                    'token' => Hash::make($otp),
                    'otp_attempts' => 0,
                    'expires_at' => now()->addMinutes(AuthService::OTP_EXPIRY_MINUTES),
                    'created_at' => now(),
                ]
            );

            // Send OTP via email
            try {
                Mail::to($user->email)->send(new OtpMail($otp, $user->full_name));
            } catch (\Exception $e) {
                Log::error('Failed to send OTP email', [
                    'email' => $this->maskEmail($email),
                    'error' => $e->getMessage(),
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send OTP. Please try again later.',
                    'error' => 'email_failed',
                ], 500);
            }

            Log::info('Password reset OTP sent', ['email' => $this->maskEmail($email)]);
        }

        // Always return success to prevent email enumeration
        return response()->json([
            'success' => true,
            'message' => 'If an account exists with this email, an OTP has been sent.',
            'expires_in' => AuthService::OTP_EXPIRY_MINUTES * 60,
        ]);
    }

    /**
     * Verify OTP and return reset token.
     * 
     * Security features:
     * - Maximum 3 OTP attempts
     * - OTP verified using hash comparison
     * - Reset token generated on success
     */
    public function verifyOtp(VerifyOtpRequest $request): JsonResponse
    {
        $email = strtolower(trim($request->email));

        // Check OTP attempts
        $otpCheck = $this->authService->checkOtpAttempts($email);
        if (!$otpCheck['allowed']) {
            return response()->json([
                'success' => false,
                'message' => 'Too many failed OTP attempts. Please request a new OTP.',
                'error' => 'otp_attempts_exceeded',
            ], 429);
        }

        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (!$resetRecord) {
            return response()->json([
                'success' => false,
                'message' => 'No password reset request found. Please request a new OTP.',
                'error' => 'no_reset_request',
            ], 404);
        }

        // Check if OTP expired
        if (now()->isAfter($resetRecord->expires_at)) {
            DB::table('password_reset_tokens')->where('email', $email)->delete();
            $this->authService->clearOtpAttempts($email);

            return response()->json([
                'success' => false,
                'message' => 'OTP has expired. Please request a new one.',
                'error' => 'otp_expired',
            ], 422);
        }

        // Verify OTP using hash comparison
        if (!Hash::check($request->otp, $resetRecord->token)) {
            $attempts = $this->authService->recordFailedOtpAttempt($email);
            $remaining = AuthService::MAX_OTP_ATTEMPTS - $attempts;

            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP. Please check and try again.',
                'error' => 'invalid_otp',
                'attempts_remaining' => max(0, $remaining),
            ], 422);
        }

        // Clear OTP attempts on success
        $this->authService->clearOtpAttempts($email);

        // Generate secure reset token
        $resetToken = Str::random(64);

        // Update with reset token and extend expiry to 1 hour
        DB::table('password_reset_tokens')
            ->where('email', $email)
            ->update([
                'token' => Hash::make($resetToken),
                'otp_attempts' => 0,
                'expires_at' => now()->addHour(),
            ]);

        Log::info('OTP verified successfully', ['email' => $this->maskEmail($email)]);

        return response()->json([
            'success' => true,
            'message' => 'OTP verified successfully.',
            'reset_token' => $resetToken,
            'expires_in' => 3600, // 1 hour in seconds
        ]);
    }

    /**
     * Reset password using reset token.
     * 
     * Security features:
     * - Token verified using hash comparison
     * - All existing tokens revoked on password change
     * - Reset record deleted after use
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $email = strtolower(trim($request->email));

        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (!$resetRecord) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired reset token.',
                'error' => 'invalid_token',
            ], 422);
        }

        // Check if token expired
        if (now()->isAfter($resetRecord->expires_at)) {
            DB::table('password_reset_tokens')->where('email', $email)->delete();

            return response()->json([
                'success' => false,
                'message' => 'Reset token has expired. Please start the process again.',
                'error' => 'token_expired',
            ], 422);
        }

        // Verify reset token using hash comparison
        if (!Hash::check($request->reset_token, $resetRecord->token)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid reset token.',
                'error' => 'invalid_token',
            ], 422);
        }

        // Find user and update password
        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
                'error' => 'user_not_found',
            ], 404);
        }

        // Update password (will be hashed by model cast)
        $user->update([
            'password' => $request->password,
        ]);

        // Invalidate all existing tokens for security
        $user->tokens()->delete();

        // Delete reset token
        DB::table('password_reset_tokens')->where('email', $email)->delete();

        Log::info('Password reset successful', ['email' => $this->maskEmail($email)]);

        return response()->json([
            'success' => true,
            'message' => 'Password reset successfully. You can now login with your new password.',
        ]);
    }

    /**
     * Mask email for logging (privacy protection).
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
}
