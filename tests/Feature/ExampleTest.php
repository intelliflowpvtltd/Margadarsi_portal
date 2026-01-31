<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example - verify API base endpoint is accessible.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        // Test the API login endpoint (which should return 422 without data)
        $response = $this->postJson('/api/v1/auth/login', []);

        // Should return 422 for validation errors (not 500)
        $response->assertStatus(422);
    }
}
