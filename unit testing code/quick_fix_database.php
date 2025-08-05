<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\CourseDescription;
use Cloudinary\Api\Admin\AdminApi;

echo "=== QUICK FIX: UPDATE DATABASE DENGAN FILE YANG ADA ===\n\n";

try {
    $adminApi = new AdminApi();

    echo "1. Ambil file dari livewire-tmp:\n";
    $livewireFiles = $adminApi->assets(['type' => 'upload', 'prefix' => 'livewire-tmp']);

    if(isset($livewireFiles['resources']) && count($livewireFiles['resources']) > 0) {
        echo "   Found " . count($livewireFiles['resources']) . " files\n\n";

        // Get recent uploaded files (last 10)
        $recentFiles = array_slice($livewireFiles['resources'], 0, 10);

        echo "2. Recent files yang mungkin adalah gambar course:\n";
        foreach($recentFiles as $index => $file) {
            $publicId = $file['public_id'];
            $url = $file['secure_url'];
            $created = $file['created_at'];
            $size = $file['bytes'];

            echo "   [{$index}] {$publicId}\n";
            echo "       URL: {$url}\n";
            echo "       Created: {$created}\n";
            echo "       Size: " . number_format($size) . " bytes\n\n";
        }

        echo "3. Map dengan courses di database:\n";
        $courses = CourseDescription::all();

        // Manual mapping berdasarkan order upload
        $mapping = [
            1 => 0, // Course 1 -> file index 0 (terbaru)
            2 => 1, // Course 2 -> file index 1
            3 => 2  // Course 3 -> file index 2
        ];

        foreach($courses as $course) {
            $courseId = $course->id;

            if(isset($mapping[$courseId]) && isset($recentFiles[$mapping[$courseId]])) {
                $fileIndex = $mapping[$courseId];
                $file = $recentFiles[$fileIndex];
                $newImageUrl = $file['public_id'];

                echo "   Course {$courseId}: '{$course->title}'\n";
                echo "     Old: {$course->image_url}\n";
                echo "     New: {$newImageUrl}\n";

                // Update database
                $course->update(['image_url' => $newImageUrl]);

                echo "     ✅ Updated!\n\n";
            } else {
                echo "   Course {$courseId}: No mapping found\n\n";
            }
        }

    } else {
        echo "   No files found in livewire-tmp\n";
    }

} catch(\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "=== QUICK FIX COMPLETED ===\n";
echo "Silakan test di browser!\n";
