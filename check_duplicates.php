<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== CHECKING DATABASE DUPLICATES ===\n";

use Illuminate\Support\Facades\DB;

// Check for duplicate courses
echo "1. Checking for duplicate courses...\n";
$duplicates = DB::table('courses')
    ->select('course_description_id', DB::raw('COUNT(*) as count'))
    ->groupBy('course_description_id')
    ->having('count', '>', 1)
    ->get();

echo "Duplicates found: " . $duplicates->count() . "\n";
foreach($duplicates as $dup) {
    echo "Course Description ID: " . $dup->course_description_id . " has " . $dup->count . " entries\n";
}

// Check total counts
echo "\n2. Checking total counts...\n";
$courseDescCount = DB::table('course_description')->count();
$courseCount = DB::table('courses')->count();

echo "Course Descriptions: " . $courseDescCount . "\n";
echo "Courses: " . $courseCount . "\n";

if ($courseCount > $courseDescCount) {
    echo "⚠️  WARNING: More courses than course descriptions - possible duplication!\n";
}

// Check specific course that shows 200%
echo "\n3. Checking specific course with title 'Complete Web Development Bootcamp 2025'...\n";
$courseDesc = DB::table('course_description')
    ->where('title', 'Complete Web Development Bootcamp 2025')
    ->first();

if ($courseDesc) {
    echo "Course Description ID: " . $courseDesc->id . "\n";
    echo "Video Count: " . $courseDesc->video_count . "\n";
    
    $courses = DB::table('courses')
        ->where('course_description_id', $courseDesc->id)
        ->get();
    
    echo "Related Courses count: " . $courses->count() . "\n";
    foreach($courses as $course) {
        echo "- Course ID: " . $course->id . ", Video Count: " . $course->video_count . "\n";
    }
}
