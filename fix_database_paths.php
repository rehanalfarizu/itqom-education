<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\CourseDescription;

echo "=== FIX DATABASE IMAGE PATHS ===\n\n";

try {
    $courses = CourseDescription::all();

    // Manual mapping berdasarkan files yang ada di Cloudinary
    $correctMapping = [
        1 => 'courses/5012b739de3b99f57f0a96c8625bc188.jpg', // Course 1
        2 => 'courses/Blank diagram (1).png',                // Course 2
        3 => 'courses/test.jpg'                             // Course 3
    ];

    foreach ($courses as $course) {
        $oldImageUrl = $course->image_url;

        if (isset($correctMapping[$course->id])) {
            $newImageUrl = $correctMapping[$course->id];

            echo "Course {$course->id}: '{$course->title}'\n";
            echo "  Old: $oldImageUrl\n";
            echo "  New: $newImageUrl\n";

            $course->update(['image_url' => $newImageUrl]);
            echo "  ✅ Updated!\n\n";
        }
    }

    echo "✅ Database paths fixed!\n";

} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== DONE ===\n";
