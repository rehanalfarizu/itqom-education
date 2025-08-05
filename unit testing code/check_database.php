<?php

use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DATABASE STRUCTURE CHECK ===\n\n";

try {
    // Check course_description table structure
    echo "1. Course Description Table Columns:\n";
    $columns = DB::select('SHOW COLUMNS FROM course_description');
    foreach($columns as $col) {
        echo "   - {$col->Field} ({$col->Type})\n";
    }

    echo "\n2. Sample Data (first 3 rows):\n";
    $courses = DB::table('course_description')->limit(3)->get();
    foreach($courses as $course) {
        echo "   ID: {$course->id}\n";
        echo "   Title: " . ($course->title ?? 'N/A') . "\n";

        // Check different possible image column names
        $imageField = null;
        if(isset($course->image)) {
            $imageField = 'image';
        } elseif(isset($course->image_url)) {
            $imageField = 'image_url';
        } elseif(isset($course->image_path)) {
            $imageField = 'image_path';
        } elseif(isset($course->gambar)) {
            $imageField = 'gambar';
        } elseif(isset($course->foto)) {
            $imageField = 'foto';
        }

        if($imageField) {
            echo "   Image ({$imageField}): " . $course->$imageField . "\n";
        } else {
            echo "   Image: No image field found\n";
        }
        echo "   ---\n";
    }

    echo "\n3. Count total courses:\n";
    $total = DB::table('course_description')->count();
    echo "   Total courses: {$total}\n";

} catch(\Exception $e) {
    echo "Database Error: " . $e->getMessage() . "\n";
}

echo "\nDone!\n";
