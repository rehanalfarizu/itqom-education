<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\CourseDescription;

echo "=== TEST FRONTEND IMAGE DISPLAY ===\n\n";

try {
    // Get a sample course
    $course = CourseDescription::whereNotNull('image')
        ->where('image', '!=', '')
        ->first();

    if (!$course) {
        echo "No courses with images found!\n";
        exit(1);
    }

    echo "Testing with Course ID: {$course->id}\n";
    echo "Course Title: {$course->title}\n";
    echo "Original Image Path: {$course->image}\n\n";

    // Test different URL generation approaches
    $cloudName = env('CLOUDINARY_CLOUD_NAME', 'hltd67bzw');

    echo "=== URL GENERATION TESTS ===\n\n";

    // Test 1: Direct Cloudinary URL
    $directUrl = "https://res.cloudinary.com/{$cloudName}/image/upload/q_auto,f_auto/{$course->image}";
    echo "1. Direct Cloudinary URL:\n";
    echo "   {$directUrl}\n\n";

    // Test 2: With transformations
    $transformedUrl = "https://res.cloudinary.com/{$cloudName}/image/upload/w_800,h_450,c_fill,q_auto,f_auto/{$course->image}";
    echo "2. With transformations (800x450):\n";
    echo "   {$transformedUrl}\n\n";

    // Test 3: Thumbnail version
    $thumbnailUrl = "https://res.cloudinary.com/{$cloudName}/image/upload/w_300,h_200,c_fill,q_auto,f_auto/{$course->image}";
    echo "3. Thumbnail version (300x200):\n";
    echo "   {$thumbnailUrl}\n\n";

    // Test 4: Check if this is a livewire-tmp path that needs fixing
    if (str_contains($course->image, 'livewire-tmp/')) {
        $fixedPath = 'courses/' . basename($course->image);
        $fixedUrl = "https://res.cloudinary.com/{$cloudName}/image/upload/q_auto,f_auto/{$fixedPath}";
        echo "4. FIXED PATH (converted from livewire-tmp):\n";
        echo "   Original: {$course->image}\n";
        echo "   Fixed: {$fixedPath}\n";
        echo "   URL: {$fixedUrl}\n\n";

        echo "⚠️  This course needs database path update!\n";
    } else {
        echo "4. Path Status: ✓ Already in correct format\n\n";
    }

    echo "=== FRONTEND TESTING INSTRUCTIONS ===\n";
    echo "1. Copy any of the URLs above\n";
    echo "2. Open them in your browser to test if images load\n";
    echo "3. Check your Vue.js frontend to see if images display\n";
    echo "4. Verify the URLs match what your frontend is generating\n\n";

    // Test Vue.js API endpoint
    echo "=== API ENDPOINT TEST ===\n";
    $apiUrl = "https://itqom-platform-aa0ffce6a276.herokuapp.com/api/courses";
    echo "Test this API endpoint: {$apiUrl}\n";
    echo "Check if the image URLs in the JSON response are correct.\n\n";

    echo "✓ Testing preparation complete!\n";

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\nDone!\n";
