<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    private Company $company;

    protected function setUp(): void
    {
        parent::setUp();
        $this->company = Company::factory()->create();
    }

    public function test_can_list_all_projects(): void
    {
        Project::factory()->count(3)->create(['company_id' => $this->company->id]);

        $response = $this->getJson('/api/v1/projects');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_can_create_residential_project(): void
    {
        $data = [
            'company_id' => $this->company->id,
            'name' => 'Margadarsi Heights',
            'type' => 'residential',
            'city' => 'Hyderabad',
            'state' => 'Telangana',
            'rera_number' => 'P02400003456',
            'total_land_area' => 5.5,
            'land_area_unit' => 'acres',
        ];

        $response = $this->postJson('/api/v1/projects', $data);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'Margadarsi Heights')
            ->assertJsonPath('data.type', 'residential');

        $this->assertDatabaseHas('projects', ['name' => 'Margadarsi Heights']);
        $this->assertDatabaseHas('residential_specs', ['project_id' => $response->json('data.id')]);
    }

    public function test_can_create_open_plots_project(): void
    {
        $data = [
            'company_id' => $this->company->id,
            'name' => 'Margadarsi Green Meadows',
            'type' => 'open_plots',
            'city' => 'Hyderabad',
            'state' => 'Telangana',
        ];

        $response = $this->postJson('/api/v1/projects', $data);

        $response->assertStatus(201)
            ->assertJsonPath('data.type', 'open_plots');

        $this->assertDatabaseHas('open_plot_specs', ['project_id' => $response->json('data.id')]);
    }

    public function test_cannot_create_project_without_required_fields(): void
    {
        $response = $this->postJson('/api/v1/projects', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'type', 'company_id', 'city', 'state']);
    }

    public function test_can_show_single_project_with_relations(): void
    {
        $project = Project::factory()->create(['company_id' => $this->company->id]);

        $response = $this->getJson("/api/v1/projects/{$project->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $project->id)
            ->assertJsonStructure([
                'data' => ['id', 'name', 'type', 'location', 'timeline']
            ]);
    }

    public function test_can_update_project(): void
    {
        $project = Project::factory()->create(['company_id' => $this->company->id]);

        $response = $this->putJson("/api/v1/projects/{$project->id}", [
            'name' => 'Updated Project Name',
            'status' => 'ongoing',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'Updated Project Name')
            ->assertJsonPath('data.status', 'ongoing');
    }

    public function test_can_delete_project(): void
    {
        $project = Project::factory()->create(['company_id' => $this->company->id]);

        $response = $this->deleteJson("/api/v1/projects/{$project->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted('projects', ['id' => $project->id]);
    }

    public function test_can_filter_projects_by_type(): void
    {
        Project::factory()->count(2)->create([
            'company_id' => $this->company->id,
            'type' => 'residential'
        ]);
        Project::factory()->create([
            'company_id' => $this->company->id,
            'type' => 'commercial'
        ]);

        $response = $this->getJson('/api/v1/projects?type=residential');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    public function test_can_filter_projects_by_city(): void
    {
        Project::factory()->create([
            'company_id' => $this->company->id,
            'city' => 'Hyderabad'
        ]);
        Project::factory()->create([
            'company_id' => $this->company->id,
            'city' => 'Bangalore'
        ]);

        $response = $this->getJson('/api/v1/projects?city=Hyderabad');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_validates_pincode_format(): void
    {
        $response = $this->postJson('/api/v1/projects', [
            'company_id' => $this->company->id,
            'name' => 'Test Project',
            'type' => 'residential',
            'city' => 'Hyderabad',
            'state' => 'Telangana',
            'pincode' => '012345', // Invalid - starts with 0
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['pincode']);
    }

    public function test_company_has_many_projects(): void
    {
        Project::factory()->count(3)->create(['company_id' => $this->company->id]);

        $this->assertCount(3, $this->company->projects);
    }
}
