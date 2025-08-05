<?php

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\CourseDescription;

echo "=== REVERTING TO LIVEWIRE-TMP PATHS ===\n\n";

// Get all courses with courses/ paths
$courses = CourseDescription::whereNotNull('image_url')
    ->where('image_url', 'like', 'courses/%')
    ->get();

echo "Found " . $courses->count() . " courses to revert to livewire-tmp/\n\n";

$updated = 0;

foreach ($courses as $course) {
    $oldPath = $course->image_url;
    
    // Convert courses/ back to livewire-tmp/
    $newPath = str_replace('courses/', 'livewire-tmp/', $oldPath);
    
    echo "Course {$course->id}: '{$course->title}'\n";
    echo "  Old: {$oldPath}\n";
    echo "  New: {$newPath}\n";
    
    // Update the path
    $course->image_url = $newPath;
    $course->save();
    
    echo "  ✅ Reverted!\n\n";
    $updated++;
}

echo "✅ Database paths reverted to livewire-tmp/! Updated: {$updated} courses\n";
echo "\n=== DONE ===\n";
