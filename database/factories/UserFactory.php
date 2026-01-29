<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password = null;

    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'role_id' => Role::factory(),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'phone' => fake()->regexify('^[6-9][0-9]{9}$'), // Indian mobile format
            'avatar' => null,
            'is_active' => true,
            'last_login_at' => null,
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the user is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn() => ['is_active' => false]);
    }

    /**
     * Indicate that the user's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn() => ['email_verified_at' => null]);
    }

    /**
     * Set user with specific role.
     */
    public function withRole(int $roleId): static
    {
        return $this->state(fn() => ['role_id' => $roleId]);
    }

    /**
     * Set user with specific company and ensure role belongs to same company.
     */
    public function forCompany(int $companyId): static
    {
        return $this->state(function () use ($companyId) {
            return [
                'company_id' => $companyId,
                'role_id' => Role::factory()->create(['company_id' => $companyId])->id,
            ];
        });
    }
}
