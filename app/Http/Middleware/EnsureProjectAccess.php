<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to ensure user has access to the requested project.
 * 
 * Usage in routes:
 * Route::middleware('project.access')->group(...)
 * Route::middleware('project.access:project_id')->get(...) // Custom parameter name
 */
class EnsureProjectAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $paramName  The request parameter containing project_id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string $paramName = 'project_id'): Response
    {
        $user = $request->user();

        if (!$user) {
            return $this->unauthorized($request, 'Unauthenticated.');
        }

        // Get project_id from route parameter, query string, or request body
        $projectId = $request->route($paramName) 
            ?? $request->input($paramName) 
            ?? $request->input('project');

        // If no project specified, allow through (might be listing all accessible projects)
        if (!$projectId) {
            return $next($request);
        }

        // Users with company-wide access can access all projects in their company
        if ($user->hasCompanyWideAccess()) {
            // Verify the project belongs to user's company
            $project = \App\Models\Project::find($projectId);
            
            if (!$project || $project->company_id !== $user->company_id) {
                return $this->forbidden($request, 'Project not found or access denied.');
            }

            return $next($request);
        }

        // Check if user has access to this specific project
        if (!$user->hasProjectAccess((int) $projectId)) {
            return $this->forbidden($request, 'You do not have access to this project.');
        }

        return $next($request);
    }

    /**
     * Return unauthorized response.
     */
    protected function unauthorized(Request $request, string $message): Response
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => $message], 401);
        }
        return redirect()->route('login');
    }

    /**
     * Return forbidden response.
     */
    protected function forbidden(Request $request, string $message): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'error' => 'project_access_denied'
            ], 403);
        }
        return redirect()->back()->with('error', $message);
    }
}
