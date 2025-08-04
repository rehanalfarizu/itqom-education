<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Cloudinary\Cloudinary;

echo "=== Cloudinary Image Availability Check ===\n\n";

// Get Cloudinary configuration
$cloudName = config('cloudinary.cloud.cloud_name');
$apiKey = config('cloudinary.cloud.api_key');
$apiSecret = config('cloudinary.cloud.api_secret');

echo "Cloud Name: {$cloudName}\n\n";

// Function to check if an image exists on Cloudinary
function imageExists($url) {
    try {
        $response = Http::timeout(5)->head($url);
        return $response->successful();
    } catch (\Exception $e) {
        return false;
    }
}

// Get all course descriptions with image URLs
$courses = DB::table('course_description')->select(['id', 'title', 'image_url'])->get();

echo "Found " . count($courses) . " courses in database\n\n";

// Check each image URL
$availableCount = 0;
$missingCount = 0;
$errorCount = 0;

foreach ($courses as $course) {
    echo "Course ID: {$course->id} - {$course->title}\n";
    echo "  Image URL: " . ($course->image_url ?: 'NOT SET') . "\n";
    
    if (!$course->image_url) {
        echo "  No image URL set, skipping check.\n";
        continue;
    }
    
    // Format the URL for checking
    if (filter_var($course->image_url, FILTER_VALIDATE_URL)) {
        $fullUrl = $course->image_url;
    } else {
        // It's a path, construct full URL
        $fullUrl = "https://res.cloudinary.com/{$cloudName}/image/upload/{$course->image_url}";
    }
    
    echo "  Checking URL: {$fullUrl}\n";
    
    // Check if the image exists
    if (imageExists($fullUrl)) {
        echo "  ✓ Image is available\n";
        $availableCount++;
    } else {
        echo "  ✗ Image is NOT available\n";
        
        // Check if a different folder would work
        $fileName = basename($course->image_url);
        $alternativeUrl = "https://res.cloudinary.com/{$cloudName}/image/upload/courses/{$fileName}";
        
        if ($alternativeUrl != $fullUrl) {
            echo "  Trying alternative URL: {$alternativeUrl}\n";
            if (imageExists($alternativeUrl)) {
                echo "  ✓ Alternative URL is available! Consider updating to use this path.\n";
            } else {
                echo "  ✗ Alternative URL is also not available\n";
                
                // Try one more alternative with images folder
                $anotherUrl = "https://res.cloudinary.com/{$cloudName}/image/upload/images/{$fileName}";
                echo "  Trying another alternative: {$anotherUrl}\n";
                if (imageExists($anotherUrl)) {
                    echo "  ✓ This alternative URL works! Consider updating to use this path.\n";
                } else {
                    echo "  ✗ None of the tested URLs work. The image may not exist in Cloudinary.\n";
                }
            }
        }
        
        $missingCount++;
    }
    
    echo "\n";
}

// Check frontend URLs directly
echo "=== Frontend URLs Check ===\n";
$frontendUrls = [
    "https://res.cloudinary.com/{$cloudName}/image/upload/w_800,h_450,c_fill,q_auto,f_auto/images/ui-ux-design.jpg",
    "https://res.cloudinary.com/{$cloudName}/image/upload/w_800,h_450,c_fill,q_auto,f_auto/images/react-native.jpg",
    "https://res.cloudinary.com/{$cloudName}/image/upload/w_800,h_450,c_fill,q_auto,f_auto/courses/course_1754320518_6890ce867b1d5.png"
];

foreach ($frontendUrls as $url) {
    echo "Checking frontend URL: {$url}\n";
    if (imageExists($url)) {
        echo "  ✓ URL is accessible\n";
    } else {
        echo "  ✗ URL is NOT accessible\n";
        
        // Try without transformations
        $baseUrl = preg_replace('|/w_800,h_450,c_fill,q_auto,f_auto/|', '/', $url);
        echo "  Trying without transformations: {$baseUrl}\n";
        if (imageExists($baseUrl)) {
            echo "  ✓ Base URL without transformations is accessible\n";
            echo "  → Issue may be with the transformations, not the image itself\n";
        } else {
            echo "  ✗ Base URL is also not accessible\n";
        }
    }
    echo "\n";
}

echo "=== Summary ===\n";
echo "Total images checked: " . count($courses) . "\n";
echo "Available images: {$availableCount}\n";
echo "Missing images: {$missingCount}\n";
echo "Errors encountered: {$errorCount}\n\n";

echo "=== Recommendations ===\n";
if ($missingCount > 0) {
    echo "1. Upload missing images to Cloudinary\n";
    echo "2. Make sure all images are in the same folder structure (preferably 'courses/')\n";
    echo "3. Update database URLs to match the actual Cloudinary paths\n";
}

echo "4. Fix the CloudinaryService.php file to handle array transformations correctly\n";
echo "5. Check if frontend is correctly using the image URLs\n\n";

echo "Done!\n";
