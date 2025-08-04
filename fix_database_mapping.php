<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\CloudinaryService;
use App\Models\CourseDescription;

echo "=== FIX DATABASE DENGAN FILE CLOUDINARY YANG ADA ===\n\n";

$cloudinaryService = app(CloudinaryService::class);

try {
    // Mapping manual berdasarkan screenshot
    $mapping = [
        1 => 'd0jiiAf5BbAPb0ADJTiTd12cgeSh3y-cat-chef', // Course ID 1 - Complete Web Development
        2 => 'react-native', // Course ID 2 - React Native
        3 => 'ui-ux-design'  // Course ID 3 - UI/UX Design
    ];

    echo "1. Mapping yang akan diterapkan:\n";
    foreach($mapping as $courseId => $publicId) {
        echo "   Course ID {$courseId} -> {$publicId}\n";
    }

    echo "\n2. Update database:\n";
    foreach($mapping as $courseId => $publicId) {
        $course = CourseDescription::find($courseId);
        if($course) {
            $oldImageUrl = $course->image_url;

            // Pastikan public_id sudah dalam format courses/
            if(strpos($publicId, 'courses/') !== 0) {
                $newImageUrl = 'courses/' . $publicId;
            } else {
                $newImageUrl = $publicId;
            }

            $course->image_url = $newImageUrl;
            $course->save();

            echo "   ✓ Course ID {$courseId}: '{$oldImageUrl}' -> '{$newImageUrl}'\n";

            // Test URL generation
            $testUrl = $cloudinaryService->getOptimizedUrl($newImageUrl);
            echo "     Test URL: {$testUrl}\n\n";
        } else {
            echo "   ❌ Course ID {$courseId} tidak ditemukan\n";
        }
    }

    echo "3. Verifikasi hasil:\n";
    $courses = CourseDescription::all();
    foreach($courses as $course) {
        echo "   Course ID {$course->id}: {$course->title}\n";
        echo "     Image URL: {$course->image_url}\n";

        // Generate final URL
        $finalUrl = $cloudinaryService->getOptimizedUrl($course->image_url, [
            'width' => 800,
            'height' => 450,
            'crop' => 'fill',
            'quality' => 'auto',
            'fetch_format' => 'auto'
        ]);
        echo "     Final URL: {$finalUrl}\n\n";
    }

} catch(\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

echo "=== PERBAIKAN SELESAI ===\n";
