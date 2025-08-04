<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Course Images Database Check ===\n\n";

// Get raw data from database
$courses = DB::table('course_description')->select(['id', 'title', 'image_url'])->get();

echo "Total course descriptions in database: " . count($courses) . "\n\n";

if (count($courses) > 0) {
    foreach ($courses as $course) {
        echo "Course ID: {$course->id}\n";
        echo "Title: {$course->title}\n";
        echo "Image URL: " . ($course->image_url ?: 'NOT SET') . "\n";
        echo "----------------------------------------\n";
    }
}

// Check for courses in related tables
echo "\n=== Checking Course table ===\n";
$regularCourses = DB::table('courses')->select(['id', 'title', 'image'])->get();
echo "Total courses in courses table: " . count($regularCourses) . "\n\n";

if (count($regularCourses) > 0) {
    foreach ($regularCourses as $course) {
        echo "Course ID: {$course->id}\n";
        echo "Title: {$course->title}\n";
        echo "Image URL: " . ($course->image ?: 'NOT SET') . "\n";
        echo "----------------------------------------\n";
    }
}

echo "\nDone!\n";
