<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "=== SYSTEMATIC LOGIN VERIFICATION ===" . PHP_EOL . PHP_EOL;

// Step 1: Check if user exists
echo "STEP 1: USER EXISTS CHECK" . PHP_EOL;
$user = User::where('email', 'superadmin@margadarsi.in')->first();

if (!$user) {
    echo "❌ User NOT FOUND with email: superadmin@margadarsi.in" . PHP_EOL;
    echo "Available users:" . PHP_EOL;
    User::all(['email', 'first_name', 'last_name'])->each(function($u) {
        echo "  - {$u->email} ({$u->first_name} {$u->last_name})" . PHP_EOL;
    });
    exit(1);
}

echo "✅ User EXISTS" . PHP_EOL;
echo "   ID: {$user->id}" . PHP_EOL;
echo "   Email: {$user->email}" . PHP_EOL;
echo "   Name: {$user->first_name} {$user->last_name}" . PHP_EOL;
echo PHP_EOL;

// Step 2: Check user status
echo "STEP 2: USER STATUS CHECK" . PHP_EOL;
echo "   is_active: " . ($user->is_active ? "✅ YES" : "❌ NO") . PHP_EOL;
echo "   email_verified_at: " . ($user->email_verified_at ? "✅ {$user->email_verified_at}" : "❌ NULL") . PHP_EOL;
echo "   deleted_at: " . ($user->deleted_at ? "❌ {$user->deleted_at}" : "✅ NULL (not deleted)") . PHP_EOL;
echo PHP_EOL;

// Step 3: Check password
echo "STEP 3: PASSWORD CHECK" . PHP_EOL;
echo "   Has password hash: " . (strlen($user->password) > 0 ? "✅ YES" : "❌ NO") . PHP_EOL;
echo "   Password hash length: " . strlen($user->password) . PHP_EOL;
echo "   First 10 chars: " . substr($user->password, 0, 10) . "..." . PHP_EOL;
echo PHP_EOL;

// Step 4: Test password
echo "STEP 4: PASSWORD VALIDATION TEST" . PHP_EOL;
$testPassword = 'password123';
$isValid = Hash::check($testPassword, $user->password);
echo "   Testing password: '{$testPassword}'" . PHP_EOL;
echo "   Result: " . ($isValid ? "✅ VALID" : "❌ INVALID") . PHP_EOL;
echo PHP_EOL;

// Step 5: Check role
echo "STEP 5: ROLE CHECK" . PHP_EOL;
if ($user->role) {
    echo "   Role ID: {$user->role_id}" . PHP_EOL;
    echo "   Role Name: {$user->role->name}" . PHP_EOL;
    echo "   Role Slug: {$user->role->slug}" . PHP_EOL;
} else {
    echo "   ❌ NO ROLE ASSIGNED" . PHP_EOL;
}
echo PHP_EOL;

// Step 6: Check company
echo "STEP 6: COMPANY CHECK" . PHP_EOL;
if ($user->company) {
    echo "   Company ID: {$user->company_id}" . PHP_EOL;
    echo "   Company Name: {$user->company->name}" . PHP_EOL;
} else {
    echo "   ❌ NO COMPANY ASSIGNED" . PHP_EOL;
}
echo PHP_EOL;

// Step 7: Test Auth::attempt
echo "STEP 7: AUTH::ATTEMPT TEST" . PHP_EOL;
$credentials = [
    'email' => 'superadmin@margadarsi.in',
    'password' => $testPassword,
];

$attemptResult = auth()->attempt($credentials);
echo "   Auth::attempt() result: " . ($attemptResult ? "✅ SUCCESS" : "❌ FAILED") . PHP_EOL;

if ($attemptResult) {
    echo "   Authenticated user ID: " . auth()->id() . PHP_EOL;
    auth()->logout();
}

echo PHP_EOL;
echo "=== END OF VERIFICATION ===" . PHP_EOL;
