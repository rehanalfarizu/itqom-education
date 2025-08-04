<?php

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\CloudinaryService;
use App\Models\CourseDescription;

echo "=== NULL HANDLING TEST ===\n";

$cloudinaryService = new CloudinaryService();

// Test 1: Null input
echo "1. Testing null input: ";
try {
    $url = $cloudinaryService->getOptimizedUrl(null);
    echo ($url === '/images/default-course.jpg') ? "PASS" : "FAIL - got: $url";
} catch (Exception $e) {
    echo "FAIL - Error: " . $e->getMessage();
}
echo "\n";

// Test 2: Empty string input
echo "2. Testing empty string: ";
try {
    $url = $cloudinaryService->getOptimizedUrl('');
    echo ($url === '/images/default-course.jpg') ? "PASS" : "FAIL - got: $url";
} catch (Exception $e) {
    echo "FAIL - Error: " . $e->getMessage();
}
echo "\n";

// Test 3: Valid input
echo "3. Testing valid input: ";
try {
    $url = $cloudinaryService->getOptimizedUrl('courses/test-image');
    echo (str_contains($url, 'cloudinary.com') || str_contains($url, '/storage/')) ? "PASS" : "FAIL - got: $url";
} catch (Exception $e) {
    echo "FAIL - Error: " . $e->getMessage();
}
echo "\n";

// Test 4: Check database courses
echo "\n=== COURSE TESTS ===\n";
$courses = CourseDescription::whereNotNull('image')->limit(3)->get();

foreach ($courses as $index => $course) {
    $courseNum = $index + 1;
    echo "Course {$courseNum} (ID: {$course->id}):\n";
    echo "  - DB Value: '{$course->image}'\n";
    
    try {
        $imageUrl = $course->image_url;
        echo "  - Generated URL: '{$imageUrl}'\n";
        echo "  - Status: " . (($imageUrl !== '/images/default-course.jpg') ? "OK" : "DEFAULT") . "\n";
    } catch (Exception $e) {
        echo "  - Error: " . $e->getMessage() . "\n";
    }
    echo "\n";
}

echo "=== SUMMARY ===\n";
echo "Null handling fixes applied to CloudinaryService methods:\n";
echo "- getOptimizedUrl: Fixed to handle null input\n";
echo "- getBestImageUrl: Fixed to handle null input\n";
echo "- checkImageAvailability: Fixed to handle null input\n";
echo "- deleteImage: Fixed to handle null input\n";
echo "- checkFileExists: Fixed to handle null input\n";
echo "- getLocalImageUrl: Fixed to handle null input\n";
echo "\nTest completed!\n";
