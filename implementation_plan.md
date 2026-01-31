# Implementation Plan - Auth System Hardening

## Goal Description
Enhance the security of the Authentication system by implementing rate limiting for the web login route and ensuring API tokens are revoked upon password reset. This addresses findings from the security audit and verification process.

## User Review Required
> [!IMPORTANT]
> This change will enforce a limit of 5 login attempts per minute on the web interface.

## Proposed Changes

### Backend

#### [MODIFY] [web.php](file:///d:/Margadarsi%20Portal/Backend/routes/web.php)
- Apply `throttle:5,1` middleware to the `POST /login` route to prevent brute-force attacks.

#### [MODIFY] [AuthController.php](file:///d:/Margadarsi%20Portal/Backend/app/Http/Controllers/Web/AuthController.php)
- In `resetPassword` method, add `$user->tokens()->delete()` to invalidate all existing API tokens (Sanctum) when the password is successfully reset.

## Verification Plan

### Automated Tests
- N/A (We will rely on code verification as these are configuration/logic changes best tested manually or via specific integration tests which we are not creating right now).

### Manual Verification
1.  **Throttling**:
    - Attempt to login with invalid credentials 6 times in rapid succession on the web interface.
    - Verify that the 6th attempt is blocked with a "Too Many Attempts" error.
2.  **Token Revocation**:
    - (Logical Check): Verify the code change explicitly calls `tokens()->delete()`.
