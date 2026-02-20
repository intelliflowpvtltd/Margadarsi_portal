<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed permissions
        Permission::seedPermissions();
    }

    public function test_all_permissions_are_seeded(): void
    {
        // companies: 6, projects: 7, departments: 5, roles: 7, users: 7, teams: 4, leads: 11, incentives: 4, reports: 4, settings: 2, masters: 2 = 59 total
        $this->assertEquals(59, Permission::count());
    }

    public function test_permissions_grouped_by_module(): void
    {
        $companiesPerms = Permission::forModule('companies')->count();
        $projectsPerms = Permission::forModule('projects')->count();
        $rolesPerms = Permission::forModule('roles')->count();
        $usersPerms = Permission::forModule('users')->count();

        $this->assertEquals(6, $companiesPerms);
        $this->assertEquals(7, $projectsPerms);
        $this->assertEquals(7, $rolesPerms);
        $this->assertEquals(7, $usersPerms);
    }

    public function test_can_assign_permissions_to_role(): void
    {
        $company = Company::factory()->create();
        $role = Role::factory()->create(['company_id' => $company->id]);

        $permissions = Permission::forModule('companies')->get();
        $role->permissions()->attach($permissions->pluck('id'));

        $this->assertEquals(6, $role->permissions()->count());
    }

    public function test_role_permissions_relationship_works(): void
    {
        $company = Company::factory()->create();
        $role = Role::factory()->create(['company_id' => $company->id]);
        $permission = Permission::where('name', 'companies.view')->first();

        $role->permissions()->attach($permission->id);

        $this->assertTrue($role->permissions->contains($permission));
    }

    public function test_super_admin_gets_all_permissions(): void
    {
        $company = Company::factory()->create();

        // Create system roles
        Role::createSystemRolesForCompany($company->id);

        $superAdmin = Role::where('slug', 'super_admin')
            ->where('company_id', $company->id)
            ->first();

        // Assign all permissions to super admin
        $allPermissions = Permission::all();
        $superAdmin->permissions()->sync($allPermissions->pluck('id'));

        $this->assertEquals(59, $superAdmin->permissions()->count());
    }

    public function test_sales_executive_has_limited_permissions(): void
    {
        $company = Company::factory()->create();
        Role::createSystemRolesForCompany($company->id);

        $salesExec = Role::where('slug', 'telecaller')
            ->where('company_id', $company->id)
            ->first();

        // Assign only view permissions
        $viewPermissions = Permission::byNames([
            'companies.view',
            'projects.view',
            'roles.view',
        ])->get();

        $salesExec->permissions()->sync($viewPermissions->pluck('id'));

        $this->assertEquals(3, $salesExec->permissions()->count());
        $this->assertTrue($salesExec->permissions->contains('name', 'companies.view'));
        $this->assertFalse($salesExec->permissions->contains('name', 'companies.create'));
    }

    public function test_permission_has_many_roles(): void
    {
        $company = Company::factory()->create();
        $role1 = Role::factory()->create(['company_id' => $company->id]);
        $role2 = Role::factory()->create(['company_id' => $company->id]);

        $permission = Permission::where('name', 'companies.view')->first();

        $role1->permissions()->attach($permission->id);
        $role2->permissions()->attach($permission->id);

        $this->assertEquals(2, $permission->roles()->count());
    }

    public function test_can_get_all_permission_names_list(): void
    {
        $names = Permission::getAllPermissionNames();

        $this->assertIsArray($names);
        $this->assertCount(59, $names);
        $this->assertContains('companies.view', $names);
        $this->assertContains('projects.create', $names);
        $this->assertContains('roles.seed', $names);
        $this->assertContains('users.assign-projects', $names);
    }

    public function test_permission_unique_constraint(): void
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        Permission::create([
            'name' => 'companies.view',
            'display_name' => 'Duplicate',
            'module' => 'companies',
        ]);
    }
}
