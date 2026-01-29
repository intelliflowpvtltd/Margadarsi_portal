<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected Company $company;
    protected Role $role;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed permissions
        Permission::seedPermissions();

        $this->company = Company::factory()->create();
        $this->role = Role::factory()->create([
            'company_id' => $this->company->id,
            'slug' => 'admin',
        ]);

        // Assign all permissions to role
        $permissions = Permission::all();
        $this->role->permissions()->attach($permissions);

        $this->user = User::factory()->create([
            'company_id' => $this->company->id,
            'role_id' => $this->role->id,
            'email' => 'test@example.com',
            'password' => 'password123',
            'is_active' => true,
        ]);
    }

    public function test_can_login_with_valid_credentials(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'token',
                'user' => ['id', 'email', 'full_name'],
                'permissions',
            ]);

        $this->assertNotEmpty($response->json('token'));
        $this->assertNotEmpty($response->json('permissions'));
    }

    public function test_cannot_login_with_invalid_password(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
            ->assertJson(['message' => 'Invalid credentials.']);
    }

    public function test_cannot_login_with_nonexistent_email(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(401)
            ->assertJson(['message' => 'Invalid credentials.']);
    }

    public function test_cannot_login_when_inactive(): void
    {
        $this->user->update(['is_active' => false]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(403)
            ->assertJsonFragment(['message' => 'Your account has been deactivated. Please contact administrator.']);
    }

    public function test_login_updates_last_login_at(): void
    {
        $this->assertNull($this->user->last_login_at);

        $this->postJson('/api/v1/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $this->user->refresh();
        $this->assertNotNull($this->user->last_login_at);
    }

    public function test_can_get_authenticated_user(): void
    {
        $token = $this->user->createToken('test-token')->plainTextToken;

        $response = $this->getJson('/api/v1/auth/me', [
            'Authorization' => "Bearer {$token}",
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $this->user->id)
            ->assertJsonPath('data.email', $this->user->email);
    }

    public function test_cannot_access_me_without_token(): void
    {
        $response = $this->getJson('/api/v1/auth/me');

        $response->assertStatus(401);
    }

    public function test_can_logout(): void
    {
        $token = $this->user->createToken('test-token')->plainTextToken;

        $response = $this->postJson('/api/v1/auth/logout', [], [
            'Authorization' => "Bearer {$token}",
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Logged out successfully.']);

        // Token should be deleted
        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $this->user->id,
        ]);
    }

    public function test_can_request_password_reset_otp(): void
    {
        Mail::fake();

        $response = $this->postJson('/api/v1/auth/forgot-password', [
            'email' => 'test@example.com',
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['message' => 'OTP sent to your email address. Please check your inbox.']);

        Mail::assertSent(\App\Mail\OtpMail::class);

        $this->assertDatabaseHas('password_reset_tokens', [
            'email' => 'test@example.com',
        ]);
    }

    public function test_can_verify_valid_otp(): void
    {
        // Create OTP
        $otp = '123456';
        DB::table('password_reset_tokens')->insert([
            'email' => 'test@example.com',
            'token' => Hash::make($otp),
            'otp' => $otp,
            'expires_at' => now()->addMinutes(10),
            'created_at' => now(),
        ]);

        $response = $this->postJson('/api/v1/auth/verify-otp', [
            'email' => 'test@example.com',
            'otp' => $otp,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['message', 'reset_token']);
    }

    public function test_cannot_verify_expired_otp(): void
    {
        $otp = '123456';
        DB::table('password_reset_tokens')->insert([
            'email' => 'test@example.com',
            'token' => Hash::make($otp),
            'otp' => $otp,
            'expires_at' => now()->subMinutes(1), // Expired
            'created_at' => now()->subMinutes(15),
        ]);

        $response = $this->postJson('/api/v1/auth/verify-otp', [
            'email' => 'test@example.com',
            'otp' => $otp,
        ]);

        $response->assertStatus(422)
            ->assertJsonFragment(['message' => 'OTP has expired. Please request a new one.']);
    }

    public function test_cannot_verify_invalid_otp(): void
    {
        DB::table('password_reset_tokens')->insert([
            'email' => 'test@example.com',
            'token' => Hash::make('123456'),
            'otp' => '123456',
            'expires_at' => now()->addMinutes(10),
            'created_at' => now(),
        ]);

        $response = $this->postJson('/api/v1/auth/verify-otp', [
            'email' => 'test@example.com',
            'otp' => '999999', // Wrong OTP
        ]);

        $response->assertStatus(422)
            ->assertJsonFragment(['message' => 'Invalid OTP. Please check and try again.']);
    }

    public function test_can_reset_password_with_valid_token(): void
    {
        $resetToken = 'valid-reset-token';
        DB::table('password_reset_tokens')->insert([
            'email' => 'test@example.com',
            'token' => Hash::make($resetToken),
            'otp' => null,
            'expires_at' => now()->addHour(),
            'created_at' => now(),
        ]);

        $response = $this->postJson('/api/v1/auth/reset-password', [
            'reset_token' => $resetToken,
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['message' => 'Password reset successfully. You can now login with your new password.']);

        // Verify password changed
        $this->user->refresh();
        $this->assertTrue(Hash::check('newpassword123', $this->user->password));

        // Token should be deleted
        $this->assertDatabaseMissing('password_reset_tokens', [
            'email' => 'test@example.com',
        ]);
    }

    public function test_protected_routes_require_authentication(): void
    {
        $response = $this->getJson('/api/v1/companies');

        $response->assertStatus(401)
            ->assertJson(['message' => 'Unauthenticated.']);
    }

    public function test_protected_routes_check_permissions(): void
    {
        // Create user with no permissions
        $userWithoutPermissions = User::factory()->create([
            'company_id' => $this->company->id,
            'role_id' => Role::factory()->create([
                'company_id' => $this->company->id,
                'slug' => 'basic-user',
            ])->id,
        ]);

        $token = $userWithoutPermissions->createToken('test-token')->plainTextToken;

        $response = $this->getJson('/api/v1/companies', [
            'Authorization' => "Bearer {$token}",
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'message' => 'Unauthorized.',
                'required_permission' => 'companies.view',
            ]);
    }
}
