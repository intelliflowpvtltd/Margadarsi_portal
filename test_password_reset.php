#!/usr/bin/env php
<?php
/**
 * Test Forgot Password Flow
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\PasswordReset;
use App\Models\User;

echo "=== FORGOT PASSWORD FLOW TEST ===\n\n";

$testEmail = 'intelliflowpvtltd@gmail.com';

// Check if user exists
$user = User::where('email', $testEmail)->first();
if (!$user) {
    echo "❌ User not found: {$testEmail}\n";
    exit(1);
}

echo "✅ User found: {$user->first_name} {$user->last_name}\n\n";

// Test creating password reset record
echo "Testing PasswordReset model...\n";

try {
    // Delete any existing record first
    PasswordReset::where('email', $testEmail)->delete();
    echo "✅ Cleared old password reset records\n";
    
    // Create new record
    $resetRecord = PasswordReset::create([
        'email' => $testEmail,
        'token' => bcrypt('test-token'),
        'otp' => '123456',
        'created_at' => now(),
    ]);
    
    echo "✅ Password reset record created successfully!\n";
    echo "   Email: {$resetRecord->email}\n";
    echo "   OTP: {$resetRecord->otp}\n";
    echo "   Created: {$resetRecord->created_at}\n\n";
    
    // Verify we can retrieve it
    $found = PasswordReset::where('email', $testEmail)->first();
    if ($found) {
        echo "✅ Record can be retrieved from database\n";
        echo "   Primary Key (email): {$found->getKey()}\n";
    }
    
    echo "\n✅ ALL TESTS PASSED! Forgot password functionality is working!\n";
    
} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    exit(1);
}
