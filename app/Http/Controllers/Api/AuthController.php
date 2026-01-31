<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * Maximum login attempts before lockout.
     */
    private const MAX_ATTEMPTS = 5;

    /**
     * Lockout duration in minutes.
     */
    private const LOCKOUT_MINUTES = 15;

    /**
     * Login user and create token.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $email = $request->email;
        $lockoutKey = 'login_lockout_' . md5($email);
        $attemptsKey = 'login_attempts_' . md5($email);

        // Check if account is locked out
        if (Cache::has($lockoutKey)) {
            $remainingMinutes = ceil(Cache::get($lockoutKey) - time()) / 60;
            Log::warning('Login attempt on locked account', ['email' => $email, 'ip' => $request->ip()]);
            return response()->json([
                'message' => 'Too many failed attempts. Account locked for ' . ceil($remainingMinutes) . ' minutes.',
            ], 429);
        }

        $user = User::where('email', $email)->first();

        // Validate password
        if (!$user || !Hash::check($request->password, $user->password)) {
            // Increment failed attempts
            $attempts = Cache::get($attemptsKey, 0) + 1;
            Cache::put($attemptsKey, $attempts, now()->addMinutes(self::LOCKOUT_MINUTES));

            // Lock account if max attempts reached
            if ($attempts >= self::MAX_ATTEMPTS) {
                Cache::put($lockoutKey, time() + (self::LOCKOUT_MINUTES * 60), now()->addMinutes(self::LOCKOUT_MINUTES));
                Cache::forget($attemptsKey);
                Log::warning('Account locked due to failed attempts', ['email' => $email, 'ip' => $request->ip()]);
                return response()->json([
                    'message' => 'Too many failed attempts. Account locked for ' . self::LOCKOUT_MINUTES . ' minutes.',
                ], 429);
            }

            Log::info('Failed login attempt', ['email' => $email, 'attempts' => $attempts, 'ip' => $request->ip()]);
            return response()->json([
                'message' => 'Invalid credentials.',
                'attempts_remaining' => self::MAX_ATTEMPTS - $attempts,
            ], 401);
        }

        // Check if user is active
        if (!$user->is_active) {
            return response()->json([
                'message' => 'Your account has been deactivated. Please contact administrator.',
            ], 403);
        }

        // Clear failed attempts on successful login
        Cache::forget($attemptsKey);
        Cache::forget($lockoutKey);

        // Update last login
        $user->update(['last_login_at' => now()]);

        // Load relationships
        $user->load(['company', 'role.permissions', 'projects']);

        // Get all permissions through role
        $permissions = $user->role->permissions->pluck('name')->toArray();

        // Create token with permissions as abilities
        $token = $user->createToken('auth-token', $permissions)->plainTextToken;

        Log::info('Successful login', ['user_id' => $user->id, 'email' => $email, 'ip' => $request->ip()]);

        return response()->json([
            'message' => 'Login successful.',
            'token' => $token,
            'user' => new UserResource($user),
            'permissions' => $permissions,
        ]);
    }

    /**
     * Logout user (revoke token).
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully.',
        ]);
    }

    /**
     * Get authenticated user.
     */
    public function me(Request $request): UserResource
    {
        return new UserResource(
            $request->user()->load(['company', 'role.permissions', 'projects'])
        );
    }
}
