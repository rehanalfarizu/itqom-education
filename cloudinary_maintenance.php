<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\CloudinaryService;
use Illuminate\Support\Facades\DB;

echo "=== CLOUDINARY MAINTENANCE & CLEANUP ===\n\n";

$cloudinaryService = app(CloudinaryService::class);

echo "1. Check files in livewire-tmp folder:\n";
try {
    $result = $cloudinaryService->listFiles('livewire-tmp');
    $files = $result['resources'] ?? [];

    echo "   Found " . count($files) . " files in livewire-tmp\n";

    if(count($files) > 0) {
        echo "\n2. Old files that can be cleaned up (older than 24 hours):\n";
        $oldFiles = [];
        $now = new DateTime();

        foreach($files as $file) {
            $createdAt = new DateTime($file->created_at);
            $hoursDiff = $now->diff($createdAt)->h + ($now->diff($createdAt)->days * 24);

            if($hoursDiff > 24) {
                $oldFiles[] = $file;
                echo "   - {$file->public_id} (created {$hoursDiff} hours ago)\n";
            }
        }

        if(count($oldFiles) > 0) {
            echo "\n   ⚠️ Consider cleaning up " . count($oldFiles) . " old files\n";
        } else {
            echo "   ✅ No old files to cleanup\n";
        }
    } else {
        echo "   ✅ Livewire-tmp folder is clean!\n";
    }

} catch(\Exception $e) {
    echo "   Error: " . $e->getMessage() . "\n";
}

echo "\n3. Check files in courses folder:\n";
try {
    $result = $cloudinaryService->listFiles('courses');
    $files = $result['resources'] ?? [];

    echo "   Found " . count($files) . " files in courses folder\n";

    if(count($files) > 0) {
        echo "   Recent files:\n";
        $recentFiles = array_slice($files, 0, 5);
        foreach($recentFiles as $file) {
            echo "   - {$file->public_id}\n";
        }
    }

} catch(\Exception $e) {
    echo "   Error: " . $e->getMessage() . "\n";
}

echo "\n4. Database consistency check:\n";
try {
    $courses = DB::table('course_description')->get();
    $inconsistent = 0;

    foreach($courses as $course) {
        $imageUrl = $course->image_url;
        if(strpos($imageUrl, 'livewire-tmp/') !== false) {
            $inconsistent++;
            echo "   ⚠️ Course {$course->id}: Still uses livewire-tmp path\n";
        }
    }

    if($inconsistent == 0) {
        echo "   ✅ All courses use proper paths\n";
    } else {
        echo "   ❌ Found {$inconsistent} courses with inconsistent paths\n";
    }

} catch(\Exception $e) {
    echo "   Error: " . $e->getMessage() . "\n";
}

echo "\n=== MAINTENANCE COMPLETED ===\n";
echo "\nRun this script regularly to monitor your Cloudinary usage!\n";
