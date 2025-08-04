<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\CourseDescription;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

echo "=== Fix Livewire Temporary Paths ===\n\n";

// Initialize Cloudinary configuration
try {
    $cloudinary = app(\App\Services\CloudinaryService::class);
    $cloudName = config('cloudinary.cloud.cloud_name') ?: 
                 config('filesystems.disks.cloudinary.cloud_name') ?: 
                 env('CLOUDINARY_CLOUD_NAME');
    
    echo "Cloud Name: {$cloudName}\n\n";
} catch (\Exception $e) {
    echo "Error initializing Cloudinary: {$e->getMessage()}\n";
    exit(1);
}

// Find courses with livewire-tmp paths
$courses = CourseDescription::whereRaw("image_url LIKE '%livewire-tmp%'")->get();

echo "Found " . count($courses) . " courses with livewire-tmp paths\n\n";

foreach ($courses as $course) {
    echo "Course ID: {$course->id} - {$course->title}\n";
    echo "  Original URL: {$course->getRawOriginal('image_url')}\n";
    
    // Extract the base64 part from the livewire-tmp path
    $originalPath = $course->getRawOriginal('image_url');
    
    if (preg_match('/livewire-tmp\/(.+?)-meta(.+?)-/', $originalPath, $matches)) {
        $publicId = $matches[1];
        $base64Part = $matches[2];
        
        // Try to decode the base64 part to get the original filename
        try {
            $decodedFilename = base64_decode($base64Part);
            echo "  Decoded filename: {$decodedFilename}\n";
            
            // Extract extension from decoded filename
            $extension = pathinfo($decodedFilename, PATHINFO_EXTENSION);
            if (!$extension) {
                $extension = 'jpg'; // Default extension if we can't determine it
            }
            
            // Create a new standardized path
            $newPath = "courses/course_{$course->id}_" . time() . "." . $extension;
            echo "  New path would be: {$newPath}\n";
            
            // Ask user for confirmation
            echo "  No automatic fix available for livewire-tmp paths.\n";
            echo "  These files need to be manually uploaded to Cloudinary.\n";
            echo "  Would you like to update the database path to: {$newPath}? (y/n): ";
            
            // In production, this would require user input, but for automation:
            $confirmation = 'y'; // Auto-confirm for this script
            
            if ($confirmation === 'y') {
                try {
                    $course->image_url = $newPath;
                    $course->save();
                    echo "  ✓ Path updated in database\n";
                } catch (\Exception $e) {
                    echo "  ✗ Error updating database: {$e->getMessage()}\n";
                }
            } else {
                echo "  ✗ Skipped\n";
            }
        } catch (\Exception $e) {
            echo "  ✗ Error decoding filename: {$e->getMessage()}\n";
        }
    } else {
        echo "  ✗ Could not parse livewire-tmp path\n";
    }
    
    echo "\n";
}

// Also check for any other issues with image paths
$allCourses = CourseDescription::all();
$issueCount = 0;

echo "=== Checking All Image Paths ===\n\n";

foreach ($allCourses as $course) {
    $originalPath = $course->getRawOriginal('image_url');
    $hasIssue = false;
    $issueDescription = "";
    
    // Check for common issues
    if (empty($originalPath)) {
        $hasIssue = true;
        $issueDescription = "Empty path";
    } else if (str_contains($originalPath, 'livewire-tmp')) {
        $hasIssue = true;
        $issueDescription = "Contains livewire-tmp";
    } else if (!str_contains($originalPath, '/')) {
        $hasIssue = true;
        $issueDescription = "Missing folder prefix";
    }
    
    if ($hasIssue) {
        $issueCount++;
        echo "Course ID: {$course->id} - {$course->title}\n";
        echo "  Issue: {$issueDescription}\n";
        echo "  Path: {$originalPath}\n\n";
    }
}

echo "=== Summary ===\n";
echo "Total courses with livewire-tmp paths: " . count($courses) . "\n";
echo "Total courses with other path issues: {$issueCount}\n";
echo "\n=== Next Steps ===\n";
echo "1. Upload your course images to Cloudinary with the correct paths\n";
echo "2. You can use the Cloudinary dashboard to upload files directly\n";
echo "3. Make sure to use the paths that match what's in your database\n";
echo "4. Each image should be uploaded to the 'courses/' folder in Cloudinary\n";
echo "\nDone!\n";
