<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

echo "=== LOGIN SIMULATION TEST ===" . PHP_EOL . PHP_EOL;

$testCases = [
    ['email' => 'superadmin@margadarsi.in', 'password' => 'password123'],
    ['email' => 'SUPERADMIN@MARGADARSI.IN', 'password' => 'password123'],
    ['email' => ' superadmin@margadarsi.in ', 'password' => 'password123'],
    ['email' => 'superadmin@margadarsi.in', 'password' => 'Password123'],
    ['email' => 'superadmin@margadarsi.in', 'password' => ' password123'],
    ['email' => 'superadmin@margadarsi.in', 'password' => 'password123 '],
];

foreach ($testCases as $index => $credentials) {
    echo "Test Case " . ($index + 1) . ":" . PHP_EOL;
    echo "  Email: '{$credentials['email']}'" . PHP_EOL;
    echo "  Password: '{$credentials['password']}'" . PHP_EOL;
    
    // Simulate form processing (trim + lowercase)
    $processedEmail = trim(strtolower($credentials['email']));
    $processedPassword = $credentials['password'];
    
    echo "  Processed Email: '{$processedEmail}'" . PHP_EOL;
    
    // Try auth
    $result = Auth::attempt([
        'email' => $processedEmail,
        'password' => $processedPassword
    ]);
    
    echo "  Result: " . ($result ? "✅ SUCCESS" : "❌ FAILED") . PHP_EOL;
    
    if ($result) {
        Auth::logout();
    }
    
    echo PHP_EOL;
}

// Check actual database email format
echo "=== DATABASE EMAIL CHECK ===" . PHP_EOL;
$user = User::first();
echo "Stored email: '{$user->email}'" . PHP_EOL;
echo "Has spaces: " . (preg_match('/\s/', $user->email) ? "YES" : "NO") . PHP_EOL;
echo "Email length: " . strlen($user->email) . PHP_EOL;
echo "First char ASCII: " . ord($user->email[0]) . PHP_EOL;
echo "Last char ASCII: " . ord($user->email[strlen($user->email)-1]) . PHP_EOL;
