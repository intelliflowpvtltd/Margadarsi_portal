<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Project;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    protected Company $company;
    protected Role $role;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = Company::factory()->create();
        $this->role = Role::factory()->create(['company_id' => $this->company->id]);
    }

    public function test_can_list_all_users_for_company(): void
    {
        User::factory()->count(3)->create([
            'company_id' => $this->company->id,
            'role_id' => $this->role->id,
        ]);

        $response = $this->getJson("/api/v1/users?company_id={$this->company->id}");

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_can_create_user(): void
    {
        $data = [
            'company_id' => $this->company->id,
            'role_id' => $this->role->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '9876543210',
            'is_active' => true,
        ];

        $response = $this->postJson('/api/v1/users', $data);

        $response->assertStatus(201)
            ->assertJsonPath('data.first_name', 'John')
            ->assertJsonPath('data.full_name', 'John Doe');

        $this->assertDatabaseHas('users', [
            'company_id' => $this->company->id,
            'email' => 'john@example.com',
        ]);
    }

    public function test_email_unique_per_company(): void
    {
        User::factory()->create([
            'company_id' => $this->company->id,
            'role_id' => $this->role->id,
            'email' => 'duplicate@example.com',
        ]);

        $data = [
            'company_id' => $this->company->id,
            'role_id' => $this->role->id,
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'duplicate@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/api/v1/users', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_same_email_allowed_in_different_companies(): void
    {
        $company2 = Company::factory()->create();
        $role2 = Role::factory()->create(['company_id' => $company2->id]);

        User::factory()->create([
            'company_id' => $this->company->id,
            'role_id' => $this->role->id,
            'email' => 'same@example.com',
        ]);

        $data = [
            'company_id' => $company2->id,
            'role_id' => $role2->id,
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'same@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/api/v1/users', $data);

        $response->assertStatus(201);
    }

    public function test_can_assign_user_to_projects(): void
    {
        $user = User::factory()->create([
            'company_id' => $this->company->id,
            'role_id' => $this->role->id,
        ]);

        $project1 = Project::factory()->create(['company_id' => $this->company->id]);
        $project2 = Project::factory()->create(['company_id' => $this->company->id]);

        $response = $this->postJson("/api/v1/users/{$user->id}/projects", [
            'project_ids' => [$project1->id, $project2->id],
        ]);

        $response->assertStatus(200);
        $this->assertEquals(2, $user->projects()->count());
    }

    public function test_user_can_only_be_assigned_to_projects_in_same_company(): void
    {
        $user = User::factory()->create([
            'company_id' => $this->company->id,
            'role_id' => $this->role->id,
        ]);

        $otherCompany = Company::factory()->create();
        $otherProject = Project::factory()->create(['company_id' => $otherCompany->id]);

        $response = $this->postJson("/api/v1/users/{$user->id}/projects", [
            'project_ids' => [$otherProject->id],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['project_ids.0']);
    }

    public function test_can_list_user_projects(): void
    {
        $user = User::factory()->create([
            'company_id' => $this->company->id,
            'role_id' => $this->role->id,
        ]);

        $project = Project::factory()->create(['company_id' => $this->company->id]);
        $user->assignToProject($project->id);

        $response = $this->getJson("/api/v1/users/{$user->id}/projects");

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_can_remove_user_from_project(): void
    {
        $user = User::factory()->create([
            'company_id' => $this->company->id,
            'role_id' => $this->role->id,
        ]);

        $project = Project::factory()->create(['company_id' => $this->company->id]);
        $user->assignToProject($project->id);

        $response = $this->deleteJson("/api/v1/users/{$user->id}/projects/{$project->id}");

        $response->assertStatus(200);
        $this->assertEquals(0, $user->projects()->count());
    }

    public function test_user_project_access_check(): void
    {
        $user = User::factory()->create([
            'company_id' => $this->company->id,
            'role_id' => $this->role->id,
        ]);

        $project1 = Project::factory()->create(['company_id' => $this->company->id]);
        $project2 = Project::factory()->create(['company_id' => $this->company->id]);

        $user->assignToProject($project1->id);

        $this->assertTrue($user->hasProjectAccess($project1->id));
        $this->assertFalse($user->hasProjectAccess($project2->id));
    }

    public function test_company_has_many_users(): void
    {
        User::factory()->count(3)->create([
            'company_id' => $this->company->id,
            'role_id' => $this->role->id,
        ]);

        $this->assertCount(3, $this->company->users);
    }

    public function test_user_full_name_accessor(): void
    {
        $user = User::factory()->create([
            'company_id' => $this->company->id,
            'role_id' => $this->role->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        $this->assertEquals('John Doe', $user->full_name);
        $this->assertEquals('JD', $user->initials);
    }
}
