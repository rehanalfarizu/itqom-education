<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== CHECKING PROGRESS DATA ===\n";

use Illuminate\Support\Facades\DB;

// Check user progress data
echo "1. Checking user progress data...\n";
$progressData = DB::table('user_profiles')->get();

echo "Found " . $progressData->count() . " user profiles\n";
foreach($progressData as $profile) {
    echo "User ID: " . $profile->user_id . ", Progress: " . ($profile->progress ?? 'NULL') . "\n";
}

// Check if there's a separate progress table
echo "\n2. Checking for progress table...\n";
try {
    $progressTable = DB::table('progress')->get();
    echo "Progress table exists with " . $progressTable->count() . " records\n";
    foreach($progressTable as $progress) {
        echo "Progress: completed_modules=" . ($progress->completed_modules ?? 'NULL') . 
             ", total_modules=" . ($progress->total_modules ?? 'NULL') . "\n";
    }
} catch (Exception $e) {
    echo "No progress table found or error: " . $e->getMessage() . "\n";
}

// Check user courses for progress data
echo "\n3. Checking user courses...\n";
$userCourses = DB::table('user_courses')->get();
echo "Found " . $userCourses->count() . " user courses\n";
foreach($userCourses as $uc) {
    echo "User: " . $uc->user_id . ", Course: " . $uc->course_id . 
         ", Progress: " . ($uc->progress_percentage ?? 'NULL') . "%\n";
}
