<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TESTING FIXED DASHBOARD CONTROLLER ===\n";

// Create a test user
$user = App\Models\User::first();
if (!$user) {
    $user = App\Models\User::create([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => bcrypt('password'),
    ]);
}

// Simulate auth
Auth::login($user);

echo "User: {$user->name} (ID: {$user->id})\n";

// Test the fixed dashboard logic
$userCourses = $user->userCourses;
$totalCourses = $userCourses->count();
$completedCourses = $userCourses->where('progress', 100)->count();

echo "User Courses: {$totalCourses}\n";
echo "Completed Courses: {$completedCourses}\n";

// Calculate modules
$totalModules = 0;
$completedModules = 0;

foreach ($userCourses as $userCourse) {
    $courseContents = $userCourse->course ? $userCourse->course->courseDescription->courseContents : collect();
    $totalModules += $courseContents->count();
    $completedModules += floor($courseContents->count() * ($userCourse->progress / 100));
}

// Fallback
if ($totalModules == 0) {
    $totalModules = 1;
    $completedModules = 0;
}

echo "Total Modules: {$totalModules}\n";
echo "Completed Modules: {$completedModules}\n";

$percentage = $totalModules > 0 ? round(($completedModules / $totalModules) * 100) : 0;
echo "Percentage: {$percentage}%\n";

echo "\nFixed response would be: {$completedModules} dari {$totalModules} materi ({$percentage}%)\n";
