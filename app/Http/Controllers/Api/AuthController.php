<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Login user and create token.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        // Validate password
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials.',
            ], 401);
        }

        // Check if user is active
        if (!$user->is_active) {
            return response()->json([
                'message' => 'Your account has been deactivated. Please contact administrator.',
            ], 403);
        }

        // Update last login
        $user->update(['last_login_at' => now()]);

        // Load relationships
        $user->load(['company', 'role.permissions', 'projects']);

        // Get all permissions through role
        $permissions = $user->role->permissions->pluck('name')->toArray();

        // Create token with permissions as abilities
        $token = $user->createToken('auth-token', $permissions)->plainTextToken;

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
