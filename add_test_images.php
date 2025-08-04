<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\CourseDescription;
use Illuminate\Support\Facades\DB;

echo "=== Adding Test Image URLs to Courses ===\n";

// Sample Cloudinary URLs - these should be valid for your account
$sampleUrls = [
    'https://res.cloudinary.com/hltd67bzw/image/upload/w_800,h_450,c_fill,q_auto,f_auto/itqom-platform/tests/course_sample_1.jpg',
    'https://res.cloudinary.com/hltd67bzw/image/upload/w_800,h_450,c_fill,q_auto,f_auto/itqom-platform/tests/course_sample_2.jpg',
    'https://res.cloudinary.com/hltd67bzw/image/upload/w_800,h_450,c_fill,q_auto,f_auto/itqom-platform/tests/course_sample_3.jpg'
];

// Get all course descriptions
$courses = CourseDescription::all();

echo "Found " . count($courses) . " courses\n";

$updated = 0;
foreach ($courses as $index => $course) {
    $urlIndex = $index % count($sampleUrls);
    $imageUrl = $sampleUrls[$urlIndex];

    echo "Updating course ID: {$course->id} - {$course->title}\n";
    echo "  Setting image URL: {$imageUrl}\n";

    // Update using DB query untuk bypass accessor/mutator
    DB::table('course_description')
        ->where('id', $course->id)
        ->update(['image_url' => $imageUrl]);

    $updated++;
}

echo "\n=== Summary ===\n";
echo "Total courses updated: {$updated}\n";
echo "Done!\n";
