<?php

namespace Database\Factories;

use App\Models\Lead;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lead>
 */
class LeadFactory extends Factory
{
    protected $model = Lead::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'mobile' => '9' . fake()->numerify('#########'),
            'email' => fake()->unique()->safeEmail(),
            'city' => fake()->city(),
            'state' => 'Telangana',
            'pincode' => fake()->numerify('5#####'),
            'status' => Lead::STATUS_NEW,
            'stage' => Lead::STAGE_NEW,
            'call_attempts' => 0,
            'connected_calls' => 0,
            'engagement_score' => 0,
            'sla_breached' => false,
            'is_duplicate' => false,
            'is_dormant' => false,
            'ownership_locked' => false,
            'budget_confirmed' => false,
        ];
    }

    public function contacted(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Lead::STATUS_CONTACTED,
            'stage' => Lead::STAGE_CONNECTED,
            'call_attempts' => 1,
            'connected_calls' => 1,
        ]);
    }

    public function qualified(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Lead::STATUS_QUALIFIED,
            'stage' => Lead::STAGE_QUALIFIED,
            'call_attempts' => 2,
            'connected_calls' => 2,
        ]);
    }

    public function unreachable(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Lead::STATUS_UNREACHABLE,
            'stage' => Lead::STAGE_ATTEMPTING,
            'call_attempts' => 3,
            'connected_calls' => 0,
        ]);
    }
}
