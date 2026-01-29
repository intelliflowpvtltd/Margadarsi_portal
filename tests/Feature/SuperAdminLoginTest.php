<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperAdminLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_login_successfully(): void
    {
        // Seed permissions
        Permission::seedPermissions();

        // Create company
        $company = Company::factory()->create([
            'name' => 'Test Company',
        ]);

        // Create Super Admin role
        $superAdminRole = Role::factory()->create([
            'company_id' => $company->id,
            'name' => 'Super Admin',
            'slug' => 'super-admin',
            'hierarchy_level' => 1,
            'is_system' => true,
        ]);

        // Assign ALL permissions to Super Admin
        $permissions = Permission::all();
        $superAdminRole->permissions()->sync($permissions->pluck('id'));

        // Create Super Admin user
        $superAdmin = User::factory()->create([
            'company_id' => $company->id,
            'role_id' => $superAdminRole->id,
            'email' => 'superadmin@test.com',
            'password' => 'password123',
            'is_active' => true,
        ]);

        // Attempt login
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'superadmin@test.com',
            'password' => 'password123',
        ]);

        // Assert successful login
        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'token',
                'user' => [
                    'id',
                    'first_name',
                    'last_name',
                    'email',
                    'company_id',
                    'role_id',
                ],
                'permissions',
            ])
            ->assertJsonPath('user.email', 'superadmin@test.com')
            ->assertJsonPath('message', 'Login successful.');

        // Verify all 25 permissions are included
        $returnedPermissions = $response->json('permissions');
        $this->assertCount(25, $returnedPermissions);
        $this->assertContains('companies.create', $returnedPermissions);
        $this->assertContains('companies.force-delete', $returnedPermissions);
        $this->assertContains('projects.create', $returnedPermissions);
        $this->assertContains('users.create', $returnedPermissions);

        // Verify token works
        $token = $response->json('token');
        $this->assertNotEmpty($token);

        // Test using the token to access a protected route
        $protectedResponse = $this->getJson('/api/v1/companies', [
            'Authorization' => "Bearer {$token}",
        ]);

        $protectedResponse->assertStatus(200);
    }

    public function test_super_admin_with_seeded_data_can_login(): void
    {
        // Run the actual seeder
        $this->artisan('db:seed', ['--class' => 'UserSeeder']);

        // Login with the seeded super admin
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'superadmin@margadarsi.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('user.email', 'superadmin@margadarsi.com')
            ->assertJsonPath('message', 'Login successful.');

        // Verify 25 permissions
        $this->assertCount(25, $response->json('permissions'));

        // Test token works
        $token = $response->json('token');
        $companiesResponse = $this->getJson('/api/v1/companies', [
            'Authorization' => "Bearer {$token}",
        ]);
        $companiesResponse->assertStatus(200);

        echo "\n✅ Super Admin Login: SUCCESS\n";
        echo "✅ Email: superadmin@margadarsi.com\n";
        echo "✅ Password: password123\n";
        echo "✅ Token Generated: " . substr($token, 0, 20) . "...\n";
        echo "✅ Permissions Count: 25\n";
        echo "✅ Protected Route Access: WORKING\n";
    }

    public function test_invalid_credentials_return_401(): void
    {
        // Run seeder
        $this->artisan('db:seed', ['--class' => 'UserSeeder']);

        // Try with wrong password
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'superadmin@margadarsi.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Invalid credentials.',
            ]);
    }

    public function test_inactive_user_cannot_login(): void
    {
        Permission::seedPermissions();
        $company = Company::factory()->create();
        $role = Role::factory()->create(['company_id' => $company->id]);

        // Create inactive user
        $user = User::factory()->create([
            'company_id' => $company->id,
            'role_id' => $role->id,
            'email' => 'inactive@test.com',
            'is_active' => false,
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'inactive@test.com',
            'password' => 'password',
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'message' => 'Your account has been deactivated. Please contact administrator.',
            ]);
    }
}
