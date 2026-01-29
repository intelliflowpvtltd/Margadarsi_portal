<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Role>
 */
class RoleFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->jobTitle() . ' ' . fake()->randomElement(['Manager', 'Executive', 'Lead', 'Specialist']);

        return [
            'company_id' => Company::factory(),
            'name' => $name,
            'slug' => Str::slug($name) . '-' . fake()->unique()->randomNumber(4),
            'description' => fake()->sentence(),
            'hierarchy_level' => fake()->numberBetween(1, 10),
            'is_system' => false,
            'is_active' => true,
        ];
    }

    public function system(): static
    {
        return $this->state(fn() => ['is_system' => true]);
    }

    public function inactive(): static
    {
        return $this->state(fn() => ['is_active' => false]);
    }

    public function withLevel(int $level): static
    {
        return $this->state(fn() => ['hierarchy_level' => $level]);
    }
}
