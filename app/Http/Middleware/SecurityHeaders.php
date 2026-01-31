<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Security Headers Middleware
 * 
 * Adds essential security headers to all responses to protect against:
 * - XSS attacks
 * - Clickjacking
 * - MIME type sniffing
 * - Information leakage
 */
class SecurityHeaders
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Prevent MIME type sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Prevent clickjacking - deny embedding in iframes
        $response->headers->set('X-Frame-Options', 'DENY');

        // Enable XSS filter in browsers (legacy, but still useful)
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Control how much referrer information is sent
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Prevent caching of sensitive pages
        if ($request->is('api/*') || $request->is('login') || $request->is('logout')) {
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
            $response->headers->set('Pragma', 'no-cache');
        }

        // Remove server version headers (information disclosure)
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');

        // Content Security Policy (allowing Bootstrap CDN)
        if (!$request->is('api/*')) {
            $response->headers->set(
                'Content-Security-Policy',
                "default-src 'self'; " .
                "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net; " .
                "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net; " .
                "font-src 'self' https://fonts.gstatic.com https://cdn.jsdelivr.net; " .
                "img-src 'self' data: https:; " .
                "connect-src 'self' https://cdn.jsdelivr.net; " .
                "frame-ancestors 'none';"
            );
        }

        // Permissions Policy - disable potentially dangerous features
        $response->headers->set(
            'Permissions-Policy',
            'camera=(), microphone=(), geolocation=(), payment=()'
        );

        return $response;
    }
}
