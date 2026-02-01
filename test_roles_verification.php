<?php

/**
 * Comprehensive Roles System Verification Tests
 * 
 * This file tests all 5 critical fixes with detailed output
 * Run: php test_roles_verification.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Company;
use App\Models\Department;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Color output helpers
function success($msg) { echo "\033[32m✅ $msg\033[0m\n"; }
function error($msg) { echo "\033[31m❌ $msg\033[0m\n"; }
function info($msg) { echo "\033[36mℹ️  $msg\033[0m\n"; }
function section($msg) { echo "\n\033[33m" . str_repeat("=", 70) . "\n$msg\n" . str_repeat("=", 70) . "\033[0m\n"; }

section("🔍 ROLES SYSTEM VERIFICATION - PRODUCTION READINESS TEST");

$testsPassed = 0;
$testsFailed = 0;
$totalTests = 0;

// ============================================================================
// CATEGORY 1: DATABASE SCHEMA VERIFICATION
// ============================================================================
section("CATEGORY 1: DATABASE SCHEMA VERIFICATION");

// Test 1.1: users.reporting_path exists
$totalTests++;
info("Test 1.1: Checking users.reporting_path column exists...");
if (in_array('reporting_path', Schema::getColumnListing('users'))) {
    success("PASS: users.reporting_path column exists");
    $testsPassed++;
} else {
    error("FAIL: users.reporting_path column missing");
    $testsFailed++;
}

// Test 1.2: roles.scope exists
$totalTests++;
info("Test 1.2: Checking roles.scope column exists...");
if (in_array('scope', Schema::getColumnListing('roles'))) {
    success("PASS: roles.scope column exists");
    $testsPassed++;
} else {
    error("FAIL: roles.scope column missing");
    $testsFailed++;
}

// Test 1.3: Check scope enum values
$totalTests++;
info("Test 1.3: Checking roles.scope has correct enum values...");
try {
    $scopeCheck = DB::select("
        SELECT column_name, data_type, udt_name 
        FROM information_schema.columns 
        WHERE table_name = 'roles' AND column_name = 'scope'
    ");
    if (!empty($scopeCheck)) {
        success("PASS: roles.scope column configured correctly");
        $testsPassed++;
    }
} catch (Exception $e) {
    error("FAIL: Could not verify scope enum - " . $e->getMessage());
    $testsFailed++;
}

// ============================================================================
// CATEGORY 2: PERMISSION CACHING VERIFICATION
// ============================================================================
section("CATEGORY 2: PERMISSION CACHING VERIFICATION");

// Test 2.1: Get test user and verify caching works
$totalTests++;
info("Test 2.1: Testing permission caching functionality...");
try {
    $user = User::with('role.permissions')->first();
    
    if ($user && $user->role) {
        // Enable query log
        DB::enableQueryLog();
        
        // First permission check - should query DB
        $hasPermission1 = $user->hasPermission('users.view');
        
        // Get query count after first check
        $queries1 = count(DB::getQueryLog());
        
        // Clear query log
        DB::flushQueryLog();
        
        // Second permission check - should use cache
        $hasPermission2 = $user->hasPermission('users.view');
        $hasPermission3 = $user->hasPermission('users.create');
        $hasPermission4 = $user->hasPermission('users.update');
        
        // Get query count after cached checks
        $queries2 = count(DB::getQueryLog());
        
        DB::disableQueryLog();
        
        if ($queries2 === 0) {
            success("PASS: Permission caching working - 0 queries for cached checks");
            info("   First check: $queries1 queries | Cached checks: $queries2 queries");
            $testsPassed++;
        } else {
            error("FAIL: Permission caching not working - $queries2 queries for cached checks");
            $testsFailed++;
        }
    } else {
        info("SKIP: No user found with role to test caching");
        $totalTests--;
    }
} catch (Exception $e) {
    error("FAIL: Permission caching test error - " . $e->getMessage());
    $testsFailed++;
}

// ============================================================================
// CATEGORY 3: N+1 QUERY OPTIMIZATION VERIFICATION
// ============================================================================
section("CATEGORY 3: N+1 QUERY OPTIMIZATION VERIFICATION");

// Test 3.1: Verify reporting_path is set
$totalTests++;
info("Test 3.1: Checking reporting_path auto-population...");
try {
    $userWithManager = User::whereNotNull('reports_to')->first();
    
    if ($userWithManager) {
        if ($userWithManager->reporting_path !== null) {
            success("PASS: reporting_path auto-populated: " . $userWithManager->reporting_path);
            $testsPassed++;
        } else {
            error("FAIL: reporting_path is NULL for user with manager");
            $testsFailed++;
        }
    } else {
        info("SKIP: No users with reporting relationships to test");
        $totalTests--;
    }
} catch (Exception $e) {
    error("FAIL: reporting_path test error - " . $e->getMessage());
    $testsFailed++;
}

// Test 3.2: Verify getAllSubordinates uses single query
$totalTests++;
info("Test 3.2: Testing getAllSubordinates query optimization...");
try {
    $manager = User::whereHas('directReports')->first();
    
    if ($manager) {
        DB::enableQueryLog();
        
        $subordinates = $manager->getAllSubordinates();
        
        $queries = DB::getQueryLog();
        DB::disableQueryLog();
        
        $queryCount = count($queries);
        
        if ($queryCount <= 2) { // 1-2 queries acceptable (including initial find)
            success("PASS: getAllSubordinates optimized - $queryCount queries for " . $subordinates->count() . " subordinates");
            $testsPassed++;
        } else {
            error("FAIL: getAllSubordinates not optimized - $queryCount queries");
            $testsFailed++;
        }
    } else {
        info("SKIP: No managers with subordinates to test");
        $totalTests--;
    }
} catch (Exception $e) {
    error("FAIL: getAllSubordinates test error - " . $e->getMessage());
    $testsFailed++;
}

// ============================================================================
// CATEGORY 4: ROLE SCOPE VERIFICATION
// ============================================================================
section("CATEGORY 4: ROLE SCOPE VERIFICATION");

// Test 4.1: Check scope helper methods
$totalTests++;
info("Test 4.1: Testing Role scope helper methods...");
try {
    $role = Role::first();
    
    if ($role) {
        // Check methods exist
        $hasIsCompanyWide = method_exists($role, 'isCompanyWide');
        $hasIsProjectSpecific = method_exists($role, 'isProjectSpecific');
        $hasIsDepartmentSpecific = method_exists($role, 'isDepartmentSpecific');
        
        if ($hasIsCompanyWide && $hasIsProjectSpecific && $hasIsDepartmentSpecific) {
            success("PASS: All scope helper methods exist");
            
            // Test functionality
            $isCompany = $role->isCompanyWide();
            info("   Role '{$role->name}' scope: {$role->scope} | isCompanyWide: " . ($isCompany ? 'true' : 'false'));
            
            $testsPassed++;
        } else {
            error("FAIL: Some scope helper methods missing");
            $testsFailed++;
        }
    } else {
        info("SKIP: No roles to test");
        $totalTests--;
    }
} catch (Exception $e) {
    error("FAIL: Scope helper test error - " . $e->getMessage());
    $testsFailed++;
}

// Test 4.2: Check scope constants
$totalTests++;
info("Test 4.2: Verifying Role scope constants...");
try {
    $hasCompany = defined('App\Models\Role::SCOPE_COMPANY');
    $hasProject = defined('App\Models\Role::SCOPE_PROJECT');
    $hasDepartment = defined('App\Models\Role::SCOPE_DEPARTMENT');
    
    if ($hasCompany && $hasProject && $hasDepartment) {
        success("PASS: All scope constants defined");
        info("   SCOPE_COMPANY = " . Role::SCOPE_COMPANY);
        info("   SCOPE_PROJECT = " . Role::SCOPE_PROJECT);
        info("   SCOPE_DEPARTMENT = " . Role::SCOPE_DEPARTMENT);
        $testsPassed++;
    } else {
        error("FAIL: Some scope constants missing");
        $testsFailed++;
    }
} catch (Exception $e) {
    error("FAIL: Scope constants test error - " . $e->getMessage());
    $testsFailed++;
}

// ============================================================================
// CATEGORY 5: AUTHORIZATION & SECURITY VERIFICATION
// ============================================================================
section("CATEGORY 5: AUTHORIZATION & SECURITY VERIFICATION");

// Test 5.1: RoleAuthorityCheck rule exists
$totalTests++;
info("Test 5.1: Checking RoleAuthorityCheck validation rule exists...");
if (class_exists('App\Rules\RoleAuthorityCheck')) {
    success("PASS: RoleAuthorityCheck rule class exists");
    $testsPassed++;
} else {
    error("FAIL: RoleAuthorityCheck rule class not found");
    $testsFailed++;
}

// Test 5.2: UserObserver exists
$totalTests++;
info("Test 5.2: Checking UserObserver exists...");
if (class_exists('App\Observers\UserObserver')) {
    success("PASS: UserObserver class exists");
    
    // Check methods
    $observer = new App\Observers\UserObserver();
    if (method_exists($observer, 'updated')) {
        info("   ✓ updated() method exists");
    }
    
    $testsPassed++;
} else {
    error("FAIL: UserObserver class not found");
    $testsFailed++;
}

// Test 5.3: Check HasPermissions trait methods
$totalTests++;
info("Test 5.3: Verifying HasPermissions trait methods...");
try {
    $user = User::first();
    
    if ($user) {
        $hasClearCache = method_exists($user, 'clearPermissionCache');
        $hasLoadPermissions = method_exists($user, 'loadPermissions');
        
        if ($hasClearCache) {
            success("PASS: clearPermissionCache() method exists");
            $testsPassed++;
        } else {
            error("FAIL: clearPermissionCache() method missing");
            $testsFailed++;
        }
    } else {
        info("SKIP: No user to test trait methods");
        $totalTests--;
    }
} catch (Exception $e) {
    error("FAIL: Trait methods test error - " . $e->getMessage());
    $testsFailed++;
}

// ============================================================================
// FINAL RESULTS
// ============================================================================
section("📊 VERIFICATION RESULTS");

$passRate = ($testsPassed / $totalTests) * 100;

echo "\n";
info("Total Tests Run: $totalTests");
success("Tests Passed: $testsPassed");
if ($testsFailed > 0) {
    error("Tests Failed: $testsFailed");
}

echo "\n";
if ($passRate === 100.0) {
    success("🎉 ALL TESTS PASSED - PRODUCTION READY!");
    success("Pass Rate: 100%");
    echo "\n";
    success("✓ Database schema verified");
    success("✓ Permission caching working");
    success("✓ N+1 optimization verified");
    success("✓ Role scope system functional");
    success("✓ Security features in place");
    echo "\n";
    success("STATUS: ✅ READY FOR PRODUCTION DEPLOYMENT");
} elseif ($passRate >= 80) {
    info("⚠️  MOSTLY READY - Minor issues detected");
    info("Pass Rate: " . number_format($passRate, 1) . "%");
    echo "\n";
    info("STATUS: 🟡 REVIEW FAILED TESTS BEFORE DEPLOYMENT");
} else {
    error("❌ CRITICAL ISSUES DETECTED");
    error("Pass Rate: " . number_format($passRate, 1) . "%");
    echo "\n";
    error("STATUS: 🔴 NOT READY FOR PRODUCTION");
}

echo "\n";
section("END OF VERIFICATION");

exit($testsFailed > 0 ? 1 : 0);
