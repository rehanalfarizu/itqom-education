<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\CourseDescription;
use Illuminate\Support\Facades\DB;

echo "=== Course Images Database Check ===\n";

// Get raw data from database
$courses = DB::table('course_description')->select(['id', 'title', 'image_url'])->get();

echo "Total courses in database: " . count($courses) . "\n\n";

foreach ($courses as $course) {
    echo "Course ID: {$course->id} - {$course->title}\n";
    echo "  Image URL: " . ($course->image_url ?: 'NOT SET') . "\n\n";
}

// Check for courses in related tables
echo "=== Checking Course table ===\n";
$regularCourses = DB::table('courses')->select(['id', 'title', 'image'])->get();
echo "Total courses in courses table: " . count($regularCourses) . "\n\n";

foreach ($regularCourses as $course) {
    echo "Course ID: {$course->id} - {$course->title}\n";
    echo "  Image URL: " . ($course->image ?: 'NOT SET') . "\n\n";
}

echo "Done!\n";
