<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\CourseDescription;
use App\Services\CloudinaryService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

echo "=== Cloudinary URL Consistency Fix ===\n\n";

// Initialize Cloudinary Service
$cloudinaryService = app(CloudinaryService::class);
$cloudName = config('cloudinary.cloud.cloud_name');

echo "Cloud Name: {$cloudName}\n";

// Get all courses
$courses = CourseDescription::all();

echo "Found " . count($courses) . " courses in database\n\n";

$fixed = 0;
$errors = 0;
$skipped = 0;

foreach ($courses as $course) {
    echo "Processing course ID: {$course->id} - {$course->title}\n";
    
    $imageUrl = $course->getRawOriginal('image_url');
    if (!$imageUrl) {
        echo "  No image URL found, skipping.\n";
        $skipped++;
        continue;
    }
    
    echo "  Original URL: {$imageUrl}\n";
    
    // Standardize all paths to use the 'courses' folder
    // Extract filename without folder
    $fileName = basename($imageUrl);
    
    // Create new path in the courses folder
    $newPath = 'courses/' . $fileName;
    
    // Only update if it's different from current path
    if ($imageUrl != $newPath) {
        // Update database with standardized path
        try {
            DB::table('course_description')
                ->where('id', $course->id)
                ->update(['image_url' => $newPath]);
            
            echo "  ✓ Updated to: {$newPath}\n";
            $fixed++;
        } catch (\Exception $e) {
            echo "  ✗ Error updating: {$e->getMessage()}\n";
            $errors++;
        }
    } else {
        echo "  ✓ Already using correct folder, skipping.\n";
        $skipped++;
    }
    
    echo "\n";
}

echo "=== Summary ===\n";
echo "Total courses processed: " . count($courses) . "\n";
echo "Fixed URLs: {$fixed}\n";
echo "Skipped: {$skipped}\n";
echo "Errors: {$errors}\n";

echo "\n=== Fixing CloudinaryService ===\n";
echo "To fix the 'Array to string conversion' error in CloudinaryService.php, update the following:\n\n";

echo "In the getOptimizedUrl method, replace:\n";
echo "return \$this->cloudinary->image(\$publicId)->toUrl(\$options);\n\n";

echo "With:\n";
echo "// Create an array of transformations for Cloudinary API compatibility\n";
echo "return \$this->cloudinary->image(\$publicId)->toUrl(['transformation' => \$options]);\n\n";

echo "This modification will correctly format the transformation options for the Cloudinary SDK.\n\n";

echo "=== Additional Steps Needed ===\n";
echo "1. Upload the same images to both 'courses' and 'images' folders in Cloudinary\n";
echo "2. Or modify frontend code to use the 'courses' folder path\n";
echo "3. Clear browser cache after making these changes\n\n";

echo "Done!\n";
