<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Project;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        // If AJAX request, return JSON
        if ($request->ajax() || $request->wantsJson()) {
            return $this->getUsersJson($request);
        }

        // Otherwise return view
        return view('users.index');
    }

    /**
     * Get users as JSON for AJAX requests.
     */
    private function getUsersJson(Request $request): JsonResponse
    {
        $user = auth()->user();
        $query = User::with(['company', 'role', 'manager']);

        // Filter by company (Super Admin can see all, others see their company only)
        if ($user->role->hierarchy_level > 1) {
            $query->where('company_id', $user->company_id);
        } elseif ($request->filled('company_id')) {
            $query->where('company_id', $request->input('company_id'));
        }

        // Filter by role
        if ($request->has('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        // Filter by department
        if ($request->has('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        // Filter by project (users in departments of that project)
        if ($request->has('project_id')) {
            $query->inProjectDepartments($request->project_id);
        }

        // Filter by active status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Search by name, email, or phone
        if ($request->filled('search')) {
            $query->search($request->input('search'));
        }

        // Include soft deleted if requested
        if ($request->boolean('with_trashed')) {
            $query->withTrashed();
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'created_at');
        $sortDir = $request->input('sort_dir', 'desc');
        $allowedSorts = ['first_name', 'last_name', 'email', 'created_at', 'updated_at'];

        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDir === 'asc' ? 'asc' : 'desc');
        }

        // Pagination
        $perPage = min($request->input('per_page', 15), 100);
        $users = $query->paginate($perPage);

        return response()->json([
            'data' => $users->items(),
            'meta' => [
                'current_page' => $users->currentPage(),
                'from' => $users->firstItem(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'to' => $users->lastItem(),
                'total' => $users->total(),
            ]
        ]);
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $user = auth()->user();
        
        // Get companies (Super Admin sees all, others see their own)
        if ($user->role->hierarchy_level <= 1) {
            $companies = Company::active()->orderBy('name')->get();
        } else {
            $companies = Company::where('id', $user->company_id)->get();
        }

        // Get roles the user can assign (only equal or lower hierarchy)
        $roles = Role::where('company_id', $user->company_id)
            ->where('hierarchy_level', '>=', $user->role->hierarchy_level)
            ->active()
            ->orderByHierarchy()
            ->get();

        // Get potential managers
        $managers = User::where('company_id', $user->company_id)
            ->active()
            ->orderBy('first_name')
            ->get();

        return view('users.create', compact('companies', 'roles', 'managers'));
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request): JsonResponse
    {
        $user = auth()->user();

        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'role_id' => 'required|exists:roles,id',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|max:255',
            'password' => ['required', Password::min(8)->mixedCase()->numbers()->symbols()],
            'phone' => 'nullable|string|max:20',
            'employee_code' => 'nullable|string|max:50',
            'designation' => 'nullable|string|max:100',
            'department' => 'nullable|string|max:100',
            'reports_to' => 'nullable|exists:users,id',
            'is_active' => 'boolean',
            'project_ids' => 'nullable|array',
            'project_ids.*' => 'exists:projects,id',
        ]);

        // Check role hierarchy - can only assign roles at or below their level
        $targetRole = Role::findOrFail($validated['role_id']);
        if ($targetRole->hierarchy_level < $user->role->hierarchy_level) {
            return response()->json([
                'message' => 'You cannot assign a role with higher authority than your own.',
            ], 403);
        }

        // Check if role requires project assignment (hierarchy level > 3)
        $requiresProject = $targetRole->hierarchy_level > 3;
        $projectIds = $validated['project_ids'] ?? [];

        if ($requiresProject && empty($projectIds)) {
            return response()->json([
                'message' => 'Users with this role must be assigned to at least one project.',
                'errors' => ['project_ids' => ['At least one project is required for this role.']],
            ], 422);
        }

        // Check email uniqueness within company
        $existingUser = User::where('company_id', $validated['company_id'])
            ->where('email', $validated['email'])
            ->first();

        if ($existingUser) {
            return response()->json([
                'message' => 'A user with this email already exists in the company.',
                'errors' => ['email' => ['Email already exists in this company.']],
            ], 422);
        }

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('users/avatars', 'public');
            $validated['avatar'] = '/storage/' . $avatarPath;
        }

        // Remove project_ids from validated data before creating user
        unset($validated['project_ids']);
        
        $validated['password'] = Hash::make($validated['password']);
        $validated['email_verified_at'] = now();

        $newUser = User::create($validated);

        // Assign projects if provided
        if (!empty($projectIds)) {
            $syncData = [];
            foreach ($projectIds as $projectId) {
                $syncData[$projectId] = [
                    'access_level' => 'member',
                    'assigned_at' => now(),
                    'assigned_by' => $user->id,
                ];
            }
            $newUser->projects()->sync($syncData);
        }

        return response()->json([
            'message' => 'User created successfully.',
            'data' => $newUser->load(['company', 'role', 'manager', 'projects']),
        ], 201);
    }

    /**
     * Display the specified user.
     */
    public function show(Request $request, User $user)
    {
        // If AJAX request, return JSON
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'data' => $user->load(['company', 'role', 'manager', 'projects']),
            ]);
        }

        // Load relationships for the view
        $user->load(['company', 'role', 'manager', 'directReports', 'projects']);

        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        $authUser = auth()->user();

        // Get companies
        if ($authUser->role->hierarchy_level <= 1) {
            $companies = Company::active()->orderBy('name')->get();
        } else {
            $companies = Company::where('id', $authUser->company_id)->get();
        }

        // Get roles the user can assign
        $roles = Role::where('company_id', $user->company_id)
            ->where('hierarchy_level', '>=', $authUser->role->hierarchy_level)
            ->active()
            ->orderByHierarchy()
            ->get();

        // Get potential managers (excluding the user being edited)
        $managers = User::where('company_id', $user->company_id)
            ->where('id', '!=', $user->id)
            ->active()
            ->orderBy('first_name')
            ->get();

        return view('users.edit', compact('user', 'companies', 'roles', 'managers'));
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user): JsonResponse
    {
        $authUser = auth()->user();

        $validated = $request->validate([
            'role_id' => 'sometimes|exists:roles,id',
            'first_name' => 'sometimes|string|max:100',
            'last_name' => 'sometimes|string|max:100',
            'email' => 'sometimes|email|max:255',
            'password' => ['sometimes', 'nullable', Password::min(8)->mixedCase()->numbers()->symbols()],
            'phone' => 'nullable|string|max:20',
            'employee_code' => 'nullable|string|max:50',
            'designation' => 'nullable|string|max:100',
            'department' => 'nullable|string|max:100',
            'reports_to' => 'nullable|exists:users,id',
            'is_active' => 'boolean',
        ]);

        // Check role hierarchy if changing role
        if (isset($validated['role_id'])) {
            $targetRole = Role::findOrFail($validated['role_id']);
            if ($targetRole->hierarchy_level < $authUser->role->hierarchy_level) {
                return response()->json([
                    'message' => 'You cannot assign a role with higher authority than your own.',
                ], 403);
            }
        }

        // Check email uniqueness if changing email
        if (isset($validated['email']) && $validated['email'] !== $user->email) {
            $existingUser = User::where('company_id', $user->company_id)
                ->where('email', $validated['email'])
                ->where('id', '!=', $user->id)
                ->first();

            if ($existingUser) {
                return response()->json([
                    'message' => 'A user with this email already exists in the company.',
                    'errors' => ['email' => ['Email already exists in this company.']],
                ], 422);
            }
        }

        // Handle password
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                $oldPath = str_replace('/storage/', '', $user->avatar);
                Storage::disk('public')->delete($oldPath);
            }
            $avatarPath = $request->file('avatar')->store('users/avatars', 'public');
            $validated['avatar'] = '/storage/' . $avatarPath;
        }

        $user->update($validated);
        $user->fresh()->load(['company', 'role', 'manager']);

        return response()->json([
            'message' => 'User updated successfully.',
            'data' => $user,
        ]);
    }

    /**
     * Remove the specified user (soft delete).
     */
    public function destroy(User $user): JsonResponse
    {
        // Prevent self-deletion
        if ($user->id === auth()->id()) {
            return response()->json([
                'message' => 'You cannot delete your own account.',
            ], 403);
        }

        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully.',
        ]);
    }

    /**
     * Restore a soft-deleted user.
     */
    public function restore(int $id): JsonResponse
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();

        return response()->json([
            'message' => 'User restored successfully.',
            'data' => $user->load(['company', 'role']),
        ]);
    }

    /**
     * Permanently delete a user.
     */
    public function forceDelete(int $id): JsonResponse
    {
        $user = User::withTrashed()->findOrFail($id);

        // Delete avatar if exists
        if ($user->avatar) {
            $oldPath = str_replace('/storage/', '', $user->avatar);
            Storage::disk('public')->delete($oldPath);
        }

        $user->forceDelete();

        return response()->json([
            'message' => 'User permanently deleted.',
        ]);
    }

    /**
     * Assign projects to a user.
     */
    public function assignProjects(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'project_ids' => 'required|array',
            'project_ids.*' => 'exists:projects,id',
            'access_level' => 'sometimes|string|in:viewer,member,manager',
        ]);

        $accessLevel = $validated['access_level'] ?? 'member';
        $assignedBy = auth()->id();

        $syncData = [];
        foreach ($validated['project_ids'] as $projectId) {
            $syncData[$projectId] = [
                'access_level' => $accessLevel,
                'assigned_at' => now(),
                'assigned_by' => $assignedBy,
            ];
        }

        $user->projects()->syncWithoutDetaching($syncData);

        return response()->json([
            'message' => 'Projects assigned successfully.',
            'data' => $user->load('projects'),
        ]);
    }
}
