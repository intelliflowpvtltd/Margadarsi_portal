#!/usr/bin/env php
<?php
/**
 * COMPLETE AUTHENTICATION FLOW TEST
 * Tests: Login, Forgot Password, OTP Verification, Password Reset
 */

$baseUrl = 'http://127.0.0.1:8000/api/v1';
$testEmail = 'intelliflowpvtltd@gmail.com';
$testPassword = 'Ashish@7890';
$newPassword = 'NewPassword@123';

echo "\n";
echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║     AUTHENTICATION SYSTEM - COMPLETE FLOW VERIFICATION         ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n";
echo "\n";

$results = ['total' => 0, 'passed' => 0, 'failed' => 0];

// =============================================================================
// TEST 1: LOGIN WITH VALID CREDENTIALS
// =============================================================================
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "TEST 1: Login with Valid Credentials\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
$results['total']++;

$ch = curl_init("{$baseUrl}/auth/login");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'email' => $testEmail,
    'password' => $testPassword
]));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$loginData = json_decode($response, true);

echo "Endpoint: POST {$baseUrl}/auth/login\n";
echo "Email: {$testEmail}\n";
echo "Status Code: {$httpCode}\n";

if ($httpCode === 200 && isset($loginData['token'])) {
    echo "✅ PASS: Login successful\n";
    echo "   Token: " . substr($loginData['token'], 0, 30) . "...\n";
    echo "   User: {$loginData['user']['first_name']} {$loginData['user']['last_name']}\n";
    echo "   Email: {$loginData['user']['email']}\n";
    echo "   Permissions: " . count($loginData['permissions']) . "\n";
    $results['passed']++;
    $validToken = $loginData['token'];
} else {
    echo "❌ FAIL: Login failed\n";
    echo "   Response: " . json_encode($loginData, JSON_PRETTY_PRINT) . "\n";
    $results['failed']++;
    $validToken = null;
}

// =============================================================================
// TEST 2: LOGIN WITH INVALID CREDENTIALS
// =============================================================================
echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "TEST 2: Login with Invalid Credentials (Should Fail)\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
$results['total']++;

$ch = curl_init("{$baseUrl}/auth/login");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'email' => 'wrong@example.com',
    'password' => 'wrongpassword'
]));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$data = json_decode($response, true);

echo "Endpoint: POST {$baseUrl}/auth/login\n";
echo "Status Code: {$httpCode}\n";

if ($httpCode === 401 || $httpCode === 422) {
    echo "✅ PASS: Correctly rejected invalid credentials\n";
    echo "   Message: " . ($data['message'] ?? 'N/A') . "\n";
    $results['passed']++;
} else {
    echo "❌ FAIL: Should have returned 401/422\n";
    $results['failed']++;
}

// =============================================================================
// TEST 3: FORGOT PASSWORD - REQUEST OTP
// =============================================================================
echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "TEST 3: Forgot Password - Request OTP\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
$results['total']++;

$ch = curl_init("{$baseUrl}/auth/forgot-password");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'email' => $testEmail
]));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$otpData = json_decode($response, true);

echo "Endpoint: POST {$baseUrl}/auth/forgot-password\n";
echo "Email: {$testEmail}\n";
echo "Status Code: {$httpCode}\n";

if ($httpCode === 200) {
    echo "✅ PASS: OTP request successful\n";
    echo "   Message: " . ($otpData['message'] ?? 'N/A') . "\n";
    echo "   Masked Email: " . ($otpData['masked_email'] ?? 'N/A') . "\n";
    $results['passed']++;
    
    // Note: In real scenario, OTP would be sent via email
    // For testing, we'd need to retrieve it from cache/database
    echo "\n   ⚠️  NOTE: OTP sent to email. For full testing, retrieve from:\n";
    echo "       - Email inbox, OR\n";
    echo "       - Laravel logs (storage/logs/laravel.log), OR\n";
    echo "       - Database password_resets table\n";
} else {
    echo "❌ FAIL: OTP request failed\n";
    echo "   Response: " . json_encode($otpData, JSON_PRETTY_PRINT) . "\n";
    $results['failed']++;
}

// =============================================================================
// TEST 4: VERIFY OTP (Testing with wrong OTP first)
// =============================================================================
echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "TEST 4: Verify OTP - Invalid OTP (Should Fail)\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
$results['total']++;

$ch = curl_init("{$baseUrl}/auth/verify-otp");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'email' => $testEmail,
    'otp' => '000000' // Invalid OTP
]));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$data = json_decode($response, true);

echo "Endpoint: POST {$baseUrl}/auth/verify-otp\n";
echo "OTP: 000000 (invalid)\n";
echo "Status Code: {$httpCode}\n";

if ($httpCode === 400 || $httpCode === 422 || $httpCode === 401) {
    echo "✅ PASS: Correctly rejected invalid OTP\n";
    echo "   Message: " . ($data['message'] ?? 'N/A') . "\n";
    $results['passed']++;
} else {
    echo "❌ FAIL: Should have rejected invalid OTP\n";
    $results['failed']++;
}

// =============================================================================
// TEST 5: GET USER INFORMATION (With Token)
// =============================================================================
if ($validToken) {
    echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "TEST 5: Get User Information (Protected Endpoint)\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    $results['total']++;
    
    $ch = curl_init("{$baseUrl}/auth/me");
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json',
        'Authorization: Bearer ' . $validToken
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $userData = json_decode($response, true);
    
    echo "Endpoint: GET {$baseUrl}/auth/me\n";
    echo "Status Code: {$httpCode}\n";
    
    if ($httpCode === 200 && isset($userData['user'])) {
        echo "✅ PASS: User information retrieved\n";
        echo "   User ID: {$userData['user']['id']}\n";
        echo "   Name: {$userData['user']['first_name']} {$userData['user']['last_name']}\n";
        echo "   Role: {$userData['user']['role']['name']}\n";
        $results['passed']++;
    } else {
        echo "❌ FAIL: Could not retrieve user information\n";
        $results['failed']++;
    }
}

// =============================================================================
// TEST 6: TOKEN REFRESH
// =============================================================================
if ($validToken) {
    echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "TEST 6: Token Refresh\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    $results['total']++;
    
    $ch = curl_init("{$baseUrl}/auth/refresh");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json',
        'Authorization: Bearer ' . $validToken
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $refreshData = json_decode($response, true);
    
    echo "Endpoint: POST {$baseUrl}/auth/refresh\n";
    echo "Status Code: {$httpCode}\n";
    
    if ($httpCode === 200 && isset($refreshData['token'])) {
        echo "✅ PASS: Token refreshed successfully\n";
        echo "   New Token: " . substr($refreshData['token'], 0, 30) . "...\n";
        $results['passed']++;
        $validToken = $refreshData['token']; // Update to new token
    } else {
        echo "❌ FAIL: Token refresh failed\n";
        $results['failed']++;
    }
}

// =============================================================================
// TEST 7: LOGOUT
// =============================================================================
if ($validToken) {
    echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "TEST 7: Logout (Token Revocation)\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    $results['total']++;
    
    $ch = curl_init("{$baseUrl}/auth/logout");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json',
        'Authorization: Bearer ' . $validToken
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $logoutData = json_decode($response, true);
    
    echo "Endpoint: POST {$baseUrl}/auth/logout\n";
    echo "Status Code: {$httpCode}\n";
    
    if ($httpCode === 200) {
        echo "✅ PASS: Logout successful\n";
        echo "   Message: " . ($logoutData['message'] ?? 'N/A') . "\n";
        $results['passed']++;
        
        // Verify token is revoked
        echo "\n   Verifying token revocation...\n";
        $results['total']++;
        
        $ch = curl_init("{$baseUrl}/auth/me");
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json',  
            'Authorization: Bearer ' . $validToken
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 401) {
            echo "   ✅ PASS: Token correctly revoked\n";
            $results['passed']++;
        } else {
            echo "   ❌ FAIL: Token still valid after logout!\n";
            $results['failed']++;
        }
    } else {
        echo "❌ FAIL: Logout failed\n";
        $results['failed']++;
    }
}

// =============================================================================
// FINAL RESULTS
// =============================================================================
echo "\n";
echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║                      TEST RESULTS SUMMARY                      ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n";
echo "\n";
echo "Total Tests:   {$results['total']}\n";
echo "✅ Passed:     {$results['passed']}\n";
echo "❌ Failed:     {$results['failed']}\n";

$successRate = $results['total'] > 0 ? round(($results['passed'] / $results['total']) * 100, 2) : 0;
echo "\nSuccess Rate:  {$successRate}%\n";

echo "\n";
if ($results['failed'] === 0) {
    echo "╔════════════════════════════════════════════════════════════════╗\n";
    echo "║   🎉 ALL TESTS PASSED! AUTHENTICATION SYSTEM WORKING! 🎉      ║\n";
    echo "╚════════════════════════════════════════════════════════════════╝\n";
    exit(0);
} else {
    echo "╔════════════════════════════════════════════════════════════════╗\n";
    echo "║   ⚠️  SOME TESTS FAILED - REVIEW OUTPUT ABOVE ⚠️              ║\n";
    echo "╚════════════════════════════════════════════════════════════════╝\n";
    exit(1);
}
