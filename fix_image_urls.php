<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\CourseDescription;
use App\Services\CloudinaryService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

echo "=== Fixing Image URLs in Database ===\n";

// Initialize Cloudinary Service
$cloudinaryService = app(CloudinaryService::class);

// Get all course descriptions with image URLs
$courseDescriptions = CourseDescription::all();

$fixed = 0;
$errors = 0;
$skipped = 0;

foreach ($courseDescriptions as $course) {
    echo "Processing course ID: {$course->id} - {$course->title}\n";

    $imageUrl = $course->getRawOriginal('image_url');
    if (!$imageUrl) {
        echo "  No image URL found, skipping.\n";
        $skipped++;
        continue;
    }

    echo "  Original URL: {$imageUrl}\n";

    // Check if it's already a valid URL
    if (filter_var($imageUrl, FILTER_VALIDATE_URL)) {
        // Check if it's a cloudinary URL
        if (str_contains($imageUrl, 'cloudinary.com')) {
            echo "  Already a valid Cloudinary URL, skipping.\n";
            $skipped++;
            continue;
        }
    }

    // Attempt to fix the URL
    try {
        // Clean path
        $cleanPath = $imageUrl;
        $cleanPath = ltrim($cleanPath, '/');
        $cleanPath = str_replace('storage/', '', $cleanPath);

        // Add folder prefix if needed
        if (!str_contains($cleanPath, '/')) {
            if (str_contains($cleanPath, 'course_') || str_contains($cleanPath, '.jpg') || str_contains($cleanPath, '.png')) {
                $cleanPath = 'courses/' . $cleanPath;
            }
        }

        // Get the proper Cloudinary URL
        $correctedUrl = $cloudinaryService->getOptimizedUrl($cleanPath, [
            'width' => 800,
            'height' => 450,
            'crop' => 'fill'
        ]);

        // Update in database only if we got a valid URL back
        if (filter_var($correctedUrl, FILTER_VALIDATE_URL) && str_contains($correctedUrl, 'cloudinary.com')) {
            DB::table('course_description')
                ->where('id', $course->id)
                ->update(['image_url' => $correctedUrl]);

            echo "  ✅ Fixed URL: {$correctedUrl}\n";
            $fixed++;
        } else {
            echo "  ❌ Failed to generate valid Cloudinary URL: {$correctedUrl}\n";
            $errors++;
        }
    } catch (Exception $e) {
        echo "  ❌ Error: " . $e->getMessage() . "\n";
        $errors++;
    }

    echo "\n";
}

echo "=== Summary ===\n";
echo "Total courses processed: " . count($courseDescriptions) . "\n";
echo "Fixed URLs: {$fixed}\n";
echo "Skipped (already valid): {$skipped}\n";
echo "Errors: {$errors}\n";
echo "Done!\n";
