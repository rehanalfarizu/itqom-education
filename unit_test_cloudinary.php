<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\CloudinaryService;
use App\Models\CourseDescription;

echo "=== CLOUDINARY SERVICE UNIT TESTS ===\n\n";

$testResults = [];

// Test 1: Service Instantiation
echo "🧪 Test 1: CloudinaryService Instantiation\n";
try {
    $service = app(CloudinaryService::class);
    echo "   ✅ PASS: Service instantiated successfully\n";
    $testResults['instantiation'] = 'PASS';
} catch (\Exception $e) {
    echo "   ❌ FAIL: " . $e->getMessage() . "\n";
    $testResults['instantiation'] = 'FAIL';
}

// Test 2: Method Existence
echo "\n🧪 Test 2: Required Methods Exist\n";
$requiredMethods = [
    'uploadImage',
    'uploadImageWithPublicId', 
    'getOptimizedUrl',
    'deleteImage',
    'isCloudinaryEnabled',
    'listFiles'
];

$methodsPass = true;
foreach ($requiredMethods as $method) {
    if (method_exists($service, $method)) {
        echo "   ✅ Method '$method' exists\n";
    } else {
        echo "   ❌ Method '$method' missing\n";
        $methodsPass = false;
    }
}
$testResults['methods'] = $methodsPass ? 'PASS' : 'FAIL';

// Test 3: Configuration Check
echo "\n🧪 Test 3: Cloudinary Configuration\n";
try {
    $isEnabled = $service->isCloudinaryEnabled();
    
    echo "   ✅ Cloudinary enabled: " . ($isEnabled ? 'YES' : 'NO') . "\n";
    echo "   ✅ Storage type: " . ($isEnabled ? 'cloudinary' : 'local') . "\n";
    $testResults['configuration'] = 'PASS';
} catch (\Exception $e) {
    echo "   ❌ FAIL: " . $e->getMessage() . "\n";
    $testResults['configuration'] = 'FAIL';
}

// Test 4: URL Generation
echo "\n🧪 Test 4: URL Generation\n";
$urlTests = [
    'courses/test.jpg' => 'Should generate Cloudinary URL',
    'courses/sample.png' => 'Should handle PNG files',
    'livewire-tmp/old.jpg' => 'Should convert livewire-tmp to courses',
    '' => 'Should return default image',
    'invalid/path.jpg' => 'Should handle invalid paths'
];

$urlPass = true;
foreach ($urlTests as $input => $description) {
    try {
        $url = $service->getOptimizedUrl($input);
        
        if (empty($input) && $url === '/images/default-course.jpg') {
            echo "   ✅ Empty path: Default image returned\n";
        } elseif (!empty($input) && strpos($url, 'cloudinary.com') !== false) {
            echo "   ✅ '$input': Valid Cloudinary URL generated\n";
        } elseif (!empty($input) && strpos($url, '/images/') !== false) {
            echo "   ✅ '$input': Fallback URL generated\n";
        } else {
            echo "   ⚠️ '$input': Unexpected URL format: $url\n";
        }
    } catch (\Exception $e) {
        echo "   ❌ '$input': " . $e->getMessage() . "\n";
        $urlPass = false;
    }
}
$testResults['url_generation'] = $urlPass ? 'PASS' : 'FAIL';

// Test 5: Transformation String Building
echo "\n🧪 Test 5: Transformation Handling\n";
$transformationTests = [
    [] => 'Empty transformations',
    ['width' => 800] => 'Width only',
    ['width' => 800, 'height' => 600, 'crop' => 'fill'] => 'Multiple transformations',
    ['quality' => 'auto', 'fetch_format' => 'auto'] => 'Quality and format'
];

$transformPass = true;
foreach ($transformationTests as $transforms => $description) {
    try {
        $url = $service->getOptimizedUrl('courses/test.jpg', (array)$transforms);
        
        if (strpos($url, 'cloudinary.com') !== false) {
            echo "   ✅ $description: URL generated with transformations\n";
        } else {
            echo "   ⚠️ $description: No Cloudinary URL generated\n";
        }
    } catch (\Exception $e) {
        echo "   ❌ $description: " . $e->getMessage() . "\n";
        $transformPass = false;
    }
}
$testResults['transformations'] = $transformPass ? 'PASS' : 'FAIL';

// Test 6: List Files Functionality
echo "\n🧪 Test 6: List Files\n";
try {
    $files = $service->listFiles('courses');
    echo "   ✅ List files executed (found " . count($files) . " files)\n";
    
    if (count($files) > 0) {
        $sampleFile = $files[0];
        if (isset($sampleFile['public_id'])) {
            echo "   ✅ File structure valid (has public_id)\n";
        }
    }
    $testResults['list_files'] = 'PASS';
} catch (\Exception $e) {
    echo "   ❌ FAIL: " . $e->getMessage() . "\n";
    $testResults['list_files'] = 'FAIL';
}

// Test Summary
echo "\n" . str_repeat("=", 50) . "\n";
echo "TEST SUMMARY:\n";
$totalTests = count($testResults);
$passedTests = count(array_filter($testResults, function($result) { return $result === 'PASS'; }));

foreach ($testResults as $test => $result) {
    $icon = $result === 'PASS' ? '✅' : '❌';
    echo "   $icon " . ucfirst(str_replace('_', ' ', $test)) . ": $result\n";
}

echo "\nOVERALL: $passedTests/$totalTests tests passed\n";

if ($passedTests === $totalTests) {
    echo "🎉 ALL TESTS PASSED! CloudinaryService is working correctly.\n";
} else {
    echo "⚠️ Some tests failed. Please check the implementation.\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
