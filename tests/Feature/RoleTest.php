<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleTest extends TestCase
{
    use RefreshDatabase;

    private Company $company;

    protected function setUp(): void
    {
        parent::setUp();
        $this->company = Company::factory()->create();
    }

    public function test_can_list_all_roles_for_company(): void
    {
        Role::factory()->count(3)->create(['company_id' => $this->company->id]);

        $response = $this->getJson("/api/v1/roles?company_id={$this->company->id}");

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_roles_are_ordered_by_hierarchy_level(): void
    {
        Role::factory()->create(['company_id' => $this->company->id, 'hierarchy_level' => 5]);
        Role::factory()->create(['company_id' => $this->company->id, 'hierarchy_level' => 1]);
        Role::factory()->create(['company_id' => $this->company->id, 'hierarchy_level' => 3]);

        $response = $this->getJson("/api/v1/roles?company_id={$this->company->id}");

        $response->assertStatus(200);
        $data = $response->json('data');

        $this->assertEquals(1, $data[0]['hierarchy_level']);
        $this->assertEquals(3, $data[1]['hierarchy_level']);
        $this->assertEquals(5, $data[2]['hierarchy_level']);
    }

    public function test_can_create_role(): void
    {
        $data = [
            'company_id' => $this->company->id,
            'name' => 'Marketing Manager',
            'slug' => 'marketing-manager',
            'description' => 'Handles marketing activities',
            'hierarchy_level' => 4,
        ];

        $response = $this->postJson('/api/v1/roles', $data);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'Marketing Manager')
            ->assertJsonPath('data.is_system', false);

        $this->assertDatabaseHas('roles', [
            'company_id' => $this->company->id,
            'slug' => 'marketing-manager',
        ]);
    }

    public function test_cannot_create_duplicate_slug_in_same_company(): void
    {
        Role::factory()->create([
            'company_id' => $this->company->id,
            'slug' => 'sales-manager',
        ]);

        $response = $this->postJson('/api/v1/roles', [
            'company_id' => $this->company->id,
            'name' => 'Sales Manager 2',
            'slug' => 'sales-manager',
            'hierarchy_level' => 3,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['slug']);
    }

    public function test_can_use_same_slug_in_different_company(): void
    {
        $otherCompany = Company::factory()->create();

        Role::factory()->create([
            'company_id' => $this->company->id,
            'slug' => 'sales-manager',
        ]);

        $response = $this->postJson('/api/v1/roles', [
            'company_id' => $otherCompany->id,
            'name' => 'Sales Manager',
            'slug' => 'sales-manager',
            'hierarchy_level' => 3,
        ]);

        $response->assertStatus(201);
    }

    public function test_can_show_single_role(): void
    {
        $role = Role::factory()->create(['company_id' => $this->company->id]);

        $response = $this->getJson("/api/v1/roles/{$role->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $role->id);
    }

    public function test_can_update_role(): void
    {
        $role = Role::factory()->create(['company_id' => $this->company->id]);

        $response = $this->putJson("/api/v1/roles/{$role->id}", [
            'name' => 'Updated Role Name',
            'hierarchy_level' => 8,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'Updated Role Name')
            ->assertJsonPath('data.hierarchy_level', 8);
    }

    public function test_cannot_delete_system_role(): void
    {
        $role = Role::factory()->system()->create(['company_id' => $this->company->id]);

        $response = $this->deleteJson("/api/v1/roles/{$role->id}");

        $response->assertStatus(403)
            ->assertJsonPath('message', 'System roles cannot be deleted.');
    }

    public function test_can_delete_custom_role(): void
    {
        $role = Role::factory()->create([
            'company_id' => $this->company->id,
            'is_system' => false,
        ]);

        $response = $this->deleteJson("/api/v1/roles/{$role->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted('roles', ['id' => $role->id]);
    }

    public function test_can_seed_system_roles_for_company(): void
    {
        $response = $this->postJson('/api/v1/roles-config/seed', [
            'company_id' => $this->company->id,
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('message', 'System roles created successfully.');

        $this->assertDatabaseHas('roles', [
            'company_id' => $this->company->id,
            'slug' => 'super-admin',
            'is_system' => true,
        ]);

        $this->assertEquals(7, Role::forCompany($this->company->id)->system()->count());
    }

    public function test_cannot_seed_system_roles_twice(): void
    {
        Role::createSystemRolesForCompany($this->company->id);

        $response = $this->postJson('/api/v1/roles-config/seed', [
            'company_id' => $this->company->id,
        ]);

        $response->assertStatus(409)
            ->assertJsonPath('message', 'Company already has system roles.');
    }

    public function test_can_get_system_roles_configuration(): void
    {
        $response = $this->getJson('/api/v1/roles-config/system');

        $response->assertStatus(200)
            ->assertJsonCount(7, 'data');
    }

    public function test_company_has_many_roles(): void
    {
        Role::factory()->count(3)->create(['company_id' => $this->company->id]);

        $this->assertCount(3, $this->company->roles);
    }

    public function test_role_hierarchy_comparison(): void
    {
        $superAdmin = Role::factory()->create([
            'company_id' => $this->company->id,
            'hierarchy_level' => 1,
        ]);

        $salesExec = Role::factory()->create([
            'company_id' => $this->company->id,
            'hierarchy_level' => 5,
        ]);

        $this->assertTrue($superAdmin->hasHigherAuthorityThan($salesExec));
        $this->assertFalse($salesExec->hasHigherAuthorityThan($superAdmin));
        $this->assertTrue($superAdmin->hasAuthorityOver($salesExec));
    }
}
