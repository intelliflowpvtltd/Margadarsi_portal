<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\VerifyOtpRequest;
use App\Mail\OtpMail;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    /**
     * Send OTP to email for password reset.
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        // Generate 6-digit OTP
        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store in database with 10-minute expiration
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => Hash::make($otp), // Hashed for security
                'otp' => $otp, // Plain text for email
                'expires_at' => now()->addMinutes(10),
                'created_at' => now(),
            ]
        );

        // Send OTP via email
        Mail::to($user->email)->send(new OtpMail($otp, $user->full_name));

        return response()->json([
            'message' => 'OTP sent to your email address. Please check your inbox.',
        ]);
    }

    /**
     * Verify OTP and return reset token.
     */
    public function verifyOtp(VerifyOtpRequest $request): JsonResponse
    {
        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$resetRecord) {
            return response()->json([
                'message' => 'No password reset request found for this email.',
            ], 404);
        }

        // Check if OTP expired
        if (now()->isAfter($resetRecord->expires_at)) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return response()->json([
                'message' => 'OTP has expired. Please request a new one.',
            ], 422);
        }

        // Verify OTP
        if ($resetRecord->otp !== $request->otp) {
            return response()->json([
                'message' => 'Invalid OTP. Please check and try again.',
            ], 422);
        }

        // Generate reset token
        $resetToken = Str::random(64);

        // Update with reset token and extend expiry to 1 hour
        DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->update([
                'token' => Hash::make($resetToken),
                'otp' => null, // Clear OTP after verification
                'expires_at' => now()->addHour(),
            ]);

        return response()->json([
            'message' => 'OTP verified successfully.',
            'reset_token' => $resetToken,
        ]);
    }

    /**
     * Reset password using reset token.
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        // Find reset record with matching token
        $resetRecords = DB::table('password_reset_tokens')->get();
        $resetRecord = null;

        foreach ($resetRecords as $record) {
            if (Hash::check($request->reset_token, $record->token)) {
                $resetRecord = $record;
                break;
            }
        }

        if (!$resetRecord) {
            return response()->json([
                'message' => 'Invalid reset token.',
            ], 422);
        }

        // Check if token expired
        if (now()->isAfter($resetRecord->expires_at)) {
            DB::table('password_reset_tokens')->where('email', $resetRecord->email)->delete();
            return response()->json([
                'message' => 'Reset token has expired. Please start the process again.',
            ], 422);
        }

        // Update user password
        $user = User::where('email', $resetRecord->email)->first();
        $user->update([
            'password' => $request->password, // Will be hashed by model cast
        ]);

        // Delete reset token
        DB::table('password_reset_tokens')->where('email', $resetRecord->email)->delete();

        return response()->json([
            'message' => 'Password reset successfully. You can now login with your new password.',
        ]);
    }
}
