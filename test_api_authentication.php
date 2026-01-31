#!/usr/bin/env php
<?php
/**
 * API Authentication Endpoint Test Suite
 * Tests all authentication-related API endpoints systematically
 */

define('BASE_URL', 'http://127.0.0.1:8000/api/v1');


// Test credentials
$superadmin = [
    'email' => 'intelliflowpvtltd@gmail.com',
    'password' => 'Ashish@7890'
];

$invalidCredentials = [
    'email' => 'invalid@example.com',
    'password' => 'wrongpassword'
];

// Color codes for terminal output
$colors = [
    'green' => "\033[0;32m",
    'red' => "\033[0;31m",
    'yellow' => "\033[1;33m",
    'blue' => "\033[0;34m",
    'reset' => "\033[0m"
];

function printHeader($text) {
    global $colors;
    echo "\n" . $colors['blue'] . str_repeat('=', 80) . $colors['reset'] . "\n";
    echo $colors['blue'] . $text . $colors['reset'] . "\n";
    echo $colors['blue'] . str_repeat('=', 80) . $colors['reset'] . "\n\n";
}

function printTest($name) {
    global $colors;
    echo $colors['yellow'] . "🧪 TEST: " . $name . $colors['reset'] . "\n";
}

function printSuccess($message) {
    global $colors;
    echo $colors['green'] . "✅ PASS: " . $message . $colors['reset'] . "\n";
}

function printError($message) {
    global $colors;
    echo $colors['red'] . "❌ FAIL: " . $message . $colors['reset'] . "\n";
}

function apiRequest($method, $endpoint, $data = null, $token = null) {
    global $colors;
    
    $url = BASE_URL . $endpoint;
    $ch = curl_init($url);
    
    $headers = [
        'Content-Type: application/json',
        'Accept: application/json',
    ];
    
    if ($token) {
        $headers[] = 'Authorization: Bearer ' . $token;
    }
    
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    
    $headers = substr($response, 0, $headerSize);
    $body = substr($response, $headerSize);
    
    curl_close($ch);
    
    return [
        'code' => $httpCode,
        'headers' => $headers,
        'body' => json_decode($body, true),
        'raw_body' => $body
    ];
}

// =============================================================================
// TEST SUITE
// =============================================================================

$testResults = [
    'total' => 0,
    'passed' => 0,
    'failed' => 0
];

printHeader("API AUTHENTICATION ENDPOINT TEST SUITE");

// -----------------------------------------------------------------------------
// TEST 1: POST /api/login - Valid Credentials
// -----------------------------------------------------------------------------
printTest("1. POST /api/login - Valid Credentials (SuperAdmin)");
$testResults['total']++;

$response = apiRequest('POST', '/auth/login', $superadmin);

if ($response['code'] === 200 && isset($response['body']['token'])) {
    printSuccess("Login successful with status 200");
    printSuccess("Token received: " . substr($response['body']['token'], 0, 20) . "...");
    printSuccess("Token type: " . ($response['body']['token_type'] ?? 'N/A'));
    printSuccess("User: " . $response['body']['user']['first_name'] . " " . $response['body']['user']['last_name']);
    printSuccess("Email: " . $response['body']['user']['email']);
    printSuccess("Permissions count: " . count($response['body']['permissions'] ?? []));
    $testResults['passed']++;
    
    // Store token for subsequent tests
    $validToken = $response['body']['token'];
    $userId = $response['body']['user']['id'];
} else {
    printError("Login failed. Status: " . $response['code']);
    printError("Response: " . json_encode($response['body'], JSON_PRETTY_PRINT));
    $testResults['failed']++;
    $validToken = null;
}

// -----------------------------------------------------------------------------
// TEST 2: POST /api/login - Invalid Credentials
// -----------------------------------------------------------------------------
printTest("2. POST /api/login - Invalid Credentials");
$testResults['total']++;

$response = apiRequest('POST', '/login', $invalidCredentials);

if ($response['code'] === 401 && !isset($response['body']['token'])) {
    printSuccess("Correctly rejected invalid credentials with 401");
    printSuccess("Error message: " . ($response['body']['message'] ?? 'N/A'));
    $testResults['passed']++;
} else {
    printError("Should have returned 401. Got: " . $response['code']);
    printError("Response: " . json_encode($response['body'], JSON_PRETTY_PRINT));
    $testResults['failed']++;
}

// -----------------------------------------------------------------------------
// TEST 3: POST /api/login - Missing Email
// -----------------------------------------------------------------------------
printTest("3. POST /api/login - Missing Email (Validation)");
$testResults['total']++;

$response = apiRequest('POST', '/login', ['password' => 'test123']);

if ($response['code'] === 422) {
    printSuccess("Correctly validated missing email with 422");
    printSuccess("Validation errors: " . json_encode($response['body']['errors'] ?? []));
    $testResults['passed']++;
} else {
    printError("Should have returned 422. Got: " . $response['code']);
    $testResults['failed']++;
}

// -----------------------------------------------------------------------------
// TEST 4: POST /api/login - Missing Password
// -----------------------------------------------------------------------------
printTest("4. POST /api/login - Missing Password (Validation)");
$testResults['total']++;

$response = apiRequest('POST', '/login', ['email' => 'test@example.com']);

if ($response['code'] === 422) {
    printSuccess("Correctly validated missing password with 422");
    $testResults['passed']++;
} else {
    printError("Should have returned 422. Got: " . $response['code']);
    $testResults['failed']++;
}

// -----------------------------------------------------------------------------
// TEST 5: GET /api/me - With Valid Token
// -----------------------------------------------------------------------------
if ($validToken) {
    printTest("5. GET /api/me - With Valid Token");
    $testResults['total']++;
    
    $response = apiRequest('GET', '/me', null, $validToken);
    
    if ($response['code'] === 200 && isset($response['body']['user'])) {
        printSuccess("User retrieval successful");
        printSuccess("User ID: " . $response['body']['user']['id']);
        printSuccess("Email: " . $response['body']['user']['email']);
        $testResults['passed']++;
    } else {
        printError("Failed to retrieve user. Status: " . $response['code']);
        $testResults['failed']++;
    }
}

// -----------------------------------------------------------------------------
// TEST 6: GET /api/me - Without Token
// -----------------------------------------------------------------------------
printTest("6. GET /api/me - Without Token (Should Fail)");
$testResults['total']++;

$response = apiRequest('GET', '/me');

if ($response['code'] === 401) {
    printSuccess("Correctly rejected request without token (401)");
    $testResults['passed']++;
} else {
    printError("Should have returned 401. Got: " . $response['code']);
    $testResults['failed']++;
}

// -----------------------------------------------------------------------------
// TEST 7: POST /api/refresh - Token Refresh
// -----------------------------------------------------------------------------
if ($validToken) {
    printTest("7. POST /api/refresh - Token Refresh");
    $testResults['total']++;
    
    $response = apiRequest('POST', '/refresh', null, $validToken);
    
    if ($response['code'] === 200 && isset($response['body']['token'])) {
        printSuccess("Token refresh successful");
        printSuccess("New token received: " . substr($response['body']['token'], 0, 20) . "...");
        $testResults['passed']++;
        
        // Update token
        $validToken = $response['body']['token'];
    } else {
        printError("Token refresh failed. Status: " . $response['code']);
        $testResults['failed']++;
    }
}

// -----------------------------------------------------------------------------
// TEST 8: POST /api/logout - Single Device Logout
// -----------------------------------------------------------------------------
if ($validToken) {
    printTest("8. POST /api/logout - Single Device Logout");
    $testResults['total']++;
    
    $response = apiRequest('POST', '/logout', null, $validToken);
    
    if ($response['code'] === 200) {
        printSuccess("Logout successful");
        printSuccess("Message: " . ($response['body']['message'] ?? 'N/A'));
        $testResults['passed']++;
        
        // Token should now be invalid
        printTest("8a. Verify token is revoked");
        $testResults['total']++;
        $verifyResponse = apiRequest('GET', '/me', null, $validToken);
        
        if ($verifyResponse['code'] === 401) {
            printSuccess("Token correctly revoked after logout");
            $testResults['passed']++;
        } else {
            printError("Token still valid after logout!");
            $testResults['failed']++;
        }
        
        // Get new token for remaining tests
        $loginResponse = apiRequest('POST', '/login', $superadmin);
        $validToken = $loginResponse['body']['token'] ?? null;
    } else {
        printError("Logout failed. Status: " . $response['code']);
        $testResults['failed']++;
    }
}

// -----------------------------------------------------------------------------
// TEST 9: POST /api/logout-all - All Devices Logout
// -----------------------------------------------------------------------------
if ($validToken) {
    printTest("9. POST /api/logout-all - All Devices Logout");
    $testResults['total']++;
    
    $response = apiRequest('POST', '/logout-all', null, $validToken);
    
    if ($response['code'] === 200) {
        printSuccess("Logout from all devices successful");
        printSuccess("Tokens revoked: " . ($response['body']['tokens_revoked'] ?? 'N/A'));
        $testResults['passed']++;
    } else {
        printError("Logout all failed. Status: " . $response['code']);
        $testResults['failed']++;
    }
}

// =============================================================================
// TEST RESULTS SUMMARY
// =============================================================================

printHeader("TEST RESULTS SUMMARY");

echo "Total Tests: " . $testResults['total'] . "\n";
echo $colors['green'] . "Passed: " . $testResults['passed'] . $colors['reset'] . "\n";
echo $colors['red'] . "Failed: " . $testResults['failed'] . $colors['reset'] . "\n";

$successRate = round(($testResults['passed'] / $testResults['total']) * 100, 2);
echo "\nSuccess Rate: " . $successRate . "%\n";

if ($testResults['failed'] === 0) {
    echo $colors['green'] . "\n🎉 ALL TESTS PASSED! API is working perfectly!\n" . $colors['reset'];
    exit(0);
} else {
    echo $colors['red'] . "\n⚠️  Some tests failed. Please review the output above.\n" . $colors['reset'];
    exit(1);
}
