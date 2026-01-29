<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->company() . ' ' . fake()->randomElement(['Heights', 'Towers', 'Villas', 'Gardens', 'Meadows']);
        $types = ['residential', 'commercial', 'villa', 'open_plots'];
        $statuses = ['upcoming', 'ongoing', 'completed', 'sold_out'];

        $cities = ['Hyderabad', 'Bangalore', 'Chennai', 'Mumbai', 'Pune'];
        $states = ['Telangana', 'Karnataka', 'Tamil Nadu', 'Maharashtra', 'Maharashtra'];

        $cityIndex = array_rand($cities);

        return [
            'company_id' => Company::factory(),
            'name' => $name,
            'slug' => Str::slug($name) . '-' . fake()->unique()->randomNumber(4),
            'logo' => null,
            'type' => fake()->randomElement($types),
            'status' => fake()->randomElement($statuses),
            'description' => fake()->paragraph(3),
            'highlights' => fake()->randomElements([
                'Premium Location',
                'World-class Amenities',
                'Vastu Compliant',
                'Green Building',
                'Smart Home Ready',
                'Near IT Hub',
                'Close to Metro',
            ], 3),
            'rera_number' => 'P02400' . fake()->numerify('######'),
            'rera_valid_until' => fake()->dateTimeBetween('+1 year', '+5 years'),
            'address_line1' => fake()->streetAddress(),
            'address_line2' => fake()->optional()->secondaryAddress(),
            'landmark' => fake()->optional()->streetName(),
            'city' => $cities[$cityIndex],
            'state' => $states[$cityIndex],
            'pincode' => fake()->numerify('5#####'),
            'latitude' => fake()->latitude(12, 19),
            'longitude' => fake()->longitude(74, 84),
            'total_land_area' => fake()->randomFloat(2, 1, 50),
            'land_area_unit' => 'acres',
            'launch_date' => fake()->dateTimeBetween('-2 years', 'now'),
            'possession_date' => fake()->dateTimeBetween('+1 year', '+5 years'),
            'completion_percentage' => fake()->numberBetween(0, 100),
            'is_featured' => fake()->boolean(20),
            'is_active' => true,
        ];
    }

    public function residential(): static
    {
        return $this->state(fn() => ['type' => 'residential']);
    }

    public function commercial(): static
    {
        return $this->state(fn() => ['type' => 'commercial']);
    }

    public function villa(): static
    {
        return $this->state(fn() => ['type' => 'villa']);
    }

    public function openPlots(): static
    {
        return $this->state(fn() => ['type' => 'open_plots']);
    }

    public function featured(): static
    {
        return $this->state(fn() => ['is_featured' => true]);
    }
}
