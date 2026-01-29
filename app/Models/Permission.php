<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'module',
    ];

    /**
     * Permission definitions grouped by module.
     */
    public const PERMISSIONS = [
        'companies' => [
            ['name' => 'companies.view', 'display_name' => 'View Companies', 'description' => 'Can view and list companies'],
            ['name' => 'companies.create', 'display_name' => 'Create Companies', 'description' => 'Can create new companies'],
            ['name' => 'companies.update', 'display_name' => 'Update Companies', 'description' => 'Can update existing companies'],
            ['name' => 'companies.delete', 'display_name' => 'Delete Companies', 'description' => 'Can soft delete companies'],
            ['name' => 'companies.restore', 'display_name' => 'Restore Companies', 'description' => 'Can restore deleted companies'],
            ['name' => 'companies.force-delete', 'display_name' => 'Permanently Delete Companies', 'description' => 'Can permanently delete companies'],
        ],
        'projects' => [
            ['name' => 'projects.view', 'display_name' => 'View Projects', 'description' => 'Can view and list projects'],
            ['name' => 'projects.create', 'display_name' => 'Create Projects', 'description' => 'Can create new projects'],
            ['name' => 'projects.update', 'display_name' => 'Update Projects', 'description' => 'Can update existing projects'],
            ['name' => 'projects.delete', 'display_name' => 'Delete Projects', 'description' => 'Can soft delete projects'],
            ['name' => 'projects.restore', 'display_name' => 'Restore Projects', 'description' => 'Can restore deleted projects'],
            ['name' => 'projects.force-delete', 'display_name' => 'Permanently Delete Projects', 'description' => 'Can permanently delete projects'],
            ['name' => 'projects.manage-specifications', 'display_name' => 'Manage Project Specifications', 'description' => 'Can update project specifications'],
        ],
        'roles' => [
            ['name' => 'roles.view', 'display_name' => 'View Roles', 'description' => 'Can view and list roles'],
            ['name' => 'roles.create', 'display_name' => 'Create Roles', 'description' => 'Can create custom roles'],
            ['name' => 'roles.update', 'display_name' => 'Update Roles', 'description' => 'Can update roles'],
            ['name' => 'roles.delete', 'display_name' => 'Delete Roles', 'description' => 'Can soft delete roles'],
            ['name' => 'roles.restore', 'display_name' => 'Restore Roles', 'description' => 'Can restore deleted roles'],
            ['name' => 'roles.force-delete', 'display_name' => 'Permanently Delete Roles', 'description' => 'Can permanently delete roles'],
            ['name' => 'roles.seed', 'display_name' => 'Seed System Roles', 'description' => 'Can seed default system roles for companies'],
        ],
        'users' => [
            ['name' => 'users.view', 'display_name' => 'View Users', 'description' => 'Can view and list users'],
            ['name' => 'users.create', 'display_name' => 'Create Users', 'description' => 'Can create new users'],
            ['name' => 'users.update', 'display_name' => 'Update Users', 'description' => 'Can update existing users'],
            ['name' => 'users.delete', 'display_name' => 'Delete Users', 'description' => 'Can soft delete users'],
            ['name' => 'users.restore', 'display_name' => 'Restore Users', 'description' => 'Can restore deleted users'],
            ['name' => 'users.force-delete', 'display_name' => 'Permanently Delete Users', 'description' => 'Can permanently delete users'],
            ['name' => 'users.assign-projects', 'display_name' => 'Assign Users to Projects', 'description' => 'Can assign users to projects'],
        ],
    ];

    // ==================== RELATIONSHIPS ====================

    /**
     * Get all roles that have this permission.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_permissions');
    }

    // ==================== SCOPES ====================

    /**
     * Scope to filter by module.
     */
    public function scopeForModule($query, string $module)
    {
        return $query->where('module', $module);
    }

    /**
     * Scope to filter by permission names.
     */
    public function scopeByNames($query, array $names)
    {
        return $query->whereIn('name', $names);
    }

    // ==================== STATIC METHODS ====================

    /**
     * Get all permission names as a flat array.
     */
    public static function getAllPermissionNames(): array
    {
        $names = [];
        foreach (self::PERMISSIONS as $module => $permissions) {
            foreach ($permissions as $permission) {
                $names[] = $permission['name'];
            }
        }
        return $names;
    }

    /**
     * Seed all permissions from the constant.
     */
    public static function seedPermissions(): void
    {
        foreach (self::PERMISSIONS as $module => $permissions) {
            foreach ($permissions as $permissionData) {
                self::firstOrCreate(
                    ['name' => $permissionData['name']],
                    array_merge($permissionData, ['module' => $module])
                );
            }
        }
    }
}
