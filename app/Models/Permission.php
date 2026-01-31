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
     * Maintains backward compatibility with existing routes while adding new blueprint permissions.
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
        'teams' => [
            ['name' => 'teams.view', 'display_name' => 'View Teams', 'description' => 'Can view teams'],
            ['name' => 'teams.create', 'display_name' => 'Create Teams', 'description' => 'Can create new teams'],
            ['name' => 'teams.update', 'display_name' => 'Update Teams', 'description' => 'Can update teams'],
            ['name' => 'teams.manage-members', 'display_name' => 'Manage Team Members', 'description' => 'Can add/remove team members'],
        ],
        'leads' => [
            ['name' => 'leads.view', 'display_name' => 'View Own Leads', 'description' => 'Can view own assigned leads'],
            ['name' => 'leads.view-all', 'display_name' => 'View All Leads', 'description' => 'Can view all leads in company'],
            ['name' => 'leads.view-team', 'display_name' => 'View Team Leads', 'description' => 'Can view leads assigned to team'],
            ['name' => 'leads.create', 'display_name' => 'Create Leads', 'description' => 'Can create new leads'],
            ['name' => 'leads.update', 'display_name' => 'Update Leads', 'description' => 'Can update leads'],
            ['name' => 'leads.delete', 'display_name' => 'Delete Leads', 'description' => 'Can delete leads'],
            ['name' => 'leads.reassign', 'display_name' => 'Reassign Leads', 'description' => 'Can reassign leads to other users'],
            ['name' => 'leads.handover', 'display_name' => 'Handover Leads', 'description' => 'Can handover leads between teams'],
            ['name' => 'leads.log-call', 'display_name' => 'Log Calls', 'description' => 'Can log call activities for leads'],
            ['name' => 'leads.qualify', 'display_name' => 'Qualify Leads', 'description' => 'Can mark leads as qualified'],
            ['name' => 'leads.disqualify', 'display_name' => 'Disqualify Leads', 'description' => 'Can mark leads as not qualified'],
        ],
        'incentives' => [
            ['name' => 'incentives.view', 'display_name' => 'View Own Incentives', 'description' => 'Can view own incentives'],
            ['name' => 'incentives.view-all', 'display_name' => 'View All Incentives', 'description' => 'Can view all incentives'],
            ['name' => 'incentives.approve', 'display_name' => 'Approve Incentives', 'description' => 'Can approve incentive requests'],
            ['name' => 'incentives.reject', 'display_name' => 'Reject Incentives', 'description' => 'Can reject incentive requests'],
        ],
        'reports' => [
            ['name' => 'reports.own', 'display_name' => 'View Own Reports', 'description' => 'Can view own performance reports'],
            ['name' => 'reports.team', 'display_name' => 'View Team Reports', 'description' => 'Can view team performance reports'],
            ['name' => 'reports.project', 'display_name' => 'View Project Reports', 'description' => 'Can view project-level reports'],
            ['name' => 'reports.company', 'display_name' => 'View Company Reports', 'description' => 'Can view company-wide reports'],
        ],
        'settings' => [
            ['name' => 'settings.view', 'display_name' => 'View Settings', 'description' => 'Can view application settings'],
            ['name' => 'settings.update', 'display_name' => 'Update Settings', 'description' => 'Can modify application settings'],
        ],
    ];

    /**
     * Role-Permission Matrix based on blueprint.
     * Maps role slugs to their assigned permissions.
     * Permissions marked with * in blueprint have limited scope (handled at runtime).
     */
    public const ROLE_PERMISSION_MATRIX = [
        'super_admin' => [
            // Full access - all permissions
            'companies.view', 'companies.create', 'companies.update', 'companies.delete', 'companies.restore', 'companies.force-delete',
            'projects.view', 'projects.create', 'projects.update', 'projects.delete', 'projects.restore', 'projects.force-delete', 'projects.manage-specifications',
            'roles.view', 'roles.create', 'roles.update', 'roles.delete', 'roles.restore', 'roles.force-delete', 'roles.seed',
            'users.view', 'users.create', 'users.update', 'users.delete', 'users.restore', 'users.force-delete', 'users.assign-projects',
            'teams.view', 'teams.create', 'teams.update', 'teams.manage-members',
            'leads.view', 'leads.view-all', 'leads.view-team', 'leads.create', 'leads.update', 'leads.delete', 'leads.reassign', 'leads.handover', 'leads.log-call', 'leads.qualify', 'leads.disqualify',
            'incentives.view', 'incentives.view-all', 'incentives.approve', 'incentives.reject',
            'reports.own', 'reports.team', 'reports.project', 'reports.company',
            'settings.view', 'settings.update',
        ],
        'admin' => [
            'companies.view', 'companies.create', 'companies.update', 'companies.delete', 'companies.restore',
            'projects.view', 'projects.create', 'projects.update', 'projects.delete', 'projects.restore', 'projects.manage-specifications',
            'roles.view', 'roles.create', 'roles.update', 'roles.delete', 'roles.restore', 'roles.seed',
            'users.view', 'users.create', 'users.update', 'users.delete', 'users.restore', 'users.assign-projects',
            'teams.view', 'teams.create', 'teams.update', 'teams.manage-members',
            'leads.view', 'leads.view-all', 'leads.view-team', 'leads.create', 'leads.update', 'leads.delete', 'leads.reassign', 'leads.handover', 'leads.log-call', 'leads.qualify', 'leads.disqualify',
            'incentives.view', 'incentives.view-all', 'incentives.approve', 'incentives.reject',
            'reports.own', 'reports.team', 'reports.project', 'reports.company',
            'settings.view',
        ],
        'sales_director' => [
            'companies.view',
            'projects.view',
            'roles.view',
            'users.view', 'users.create', 'users.update', 'users.delete', 'users.assign-projects',
            'teams.view', 'teams.create', 'teams.update', 'teams.manage-members',
            'leads.view', 'leads.view-all', 'leads.view-team', 'leads.create', 'leads.update', 'leads.delete', 'leads.reassign', 'leads.handover', 'leads.log-call', 'leads.qualify', 'leads.disqualify',
            'incentives.view', 'incentives.view-all', 'incentives.approve', 'incentives.reject',
            'reports.own', 'reports.team', 'reports.project', 'reports.company',
        ],
        'sales_manager' => [
            // Limited scope: only assigned projects/teams
            'companies.view',
            'projects.view', 'projects.create', 'projects.update', 'projects.delete', 'projects.restore', 'projects.manage-specifications',
            'roles.view',
            'users.view', 'users.create', 'users.update', 'users.assign-projects',
            'teams.view', 'teams.update', 'teams.manage-members',
            'leads.view', 'leads.view-team', 'leads.create', 'leads.update', 'leads.delete', 'leads.reassign', 'leads.log-call', 'leads.qualify', 'leads.disqualify',
            'incentives.view', 'incentives.view-all', 'incentives.approve', 'incentives.reject',
            'reports.own', 'reports.team',
        ],
        'project_manager' => [
            // Limited scope: only assigned projects
            'companies.view',
            'projects.view', 'projects.update', 'projects.manage-specifications',
            'roles.view',
            'users.view',
            'teams.view', 'teams.update', 'teams.manage-members',
            'leads.view', 'leads.view-team', 'leads.create', 'leads.update', 'leads.delete', 'leads.reassign', 'leads.log-call', 'leads.qualify', 'leads.disqualify',
            'reports.own', 'reports.team',
        ],
        'team_lead' => [
            // Limited scope: only assigned projects/teams
            'companies.view',
            'projects.view',
            'roles.view',
            'users.view',
            'leads.view', 'leads.view-team', 'leads.create', 'leads.update', 'leads.reassign', 'leads.log-call', 'leads.qualify', 'leads.disqualify',
            'reports.own', 'reports.team',
        ],
        'telecaller' => [
            // Limited scope: only own leads
            'companies.view',
            'projects.view',
            'roles.view',
            'users.view',
            'leads.view', 'leads.create', 'leads.update', 'leads.log-call', 'leads.qualify', 'leads.disqualify',
            'incentives.view',
            'reports.own',
        ],
        'channel_partner' => [
            // Limited scope: only own leads, separate portal
            'projects.view',
            'leads.view', 'leads.create',
            'incentives.view',
            'reports.own',
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
