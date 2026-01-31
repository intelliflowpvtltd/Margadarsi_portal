<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureDepartmentAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $paramName = 'department_id'): Response
    {
        $user = $request->user();

        if (!$user) {
            return $this->unauthorized($request, 'Unauthenticated.');
        }

        // Get department_id from route parameter, query string, or request body
        $departmentId = $request->route($paramName)
            ?? $request->input($paramName)
            ?? $request->input('department');

        // If no department specified, allow through
        if (!$departmentId) {
            return $next($request);
        }

        // Check if user has access to this department
        if (!$user->hasDepartmentAccess((int) $departmentId)) {
            return $this->forbidden($request, 'You do not have access to this department.');
        }

        return $next($request);
    }

    /**
     * Handle unauthorized request.
     */
    protected function unauthorized(Request $request, string $message): Response
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => $message], 401);
        }

        return redirect()->route('login')->with('error', $message);
    }

    /**
     * Handle forbidden request.
     */
    protected function forbidden(Request $request, string $message): Response
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => $message], 403);
        }

        return redirect()->back()->with('error', $message);
    }
}
