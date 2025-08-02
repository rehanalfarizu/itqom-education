<?php

use Illuminate\Support\Facades\DB;

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== CHECKING PROGRESS TABLE ===\n";

try {
    // Check if progress table exists
    $tables = DB::select("SHOW TABLES LIKE 'progress'");
    if (empty($tables)) {
        echo "Table 'progress' does not exist\n";
    } else {
        echo "Table 'progress' exists, checking data...\n";
        
        $progressData = DB::table('progress')->get();
        echo "Found " . $progressData->count() . " records in progress table:\n";
        
        foreach($progressData as $progress) {
            echo "ID: " . ($progress->id ?? 'NULL') . 
                 ", User ID: " . ($progress->user_id ?? 'NULL') . 
                 ", Completed: " . ($progress->completed_modules ?? 'NULL') . 
                 ", Total: " . ($progress->total_modules ?? 'NULL') . 
                 ", Created: " . ($progress->created_at ?? 'NULL') . "\n";
        }
    }
} catch (\Exception $e) {
    echo "Error checking progress table: " . $e->getMessage() . "\n";
}

// Check user_courses progress
echo "\n=== CHECKING USER_COURSES PROGRESS ===\n";
try {
    $userCourses = DB::table('user_courses')->get();
    echo "Found " . $userCourses->count() . " user courses:\n";
    
    foreach($userCourses as $uc) {
        echo "User: " . ($uc->user_id ?? 'NULL') . 
             ", Course: " . ($uc->course_id ?? 'NULL') . 
             ", Progress: " . ($uc->progress_percentage ?? 'NULL') . "%\n";
    }
} catch (\Exception $e) {
    echo "Error checking user_courses: " . $e->getMessage() . "\n";
}

// Check frontend calculation from CourseContentController
echo "\n=== CHECKING COURSECONTENTCONTROLLER DATA ===\n";
try {
    $courseDescriptions = DB::table('course_descriptions')->get();
    echo "Found " . $courseDescriptions->count() . " course descriptions:\n";
    
    foreach($courseDescriptions as $cd) {
        $contentsCount = DB::table('course_contents')
            ->where('course_description_id', $cd->id)
            ->count();
            
        echo "Course '{$cd->title}': Total Contents = {$contentsCount}\n";
        
        // Simulate what frontend might be calculating
        if ($contentsCount > 0) {
            $completedModules = 2; // This might come from somewhere
            $percentage = ($completedModules / $contentsCount) * 100;
            echo "  Simulated calculation: {$completedModules}/{$contentsCount} = {$percentage}%\n";
        }
    }
} catch (\Exception $e) {
    echo "Error checking course data: " . $e->getMessage() . "\n";
}

?>
