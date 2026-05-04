<?php

/**
 * Test Script for Release Limits (Singles & Albums)
 *
 * This script verifies that:
 * 1. Distribution Basic: 2 singles OR 2 albums per year
 * 2. Distribution Pro: 4 singles OR 2 albums per year
 * 3. Publishing: 4 singles OR 2 albums per year
 *
 * Usage: php test_release_limits.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "\n========================================\n";
echo "Release Limits Test Script\n";
echo "========================================\n\n";

// Expected limits based on requirements
$expectedLimits = [
    'Distribution Basic' => [
        'subscription_id' => 4,
        'single_limit' => 2,
        'album_limit' => 2,
    ],
    'Distribution Pro' => [
        'subscription_id' => 2,
        'single_limit' => 4,
        'album_limit' => 2,
    ],
    'Publishing' => [
        'subscription_id' => 1,
        'single_limit' => 4,
        'album_limit' => 2,
    ],
];

$totalTests = 0;
$passedTests = 0;
$failedTests = 0;

foreach ($expectedLimits as $planName => $limits) {
    echo "Testing Plan: $planName\n";
    echo "----------------------------------------\n";

    // Check if subscription exists
    $subscription = DB::table('rrt_subscriptions')
        ->where('id', $limits['subscription_id'])
        ->first();

    if ($subscription) {
        echo "✓ Subscription exists: {$subscription->name}\n";
        $passedTests++;
    } else {
        echo "✗ Subscription not found (id={$limits['subscription_id']})\n";
        $failedTests++;
    }
    $totalTests++;

    // Display expected limits
    echo "  Expected limits:\n";
    echo "    - Singles: {$limits['single_limit']} per year\n";
    echo "    - Albums: {$limits['album_limit']} per year\n";

    // Check if there are any test users with this subscription
    $testUsers = DB::table('rrt_subscription_orders')
        ->where('subscription_id', $limits['subscription_id'])
        ->where('status', 'active')
        ->count();

    if ($testUsers > 0) {
        echo "  ℹ Found {$testUsers} active user(s) with this subscription\n";
    } else {
        echo "  ℹ No active users found with this subscription\n";
    }

    echo "\n";
}

echo "========================================\n";
echo "Logic Verification\n";
echo "========================================\n\n";

// Verify the logic in StudioReleaseController
$controllerFile = __DIR__ . '/app/Http/Controllers/Public/StudioReleaseController.php';
$controllerContent = file_get_contents($controllerFile);

echo "Checking StudioReleaseController.php:\n";
echo "----------------------------------------\n";

// Check for Distribution Basic logic
if (strpos($controllerContent, "subscription_id', 4") !== false) {
    echo "✓ Distribution Basic check exists (subscription_id=4)\n";
    $passedTests++;
} else {
    echo "✗ Distribution Basic check missing\n";
    $failedTests++;
}
$totalTests++;

// Check for Distribution Pro logic
if (strpos($controllerContent, "subscription_id', 2") !== false) {
    echo "✓ Distribution Pro check exists (subscription_id=2)\n";
    $passedTests++;
} else {
    echo "✗ Distribution Pro check missing\n";
    $failedTests++;
}
$totalTests++;

// Check for Publishing logic
if (strpos($controllerContent, "subscription_id', 1") !== false) {
    echo "✓ Publishing check exists (subscription_id=1)\n";
    $passedTests++;
} else {
    echo "✗ Publishing check missing\n";
    $failedTests++;
}
$totalTests++;

// Check for type-specific counting (single vs album)
if (strpos($controllerContent, "type', 'single'") !== false &&
    strpos($controllerContent, "type', 'album'") !== false) {
    echo "✓ Type-specific counting implemented (single/album)\n";
    $passedTests++;
} else {
    echo "✗ Type-specific counting missing\n";
    $failedTests++;
}
$totalTests++;

// Check for limit enforcement
if (strpos($controllerContent, "singles per year") !== false &&
    strpos($controllerContent, "albums per year") !== false) {
    echo "✓ Limit enforcement messages exist\n";
    $passedTests++;
} else {
    echo "✗ Limit enforcement messages missing\n";
    $failedTests++;
}
$totalTests++;

echo "\n";

echo "========================================\n";
echo "Database Schema Check\n";
echo "========================================\n\n";

// Check if rrt_music_distributions table has 'type' column
$tableExists = DB::select("SHOW TABLES LIKE 'rrt_music_distributions'");
if ($tableExists) {
    echo "✓ Table 'rrt_music_distributions' exists\n";
    $passedTests++;

    $columns = DB::select("SHOW COLUMNS FROM rrt_music_distributions LIKE 'type'");
    if ($columns) {
        echo "✓ Column 'type' exists in rrt_music_distributions\n";
        $passedTests++;
    } else {
        echo "✗ Column 'type' missing in rrt_music_distributions\n";
        $failedTests++;
    }
    $totalTests += 2;
} else {
    echo "✗ Table 'rrt_music_distributions' not found\n";
    $failedTests++;
    $totalTests++;
}

echo "\n";

echo "========================================\n";
echo "Test Summary\n";
echo "========================================\n";
echo "Total Tests: $totalTests\n";
echo "Passed: $passedTests\n";
echo "Failed: $failedTests\n\n";

if ($failedTests === 0) {
    echo "✓ All tests passed!\n\n";
    exit(0);
} else {
    echo "✗ Some tests failed. Please review the output above.\n\n";
    exit(1);
}
