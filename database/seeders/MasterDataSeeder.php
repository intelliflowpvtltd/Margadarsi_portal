<?php

namespace Database\Seeders;

use App\Models\BudgetRange;
use App\Models\Country;
use App\Models\City;
use App\Models\PropertyType;
use App\Models\SourceCategory;
use App\Models\State;
use App\Models\Timeline;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedCountries();
        $this->seedPropertyTypes();
        $this->seedBudgetRanges();
        $this->seedTimelines();
        $this->seedSourceCategories();

        $this->command->info('✅ Master data seeded successfully!');
    }

    private function seedCountries(): void
    {
        $india = Country::firstOrCreate(
            ['code' => 'IND'],
            [
                'name' => 'India',
                'slug' => 'india',
                'icon' => '🇮🇳',
                'phone_code' => '+91',
                'currency_code' => 'INR',
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        $this->seedIndianStates($india);
        $this->command->info('  - Countries and States seeded');
    }

    private function seedIndianStates(Country $india): void
    {
        $states = [
            ['name' => 'Andhra Pradesh', 'code' => 'AP', 'cities' => ['Visakhapatnam', 'Vijayawada', 'Guntur', 'Tirupati', 'Nellore', 'Kurnool', 'Rajahmundry', 'Kakinada']],
            ['name' => 'Telangana', 'code' => 'TG', 'cities' => ['Hyderabad', 'Warangal', 'Nizamabad', 'Karimnagar', 'Khammam', 'Ramagundam', 'Mahbubnagar', 'Secunderabad']],
            ['name' => 'Karnataka', 'code' => 'KA', 'cities' => ['Bengaluru', 'Mysuru', 'Hubli', 'Mangaluru', 'Belgaum', 'Gulbarga', 'Davanagere', 'Bellary']],
            ['name' => 'Tamil Nadu', 'code' => 'TN', 'cities' => ['Chennai', 'Coimbatore', 'Madurai', 'Tiruchirappalli', 'Salem', 'Tirunelveli', 'Erode', 'Vellore']],
            ['name' => 'Maharashtra', 'code' => 'MH', 'cities' => ['Mumbai', 'Pune', 'Nagpur', 'Thane', 'Nashik', 'Aurangabad', 'Solapur', 'Kolhapur']],
            ['name' => 'Gujarat', 'code' => 'GJ', 'cities' => ['Ahmedabad', 'Surat', 'Vadodara', 'Rajkot', 'Bhavnagar', 'Jamnagar', 'Gandhinagar', 'Junagadh']],
            ['name' => 'Rajasthan', 'code' => 'RJ', 'cities' => ['Jaipur', 'Jodhpur', 'Udaipur', 'Kota', 'Bikaner', 'Ajmer', 'Alwar', 'Bharatpur']],
            ['name' => 'Delhi', 'code' => 'DL', 'cities' => ['New Delhi', 'Delhi']],
            ['name' => 'Uttar Pradesh', 'code' => 'UP', 'cities' => ['Lucknow', 'Kanpur', 'Ghaziabad', 'Agra', 'Varanasi', 'Meerut', 'Prayagraj', 'Noida']],
            ['name' => 'West Bengal', 'code' => 'WB', 'cities' => ['Kolkata', 'Howrah', 'Durgapur', 'Asansol', 'Siliguri', 'Bardhaman', 'Malda', 'Kharagpur']],
            ['name' => 'Kerala', 'code' => 'KL', 'cities' => ['Thiruvananthapuram', 'Kochi', 'Kozhikode', 'Thrissur', 'Kollam', 'Kannur', 'Alappuzha', 'Palakkad']],
            ['name' => 'Punjab', 'code' => 'PB', 'cities' => ['Ludhiana', 'Amritsar', 'Jalandhar', 'Patiala', 'Bathinda', 'Mohali', 'Pathankot', 'Hoshiarpur']],
            ['name' => 'Haryana', 'code' => 'HR', 'cities' => ['Gurugram', 'Faridabad', 'Panipat', 'Ambala', 'Karnal', 'Rohtak', 'Hisar', 'Sonipat']],
            ['name' => 'Madhya Pradesh', 'code' => 'MP', 'cities' => ['Indore', 'Bhopal', 'Jabalpur', 'Gwalior', 'Ujjain', 'Sagar', 'Dewas', 'Satna']],
            ['name' => 'Bihar', 'code' => 'BR', 'cities' => ['Patna', 'Gaya', 'Bhagalpur', 'Muzaffarpur', 'Purnia', 'Darbhanga', 'Bihar Sharif', 'Arrah']],
            ['name' => 'Odisha', 'code' => 'OD', 'cities' => ['Bhubaneswar', 'Cuttack', 'Rourkela', 'Berhampur', 'Sambalpur', 'Puri', 'Balasore', 'Bhadrak']],
            ['name' => 'Jharkhand', 'code' => 'JH', 'cities' => ['Ranchi', 'Jamshedpur', 'Dhanbad', 'Bokaro', 'Hazaribagh', 'Deoghar', 'Giridih', 'Ramgarh']],
            ['name' => 'Chhattisgarh', 'code' => 'CG', 'cities' => ['Raipur', 'Bhilai', 'Bilaspur', 'Korba', 'Durg', 'Rajnandgaon', 'Raigarh', 'Jagdalpur']],
            ['name' => 'Assam', 'code' => 'AS', 'cities' => ['Guwahati', 'Silchar', 'Dibrugarh', 'Jorhat', 'Nagaon', 'Tinsukia', 'Tezpur', 'Karimganj']],
            ['name' => 'Goa', 'code' => 'GA', 'cities' => ['Panaji', 'Margao', 'Vasco da Gama', 'Mapusa', 'Ponda']],
        ];

        // Define metro and tier 1 cities
        $metroCities = ['Mumbai', 'Delhi', 'New Delhi', 'Bengaluru', 'Hyderabad', 'Chennai', 'Kolkata', 'Pune'];
        $tier1Cities = ['Ahmedabad', 'Surat', 'Jaipur', 'Lucknow', 'Kanpur', 'Nagpur', 'Visakhapatnam', 'Indore', 'Thane', 'Bhopal', 'Coimbatore', 'Kochi', 'Patna', 'Vadodara'];
        $tier2Cities = ['Guwahati', 'Chandigarh', 'Mysuru', 'Rajkot', 'Nashik', 'Vijayawada', 'Ghaziabad', 'Ludhiana', 'Agra', 'Madurai', 'Jabalpur', 'Kota', 'Raipur', 'Jodhpur'];

        $sortOrder = 1;
        foreach ($states as $stateData) {
            $state = State::firstOrCreate(
                ['country_id' => $india->id, 'name' => $stateData['name']],
                [
                    'code' => $stateData['code'],
                    'slug' => \Str::slug($stateData['name']),
                    'is_active' => true,
                    'sort_order' => $sortOrder++,
                ]
            );

            $citySortOrder = 1;
            foreach ($stateData['cities'] as $cityName) {
                $isMetro = in_array($cityName, $metroCities);
                $isTier1 = in_array($cityName, $tier1Cities);
                $isTier2 = in_array($cityName, $tier2Cities);
                $isTier3 = !$isMetro && !$isTier1 && !$isTier2;

                City::firstOrCreate(
                    ['state_id' => $state->id, 'name' => $cityName],
                    [
                        'is_metro' => $isMetro,
                        'is_tier1' => $isTier1,
                        'is_tier2' => $isTier2,
                        'is_tier3' => $isTier3,
                        'is_active' => true,
                        'sort_order' => $citySortOrder++,
                    ]
                );
            }
        }
    }

    private function seedPropertyTypes(): void
    {
        $types = [
            ['name' => 'Apartment', 'slug' => 'apartment', 'icon' => 'bi-building', 'color_code' => '#3498db', 'description' => 'Multi-story residential building units'],
            ['name' => 'Villa', 'slug' => 'villa', 'icon' => 'bi-house-door', 'color_code' => '#e74c3c', 'description' => 'Independent luxury houses'],
            ['name' => 'Plot', 'slug' => 'plot', 'icon' => 'bi-grid-3x3', 'color_code' => '#2ecc71', 'description' => 'Open land for construction'],
            ['name' => 'Commercial', 'slug' => 'commercial', 'icon' => 'bi-shop', 'color_code' => '#f39c12', 'description' => 'Office spaces and commercial properties'],
            ['name' => 'Penthouse', 'slug' => 'penthouse', 'icon' => 'bi-gem', 'color_code' => '#9b59b6', 'description' => 'Top floor luxury apartments'],
            ['name' => 'Duplex', 'slug' => 'duplex', 'icon' => 'bi-layers', 'color_code' => '#1abc9c', 'description' => 'Two-floor residential units'],
            ['name' => 'Row House', 'slug' => 'row-house', 'icon' => 'bi-houses', 'color_code' => '#e67e22', 'description' => 'Connected independent houses'],
            ['name' => 'Farmhouse', 'slug' => 'farmhouse', 'icon' => 'bi-tree', 'color_code' => '#27ae60', 'description' => 'Rural residential properties with land'],
        ];

        $sortOrder = 1;
        foreach ($types as $type) {
            PropertyType::firstOrCreate(
                ['slug' => $type['slug']],
                array_merge($type, ['is_active' => true, 'sort_order' => $sortOrder++])
            );
        }

        $this->command->info('  - Property Types seeded');
    }

    private function seedBudgetRanges(): void
    {
        $ranges = [
            ['name' => 'Below 30 Lakhs', 'min_amount' => 0, 'max_amount' => 3000000],
            ['name' => '30L - 50L', 'min_amount' => 3000000, 'max_amount' => 5000000],
            ['name' => '50L - 75L', 'min_amount' => 5000000, 'max_amount' => 7500000],
            ['name' => '75L - 1 Cr', 'min_amount' => 7500000, 'max_amount' => 10000000],
            ['name' => '1 Cr - 1.5 Cr', 'min_amount' => 10000000, 'max_amount' => 15000000],
            ['name' => '1.5 Cr - 2 Cr', 'min_amount' => 15000000, 'max_amount' => 20000000],
            ['name' => '2 Cr - 3 Cr', 'min_amount' => 20000000, 'max_amount' => 30000000],
            ['name' => '3 Cr - 5 Cr', 'min_amount' => 30000000, 'max_amount' => 50000000],
            ['name' => 'Above 5 Cr', 'min_amount' => 50000000, 'max_amount' => null],
        ];

        $sortOrder = 1;
        foreach ($ranges as $range) {
            BudgetRange::firstOrCreate(
                ['name' => $range['name']],
                array_merge($range, ['currency' => 'INR', 'is_active' => true, 'sort_order' => $sortOrder++])
            );
        }

        $this->command->info('  - Budget Ranges seeded');
    }

    private function seedTimelines(): void
    {
        $timelines = [
            ['name' => 'Immediate', 'min_days' => 0, 'max_days' => 30],
            ['name' => '1-3 Months', 'min_days' => 30, 'max_days' => 90],
            ['name' => '3-6 Months', 'min_days' => 90, 'max_days' => 180],
            ['name' => '6-12 Months', 'min_days' => 180, 'max_days' => 365],
            ['name' => 'More than 1 Year', 'min_days' => 365, 'max_days' => null],
            ['name' => 'Just Exploring', 'min_days' => null, 'max_days' => null],
        ];

        $sortOrder = 1;
        foreach ($timelines as $timeline) {
            Timeline::firstOrCreate(
                ['name' => $timeline['name']],
                array_merge($timeline, ['is_active' => true, 'sort_order' => $sortOrder++])
            );
        }

        $this->command->info('  - Timelines seeded');
    }

    private function seedSourceCategories(): void
    {
        $categories = [
            ['name' => 'Digital Marketing', 'slug' => 'digital', 'description' => 'Online advertising and marketing channels'],
            ['name' => 'Referral', 'slug' => 'referral', 'description' => 'Customer and employee referrals'],
            ['name' => 'Walk-in', 'slug' => 'walk-in', 'description' => 'Direct site visits'],
            ['name' => 'Events', 'slug' => 'events', 'description' => 'Property expos and events'],
            ['name' => 'Channel Partner', 'slug' => 'channel-partner', 'description' => 'Broker and agent referrals'],
            ['name' => 'Print Media', 'slug' => 'print-media', 'description' => 'Newspaper and magazine ads'],
            ['name' => 'Outdoor', 'slug' => 'outdoor', 'description' => 'Hoardings and billboards'],
            ['name' => 'Direct', 'slug' => 'direct', 'description' => 'Direct inquiries and cold calls'],
        ];

        $sortOrder = 1;
        foreach ($categories as $category) {
            SourceCategory::firstOrCreate(
                ['slug' => $category['slug']],
                array_merge($category, ['is_active' => true, 'sort_order' => $sortOrder++])
            );
        }

        $this->command->info('  - Source Categories seeded');
    }
}
