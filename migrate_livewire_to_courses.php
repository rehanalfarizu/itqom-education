<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\CourseDescription;
use App\Services\CloudinaryService;
use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Admin\AdminApi;
use Cloudinary\Api\Upload\UploadApi;

echo "=== MIGRATE LIVEWIRE-TMP TO COURSES FOLDER ===\n\n";

try {
    $cloudinaryService = app(CloudinaryService::class);
    $adminApi = new AdminApi();
    $uploadApi = new UploadApi();
    $adminApi = new AdminApi();

    echo "1. Ambil semua file dari livewire-tmp:\n";
    $livewireFiles = $adminApi->assets(['type' => 'upload', 'prefix' => 'livewire-tmp']);

    if(isset($livewireFiles['resources']) && count($livewireFiles['resources']) > 0) {
        echo "   Found " . count($livewireFiles['resources']) . " files in livewire-tmp\n\n";

        $migratedFiles = [];

        foreach($livewireFiles['resources'] as $file) {
            $oldPublicId = $file['public_id'];
            $newPublicId = 'courses/' . basename($oldPublicId);

            // Extract original filename if possible
            if(strpos($oldPublicId, '-meta') !== false) {
                $metaPart = substr($oldPublicId, strpos($oldPublicId, '-meta') + 5);
                $decodedName = base64_decode(str_replace(['-', '_'], ['+', '/'], $metaPart));
                if($decodedName && !empty($decodedName)) {
                    $newPublicId = 'courses/' . $decodedName;
                }
            try {
                echo "   Migrating: {$oldPublicId} -> {$newPublicId}\n";

                // Rename/move file to courses folder using upload API
                $result = $uploadApi->upload($file['secure_url'], [
                    'public_id' => $newPublicId,
                    'overwrite' => true
                ]);

                if($result && isset($result['public_id'])) {
                    // Delete the old file
                    $adminApi->deleteAssets([$oldPublicId]);
                    
                    $migratedFiles[$oldPublicId] = $result['public_id'];
                    echo "     ✅ Success: {$result['public_id']}\n";
                } else {
                    echo "     ❌ Failed to migrate\n";
                }
            } catch(\Exception $e) {
                echo "     ❌ Error: " . $e->getMessage() . "\n";
            }
            }

            echo "\n";
        }

        echo "\n2. Update database with new paths:\n";
        $courses = CourseDescription::all();

        foreach($courses as $course) {
            $currentImageUrl = $course->image_url ?? '';

            // Extract filename from current path
            $filename = basename($currentImageUrl);

            // Look for matching migrated file
            $found = false;
            foreach($migratedFiles as $newPath) {
                if(strpos($newPath, $filename) !== false ||
                   strpos($newPath, pathinfo($filename, PATHINFO_FILENAME)) !== false) {

                    echo "   Updating Course {$course->id}: {$currentImageUrl} -> {$newPath}\n";
                    $course->update(['image_url' => $newPath]);
                    $found = true;
                    break;
                }
            }

            if(!$found) {
                echo "   ⚠️ Course {$course->id}: No matching file found for {$filename}\n";
            }
        }

    } else {
        echo "   No files found in livewire-tmp\n";
    }

    echo "\n=== MIGRATION COMPLETED ===\n";

} catch(\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

echo "\nDone!\n";
