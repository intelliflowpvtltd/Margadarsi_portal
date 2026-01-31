<?php

namespace Database\Seeders;

use App\Models\GenericMaster;
use Illuminate\Database\Seeder;

class GenericMasterSeeder extends Seeder
{
    public function run(): void
    {
        $masters = [
            // Project Features
            ['type' => 'project_feature', 'name' => 'RERA Approved', 'value' => 'rera'],
            ['type' => 'project_feature', 'name' => 'Vastu Compliant', 'value' => 'vastu'],
            ['type' => 'project_feature', 'name' => 'Gated Community', 'value' => 'gated'],
            ['type' => 'project_feature', 'name' => 'Award Winning Design', 'value' => 'award_design'],
            ['type' => 'project_feature', 'name' => 'Green Building Certified', 'value' => 'green_certified'],
            ['type' => 'project_feature', 'name' => 'Smart Home Features', 'value' => 'smart_home'],
            ['type' => 'project_feature', 'name' => 'High-Speed Elevators', 'value' => 'high_speed_lift'],
            ['type' => 'project_feature', 'name' => 'Premium Clubhouse', 'value' => 'premium_clubhouse'],
            
            // Document Types
            ['type' => 'document_type', 'name' => 'Sale Deed', 'value' => 'sale_deed'],
            ['type' => 'document_type', 'name' => 'Agreement of Sale', 'value' => 'agreement_sale'],
            ['type' => 'document_type', 'name' => 'Allotment Letter', 'value' => 'allotment_letter'],
            ['type' => 'document_type', 'name' => 'Application Form', 'value' => 'application_form'],
            ['type' => 'document_type', 'name' => 'Booking Form', 'value' => 'booking_form'],
            ['type' => 'document_type', 'name' => 'Receipt', 'value' => 'receipt'],
            ['type' => 'document_type', 'name' => 'Possession Letter', 'value' => 'possession_letter'],
            ['type' => 'document_type', 'name' => 'NOC', 'value' => 'noc'],
            ['type' => 'document_type', 'name' => 'Property Papers', 'value' => 'property_papers'],
            
            // Payment Methods
            ['type' => 'payment_method', 'name' => 'Cash', 'value' => 'cash'],
            ['type' => 'payment_method', 'name' => 'Cheque', 'value' => 'cheque'],
            ['type' => 'payment_method', 'name' => 'Online Transfer', 'value' => 'online_transfer'],
            ['type' => 'payment_method', 'name' => 'RTGS/NEFT', 'value' => 'rtgs_neft'],
            ['type' => 'payment_method', 'name' => 'Demand Draft', 'value' => 'dd'],
            ['type' => 'payment_method', 'name' => 'UPI', 'value' => 'upi'],
            ['type' => 'payment_method', 'name' => 'Card Payment', 'value' => 'card'],
            ['type' => 'payment_method', 'name' => 'Home Loan', 'value' => 'home_loan'],
            
            // Currencies
            ['type' => 'currency', 'name' => 'Indian Rupee', 'value' => 'INR'],
            ['type' => 'currency', 'name' => 'US Dollar', 'value' => 'USD'],
            ['type' => 'currency', 'name' => 'Euro', 'value' => 'EUR'],
            
            // Units of Measure
            ['type' => 'unit_of_measure', 'name' => 'Square Feet', 'value' => 'sq.ft'],
            ['type' => 'unit_of_measure', 'name' => 'Square Meter', 'value' => 'sq.m'],
            ['type' => 'unit_of_measure', 'name' => 'Square Yard', 'value' => 'sq.yd'],
            ['type' => 'unit_of_measure', 'name' => 'Acre', 'value' => 'acre'],
            ['type' => 'unit_of_measure', 'name' => 'Gunta', 'value' => 'gunta'],
            ['type' => 'unit_of_measure', 'name' => 'Cent', 'value' => 'cent'],
            ['type' => 'unit_of_measure', 'name' => 'Hectare', 'value' => 'hectare'],
            
            // Facing Directions
            ['type' => 'facing_direction', 'name' => 'North', 'value' => 'north'],
            ['type' => 'facing_direction', 'name' => 'South', 'value' => 'south'],
            ['type' => 'facing_direction', 'name' => 'East', 'value' => 'east'],
            ['type' => 'facing_direction', 'name' => 'West', 'value' => 'west'],
            ['type' => 'facing_direction', 'name' => 'North-East', 'value' => 'north_east'],
            ['type' => 'facing_direction', 'name' => 'North-West', 'value' => 'north_west'],
            ['type' => 'facing_direction', 'name' => 'South-East', 'value' => 'south_east'],
            ['type' => 'facing_direction', 'name' => 'South-West', 'value' => 'south_west'],
            
            // Flooring Types
            ['type' => 'flooring_type', 'name' => 'Vitrified Tiles', 'value' => 'vitrified'],
            ['type' => 'flooring_type', 'name' => 'Marble', 'value' => 'marble'],
            ['type' => 'flooring_type', 'name' => 'Granite', 'value' => 'granite'],
            ['type' => 'flooring_type', 'name' => 'Ceramic Tiles', 'value' => 'ceramic'],
            ['type' => 'flooring_type', 'name' => 'Wooden', 'value' => 'wooden'],
            ['type' => 'flooring_type', 'name' => 'Laminate', 'value' => 'laminate'],
            ['type' => 'flooring_type', 'name' => 'Vinyl', 'value' => 'vinyl'],
            ['type' => 'flooring_type', 'name' => 'Italian Marble', 'value' => 'italian_marble'],
            
            // Parking Types
            ['type' => 'parking_type', 'name' => 'Covered Parking', 'value' => 'covered'],
            ['type' => 'parking_type', 'name' => 'Open Parking', 'value' => 'open'],
            ['type' => 'parking_type', 'name' => 'Stilt Parking', 'value' => 'stilt'],
            ['type' => 'parking_type', 'name' => 'Basement Parking', 'value' => 'basement'],
            ['type' => 'parking_type', 'name' => 'Multi-Level Parking', 'value' => 'multi_level'],
            ['type' => 'parking_type', 'name' => 'Mechanical Parking', 'value' => 'mechanical'],
        ];

        foreach ($masters as $master) {
            GenericMaster::create([
                'type' => $master['type'],
                'name' => $master['name'],
                'slug' => \Str::slug($master['name']),
                'value' => $master['value'],
                'is_active' => true,
            ]);
        }

        $this->command->info('✅ GenericMaster seeder completed: ' . count($masters) . ' records created across 8 types');
    }
}
