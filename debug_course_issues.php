<?php

/**
 * Debug script untuk course issues
 * Usage: php debug_course_issues.php
 */

// Bootstrap Laravel
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\CourseDescription;

echo "=== DEBUGGING COURSE ISSUES ===\n";

try {
    // Test database connection
    echo "1. Testing database connection...\n";
    $pdo = DB::connection()->getPdo();
    echo "   ✓ Database connected successfully\n";

    // Check tables
    echo "2. Checking database tables...\n";
    $tables = DB::select('SHOW TABLES');
    $tableNames = array_map(function($table) {
        return array_values((array)$table)[0];
    }, $tables);
    
    echo "   Available tables:\n";
    foreach($tableNames as $tableName) {
        echo "   - {$tableName}\n";
    }
    
    $hasCourseDesc = in_array('course_description', $tableNames);
    echo "\n   ✓ Course description table exists: " . ($hasCourseDesc ? 'YES' : 'NO') . "\n";

    if (!$hasCourseDesc) {
        echo "   ⚠️  course_description table not found! This is the main issue!\n";
        exit(1);
    }

    // Test model instantiation
    echo "3. Testing CourseDescription model...\n";
    
    if (!class_exists('App\Models\CourseDescription')) {
        echo "   ✗ CourseDescription class not found!\n";
        exit(1);
    }
    
    $model = new CourseDescription();
    echo "   ✓ Model instantiated successfully\n";
    
    $count = CourseDescription::count();
    echo "   ✓ Record count: {$count}\n";
    
    // Test creating a sample record if none exist
    if ($count === 0) {
        echo "   Creating sample course description...\n";
        $sample = CourseDescription::create([
            'title' => 'Sample Course',
            'tag' => 'Programming',
            'overview' => 'This is a sample course for testing',
            'price' => 100000,
            'price_discount' => 50000,
            'instructor_name' => 'Test Instructor',
            'video_count' => 10,
            'duration' => 120
        ]);
        echo "   ✓ Sample course created with ID: {$sample->id}\n";
    }

    // Test API endpoint
    echo "4. Testing API functionality...\n";
    
    // Simulate API call
    $courses = CourseDescription::all();
    $transformedCourses = $courses->map(function($courseDesc) {
        return [
            'id' => $courseDesc->id,
            'title' => $courseDesc->title,
            'instructor' => $courseDesc->instructor_name,
            'video_count' => $courseDesc->video_count . ' video',
            'duration' => $courseDesc->duration,
            'original' => number_format((float)$courseDesc->price_discount, 0, ',', '.'),
            'price' => number_format((float)$courseDesc->price, 0, ',', '.'),
            'category' => $courseDesc->tag,
            'description' => $courseDesc->title,
            'overview' => $courseDesc->overview,
        ];
    });
    
    echo "   ✓ API transformation successful\n";
    echo "   ✓ Found " . $transformedCourses->count() . " courses\n";

    echo "\n=== DEBUGGING COMPLETED SUCCESSFULLY ===\n";
    echo "The application should now work properly!\n";

} catch (\Exception $e) {
    echo "✗ Error occurred: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
