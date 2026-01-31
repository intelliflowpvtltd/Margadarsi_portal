<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    /**
     * Display a listing of the roles.
     */
    public function index()
    {
        return view('roles.index');
    }

    /**
     * Show the form for creating a new role.
     */
    public function create()
    {
        return view('roles.create');
    }

    /**
     * Store a newly created role - handled by API
     */
    public function store(Request $request)
    {
        // This is handled by the API
        // Redirect to index with message
        return redirect()->route('roles.index')->with('success', 'Role created successfully');
    }

    /**
     * Display the specified role.
     */
    public function show($id)
    {
        return view('roles.show');
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit($id)
    {
        return view('roles.edit');
    }

    /**
     * Update the specified role - handled by API
     */
    public function update(Request $request, $id)
    {
        // This is handled by the API
        return redirect()->route('roles.index')->with('success', 'Role updated successfully');
    }

    /**
     * Remove the specified role - handled by API
     */
    public function destroy($id)
    {
        // This is handled by the API
        return redirect()->route('roles.index')->with('success', 'Role deleted successfully');
    }

    /**
     * Restore a soft-deleted role
     */
    public function restore($id)
    {
        // This is handled by the API
        return redirect()->route('roles.index')->with('success', 'Role restored successfully');
    }

    /**
     * Permanently delete a role
     */
    public function forceDelete($id)
    {
        // This is handled by the API
        return redirect()->route('roles.index')->with('success', 'Role permanently deleted');
    }

    /**
     * Show the form for editing role permissions
     */
    public function editPermissions($id)
    {
        $role = Role::findOrFail($id);
        return view('roles.permissions', compact('role'));
    }

    /**
     * Update role permissions
     */
    public function updatePermissions(Request $request, $id): JsonResponse
    {
        $role = Role::findOrFail($id);

        $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'string',
        ]);

        $permissionNames = $request->input('permissions', []);

        // Get permission IDs from names
        $permissionIds = Permission::whereIn('name', $permissionNames)->pluck('id')->toArray();

        // Sync permissions with the role
        DB::transaction(function () use ($role, $permissionIds) {
            $role->permissions()->sync($permissionIds);
        });

        return response()->json([
            'message' => 'Permissions updated successfully.',
            'data' => [
                'role_id' => $role->id,
                'permissions_count' => count($permissionIds),
            ],
        ]);
    }
}
