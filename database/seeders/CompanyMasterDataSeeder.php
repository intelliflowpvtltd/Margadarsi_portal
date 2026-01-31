<?php

namespace Database\Seeders;

use App\Models\ClosureReason;
use App\Models\CommissionType;
use App\Models\Company;
use App\Models\CpStatus;
use App\Models\DisputeType;
use App\Models\LeadSource;
use App\Models\LeadStage;
use App\Models\LeadStatus;
use App\Models\NqReason;
use App\Models\SourceCategory;
use App\Models\TemperatureTag;
use Illuminate\Database\Seeder;

class CompanyMasterDataSeeder extends Seeder
{
    public function run(): void
    {
        $companies = Company::all();

        foreach ($companies as $company) {
            $this->seedCompanyMasters($company);
        }

        $this->command->info('✅ Company master data seeded successfully!');
    }

    public function seedCompanyMasters(Company $company): void
    {
        $this->seedLeadSources($company);
        $this->seedLeadStatuses($company);
        $this->seedLeadStages($company);
        $this->seedTemperatureTags($company);
        $this->seedClosureReasons($company);
        $this->seedNqReasons($company);
        $this->seedCpStatuses($company);
        $this->seedCommissionTypes($company);
        $this->seedDisputeTypes($company);
    }

    private function seedLeadSources(Company $company): void
    {
        $sourceCategories = SourceCategory::all()->keyBy('slug');

        $sources = [
            ['name' => 'Facebook Ads', 'category' => 'digital'],
            ['name' => 'Google Ads', 'category' => 'digital'],
            ['name' => 'Instagram', 'category' => 'digital'],
            ['name' => 'Website', 'category' => 'digital'],
            ['name' => 'Housing.com', 'category' => 'digital'],
            ['name' => '99acres', 'category' => 'digital'],
            ['name' => 'MagicBricks', 'category' => 'digital'],
            ['name' => 'Customer Referral', 'category' => 'referral'],
            ['name' => 'Employee Referral', 'category' => 'referral'],
            ['name' => 'Site Walk-in', 'category' => 'walk-in'],
            ['name' => 'Office Walk-in', 'category' => 'walk-in'],
            ['name' => 'Property Expo', 'category' => 'events'],
            ['name' => 'CP Referral', 'category' => 'channel-partner'],
            ['name' => 'Newspaper Ad', 'category' => 'print-media'],
            ['name' => 'Hoarding', 'category' => 'outdoor'],
            ['name' => 'Direct Call', 'category' => 'direct'],
        ];

        $sortOrder = 1;
        foreach ($sources as $source) {
            LeadSource::firstOrCreate(
                ['company_id' => $company->id, 'slug' => \Str::slug($source['name'])],
                [
                    'name' => $source['name'],
                    'source_category_id' => $sourceCategories[$source['category']]->id ?? null,
                    'is_active' => true,
                    'sort_order' => $sortOrder++,
                ]
            );
        }
    }

    private function seedLeadStatuses(Company $company): void
    {
        $statuses = [
            ['name' => 'New', 'color' => '#3B82F6', 'is_final' => false],
            ['name' => 'Contacted', 'color' => '#8B5CF6', 'is_final' => false],
            ['name' => 'Follow-up', 'color' => '#F59E0B', 'is_final' => false],
            ['name' => 'Site Visit Scheduled', 'color' => '#10B981', 'is_final' => false],
            ['name' => 'Site Visit Done', 'color' => '#06B6D4', 'is_final' => false],
            ['name' => 'Negotiation', 'color' => '#EC4899', 'is_final' => false],
            ['name' => 'Converted', 'color' => '#22C55E', 'is_final' => true],
            ['name' => 'Lost', 'color' => '#EF4444', 'is_final' => true],
            ['name' => 'Not Qualified', 'color' => '#6B7280', 'is_final' => true],
        ];

        $sortOrder = 1;
        foreach ($statuses as $status) {
            LeadStatus::firstOrCreate(
                ['company_id' => $company->id, 'slug' => \Str::slug($status['name'])],
                array_merge($status, ['is_active' => true, 'sort_order' => $sortOrder++])
            );
        }
    }

    private function seedLeadStages(Company $company): void
    {
        $stages = [
            ['name' => 'Enquiry', 'color' => '#3B82F6', 'probability' => 10],
            ['name' => 'Qualification', 'color' => '#8B5CF6', 'probability' => 20],
            ['name' => 'Site Visit', 'color' => '#F59E0B', 'probability' => 40],
            ['name' => 'Negotiation', 'color' => '#EC4899', 'probability' => 60],
            ['name' => 'Booking', 'color' => '#10B981', 'probability' => 80],
            ['name' => 'Agreement', 'color' => '#06B6D4', 'probability' => 90],
            ['name' => 'Registration', 'color' => '#22C55E', 'probability' => 100],
        ];

        $sortOrder = 1;
        foreach ($stages as $stage) {
            LeadStage::firstOrCreate(
                ['company_id' => $company->id, 'slug' => \Str::slug($stage['name'])],
                array_merge($stage, ['is_active' => true, 'sort_order' => $sortOrder++])
            );
        }
    }

    private function seedTemperatureTags(Company $company): void
    {
        $tags = [
            ['name' => 'Hot', 'color' => '#EF4444', 'priority' => 3],
            ['name' => 'Warm', 'color' => '#F59E0B', 'priority' => 2],
            ['name' => 'Cold', 'color' => '#3B82F6', 'priority' => 1],
        ];

        $sortOrder = 1;
        foreach ($tags as $tag) {
            TemperatureTag::firstOrCreate(
                ['company_id' => $company->id, 'slug' => \Str::slug($tag['name'])],
                array_merge($tag, ['is_active' => true, 'sort_order' => $sortOrder++])
            );
        }
    }

    private function seedClosureReasons(Company $company): void
    {
        $reasons = [
            // Won reasons
            ['name' => 'Price Negotiation Successful', 'type' => 'won'],
            ['name' => 'Location Preferred', 'type' => 'won'],
            ['name' => 'Good Amenities', 'type' => 'won'],
            ['name' => 'Builder Reputation', 'type' => 'won'],
            // Lost reasons
            ['name' => 'Budget Constraints', 'type' => 'lost'],
            ['name' => 'Bought from Competitor', 'type' => 'lost'],
            ['name' => 'Location Not Suitable', 'type' => 'lost'],
            ['name' => 'Project Delayed', 'type' => 'lost'],
            ['name' => 'Financial Issues', 'type' => 'lost'],
            ['name' => 'Changed Mind', 'type' => 'lost'],
            ['name' => 'Poor Communication', 'type' => 'lost'],
            ['name' => 'Better Deal Elsewhere', 'type' => 'lost'],
        ];

        $sortOrder = 1;
        foreach ($reasons as $reason) {
            ClosureReason::firstOrCreate(
                ['company_id' => $company->id, 'slug' => \Str::slug($reason['name'])],
                array_merge($reason, ['is_active' => true, 'sort_order' => $sortOrder++])
            );
        }
    }

    private function seedNqReasons(Company $company): void
    {
        $reasons = [
            ['name' => 'Invalid Contact Number'],
            ['name' => 'Not Interested'],
            ['name' => 'Already Purchased'],
            ['name' => 'Looking for Different Property Type'],
            ['name' => 'Budget Mismatch'],
            ['name' => 'Location Mismatch'],
            ['name' => 'Duplicate Lead'],
            ['name' => 'Spam/Fake Inquiry'],
            ['name' => 'Cannot Reach'],
            ['name' => 'Wrong Number'],
        ];

        $sortOrder = 1;
        foreach ($reasons as $reason) {
            NqReason::firstOrCreate(
                ['company_id' => $company->id, 'slug' => \Str::slug($reason['name'])],
                array_merge($reason, ['is_active' => true, 'sort_order' => $sortOrder++])
            );
        }
    }

    private function seedCpStatuses(Company $company): void
    {
        $statuses = [
            ['name' => 'Pending Approval', 'color' => '#F59E0B', 'allows_leads' => false],
            ['name' => 'Active', 'color' => '#22C55E', 'allows_leads' => true],
            ['name' => 'Suspended', 'color' => '#EF4444', 'allows_leads' => false],
            ['name' => 'Inactive', 'color' => '#6B7280', 'allows_leads' => false],
            ['name' => 'Blacklisted', 'color' => '#1F2937', 'allows_leads' => false],
        ];

        $sortOrder = 1;
        foreach ($statuses as $status) {
            CpStatus::firstOrCreate(
                ['company_id' => $company->id, 'slug' => \Str::slug($status['name'])],
                array_merge($status, ['is_active' => true, 'sort_order' => $sortOrder++])
            );
        }
    }

    private function seedCommissionTypes(Company $company): void
    {
        $types = [
            ['name' => 'Flat Rate', 'calculation_type' => 'flat', 'description' => 'Fixed amount per booking'],
            ['name' => 'Percentage', 'calculation_type' => 'percentage', 'description' => 'Percentage of sale value'],
            ['name' => 'Slab Based', 'calculation_type' => 'slab', 'description' => 'Different rates based on value slabs'],
        ];

        $sortOrder = 1;
        foreach ($types as $type) {
            CommissionType::firstOrCreate(
                ['company_id' => $company->id, 'slug' => \Str::slug($type['name'])],
                array_merge($type, ['is_active' => true, 'sort_order' => $sortOrder++])
            );
        }
    }

    private function seedDisputeTypes(Company $company): void
    {
        $types = [
            ['name' => 'Commission Dispute', 'description' => 'Disagreement on commission calculation', 'resolution_days' => 7],
            ['name' => 'Lead Ownership', 'description' => 'Dispute over lead attribution', 'resolution_days' => 5],
            ['name' => 'Payment Delay', 'description' => 'Commission payment not received on time', 'resolution_days' => 10],
            ['name' => 'Booking Cancellation', 'description' => 'Commission after booking cancellation', 'resolution_days' => 14],
            ['name' => 'Documentation Issue', 'description' => 'Missing or incorrect documentation', 'resolution_days' => 7],
        ];

        $sortOrder = 1;
        foreach ($types as $type) {
            DisputeType::firstOrCreate(
                ['company_id' => $company->id, 'slug' => \Str::slug($type['name'])],
                array_merge($type, ['is_active' => true, 'sort_order' => $sortOrder++])
            );
        }
    }
}
