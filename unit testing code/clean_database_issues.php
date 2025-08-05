<?php

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\CourseDescription;

echo "=== CLEANING DATABASE UPLOAD FAILURES ===\n\n";

// Temukan semua course dengan "Upload failed"
$failedCourses = CourseDescription::where('image_url', 'like', '%Upload failed%')->get();

echo "Found " . $failedCourses->count() . " courses with upload failures:\n\n";

foreach ($failedCourses as $course) {
    echo "Course {$course->id}: '{$course->title}'\n";
    echo "  Current: {$course->image_url}\n";
    
    // Set ke default image atau hapus
    $course->image_url = null; // Atau set ke path default image
    $course->save();
    
    echo "  ✅ Cleaned (set to null for default handling)\n\n";
}

// Juga bersihkan duplikasi transformasi di database
echo "=== CLEANING TRANSFORMATION DUPLICATIONS ===\n\n";

$duplicatedCourses = CourseDescription::where('image_url', 'like', '%w_800,h_450,c_fill,q_auto,f_auto/w_800,h_450,c_fill,q_auto,f_auto/%')->get();

echo "Found " . $duplicatedCourses->count() . " courses with transformation duplications:\n\n";

foreach ($duplicatedCourses as $course) {
    echo "Course {$course->id}: '{$course->title}'\n";
    echo "  Old: {$course->image_url}\n";
    
    // Hapus duplikasi
    $cleanUrl = str_replace(
        'w_800,h_450,c_fill,q_auto,f_auto/w_800,h_450,c_fill,q_auto,f_auto/',
        'w_800,h_450,c_fill,q_auto,f_auto/',
        $course->image_url
    );
    
    $course->image_url = $cleanUrl;
    $course->save();
    
    echo "  New: {$cleanUrl}\n";
    echo "  ✅ Fixed duplication\n\n";
}

echo "✅ Database cleanup completed!\n";
