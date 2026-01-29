<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        // For now, since we don't have authentication yet, we'll just return a 403
        // In real implementation, you'd check: auth()->user()->hasPermission($permission)

        // TODO: Uncomment when authentication is implemented
        // if (!auth()->check()) {
        //     return response()->json([
        //         'message' => 'Unauthenticated.',
        //     ], 401);
        // }

        // if (!auth()->user()->hasPermission($permission)) {
        //     return response()->json([
        //         'message' => 'Unauthorized. Permission required: ' . $permission,
        //     ], 403);
        // }

        return $next($request);
    }
}
