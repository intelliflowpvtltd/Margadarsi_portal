<?php

namespace Database\Seeders;

use App\Models\BudgetRange;
use App\Models\SpecificationCategory;
use App\Models\SpecificationType;
use App\Models\Timeline;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class GlobalMasterDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedBudgetRanges();
        $this->seedTimelines();
        $this->seedSpecificationCategories();

        $this->command->info('✅ Global master data seeded (Budget Ranges, Timelines, Specifications)');
    }

    private function seedBudgetRanges(): void
    {
        $ranges = [
            ['name' => 'Below ₹25 Lakhs',   'min_amount' => 0,         'max_amount' => 2500000],
            ['name' => '₹25L – ₹50L',        'min_amount' => 2500000,   'max_amount' => 5000000],
            ['name' => '₹50L – ₹75L',        'min_amount' => 5000000,   'max_amount' => 7500000],
            ['name' => '₹75L – ₹1 Cr',       'min_amount' => 7500000,   'max_amount' => 10000000],
            ['name' => '₹1 Cr – ₹1.5 Cr',    'min_amount' => 10000000,  'max_amount' => 15000000],
            ['name' => '₹1.5 Cr – ₹2 Cr',    'min_amount' => 15000000,  'max_amount' => 20000000],
            ['name' => '₹2 Cr – ₹3 Cr',      'min_amount' => 20000000,  'max_amount' => 30000000],
            ['name' => '₹3 Cr – ₹5 Cr',      'min_amount' => 30000000,  'max_amount' => 50000000],
            ['name' => 'Above ₹5 Cr',         'min_amount' => 50000000,  'max_amount' => null],
        ];

        $sortOrder = 1;
        foreach ($ranges as $range) {
            BudgetRange::firstOrCreate(
                ['slug' => Str::slug($range['name'])],
                array_merge($range, [
                    'currency' => 'INR',
                    'is_active' => true,
                    'sort_order' => $sortOrder++,
                ])
            );
        }
    }

    private function seedTimelines(): void
    {
        $timelines = [
            ['name' => 'Immediate',       'min_days' => 0,    'max_days' => 30],
            ['name' => '1-3 Months',       'min_days' => 30,   'max_days' => 90],
            ['name' => '3-6 Months',       'min_days' => 90,   'max_days' => 180],
            ['name' => '6-12 Months',      'min_days' => 180,  'max_days' => 365],
            ['name' => '1-2 Years',        'min_days' => 365,  'max_days' => 730],
            ['name' => 'More than 2 Years','min_days' => 730,  'max_days' => null],
            ['name' => 'Not Decided',      'min_days' => null,  'max_days' => null],
        ];

        $sortOrder = 1;
        foreach ($timelines as $timeline) {
            Timeline::firstOrCreate(
                ['slug' => Str::slug($timeline['name'])],
                array_merge($timeline, [
                    'is_active' => true,
                    'sort_order' => $sortOrder++,
                ])
            );
        }
    }

    private function seedSpecificationCategories(): void
    {
        // Global spec categories (not tied to property type)
        $categories = [
            [
                'name' => 'Area & Dimensions',
                'icon' => 'bi-rulers',
                'description' => 'Plot and built-up area measurements',
                'specs' => [
                    ['name' => 'Super Built-Up Area',    'data_type' => 'number', 'unit' => 'sq.ft', 'is_required' => true],
                    ['name' => 'Built-Up Area',          'data_type' => 'number', 'unit' => 'sq.ft', 'is_required' => false],
                    ['name' => 'Carpet Area',            'data_type' => 'number', 'unit' => 'sq.ft', 'is_required' => true],
                    ['name' => 'Plot Area',              'data_type' => 'number', 'unit' => 'sq.ft', 'is_required' => false],
                    ['name' => 'Facing',                 'data_type' => 'select', 'unit' => null,     'is_required' => false, 'allowed_values' => ['East', 'West', 'North', 'South', 'NE', 'NW', 'SE', 'SW']],
                ],
            ],
            [
                'name' => 'Configuration',
                'icon' => 'bi-grid',
                'description' => 'BHK, bathrooms, balconies, parking',
                'specs' => [
                    ['name' => 'Bedrooms',       'data_type' => 'number', 'unit' => 'BHK',  'is_required' => true],
                    ['name' => 'Bathrooms',      'data_type' => 'number', 'unit' => null,    'is_required' => true],
                    ['name' => 'Balconies',      'data_type' => 'number', 'unit' => null,    'is_required' => false],
                    ['name' => 'Car Parking',    'data_type' => 'number', 'unit' => null,    'is_required' => false],
                    ['name' => 'Servant Room',   'data_type' => 'boolean','unit' => null,    'is_required' => false],
                    ['name' => 'Study Room',     'data_type' => 'boolean','unit' => null,    'is_required' => false],
                ],
            ],
            [
                'name' => 'Pricing',
                'icon' => 'bi-currency-rupee',
                'description' => 'Price, EMI, and payment details',
                'specs' => [
                    ['name' => 'Base Price',         'data_type' => 'number', 'unit' => '₹',       'is_required' => true],
                    ['name' => 'Price per Sq.Ft',    'data_type' => 'number', 'unit' => '₹/sq.ft', 'is_required' => false],
                    ['name' => 'Maintenance Charge', 'data_type' => 'number', 'unit' => '₹/month', 'is_required' => false],
                    ['name' => 'Booking Amount',     'data_type' => 'number', 'unit' => '₹',       'is_required' => false],
                ],
            ],
            [
                'name' => 'Construction',
                'icon' => 'bi-building',
                'description' => 'Floor, tower, and construction details',
                'specs' => [
                    ['name' => 'Floor Number',       'data_type' => 'number', 'unit' => null,    'is_required' => false],
                    ['name' => 'Total Floors',       'data_type' => 'number', 'unit' => null,    'is_required' => false],
                    ['name' => 'Age of Property',    'data_type' => 'number', 'unit' => 'years', 'is_required' => false],
                    ['name' => 'Possession Status',  'data_type' => 'select', 'unit' => null,    'is_required' => true, 'allowed_values' => ['Ready to Move', 'Under Construction', 'Upcoming']],
                    ['name' => 'Furnishing',         'data_type' => 'select', 'unit' => null,    'is_required' => false, 'allowed_values' => ['Unfurnished', 'Semi-Furnished', 'Fully Furnished']],
                ],
            ],
            [
                'name' => 'Legal & Approvals',
                'icon' => 'bi-shield-check',
                'description' => 'RERA, approvals, and compliance',
                'specs' => [
                    ['name' => 'RERA Registered',      'data_type' => 'boolean', 'unit' => null, 'is_required' => true],
                    ['name' => 'RERA Number',           'data_type' => 'text',    'unit' => null, 'is_required' => false],
                    ['name' => 'Approved by Bank',      'data_type' => 'boolean', 'unit' => null, 'is_required' => false],
                    ['name' => 'Completion Certificate', 'data_type' => 'boolean', 'unit' => null, 'is_required' => false],
                    ['name' => 'Occupancy Certificate',  'data_type' => 'boolean', 'unit' => null, 'is_required' => false],
                ],
            ],
        ];

        $catOrder = 1;
        foreach ($categories as $catData) {
            $specs = $catData['specs'];
            unset($catData['specs']);

            $category = SpecificationCategory::firstOrCreate(
                ['slug' => Str::slug($catData['name'])],
                array_merge($catData, [
                    'property_type_id' => null, // Global
                    'is_active' => true,
                    'sort_order' => $catOrder++,
                ])
            );

            $specOrder = 1;
            foreach ($specs as $spec) {
                SpecificationType::firstOrCreate(
                    ['category_id' => $category->id, 'slug' => Str::slug($spec['name'])],
                    array_merge($spec, [
                        'is_active' => true,
                        'sort_order' => $specOrder++,
                    ])
                );
            }
        }
    }
}
