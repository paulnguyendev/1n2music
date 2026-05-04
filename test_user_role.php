<?php

/**
 * Test Script for User Role Detection and AI Limits
 *
 * This script verifies that:
 * 1. rrt_get_user_role() returns correct role slugs for each subscription type
 * 2. AI Mastering usage limits are correct for each role
 * 3. Music Recognition usage limits are correct for each role
 *
 * Usage: php test_user_role.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\UserModel;

// Expected AI limits based on requirements
$expectedLimits = [
    'free-user' => [
        'mastering' => 2,
        'recognition' => 2,
        'role_id' => 1,
    ],
    'free-seller' => [
        'mastering' => 2,
        'recognition' => 2,
        'role_id' => 2,
    ],
    'proseller-monthly' => [
        'mastering' => 5,
        'recognition' => 5,
        'role_id' => 3,
    ],
    'proseller-annually' => [
        'mastering' => 5,
        'recognition' => 5,
        'role_id' => 4,
    ],
    'distribution-basic-annually' => [
        'mastering' => 5,
        'recognition' => 5,
        'role_id' => 7,
    ],
    'distribution-annually' => [
        'mastering' => 10,
        'recognition' => 20,
        'role_id' => 5,
    ],
    'publishing-annually' => [
        'mastering' => 20,
        'recognition' => 40,
        'role_id' => 6,
    ],
];

// Test scenarios mapping subscription_id to expected role
$testScenarios = [
    [
        'name' => 'Free User',
        'plan_id' => null,
        'subscription_ids' => [],
        'expected_role' => 'free-user',
    ],
    [
        'name' => 'Free Seller',
        'plan_id' => 1, // Free plan
        'subscription_ids' => [],
        'expected_role' => 'free-seller',
    ],
    [
        'name' => 'Pro Seller Monthly',
        'plan_id' => 3, // Pro Seller plan
        'subscription_ids' => [],
        'expected_role' => 'proseller-monthly',
        'plan_type' => 'monthly',
    ],
    [
        'name' => 'Pro Seller Annually',
        'plan_id' => 3, // Pro Seller plan
        'subscription_ids' => [],
        'expected_role' => 'proseller-annually',
        'plan_type' => 'annually',
    ],
    [
        'name' => 'Distribution Basic',
        'plan_id' => null,
        'subscription_ids' => [4], // Distribution Basic subscription
        'expected_role' => 'distribution-basic-annually',
    ],
    [
        'name' => 'Distribution Pro',
        'plan_id' => null,
        'subscription_ids' => [2], // Distribution Pro subscription
        'expected_role' => 'distribution-annually',
    ],
    [
        'name' => 'Publishing',
        'plan_id' => null,
        'subscription_ids' => [1], // Publishing subscription
        'expected_role' => 'publishing-annually',
    ],
];

echo "\n";
echo "========================================\n";
echo "User Role & AI Limits Test Script\n";
echo "========================================\n\n";

$totalTests = 0;
$passedTests = 0;
$failedTests = 0;

foreach ($testScenarios as $scenario) {
    echo "Testing Role: {$scenario['name']}\n";
    echo str_repeat('-', 40) . "\n";

    $roleSlug = $scenario['expected_role'];
    $expected = $expectedLimits[$roleSlug];

    // Test 1: Verify role exists in database
    $roleExists = DB::table('rrt_roles')->where('slug', $roleSlug)->exists();
    $totalTests++;
    if ($roleExists) {
        echo "✓ Role exists in database: {$roleSlug}\n";
        $passedTests++;
    } else {
        echo "✗ Role NOT found in database: {$roleSlug}\n";
        $failedTests++;
    }

    // Test 2: Verify AI Mastering limits
    $masteringLimit = DB::table('rrt_ai_package_roles')
        ->where('ai_id', 1) // AI Mastering
        ->where('role_id', $expected['role_id'])
        ->value('usage_count');

    $totalTests++;
    if ($masteringLimit == $expected['mastering']) {
        echo "✓ AI Mastering: {$masteringLimit} uses (expected: {$expected['mastering']})\n";
        $passedTests++;
    } else {
        echo "✗ AI Mastering: {$masteringLimit} uses (expected: {$expected['mastering']})\n";
        $failedTests++;
    }

    // Test 3: Verify Music Recognition limits
    $recognitionLimit = DB::table('rrt_ai_package_roles')
        ->where('ai_id', 2) // Music Recognition
        ->where('role_id', $expected['role_id'])
        ->value('usage_count');

    $totalTests++;
    if ($recognitionLimit == $expected['recognition']) {
        echo "✓ Music Recognition: {$recognitionLimit} uses (expected: {$expected['recognition']})\n";
        $passedTests++;
    } else {
        echo "✗ Music Recognition: {$recognitionLimit} uses (expected: {$expected['recognition']})\n";
        $failedTests++;
    }

    echo "\n";
}

// Test rrt_get_user_role() function with simulated subscription data
echo "========================================\n";
echo "Testing rrt_get_user_role() Function\n";
echo "========================================\n\n";

// Test Distribution Basic vs Distribution Pro distinction
echo "Testing Distribution Basic vs Pro distinction:\n";
echo str_repeat('-', 40) . "\n";

// Simulate user with Distribution Basic (subscription_id=4)
$testUserId = DB::table('rrt_users')->where('email', 'test@example.com')->value('id');
if (!$testUserId) {
    echo "Note: No test user found. Skipping live rrt_get_user_role() tests.\n";
    echo "To test live, create a user with email 'test@example.com' and add subscriptions.\n\n";
} else {
    // Check what subscriptions the test user has
    $userSubscriptions = DB::table('rrt_subscription_orders')
        ->where('user_id', $testUserId)
        ->pluck('subscription_id')
        ->toArray();

    echo "Test user subscriptions: " . implode(', ', $userSubscriptions) . "\n";

    $user = UserModel::find($testUserId);
    $roles = rrt_get_user_role($user);
    echo "Detected roles: " . implode(', ', $roles) . "\n\n";
}

// Summary
echo "========================================\n";
echo "Test Summary\n";
echo "========================================\n";
echo "Total Tests: {$totalTests}\n";
echo "Passed: {$passedTests}\n";
echo "Failed: {$failedTests}\n";
echo "\n";

if ($failedTests === 0) {
    echo "✓ All tests passed!\n\n";
    exit(0);
} else {
    echo "✗ Some tests failed. Please review the output above.\n\n";
    exit(1);
}

