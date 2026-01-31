<?php

namespace Database\Factories;

use App\Models\NqReason;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\NqReason>
 */
class NqReasonFactory extends Factory
{
    protected $model = NqReason::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->randomElement([
            'Budget Mismatch',
            'Location Not Suitable',
            'Already Purchased',
            'Not Interested',
            'Wrong Number',
            'Duplicate Lead',
        ]);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'is_active' => true,
            'sort_order' => fake()->numberBetween(1, 10),
        ];
    }
}
