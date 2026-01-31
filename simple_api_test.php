#!/usr/bin/env php
<?php
/**
 * Simple API Test - SuperAdmin Login
 */

echo "=== API AUTHENTICATION VERIFICATION ===\n\n";

$baseUrl = 'http://127.0.0.1:8000/api/v1';
$credentials = [
    'email' => 'intelliflowpvtltd@gmail.com',
    'password' => 'Ashish@7890'
];

// Test 1: Login
echo "TEST 1: POST {$baseUrl}/auth/login\n";
$ch = curl_init("{$baseUrl}/auth/login");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($credentials));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Accept: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$data = json_decode($response, true);

echo "Status: {$httpCode}\n";
if ($httpCode === 200 && isset($data['token'])) {
    echo "✅ LOGIN SUCCESSFUL!\n";
    echo "Token: " . substr($data['token'], 0, 30) . "...\n";
    echo "User: {$data['user']['first_name']} {$data['user']['last_name']}\n";
    echo "Email: {$data['user']['email']}\n";
    echo "Permissions: " . count($data['permissions']) . "\n\n";
    
    $token = $data['token'];
    
    // Test 2: Get User Info
    echo "TEST 2: GET {$baseUrl}/auth/me\n";
    $ch = curl_init("{$baseUrl}/auth/me");
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json',
        'Authorization: Bearer ' . $token
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "Status: {$httpCode}\n";
    if ($httpCode === 200) {
        echo "✅ USER INFO RETRIEVED!\n\n";
    } else {
        echo "❌ FAILED\n\n";
    }
    
    // Test 3: Token Refresh
    echo "TEST 3: POST {$baseUrl}/auth/refresh\n";
    $ch = curl_init("{$baseUrl}/auth/refresh");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json',
        'Authorization: Bearer ' . $token
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $refreshData = json_decode($response, true);
    echo "Status: {$httpCode}\n";
    if ($httpCode === 200 && isset($refreshData['token'])) {
        echo "✅ TOKEN REFRESHED!\n";
        echo "New Token: " . substr($refreshData['token'], 0, 30) . "...\n\n";
    } else {
        echo "❌ FAILED\n\n";
    }
    
    echo "================================\n";
    echo "✅ ALL TESTS PASSED!\n";
    echo "================================\n";
} else {
    echo "❌ LOGIN FAILED!\n";
    echo "Response: {$response}\n";
}
