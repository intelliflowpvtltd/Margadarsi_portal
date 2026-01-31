<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "=== SUPERADMIN VERIFICATION ===\n\n";

$email = 'intelliflowpvtltd@gmail.com';
$password = 'Ashish@7890';

$user = User::where('email', $email)->first();

if (!$user) {
    echo "❌ SuperAdmin NOT FOUND with email: {$email}\n";
    
    // Check if old email exists
    $oldUser = User::where('email', 'superadmin@margadarsi.com')->first();
    if ($oldUser) {
        echo "⚠️  Found user with OLD email: superadmin@margadarsi.com\n";
    }
    exit(1);
}

echo "✅ User Found!\n";
echo "   ID: {$user->id}\n";
echo "   Name: {$user->first_name} {$user->last_name}\n";
echo "   Email: {$user->email}\n";
echo "   Role ID: {$user->role_id}\n";
echo "   Active: " . ($user->is_active ? 'Yes' : 'No') . "\n";

// Get role name
$role = $user->role;
echo "   Role Name: " . ($role ? $role->name . " ({$role->slug})" : 'N/A') . "\n\n";

// Test password
echo "Testing password verification...\n";
if (Hash::check($password, $user->password)) {
    echo "✅ Password CORRECT!\n\n";
} else {
    echo "❌ Password INCORRECT!\n";
    echo "   Expected: {$password}\n";
    echo "   Hash in DB: {$user->password}\n\n";
}

// Test login capability
$authService = new \App\Services\AuthService();
$canLogin = $authService->canUserLogin($user);

echo "Login Eligibility:\n";
echo "   Allowed: " . ($canLogin['allowed'] ? 'Yes' : 'No') . "\n";
if (!$canLogin['allowed']) {
    echo "   Reason: {$canLogin['reason']}\n";
}

echo "\n=== SUPERADMIN CREDENTIALS ===\n";
echo "Email: {$email}\n";
echo "Password: {$password}\n";
echo "================================\n";
