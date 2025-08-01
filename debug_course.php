<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== DEBUGGING COURSE ID 2 ===\n";

try {
    // Check if CourseDescription with ID 2 exists
    $course = App\Models\CourseDescription::find(2);
    
    if ($course) {
        echo "✅ Course ID 2 found:\n";
        echo "Title: " . $course->title . "\n";
        echo "Table: " . $course->getTable() . "\n";
    } else {
        echo "❌ Course ID 2 NOT found\n";
        echo "Available courses:\n";
        $courses = App\Models\CourseDescription::select('id', 'title')->get();
        foreach ($courses as $c) {
            echo "- ID: {$c->id}, Title: {$c->title}\n";
        }
    }
    
    // Check CourseContent for ID 2
    echo "\n=== CHECKING COURSE CONTENT ===\n";
    $content = App\Models\CourseContent::where('course_description_id', 2)->first();
    if ($content) {
        echo "✅ Course content found for ID 2\n";
        echo "Slug: " . $content->slug . "\n";
        echo "Materials count: " . (is_array($content->materials) ? count($content->materials) : 'Not array') . "\n";
    } else {
        echo "❌ No course content found for ID 2\n";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
