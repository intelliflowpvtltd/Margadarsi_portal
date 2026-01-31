<?php

namespace Database\Seeders;

use App\Models\PropertyStatus;
use Illuminate\Database\Seeder;

class PropertyStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            // Universal Statuses (no property type)
            [
                'property_type_id' => null,
                'name' => 'Under Construction',
                'slug' => 'under-construction',
                'color_code' => '#ffc107',
                'badge_class' => 'bg-warning',
                'workflow_order' => 1,
                'is_final_state' => false,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'property_type_id' => null,
                'name' => 'Ready to Move',
                'slug' => 'ready-to-move',
                'color_code' => '#28a745',
                'badge_class' => 'bg-success',
                'workflow_order' => 2,
                'is_final_state' => false,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'property_type_id' => null,
                'name' => 'Launching Soon',
                'slug' => 'launching-soon',
                'color_code' => '#17a2b8',
                'badge_class' => 'bg-info',
                'workflow_order' => 0,
                'is_final_state' => false,
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'property_type_id' => null,
                'name' => 'Sold Out',
                'slug' => 'sold-out',
                'color_code' => '#dc3545',
                'badge_class' => 'bg-danger',
                'workflow_order' => 10,
                'is_final_state' => true,
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'property_type_id' => null,
                'name' => 'On Hold',
                'slug' => 'on-hold',
                'color_code' => '#6c757d',
                'badge_class' => 'bg-secondary',
                'workflow_order' => 5,
                'is_final_state' => false,
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'property_type_id' => null,
                'name' => 'Pre-Launch',
                'slug' => 'pre-launch',
                'color_code' => '#6f42c1',
                'badge_class' => 'bg-purple',
                'workflow_order' => 0,
                'is_final_state' => false,
                'is_active' => true,
                'sort_order' => 6,
            ],
            [
                'property_type_id' => null,
                'name' => 'Possession Started',
                'slug' => 'possession-started',
                'color_code' => '#20c997',
                'badge_class' => 'bg-teal',
                'workflow_order' => 3,
                'is_final_state' => false,
                'is_active' => true,
                'sort_order' => 7,
            ],
            [
                'property_type_id' => null,
                'name' => 'Completed',
                'slug' => 'completed',
                'color_code' => '#155724',
                'badge_class' => 'bg-dark-green',
                'workflow_order' => 4,
                'is_final_state' => true,
                'is_active' => true,
                'sort_order' => 8,
            ],
        ];

        foreach ($statuses as $status) {
            PropertyStatus::create($status);
        }

        $this->command->info('✅ PropertyStatus seeder completed: 8 statuses created');
    }
}
