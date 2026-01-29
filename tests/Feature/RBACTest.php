<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Permission;
use App\Models\Project;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RBACTest extends TestCase
{
    use RefreshDatabase;

    protected Company $company;
    protected array $roles;
    protected array $users;
    protected Project $project;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed permissions
        Permission::seedPermissions();

        // Create company
        $this->company = Company::factory()->create();

        // Create all system roles
        $this->roles = $this->createSystemRoles();

        // Assign permissions to roles
        $this->assignPermissionsToRoles();

        // Create one user for each role
        $this->users = $this->createUsers();

        // Create a test project
        $this->project = Project::factory()->create([
            'company_id' => $this->company->id,
        ]);
    }

    private function createSystemRoles(): array
    {
        $roleData = [
            'super-admin' => ['name' => 'Super Admin', 'hierarchy_level' => 1],
            'admin' => ['name' => 'Admin', 'hierarchy_level' => 2],
            'sales-manager' => ['name' => 'Sales Manager', 'hierarchy_level' => 3],
            'senior-sales-executive' => ['name' => 'Senior Sales Executive', 'hierarchy_level' => 4],
            'sales-executive' => ['name' => 'Sales Executive', 'hierarchy_level' => 5],
            'team-leader' => ['name' => 'Team Leader', 'hierarchy_level' => 6],
            'tele-caller' => ['name' => 'Tele Caller', 'hierarchy_level' => 7],
        ];

        $roles = [];
        foreach ($roleData as $slug => $data) {
            $roles[$slug] = Role::factory()->create([
                'company_id' => $this->company->id,
                'name' => $data['name'],
                'slug' => $slug,
                'hierarchy_level' => $data['hierarchy_level'],
                'is_system' => true,
            ]);
        }

        return $roles;
    }

    private function assignPermissionsToRoles(): void
    {
        $permissions = Permission::all()->keyBy('name');

        // Super Admin - ALL
        $this->roles['super-admin']->permissions()->sync($permissions->pluck('id'));

        // Admin - All except force-delete
        $adminPerms = $permissions->reject(fn($p) => str_ends_with($p->name, '.force-delete'));
        $this->roles['admin']->permissions()->sync($adminPerms->pluck('id'));

        // Sales Manager
        $salesManagerPerms = [
            'companies.view',
            'projects.view',
            'projects.create',
            'projects.update',
            'projects.delete',
            'projects.restore',
            'projects.manage-specifications',
            'roles.view',
            'users.view',
            'users.create',
            'users.update',
            'users.assign-projects',
        ];
        $this->roles['sales-manager']->permissions()->sync(
            $permissions->whereIn('name', $salesManagerPerms)->pluck('id')
        );

        // Senior Sales Executive
        $srSalesPerms = [
            'companies.view',
            'projects.view',
            'projects.update',
            'projects.manage-specifications',
            'roles.view',
            'users.view',
        ];
        $this->roles['senior-sales-executive']->permissions()->sync(
            $permissions->whereIn('name', $srSalesPerms)->pluck('id')
        );

        // Sales Executive, Team Leader, Tele Caller - View only
        $viewOnlyPerms = ['companies.view', 'projects.view', 'roles.view', 'users.view'];
        foreach (['sales-executive', 'team-leader', 'tele-caller'] as $slug) {
            $this->roles[$slug]->permissions()->sync(
                $permissions->whereIn('name', $viewOnlyPerms)->pluck('id')
            );
        }
    }

    private function createUsers(): array
    {
        $users = [];
        foreach ($this->roles as $slug => $role) {
            $users[$slug] = User::factory()->create([
                'company_id' => $this->company->id,
                'role_id' => $role->id,
                'email' => "{$slug}@test.com",
                'is_active' => true,
            ]);
        }
        return $users;
    }

    // ==================== AUTHENTICATION TESTS ====================

    public function test_all_users_can_login(): void
    {
        foreach ($this->users as $slug => $user) {
            $response = $this->postJson('/api/v1/auth/login', [
                'email' => $user->email,
                'password' => 'password',
            ]);

            $response->assertStatus(200)
                ->assertJsonStructure(['token', 'user', 'permissions'])
                ->assertJsonPath('user.email', $user->email);
        }
    }

    public function test_super_admin_has_all_permissions(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $this->users['super-admin']->email,
            'password' => 'password',
        ]);

        $permissions = $response->json('permissions');
        $this->assertCount(25, $permissions);
        $this->assertContains('companies.force-delete', $permissions);
    }

    public function test_admin_has_all_except_force_delete(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $this->users['admin']->email,
            'password' => 'password',
        ]);

        $permissions = $response->json('permissions');
        $this->assertCount(23, $permissions);
        $this->assertNotContains('companies.force-delete', $permissions);
        $this->assertNotContains('projects.force-delete', $permissions);
    }

    public function test_sales_manager_has_project_focused_permissions(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $this->users['sales-manager']->email,
            'password' => 'password',
        ]);

        $permissions = $response->json('permissions');
        $this->assertCount(12, $permissions); // Actual count from assignment
        $this->assertContains('projects.create', $permissions);
        $this->assertContains('users.create', $permissions);
        $this->assertNotContains('companies.create', $permissions);
    }

    public function test_tele_caller_has_view_only_permissions(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $this->users['tele-caller']->email,
            'password' => 'password',
        ]);

        $permissions = $response->json('permissions');
        $this->assertCount(4, $permissions);
        $this->assertContains('companies.view', $permissions);
        $this->assertContains('projects.view', $permissions);
        $this->assertNotContains('companies.create', $permissions);
        $this->assertNotContains('projects.create', $permissions);
    }

    // ==================== COMPANIES RBAC TESTS ====================

    public function test_super_admin_can_create_company(): void
    {
        $token = $this->users['super-admin']->createToken('test')->plainTextToken;

        $response = $this->postJson('/api/v1/companies', [
            'name' => 'Test Company',
            'registered_city' => 'Hyderabad',
            'registered_state' => 'Telangana',
        ], ['Authorization' => "Bearer {$token}"]);

        $response->assertStatus(201);
    }

    public function test_sales_manager_cannot_create_company(): void
    {
        $token = $this->users['sales-manager']->createToken('test')->plainTextToken;

        $response = $this->postJson('/api/v1/companies', [
            'name' => 'Test Company',
            'registered_city' => 'Hyderabad',
            'registered_state' => 'Telangana',
        ], ['Authorization' => "Bearer {$token}"]);

        $response->assertStatus(403)
            ->assertJson([
                'message' => 'Unauthorized.',
                'required_permission' => 'companies.create',
            ]);
    }

    public function test_all_roles_can_view_companies(): void
    {
        foreach ($this->users as $slug => $user) {
            $token = $user->createToken('test')->plainTextToken;

            $response = $this->getJson('/api/v1/companies', [
                'Authorization' => "Bearer {$token}",
            ]);

            $response->assertStatus(200);
        }
    }

    public function test_tele_caller_cannot_update_company(): void
    {
        $company = Company::factory()->create();
        $token = $this->users['tele-caller']->createToken('test')->plainTextToken;

        $response = $this->putJson("/api/v1/companies/{$company->id}", [
            'name' => 'Updated Name',
        ], ['Authorization' => "Bearer {$token}"]);

        $response->assertStatus(403)
            ->assertJsonPath('required_permission', 'companies.update');
    }

    public function test_only_super_admin_can_force_delete_company(): void
    {
        $company = Company::factory()->create();
        $company->delete(); // Soft delete first

        // Admin should fail
        $adminToken = $this->users['admin']->createToken('test')->plainTextToken;
        $response = $this->deleteJson("/api/v1/companies/{$company->id}/force", [], [
            'Authorization' => "Bearer {$adminToken}",
        ]);
        $response->assertStatus(403);

        // Super Admin should succeed
        $superAdminToken = $this->users['super-admin']->createToken('test')->plainTextToken;
        $response = $this->deleteJson("/api/v1/companies/{$company->id}/force", [], [
            'Authorization' => "Bearer {$superAdminToken}",
        ]);
        $response->assertStatus(200);
    }

    // ==================== PROJECTS RBAC TESTS ====================

    public function test_sales_manager_can_create_project(): void
    {
        $token = $this->users['sales-manager']->createToken('test')->plainTextToken;

        $response = $this->postJson('/api/v1/projects', [
            'company_id' => $this->company->id,
            'name' => 'New Project',
            'slug' => 'new-project',
            'type' => 'residential',
            'city' => 'Hyderabad',
            'state' => 'Telangana',
        ], ['Authorization' => "Bearer {$token}"]);

        $response->assertStatus(201);
    }

    public function test_senior_sales_exec_can_update_but_not_create_project(): void
    {
        $token = $this->users['senior-sales-executive']->createToken('test')->plainTextToken;

        // Cannot create
        $response = $this->postJson('/api/v1/projects', [
            'company_id' => $this->company->id,
            'name' => 'New Project',
            'slug' => 'new-project-2',
            'type' => 'residential',
            'city' => 'Hyderabad',
            'state' => 'Telangana',
        ], ['Authorization' => "Bearer {$token}"]);
        $response->assertStatus(403);

        // Can update
        $response = $this->putJson("/api/v1/projects/{$this->project->id}", [
            'name' => 'Updated Project Name',
        ], ['Authorization' => "Bearer {$token}"]);
        $response->assertStatus(200);
    }

    public function test_tele_caller_can_only_view_projects(): void
    {
        $token = $this->users['tele-caller']->createToken('test')->plainTextToken;

        // Can view
        $response = $this->getJson('/api/v1/projects', [
            'Authorization' => "Bearer {$token}",
        ]);
        $response->assertStatus(200);

        // Cannot create
        $response = $this->postJson('/api/v1/projects', [
            'company_id' => $this->company->id,
            'name' => 'Test Project',
            'slug' => 'test-project-3',
            'type' => 'residential',
            'city' => 'Hyderabad',
            'state' => 'Telangana',
        ], ['Authorization' => "Bearer {$token}"]);
        $response->assertStatus(403);

        // Cannot update
        $response = $this->putJson("/api/v1/projects/{$this->project->id}", [
            'name' => 'Updated',
        ], ['Authorization' => "Bearer {$token}"]);
        $response->assertStatus(403);

        // Cannot delete
        $response = $this->deleteJson("/api/v1/projects/{$this->project->id}", [], [
            'Authorization' => "Bearer {$token}",
        ]);
        $response->assertStatus(403);
    }

    // ==================== USERS RBAC TESTS ====================

    public function test_sales_manager_can_create_users(): void
    {
        $token = $this->users['sales-manager']->createToken('test')->plainTextToken;

        $response = $this->postJson('/api/v1/users', [
            'company_id' => $this->company->id,
            'role_id' => $this->roles['sales-executive']->id,
            'first_name' => 'New',
            'last_name' => 'User',
            'email' => 'newuser@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ], ['Authorization' => "Bearer {$token}"]);

        $response->assertStatus(201);
    }

    public function test_tele_caller_cannot_create_users(): void
    {
        $token = $this->users['tele-caller']->createToken('test')->plainTextToken;

        $response = $this->postJson('/api/v1/users', [
            'company_id' => $this->company->id,
            'role_id' => $this->roles['tele-caller']->id,
            'first_name' => 'New',
            'last_name' => 'User',
            'email' => 'another@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ], ['Authorization' => "Bearer {$token}"]);

        $response->assertStatus(403)
            ->assertJsonPath('required_permission', 'users.create');
    }

    public function test_sales_manager_can_assign_users_to_projects(): void
    {
        $user = $this->users['sales-executive'];
        $token = $this->users['sales-manager']->createToken('test')->plainTextToken;

        $response = $this->postJson("/api/v1/users/{$user->id}/projects", [
            'project_ids' => [$this->project->id],
        ], ['Authorization' => "Bearer {$token}"]);

        $response->assertStatus(200);
        $this->assertTrue($user->hasProjectAccess($this->project->id));
    }

    // ==================== ROLES RBAC TESTS ====================

    public function test_admin_can_create_roles(): void
    {
        $token = $this->users['admin']->createToken('test')->plainTextToken;

        $response = $this->postJson('/api/v1/roles', [
            'company_id' => $this->company->id,
            'name' => 'Custom Role',
            'slug' => 'custom-role',
            'hierarchy_level' => 50,
        ], ['Authorization' => "Bearer {$token}"]);

        $response->assertStatus(201);
    }

    public function test_sales_executive_cannot_create_roles(): void
    {
        $token = $this->users['sales-executive']->createToken('test')->plainTextToken;

        $response = $this->postJson('/api/v1/roles', [
            'company_id' => $this->company->id,
            'name' => 'Another Role',
            'slug' => 'another-role',
            'hierarchy_level' => 60,
        ], ['Authorization' => "Bearer {$token}"]);

        $response->assertStatus(403);
    }

    // ==================== UNAUTHENTICATED TESTS ====================

    public function test_unauthenticated_users_cannot_access_protected_routes(): void
    {
        $endpoints = [
            ['method' => 'GET', 'url' => '/api/v1/companies', 'data' => []],
            ['method' => 'POST', 'url' => '/api/v1/companies', 'data' => ['name' => 'Test']],
            ['method' => 'GET', 'url' => '/api/v1/projects', 'data' => []],
            ['method' => 'GET', 'url' => '/api/v1/roles', 'data' => []],
            ['method' => 'GET', 'url' => '/api/v1/users', 'data' => []],
        ];

        foreach ($endpoints as $endpoint) {
            $response = $this->json($endpoint['method'], $endpoint['url'], $endpoint['data']);
            $response->assertStatus(401)
                ->assertJson(['message' => 'Unauthenticated.']);
        }
    }
}
