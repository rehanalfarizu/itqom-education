<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\CloudinaryService;
use Illuminate\Support\Facades\DB;

echo "=== Fix Cloudinary Image URLs ===\n\n";

// Initialize Cloudinary Service
$cloudinaryService = app(CloudinaryService::class);

// Get all course descriptions
$courses = DB::table('course_description')->select(['id', 'title', 'image_url'])->get();

echo "Found " . count($courses) . " courses in database\n\n";

$updated = 0;
$skipped = 0;
$errors = 0;

foreach ($courses as $course) {
    echo "Processing course ID: {$course->id} - {$course->title}\n";
    
    $imageUrl = $course->image_url;
    if (!$imageUrl) {
        echo "  No image URL found, skipping.\n";
        $skipped++;
        continue;
    }
    
    echo "  Original URL: {$imageUrl}\n";
    
    // Skip if it's already a proper Cloudinary URL 
    // and contains the correct folder (courses)
    if (str_contains($imageUrl, 'cloudinary.com') && 
        str_contains($imageUrl, '/courses/')) {
        echo "  Already a valid Cloudinary URL with correct folder, skipping.\n";
        $skipped++;
        continue;
    }
    
    // Check if it's a full Cloudinary URL but wrong folder (images instead of courses)
    if (str_contains($imageUrl, 'cloudinary.com') && 
        str_contains($imageUrl, '/images/')) {
        
        // Extract the filename
        if (preg_match('/\/images\/([^\/]+\.\w+)$/', $imageUrl, $matches)) {
            $filename = $matches[1];
            
            // Create corrected URL
            $baseParts = explode('/images/', $imageUrl);
            $correctedUrl = $baseParts[0] . '/courses/' . $filename;
            
            echo "  Correcting URL: replacing '/images/' with '/courses/'\n";
            echo "  New URL: {$correctedUrl}\n";
            
            // Update in database
            try {
                DB::table('course_description')
                    ->where('id', $course->id)
                    ->update(['image_url' => $correctedUrl]);
                    
                echo "  ✅ URL updated in database\n";
                $updated++;
            } catch (\Exception $e) {
                echo "  ❌ Error updating database: " . $e->getMessage() . "\n";
                $errors++;
            }
            
            continue;
        }
    }
    
    // If it's just a filename or relative path
    if (!str_contains($imageUrl, 'cloudinary.com')) {
        try {
            // Clean the path
            $cleanPath = $imageUrl;
            $cleanPath = ltrim($cleanPath, '/');
            $cleanPath = str_replace('storage/', '', $cleanPath);
            
            // If it doesn't have a folder prefix, add 'courses/'
            if (!str_contains($cleanPath, '/')) {
                $cleanPath = 'courses/' . $cleanPath;
            } 
            // If it has 'images/' prefix, replace with 'courses/'
            else if (str_starts_with($cleanPath, 'images/')) {
                $cleanPath = 'courses/' . substr($cleanPath, 7); // 7 = length of 'images/'
            }
            
            // Get proper Cloudinary URL
            $correctedUrl = $cloudinaryService->getOptimizedUrl($cleanPath, [
                'width' => 800,
                'height' => 450,
                'crop' => 'fill',
                'quality' => 'auto',
                'format' => 'auto'
            ]);
            
            echo "  Generated new URL: {$correctedUrl}\n";
            
            // Update in database
            if (filter_var($correctedUrl, FILTER_VALIDATE_URL)) {
                DB::table('course_description')
                    ->where('id', $course->id)
                    ->update(['image_url' => $correctedUrl]);
                    
                echo "  ✅ URL updated in database\n";
                $updated++;
            } else {
                echo "  ❌ Generated URL is not valid\n";
                $errors++;
            }
        } catch (\Exception $e) {
            echo "  ❌ Error: " . $e->getMessage() . "\n";
            $errors++;
        }
    }
    
    echo "\n";
}

echo "=== Summary ===\n";
echo "Total courses processed: " . count($courses) . "\n";
echo "Updated URLs: {$updated}\n";
echo "Skipped (already valid): {$skipped}\n";
echo "Errors: {$errors}\n";

if ($updated > 0) {
    echo "\nUploading test image to Cloudinary with correct folder...\n";
    try {
        // Create a test file
        $testFile = tempnam(sys_get_temp_dir(), 'cloudinary_test');
        file_put_contents($testFile, 'Test file for Cloudinary upload');
        
        // Create an UploadedFile instance
        $uploadedFile = new \Illuminate\Http\UploadedFile(
            $testFile, 
            'test.txt', 
            'text/plain', 
            null, 
            true
        );
        
        // Upload to courses folder
        $result = $cloudinaryService->uploadImageHybrid($uploadedFile, 'courses');
        echo "Test upload successful: {$result}\n";
        
        // Clean up
        unlink($testFile);
    } catch (\Exception $e) {
        echo "Test upload failed: " . $e->getMessage() . "\n";
    }
}

echo "Done!\n";
