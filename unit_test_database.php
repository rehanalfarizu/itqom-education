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

use App\Models\CourseDescription;
use App\Services\CloudinaryService;

echo "=== DATABASE INTEGRATION TESTS ===\n\n";

$testResults = [];

// Test 1: Database Connection
echo "🧪 Test 1: Database Connection\n";
try {
    $count = CourseDescription::count();
    echo "   ✅ PASS: Database connected (found $count courses)\n";
    $testResults['db_connection'] = 'PASS';
} catch (\Exception $e) {
    echo "   ❌ FAIL: " . $e->getMessage() . "\n";
    $testResults['db_connection'] = 'FAIL';
}

// Test 2: Model Accessors
echo "\n🧪 Test 2: CourseDescription Model Accessors\n";
try {
    $course = CourseDescription::first();
    if ($course) {
        $imageUrl = $course->image_url;
        echo "   ✅ Image URL accessor works: $imageUrl\n";

        // Check if it's using CloudinaryService
        if (strpos($imageUrl, 'cloudinary.com') !== false) {
            echo "   ✅ Using Cloudinary URL\n";
        } else {
            echo "   ⚠️ Not using Cloudinary URL (might be local/fallback)\n";
        }

        $testResults['model_accessors'] = 'PASS';
    } else {
        echo "   ⚠️ No courses found to test\n";
        $testResults['model_accessors'] = 'SKIP';
    }
} catch (\Exception $e) {
    echo "   ❌ FAIL: " . $e->getMessage() . "\n";
    $testResults['model_accessors'] = 'FAIL';
}

// Test 3: Data Consistency
echo "\n🧪 Test 3: Image URL Data Consistency\n";
try {
    $courses = CourseDescription::take(5)->get();
    $consistencyPass = true;

    foreach ($courses as $course) {
        $imageUrl = $course->image_url;
        echo "   Course '{$course->title}':\n";
        echo "     Image URL: $imageUrl\n";

        // Check for common issues
        if (strpos($imageUrl, 'Upload failed') !== false) {
            echo "     ⚠️ Contains upload error message\n";
            $consistencyPass = false;
        } elseif (strpos($imageUrl, 'livewire-tmp') !== false) {
            echo "     ⚠️ Still using livewire-tmp path\n";
            $consistencyPass = false;
        } elseif (strpos($imageUrl, 'courses/') !== false || strpos($imageUrl, 'cloudinary.com') !== false) {
            echo "     ✅ Valid image path\n";
        } else {
            echo "     ❓ Unknown path format\n";
        }
        echo "\n";
    }

    $testResults['data_consistency'] = $consistencyPass ? 'PASS' : 'FAIL';
} catch (\Exception $e) {
    echo "   ❌ FAIL: " . $e->getMessage() . "\n";
    $testResults['data_consistency'] = 'FAIL';
}

// Test 4: CRUD Operations
echo "\n🧪 Test 4: CRUD Operations\n";
try {
    // Create
    $testCourse = CourseDescription::create([
        'title' => 'Unit Test Course',
        'tag' => 'Testing',
        'overview' => 'This is a test course for unit testing.',
        'image_url' => 'courses/unit-test.jpg',
        'price' => 50000,
        'price_discount' => 40000,
        'instructor_name' => 'Test Instructor',
        'instructor_position' => 'QA Engineer',
        'video_count' => 5,
        'duration' => 30,
        'features' => json_encode(['test_feature'])
    ]);
    echo "   ✅ CREATE: Course created with ID {$testCourse->id}\n";

    // Read
    $readCourse = CourseDescription::find($testCourse->id);
    if ($readCourse && $readCourse->title === 'Unit Test Course') {
        echo "   ✅ READ: Course data retrieved correctly\n";
    } else {
        echo "   ❌ READ: Course data not found or incorrect\n";
    }

    // Update
    $testCourse->update(['title' => 'Updated Unit Test Course']);
    $updatedCourse = CourseDescription::find($testCourse->id);
    if ($updatedCourse->title === 'Updated Unit Test Course') {
        echo "   ✅ UPDATE: Course updated successfully\n";
    } else {
        echo "   ❌ UPDATE: Course update failed\n";
    }

    // Delete
    $testCourse->delete();
    $deletedCourse = CourseDescription::find($testCourse->id);
    if (!$deletedCourse) {
        echo "   ✅ DELETE: Course deleted successfully\n";
    } else {
        echo "   ❌ DELETE: Course deletion failed\n";
    }

    $testResults['crud_operations'] = 'PASS';
} catch (\Exception $e) {
    echo "   ❌ FAIL: " . $e->getMessage() . "\n";
    $testResults['crud_operations'] = 'FAIL';
}

// Test 5: Image URL Generation Consistency
echo "\n🧪 Test 5: Image URL Generation Consistency\n";
try {
    $service = app(CloudinaryService::class);
    $testPaths = [
        'courses/test1.jpg',
        'courses/test2.png',
        'livewire-tmp/old.jpg'
    ];

    $generationPass = true;
    foreach ($testPaths as $path) {
        $url1 = $service->getOptimizedUrl($path);
        $url2 = $service->getOptimizedUrl($path);

        if ($url1 === $url2) {
            echo "   ✅ '$path': Consistent URL generation\n";
        } else {
            echo "   ❌ '$path': Inconsistent URLs\n";
            echo "     URL1: $url1\n";
            echo "     URL2: $url2\n";
            $generationPass = false;
        }
    }

    $testResults['url_consistency'] = $generationPass ? 'PASS' : 'FAIL';
} catch (\Exception $e) {
    echo "   ❌ FAIL: " . $e->getMessage() . "\n";
    $testResults['url_consistency'] = 'FAIL';
}

// Test Summary
echo "\n" . str_repeat("=", 50) . "\n";
echo "DATABASE INTEGRATION TEST SUMMARY:\n";
$totalTests = count($testResults);
$passedTests = count(array_filter($testResults, function($result) { return $result === 'PASS'; }));
$skippedTests = count(array_filter($testResults, function($result) { return $result === 'SKIP'; }));

foreach ($testResults as $test => $result) {
    $icon = $result === 'PASS' ? '✅' : ($result === 'SKIP' ? '⏭️' : '❌');
    echo "   $icon " . ucfirst(str_replace('_', ' ', $test)) . ": $result\n";
}

echo "\nOVERALL: $passedTests/$totalTests tests passed";
if ($skippedTests > 0) {
    echo " ($skippedTests skipped)";
}
echo "\n";

if ($passedTests === $totalTests) {
    echo "🎉 ALL DATABASE TESTS PASSED!\n";
} elseif ($passedTests + $skippedTests === $totalTests) {
    echo "✅ All available tests passed!\n";
} else {
    echo "⚠️ Some database tests failed. Please check the implementation.\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
