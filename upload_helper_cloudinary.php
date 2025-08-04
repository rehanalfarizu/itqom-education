<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\CourseDescription;
use App\Services\CloudinaryService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Cloudinary\Cloudinary;

echo "=== Cloudinary Image Upload Helper ===\n\n";

// Initialize Cloudinary configuration
try {
    $cloudinaryService = app(CloudinaryService::class);
    $cloudName = config('cloudinary.cloud.cloud_name') ?: 
                 config('filesystems.disks.cloudinary.cloud_name') ?: 
                 env('CLOUDINARY_CLOUD_NAME');
    
    echo "Cloud Name: {$cloudName}\n\n";
} catch (\Exception $e) {
    echo "Error initializing Cloudinary: {$e->getMessage()}\n";
    exit(1);
}

echo "This script helps you upload local images to Cloudinary with paths matching your database.\n\n";

// Get all courses
$courses = CourseDescription::all();

echo "Found " . count($courses) . " courses in database\n\n";

// Function to check if image exists in Cloudinary
function doesImageExistInCloudinary($cloudName, $path) {
    $url = "https://res.cloudinary.com/{$cloudName}/image/upload/{$path}";
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return $responseCode == 200;
}

foreach ($courses as $course) {
    $imagePath = $course->getRawOriginal('image_url');
    
    echo "Course ID: {$course->id} - {$course->title}\n";
    echo "  Image path in DB: {$imagePath}\n";
    
    // Check if image exists in Cloudinary
    $exists = doesImageExistInCloudinary($cloudName, $imagePath);
    echo "  Image in Cloudinary: " . ($exists ? "✓ Yes" : "✗ No") . "\n";
    
    if (!$exists) {
        // Try to find a local file that could be uploaded
        $localPaths = [
            public_path("storage/{$imagePath}"),
            storage_path("app/public/{$imagePath}"),
            base_path($imagePath),
        ];
        
        $localFile = null;
        foreach ($localPaths as $path) {
            if (File::exists($path)) {
                $localFile = $path;
                break;
            }
        }
        
        if ($localFile) {
            echo "  Found local file: {$localFile}\n";
            echo "  To upload this file to Cloudinary with the correct path:\n";
            echo "  1. Use the Cloudinary dashboard: https://console.cloudinary.com/\n";
            echo "  2. Upload the file to the 'courses' folder\n";
            echo "  3. Name it exactly: " . basename($imagePath) . "\n";
        } else {
            echo "  ✗ No local file found\n";
            echo "  You need to manually upload an image to Cloudinary at path: {$imagePath}\n";
        }
    }
    
    echo "\n";
}

echo "=== Summary ===\n";
echo "To fix your image display issues, make sure all images exist in Cloudinary\n";
echo "with the exact paths stored in your database.\n\n";

echo "For each missing image, you should:\n";
echo "1. Find or create the image file\n";
echo "2. Upload it to Cloudinary in the 'courses' folder\n";
echo "3. Make sure the filename matches what's in your database\n";
echo "4. If you can't find the original images, consider updating the database\n";
echo "   to point to placeholder images that do exist in your Cloudinary account\n";

echo "\nDone!\n";
