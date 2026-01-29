<?php

namespace Tests\Feature;

use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test can list all companies.
     */
    public function test_can_list_all_companies(): void
    {
        Company::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/companies');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    /**
     * Test can create company with valid data.
     */
    public function test_can_create_company_with_valid_data(): void
    {
        $companyData = [
            'name' => 'Margadarsi Infra',
            'legal_name' => 'Margadarsi Infra Private Limited',
            'tagline' => 'Your Trusted Real Estate Partner',
            'email' => 'info@margadarsi.com',
            'phone' => '9876543210',
            'pan_number' => 'ABCDE1234F',
            'gstin' => '29ABCDE1234F1Z5',
            'registered_city' => 'Hyderabad',
            'registered_state' => 'Telangana',
            'registered_pincode' => '500001',
        ];

        $response = $this->postJson('/api/v1/companies', $companyData);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'Margadarsi Infra')
            ->assertJsonPath('message', 'Company created successfully.');

        $this->assertDatabaseHas('companies', [
            'name' => 'Margadarsi Infra',
            'pan_number' => 'ABCDE1234F',
        ]);
    }

    /**
     * Test cannot create company without required fields.
     */
    public function test_cannot_create_company_without_required_fields(): void
    {
        $response = $this->postJson('/api/v1/companies', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    /**
     * Test can show single company.
     */
    public function test_can_show_single_company(): void
    {
        $company = Company::factory()->create([
            'name' => 'Test Company',
        ]);

        $response = $this->getJson("/api/v1/companies/{$company->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'Test Company');
    }

    /**
     * Test can update company.
     */
    public function test_can_update_company(): void
    {
        $company = Company::factory()->create();

        $response = $this->putJson("/api/v1/companies/{$company->id}", [
            'name' => 'Updated Company Name',
            'tagline' => 'New Tagline',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'Updated Company Name')
            ->assertJsonPath('data.tagline', 'New Tagline');
    }

    /**
     * Test can delete company (soft delete).
     */
    public function test_can_delete_company(): void
    {
        $company = Company::factory()->create();

        $response = $this->deleteJson("/api/v1/companies/{$company->id}");

        $response->assertStatus(200)
            ->assertJsonPath('message', 'Company deleted successfully.');

        $this->assertSoftDeleted('companies', ['id' => $company->id]);
    }

    /**
     * Test can restore deleted company.
     */
    public function test_can_restore_deleted_company(): void
    {
        $company = Company::factory()->create();
        $company->delete();

        $response = $this->postJson("/api/v1/companies/{$company->id}/restore");

        $response->assertStatus(200)
            ->assertJsonPath('message', 'Company restored successfully.');

        $this->assertDatabaseHas('companies', [
            'id' => $company->id,
            'deleted_at' => null,
        ]);
    }

    /**
     * Test validates PAN number format.
     */
    public function test_validates_pan_number_format(): void
    {
        $response = $this->postJson('/api/v1/companies', [
            'name' => 'Test Company',
            'pan_number' => 'INVALID',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['pan_number']);
    }

    /**
     * Test validates GSTIN format.
     */
    public function test_validates_gstin_format(): void
    {
        $response = $this->postJson('/api/v1/companies', [
            'name' => 'Test Company',
            'gstin' => 'INVALID',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['gstin']);
    }

    /**
     * Test validates Indian phone number format.
     */
    public function test_validates_indian_phone_number_format(): void
    {
        $response = $this->postJson('/api/v1/companies', [
            'name' => 'Test Company',
            'phone' => '12345', // Invalid - Indian numbers start with 6-9
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['phone']);
    }

    /**
     * Test validates PIN code format.
     */
    public function test_validates_pincode_format(): void
    {
        $response = $this->postJson('/api/v1/companies', [
            'name' => 'Test Company',
            'registered_pincode' => '012345', // Invalid - cannot start with 0
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['registered_pincode']);
    }

    /**
     * Test can filter companies by active status.
     */
    public function test_can_filter_companies_by_active_status(): void
    {
        Company::factory()->count(2)->create(['is_active' => true]);
        Company::factory()->count(1)->create(['is_active' => false]);

        $response = $this->getJson('/api/v1/companies?is_active=true');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    /**
     * Test can search companies by name.
     */
    public function test_can_search_companies_by_name(): void
    {
        Company::factory()->create(['name' => 'Margadarsi Infra']);
        Company::factory()->create(['name' => 'Other Company']);

        $response = $this->getJson('/api/v1/companies?search=Margadarsi');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Margadarsi Infra');
    }
}
