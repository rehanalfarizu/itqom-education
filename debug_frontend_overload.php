<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== CHECKING FRONTEND OVERLOAD ISSUE ===\n";

// Check UserCourse data
echo "\n1. USER COURSES DATA:\n";
$userCourses = App\Models\UserCourse::all();
foreach($userCourses as $uc) {
    echo "User: {$uc->user_id}, Course: {$uc->course_id}, Progress: {$uc->progress}%\n";
}

// Check CourseContent data  
echo "\n2. COURSE CONTENT DATA:\n";
$courseContents = App\Models\CourseContent::all();
foreach($courseContents as $cc) {
    echo "Course Description: {$cc->course_description_id}, Title: {$cc->title}\n";
}

// Check if there's progress calculation logic
echo "\n3. USERS PROFILE DATA:\n";
try {
    $profiles = DB::table('users_profile')->get();
    foreach($profiles as $profile) {
        echo "User: {$profile->user_id}, Progress data: " . json_encode($profile) . "\n";
    }
} catch (Exception $e) {
    echo "Error accessing users_profile: " . $e->getMessage() . "\n";
}

// Check DashboardController logic issue
echo "\n4. SIMULATING DASHBOARD CALCULATION:\n";
$user = App\Models\User::first();
if ($user) {
    echo "User ID: {$user->id}\n";
    
    // Check if there's a progress method or relation
    if (method_exists($user, 'progress')) {
        $progress = $user->progress()->first();
        if ($progress) {
            echo "Progress: {$progress->completed_modules}/{$progress->total_modules}\n";
            $percentage = $progress->total_modules > 0 ? ($progress->completed_modules / $progress->total_modules) * 100 : 0;
            echo "Percentage: {$percentage}%\n";
        }
    }
    
    // Check user courses count
    $userCoursesCount = $user->userCourses()->count();
    echo "User courses count: {$userCoursesCount}\n";
}
