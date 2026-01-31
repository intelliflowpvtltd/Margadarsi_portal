<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Company;
use App\Models\Project;
use App\Models\Role;

echo "\n" . str_repeat("=", 70) . "\n";
echo "   MARGADARSI PORTAL - API ENDPOINT TESTING\n";
echo str_repeat("=", 70) . "\n\n";

$baseUrl = 'http://127.0.0.1:8000/api/v1';
$results = [];

// Helper function to make API requests
function apiRequest($method, $url, $data = null, $token = null) {
    $ch = curl_init();
    
    $headers = [
        'Accept: application/json',
        'Content-Type: application/json',
    ];
    
    if ($token) {
        $headers[] = "Authorization: Bearer {$token}";
    }
    
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_TIMEOUT => 30,
    ]);
    
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
    } elseif ($method === 'PUT') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
    } elseif ($method === 'DELETE') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    return [
        'code' => $httpCode,
        'body' => json_decode($response, true),
        'error' => $error,
    ];
}

function printResult($name, $result, $expectedCode = 200) {
    $status = $result['code'] == $expectedCode ? '✅' : '❌';
    $codeStr = str_pad($result['code'], 3, ' ', STR_PAD_LEFT);
    echo "{$status} [{$codeStr}] {$name}\n";
    
    if ($result['code'] != $expectedCode && isset($result['body']['message'])) {
        echo "   └─ Error: {$result['body']['message']}\n";
    }
    
    return $result['code'] == $expectedCode;
}

// ==================== 1. AUTHENTICATION ====================
echo "📌 1. AUTHENTICATION ENDPOINTS\n";
echo str_repeat("-", 50) . "\n";

// Login
$loginResult = apiRequest('POST', "{$baseUrl}/auth/login", [
    'email' => 'superadmin@margadarsi.com',
    'password' => 'password123',
]);
printResult('POST /auth/login', $loginResult, 200);
$token = $loginResult['body']['token'] ?? null;

if (!$token) {
    echo "❌ Cannot proceed without token!\n";
    exit(1);
}

echo "   └─ Token: " . substr($token, 0, 30) . "...\n";

// Get current user
$meResult = apiRequest('GET', "{$baseUrl}/auth/me", null, $token);
printResult('GET /auth/me', $meResult, 200);
if ($meResult['code'] == 200) {
    echo "   └─ User: {$meResult['body']['data']['full_name']} ({$meResult['body']['data']['email']})\n";
}

echo "\n";

// ==================== 2. COMPANIES ====================
echo "📌 2. COMPANY ENDPOINTS\n";
echo str_repeat("-", 50) . "\n";

// List companies
$companiesResult = apiRequest('GET', "{$baseUrl}/companies", null, $token);
printResult('GET /companies', $companiesResult, 200);
$totalCompanies = $companiesResult['body']['meta']['total'] ?? 0;
echo "   └─ Total companies: {$totalCompanies}\n";

// Create company
$newCompany = [
    'name' => 'Test Company ' . time(),
    'legal_name' => 'Test Company Pvt Ltd',
    'email' => 'test' . time() . '@example.com',
    'phone' => '9876543210',
];
$createCompanyResult = apiRequest('POST', "{$baseUrl}/companies", $newCompany, $token);
printResult('POST /companies', $createCompanyResult, 201);
$companyId = $createCompanyResult['body']['data']['id'] ?? null;

// Show company
if ($companyId) {
    $showCompanyResult = apiRequest('GET', "{$baseUrl}/companies/{$companyId}", null, $token);
    printResult("GET /companies/{$companyId}", $showCompanyResult, 200);
    
    // Update company
    $updateCompanyResult = apiRequest('PUT', "{$baseUrl}/companies/{$companyId}", [
        'name' => 'Updated Company ' . time(),
        'legal_name' => 'Updated Company Pvt Ltd',
        'email' => 'updated' . time() . '@example.com',
        'phone' => '9876543211',
    ], $token);
    printResult("PUT /companies/{$companyId}", $updateCompanyResult, 200);
    
    // Delete company
    $deleteCompanyResult = apiRequest('DELETE', "{$baseUrl}/companies/{$companyId}", null, $token);
    printResult("DELETE /companies/{$companyId}", $deleteCompanyResult, 200);
    
    // Restore company
    $restoreCompanyResult = apiRequest('POST', "{$baseUrl}/companies/{$companyId}/restore", null, $token);
    printResult("POST /companies/{$companyId}/restore", $restoreCompanyResult, 200);
}

echo "\n";

// ==================== 3. PROJECTS ====================
echo "📌 3. PROJECT ENDPOINTS\n";
echo str_repeat("-", 50) . "\n";

// Get first company for project creation
$company = Company::first();

// List projects
$projectsResult = apiRequest('GET', "{$baseUrl}/projects", null, $token);
printResult('GET /projects', $projectsResult, 200);
$totalProjects = $projectsResult['body']['meta']['total'] ?? 0;
echo "   └─ Total projects: {$totalProjects}\n";

// Create project
$newProject = [
    'company_id' => $company->id,
    'name' => 'Test Project ' . time(),
    'type' => 'residential',
    'status' => 'upcoming',
    'city' => 'Hyderabad',
    'state' => 'Telangana',
    'pincode' => '500001',
];
$createProjectResult = apiRequest('POST', "{$baseUrl}/projects", $newProject, $token);
printResult('POST /projects', $createProjectResult, 201);
$projectId = $createProjectResult['body']['data']['id'] ?? null;

// Show project
if ($projectId) {
    $showProjectResult = apiRequest('GET', "{$baseUrl}/projects/{$projectId}", null, $token);
    printResult("GET /projects/{$projectId}", $showProjectResult, 200);
    
    // Update project
    $updateProjectResult = apiRequest('PUT', "{$baseUrl}/projects/{$projectId}", [
        'company_id' => $company->id,
        'name' => 'Updated Project ' . time(),
        'type' => 'commercial',
        'status' => 'ongoing',
        'city' => 'Hyderabad',
        'state' => 'Telangana',
        'pincode' => '500002',
    ], $token);
    printResult("PUT /projects/{$projectId}", $updateProjectResult, 200);
    
    // Update specification
    $specResult = apiRequest('PUT', "{$baseUrl}/projects/{$projectId}/specification", [
        'total_floors' => 10,
        'total_units' => 50,
    ], $token);
    printResult("PUT /projects/{$projectId}/specification", $specResult, 200);
    
    // Delete project
    $deleteProjectResult = apiRequest('DELETE', "{$baseUrl}/projects/{$projectId}", null, $token);
    printResult("DELETE /projects/{$projectId}", $deleteProjectResult, 200);
    
    // Restore project
    $restoreProjectResult = apiRequest('POST', "{$baseUrl}/projects/{$projectId}/restore", null, $token);
    printResult("POST /projects/{$projectId}/restore", $restoreProjectResult, 200);
}

echo "\n";

// ==================== 4. ROLES ====================
echo "📌 4. ROLE ENDPOINTS\n";
echo str_repeat("-", 50) . "\n";

// List roles
$rolesResult = apiRequest('GET', "{$baseUrl}/roles?company_id={$company->id}", null, $token);
printResult('GET /roles', $rolesResult, 200);
$totalRoles = $rolesResult['body']['meta']['total'] ?? 0;
echo "   └─ Total roles: {$totalRoles}\n";

// Get system roles config
$systemRolesResult = apiRequest('GET', "{$baseUrl}/roles-config/system", null, $token);
printResult('GET /roles-config/system', $systemRolesResult, 200);

// Create role
$newRole = [
    'company_id' => $company->id,
    'name' => 'Test Role ' . time(),
    'slug' => 'test-role-' . time(),
    'description' => 'Test role description',
    'hierarchy_level' => 8,
];
$createRoleResult = apiRequest('POST', "{$baseUrl}/roles", $newRole, $token);
printResult('POST /roles', $createRoleResult, 201);
$roleId = $createRoleResult['body']['data']['id'] ?? null;

// Show role
if ($roleId) {
    $showRoleResult = apiRequest('GET', "{$baseUrl}/roles/{$roleId}", null, $token);
    printResult("GET /roles/{$roleId}", $showRoleResult, 200);
    
    // Update role
    $updateRoleResult = apiRequest('PUT', "{$baseUrl}/roles/{$roleId}", [
        'name' => 'Updated Role ' . time(),
        'description' => 'Updated description',
        'hierarchy_level' => 9,
    ], $token);
    printResult("PUT /roles/{$roleId}", $updateRoleResult, 200);
    
    // Delete role
    $deleteRoleResult = apiRequest('DELETE', "{$baseUrl}/roles/{$roleId}", null, $token);
    printResult("DELETE /roles/{$roleId}", $deleteRoleResult, 200);
    
    // Restore role
    $restoreRoleResult = apiRequest('POST', "{$baseUrl}/roles/{$roleId}/restore", null, $token);
    printResult("POST /roles/{$roleId}/restore", $restoreRoleResult, 200);
}

echo "\n";

// ==================== 5. USERS ====================
echo "📌 5. USER ENDPOINTS\n";
echo str_repeat("-", 50) . "\n";

// Get a role for user creation
$role = Role::where('company_id', $company->id)->first();

// List users
$usersResult = apiRequest('GET', "{$baseUrl}/users?company_id={$company->id}", null, $token);
printResult('GET /users', $usersResult, 200);
$totalUsers = $usersResult['body']['meta']['total'] ?? 0;
echo "   └─ Total users: {$totalUsers}\n";

// Create user
$newUser = [
    'company_id' => $company->id,
    'role_id' => $role->id,
    'first_name' => 'Test',
    'last_name' => 'User',
    'email' => 'testuser' . time() . '@example.com',
    'password' => 'Password123!',
    'password_confirmation' => 'Password123!',
    'phone' => '9876543212',
    'is_active' => true,
];
$createUserResult = apiRequest('POST', "{$baseUrl}/users", $newUser, $token);
printResult('POST /users', $createUserResult, 201);
$userId = $createUserResult['body']['data']['id'] ?? null;

// Show user
if ($userId) {
    $showUserResult = apiRequest('GET', "{$baseUrl}/users/{$userId}", null, $token);
    printResult("GET /users/{$userId}", $showUserResult, 200);
    
    // Update user
    $updateUserResult = apiRequest('PUT', "{$baseUrl}/users/{$userId}", [
        'first_name' => 'Updated',
        'last_name' => 'User',
    ], $token);
    printResult("PUT /users/{$userId}", $updateUserResult, 200);
    
    // Get user projects
    $userProjectsResult = apiRequest('GET', "{$baseUrl}/users/{$userId}/projects", null, $token);
    printResult("GET /users/{$userId}/projects", $userProjectsResult, 200);
    
    // Assign projects to user
    $project = Project::where('company_id', $company->id)->first();
    if ($project) {
        $assignResult = apiRequest('POST', "{$baseUrl}/users/{$userId}/projects", [
            'project_ids' => [$project->id],
        ], $token);
        printResult("POST /users/{$userId}/projects", $assignResult, 200);
        
        // Remove project from user
        $removeResult = apiRequest('DELETE', "{$baseUrl}/users/{$userId}/projects/{$project->id}", null, $token);
        printResult("DELETE /users/{$userId}/projects/{$project->id}", $removeResult, 200);
    }
    
    // Delete user
    $deleteUserResult = apiRequest('DELETE', "{$baseUrl}/users/{$userId}", null, $token);
    printResult("DELETE /users/{$userId}", $deleteUserResult, 200);
    
    // Restore user
    $restoreUserResult = apiRequest('POST', "{$baseUrl}/users/{$userId}/restore", null, $token);
    printResult("POST /users/{$userId}/restore", $restoreUserResult, 200);
}

echo "\n";

// ==================== 6. LOGOUT ====================
echo "📌 6. LOGOUT\n";
echo str_repeat("-", 50) . "\n";

$logoutResult = apiRequest('POST', "{$baseUrl}/auth/logout", null, $token);
printResult('POST /auth/logout', $logoutResult, 200);

echo "\n" . str_repeat("=", 70) . "\n";
echo "   API ENDPOINT TESTING COMPLETE\n";
echo str_repeat("=", 70) . "\n\n";
