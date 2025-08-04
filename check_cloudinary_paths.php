<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\CloudinaryService;
use Illuminate\Support\Facades\DB;

echo "=== Cloudinary Path Check ===\n";

// Get all image URLs from database
$courseUrls = DB::table('course_description')->select('id', 'title', 'image_url')->get();

echo "Found " . count($courseUrls) . " course images in database\n\n";

// Extract folders from URLs
$folders = [];
foreach ($courseUrls as $course) {
    echo "Course ID {$course->id}: {$course->title}\n";
    echo "  URL: {$course->image_url}\n";
    
    if (preg_match('/cloudinary\.com\/[^\/]+\/image\/upload\/[^\/]+\/([^\/]+)\//', $course->image_url, $matches)) {
        $folder = $matches[1];
        $folders[$folder] = ($folders[$folder] ?? 0) + 1;
        echo "  Folder: {$folder}\n";
    } else {
        echo "  No folder found in URL\n";
    }
    
    echo "\n";
}

echo "=== Folder Summary ===\n";
foreach ($folders as $folder => $count) {
    echo "Folder '{$folder}' used in {$count} URLs\n";
}

echo "\n=== Frontend URLs Check ===\n";
echo "Test Cloudinary path construction:\n";

$test_path = "courses/course_example.jpg";
$cloud_name = config('cloudinary.cloud.cloud_name');

// Create URL
$url = "https://res.cloudinary.com/{$cloud_name}/image/upload/w_800,h_450,c_fill,q_auto,f_auto/{$test_path}";
echo "Example URL: {$url}\n";

echo "\nDone!\n";
