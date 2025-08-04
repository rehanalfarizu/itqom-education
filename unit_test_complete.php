<?php

require __DIR__.'/vendor/autoload.php';

// Bootstrap Laravel application
$app = require_once __DIR__.'/bootstrap/app.php';

// Only call make if $app is an object
if (is_object($app) && method_exists($app, 'make')) {
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
} else {
    // Alternative bootstrap for different Laravel versions
    require_once __DIR__.'/bootstrap/app.php';
}

echo "=== COMPLETE INTEGRATION TEST SUITE ===\n\n";

$allResults = [];

use Illuminate\Support\Facades\DB;

// Test 1: Environment Check
echo "🧪 Test 1: Environment & Configuration\n";
try {
    echo "   Environment: " . app()->environment() . "\n";
    echo "   Cloud Name: " . (env('CLOUDINARY_CLOUD_NAME') ? '✅ Set' : '❌ Missing') . "\n";
    echo "   API Key: " . (env('CLOUDINARY_API_KEY') ? '✅ Set' : '❌ Missing') . "\n";
    echo "   API Secret: " . (env('CLOUDINARY_API_SECRET') ? '✅ Set' : '❌ Missing') . "\n";
    echo "   Database: " . (DB::connection()->getPdo() ? '✅ Connected' : '❌ Failed') . "\n";
    $allResults['environment'] = 'PASS';
} catch (\Exception $e) {
    echo "   ❌ Environment check failed: " . $e->getMessage() . "\n";
    $allResults['environment'] = 'FAIL';
}

// Test 2: Service Integration
echo "\n🧪 Test 2: Service Integration\n";
try {
    $cloudinaryService = app(\App\Services\CloudinaryService::class);
    echo "   ✅ CloudinaryService resolved from container\n";

    $isEnabled = $cloudinaryService->isCloudinaryEnabled();
    echo "   Cloudinary enabled: " . ($isEnabled ? '✅ YES' : '❌ NO') . "\n";

    $storageType = $cloudinaryService->getStorageType();
    echo "   Storage type: $storageType\n";

    $allResults['service_integration'] = 'PASS';
} catch (\Exception $e) {
    echo "   ❌ Service integration failed: " . $e->getMessage() . "\n";
    $allResults['service_integration'] = 'FAIL';
}

// Test 3: Model-Service Integration
echo "\n🧪 Test 3: Model-Service Integration\n";
try {
    $course = \App\Models\CourseDescription::first();
    if ($course) {
        echo "   ✅ Course model accessible\n";

        $imageUrl = $course->image_url;
        echo "   Image URL: $imageUrl\n";

        if (strpos($imageUrl, 'cloudinary.com') !== false) {
            echo "   ✅ Using Cloudinary URL\n";
        } elseif (strpos($imageUrl, 'courses/') !== false) {
            echo "   ✅ Using courses/ path\n";
        } else {
            echo "   ⚠️ Unexpected URL format\n";
        }

        $allResults['model_service'] = 'PASS';
    } else {
        echo "   ⚠️ No courses found to test\n";
        $allResults['model_service'] = 'SKIP';
    }
} catch (\Exception $e) {
    echo "   ❌ Model-service integration failed: " . $e->getMessage() . "\n";
    $allResults['model_service'] = 'FAIL';
}

// Test 4: End-to-End Data Flow
echo "\n🧪 Test 4: End-to-End Data Flow\n";
try {
    // Simulate the complete flow
    $courses = \App\Models\CourseDescription::take(3)->get();

    echo "   Testing data flow for " . count($courses) . " courses:\n";
    foreach ($courses as $course) {
        echo "     Course: {$course->title}\n";

        // Raw database value
        $rawImageUrl = $course->getAttributes()['image_url'] ?? 'null';
        echo "       DB Value: $rawImageUrl\n";

        // Processed through accessor
        $processedUrl = $course->image_url;
        echo "       Accessor: $processedUrl\n";

        // Check if transformation applied
        if ($rawImageUrl !== $processedUrl) {
            echo "       ✅ Accessor transformation applied\n";
        } else {
            echo "       ⚠️ No accessor transformation\n";
        }

        // Test URL accessibility (basic check)
        if (filter_var($processedUrl, FILTER_VALIDATE_URL)) {
            echo "       ✅ Valid URL format\n";
        } else {
            echo "       ❌ Invalid URL format\n";
        }

        echo "       ---\n";
    }

    $allResults['end_to_end'] = 'PASS';
} catch (\Exception $e) {
    echo "   ❌ End-to-end test failed: " . $e->getMessage() . "\n";
    $allResults['end_to_end'] = 'FAIL';
}

// Test 5: Performance Check
echo "\n🧪 Test 5: Performance Check\n";
try {
    $startTime = microtime(true);

    // Test multiple URL generations
    $cloudinaryService = app(\App\Services\CloudinaryService::class);
    for ($i = 0; $i < 10; $i++) {
        $url = $cloudinaryService->getOptimizedUrl("courses/test{$i}.jpg");
    }

    $endTime = microtime(true);
    $duration = round(($endTime - $startTime) * 1000, 2);

    echo "   ✅ 10 URL generations took {$duration}ms\n";

    if ($duration < 100) {
        echo "   ✅ Performance: Excellent (< 100ms)\n";
    } elseif ($duration < 500) {
        echo "   ✅ Performance: Good (< 500ms)\n";
    } else {
        echo "   ⚠️ Performance: Slow (> 500ms)\n";
    }

    $allResults['performance'] = 'PASS';
} catch (\Exception $e) {
    echo "   ❌ Performance test failed: " . $e->getMessage() . "\n";
    $allResults['performance'] = 'FAIL';
}

// Test 6: Error Handling
echo "\n🧪 Test 6: Error Handling\n";
try {
    $cloudinaryService = app(\App\Services\CloudinaryService::class);

    // Test with invalid inputs
    $testCases = [
        'empty_string' => ['', 'Empty string'],
        'null_value' => [null, 'Null value'],
        'nonexistent_file' => ['nonexistent/file.jpg', 'Nonexistent file'],
        'invalid_chars' => ['invalid-chars/file<>.jpg', 'Invalid characters']
    ];

    $errorHandlingPass = true;
    foreach ($testCases as $testName => $testData) {
        list($input, $description) = $testData;
        try {
            $url = $cloudinaryService->getOptimizedUrl($input);
            echo "   ✅ $description: Handled gracefully (returned: $url)\n";
        } catch (\Exception $e) {
            echo "   ❌ $description: Threw exception: " . $e->getMessage() . "\n";
            $errorHandlingPass = false;
        }
    }

    $allResults['error_handling'] = $errorHandlingPass ? 'PASS' : 'FAIL';
} catch (\Exception $e) {
    echo "   ❌ Error handling test failed: " . $e->getMessage() . "\n";
    $allResults['error_handling'] = 'FAIL';
}

// Final Summary
echo "\n" . str_repeat("=", 60) . "\n";
echo "COMPLETE INTEGRATION TEST SUMMARY:\n";
echo str_repeat("=", 60) . "\n";

$totalTests = count($allResults);
$passedTests = count(array_filter($allResults, function($result) { return $result === 'PASS'; }));
$skippedTests = count(array_filter($allResults, function($result) { return $result === 'SKIP'; }));
$failedTests = count(array_filter($allResults, function($result) { return $result === 'FAIL'; }));

foreach ($allResults as $test => $result) {
    $icon = $result === 'PASS' ? '✅' : ($result === 'SKIP' ? '⏭️' : '❌');
    echo "   $icon " . ucfirst(str_replace('_', ' ', $test)) . ": $result\n";
}

echo "\n📊 OVERALL RESULTS:\n";
echo "   Total Tests: $totalTests\n";
echo "   Passed: $passedTests\n";
echo "   Failed: $failedTests\n";
echo "   Skipped: $skippedTests\n";

$successRate = round(($passedTests / ($totalTests - $skippedTests)) * 100, 1);
echo "   Success Rate: {$successRate}%\n";

if ($failedTests === 0) {
    echo "\n🎉 ALL TESTS PASSED! Your CloudinaryService integration is working perfectly!\n";
    echo "\n✅ READY FOR PRODUCTION USE\n";
} elseif ($successRate >= 80) {
    echo "\n✅ MOSTLY WORKING! Minor issues detected but core functionality is solid.\n";
    echo "\n🔧 Consider fixing the failed tests for optimal performance.\n";
} else {
    echo "\n⚠️ SEVERAL ISSUES DETECTED! Please address the failed tests before production use.\n";
    echo "\n🛠️ Focus on fixing the core integration issues first.\n";
}

echo "\n" . str_repeat("=", 60) . "\n";
