<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== ANALISIS MASALAH 200% PROGRESS ===\n";

use Illuminate\Support\Facades\DB;

try {
    // 1. Cek CourseDescription
    echo "1. Checking CourseDescription data...\n";
    $courseDesc = DB::table('course_description')
        ->where('title', 'Complete Web Development Bootcamp 2025')
        ->first();
    
    if ($courseDesc) {
        echo "✅ Course Description found:\n";
        echo "   ID: " . $courseDesc->id . "\n";
        echo "   Video Count: " . ($courseDesc->video_count ?? 'NULL') . "\n";
        echo "   Duration: " . ($courseDesc->duration ?? 'NULL') . "\n";
        
        // 2. Cek CourseContent materials
        echo "\n2. Checking CourseContent materials...\n";
        $courseContent = DB::table('course_content')
            ->where('course_description_id', $courseDesc->id)
            ->first();
        
        if ($courseContent) {
            echo "✅ Course Content found:\n";
            $materials = json_decode($courseContent->materials, true);
            if (is_array($materials)) {
                echo "   Materials count: " . count($materials) . "\n";
                foreach ($materials as $i => $material) {
                    echo "   Material " . ($i + 1) . ": " . ($material['title'] ?? 'No title') . "\n";
                }
            } else {
                echo "   Materials: " . ($courseContent->materials ?? 'NULL') . "\n";
            }
        } else {
            echo "❌ No CourseContent found\n";
        }
        
        // 3. Cek UserCourse progress
        echo "\n3. Checking UserCourse progress...\n";
        $userCourses = DB::table('user_courses')
            ->where('course_id', $courseDesc->id)
            ->get();
        
        echo "Found " . $userCourses->count() . " user enrollments:\n";
        foreach ($userCourses as $uc) {
            echo "   User: " . $uc->user_id . 
                 ", Progress: " . ($uc->progress_percentage ?? 'NULL') . "%" .
                 ", Completed: " . ($uc->completed_materials ?? 'NULL') . "\n";
        }
        
    } else {
        echo "❌ Course Description not found\n";
    }
    
    // 4. Cek semua table yang mungkin menyimpan progress data
    echo "\n4. Checking all tables for progress data...\n";
    
    // Check if user_progress table exists
    try {
        $userProgress = DB::table('user_progress')->get();
        echo "✅ user_progress table found with " . $userProgress->count() . " records\n";
        foreach ($userProgress as $up) {
            echo "   User: " . ($up->user_id ?? 'NULL') . 
                 ", Completed: " . ($up->completed_modules ?? 'NULL') . 
                 ", Total: " . ($up->total_modules ?? 'NULL') . "\n";
        }
    } catch (Exception $e) {
        echo "❌ user_progress table not found\n";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
