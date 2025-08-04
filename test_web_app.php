<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\CourseDescription;

echo "=== WEB APPLICATION TEST ===\n\n";

try {
    echo "1. Test Model CourseDescription:\n";
    
    // Test if model works
    $courseCount = CourseDescription::count();
    echo "   ✓ Total courses in database: {$courseCount}\n";
    
    if($courseCount > 0) {
        echo "\n2. Sample Course Data:\n";
        $courses = CourseDescription::limit(3)->get();
        
        foreach($courses as $course) {
            echo "   Course ID: {$course->id}\n";
            echo "   Title: {$course->title}\n";
            
            // Test image URL generation
            try {
                if(method_exists($course, 'getImageUrlAttribute')) {
                    $imageUrl = $course->image_url;
                    echo "   Image URL: {$imageUrl}\n";
                } elseif(method_exists($course, 'getImageAttribute')) {
                    $image = $course->image;
                    echo "   Image: {$image}\n";
                } else {
                    echo "   Image: No image accessor found\n";
                }
            } catch(\Exception $e) {
                echo "   Image Error: " . $e->getMessage() . "\n";
            }
            echo "   ---\n";
        }
    }
    
    echo "\n3. Test Routes (simulate):\n";
    echo "   ✓ Application bootstrap successful\n";
    echo "   ✓ Database connection working\n";
    echo "   ✓ Model loading successful\n";
    
} catch(\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== TEST COMPLETED ===\n";
