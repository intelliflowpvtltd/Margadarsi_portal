<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $states = [
            'Andhra Pradesh',
            'Telangana',
            'Karnataka',
            'Tamil Nadu',
            'Maharashtra',
            'Gujarat',
            'Rajasthan',
            'Delhi',
            'Uttar Pradesh',
            'West Bengal',
        ];

        $cities = [
            'Hyderabad',
            'Bangalore',
            'Chennai',
            'Mumbai',
            'Pune',
            'Ahmedabad',
            'Jaipur',
            'Delhi',
            'Lucknow',
            'Kolkata',
        ];

        return [
            'name' => fake()->company(),
            'legal_name' => fake()->company() . ' Private Limited',
            'tagline' => fake()->catchPhrase(),
            'description' => fake()->paragraph(3),
            'logo' => null,
            'favicon' => null,

            // Registration
            'pan_number' => strtoupper(fake()->lexify('?????')) . fake()->numerify('####') . strtoupper(fake()->randomLetter()),
            'gstin' => fake()->numerify('##') . strtoupper(fake()->lexify('?????')) . fake()->numerify('####') . strtoupper(fake()->randomLetter()) . '1Z' . fake()->randomDigit(),
            'cin' => null,
            'rera_number' => null,
            'incorporation_date' => fake()->dateTimeBetween('-20 years', '-1 year'),

            // Contact
            'email' => fake()->companyEmail(),
            'phone' => fake()->numerify('9#########'),
            'alternate_phone' => fake()->optional()->numerify('9#########'),
            'whatsapp' => fake()->optional()->numerify('9#########'),
            'website' => fake()->optional()->url(),

            // Registered Address
            'registered_address_line1' => fake()->streetAddress(),
            'registered_address_line2' => fake()->optional()->secondaryAddress(),
            'registered_city' => fake()->randomElement($cities),
            'registered_state' => fake()->randomElement($states),
            'registered_pincode' => fake()->numerify('5#####'),
            'registered_country' => 'India',

            // Corporate Address
            'corporate_address_line1' => fake()->optional()->streetAddress(),
            'corporate_address_line2' => null,
            'corporate_city' => fake()->optional()->randomElement($cities),
            'corporate_state' => fake()->optional()->randomElement($states),
            'corporate_pincode' => fake()->optional()->numerify('5#####'),

            // Social Media
            'facebook_url' => fake()->optional()->url(),
            'instagram_url' => fake()->optional()->url(),
            'linkedin_url' => fake()->optional()->url(),
            'twitter_url' => fake()->optional()->url(),
            'youtube_url' => fake()->optional()->url(),

            // Status
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the company is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the company is from Hyderabad.
     */
    public function hyderabad(): static
    {
        return $this->state(fn(array $attributes) => [
            'registered_city' => 'Hyderabad',
            'registered_state' => 'Telangana',
        ]);
    }
}
