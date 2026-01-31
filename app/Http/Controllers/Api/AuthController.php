<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Login user and create token.
     * 
     * Security features:
     * - Dual rate limiting (email + IP based)
     * - Account lockout after 5 failed attempts
     * - Login activity logging
     * - Session metadata tracking
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $email = strtolower(trim($request->email));
        $ip = $request->ip();

        // Check if account is locked out (email or IP)
        if ($this->authService->isLockedOut($email, $ip)) {
            $remainingMinutes = $this->authService->getRemainingLockoutMinutes($email, $ip);
            return response()->json([
                'success' => false,
                'message' => "Too many failed attempts. Please try again in {$remainingMinutes} minute(s).",
                'error' => 'account_locked',
                'retry_after' => $remainingMinutes * 60,
            ], 429);
        }

        // Validate credentials
        $user = $this->authService->validateCredentials($email, $request->password);

        if (!$user) {
            $result = $this->authService->recordFailedAttempt($email, $ip);

            if ($result['locked']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Too many failed attempts. Account locked for ' . AuthService::LOCKOUT_MINUTES . ' minutes.',
                    'error' => 'account_locked',
                    'retry_after' => AuthService::LOCKOUT_MINUTES * 60,
                ], 429);
            }

            $remaining = AuthService::MAX_ATTEMPTS - $result['attempts'];
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials.',
                'error' => 'invalid_credentials',
                'attempts_remaining' => max(0, $remaining),
            ], 401);
        }

        // Check if user can login (active, not deleted)
        $canLogin = $this->authService->canUserLogin($user);
        if (!$canLogin['allowed']) {
            return response()->json([
                'success' => false,
                'message' => $canLogin['reason'],
                'error' => 'account_inactive',
            ], $canLogin['code']);
        }

        // Clear failed attempts on successful login
        $this->authService->clearFailedAttempts($email, $ip);

        // Record successful login
        $this->authService->recordSuccessfulLogin($user, $request);

        // Load relationships
        $user->load(['company', 'role.permissions', 'projects']);

        // Get all permissions through role
        $permissions = $user->role?->permissions?->pluck('name')->toArray() ?? [];

        // Create token with permissions as abilities
        $token = $user->createToken('auth-token', $permissions)->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful.',
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => config('sanctum.expiration') * 60, // seconds
            'user' => new UserResource($user),
            'permissions' => $permissions,
        ]);
    }

    /**
     * Logout user (revoke current token).
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully.',
        ]);
    }

    /**
     * Logout from all devices (revoke all tokens).
     */
    public function logoutAll(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out from all devices successfully.',
        ]);
    }

    /**
     * Get authenticated user details.
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load(['company', 'role.permissions', 'projects']);

        return response()->json([
            'success' => true,
            'data' => new UserResource($user),
        ]);
    }

    /**
     * Refresh token (create new token and revoke current).
     */
    public function refresh(Request $request): JsonResponse
    {
        $user = $request->user();
        $permissions = $user->role?->permissions?->pluck('name')->toArray() ?? [];

        // Delete current token
        $request->user()->currentAccessToken()->delete();

        // Create new token
        $token = $user->createToken('auth-token', $permissions)->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Token refreshed successfully.',
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => config('sanctum.expiration') * 60,
        ]);
    }
}
