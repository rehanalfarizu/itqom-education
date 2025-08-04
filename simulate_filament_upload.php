<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\CloudinaryService;
use Illuminate\Support\Facades\DB;

echo "=== SIMULATE FILAMENT UPLOAD ===\n\n";

try {
    $cloudinaryService = app(CloudinaryService::class);

    echo "1. Test upload process simulation:\n";

    // Simulate what happens in Filament FileUpload afterStateUpdated
    $testFileName = 'test-course-image.jpg';
    $testFileContent = 'fake-image-content'; // In real scenario, this would be UploadedFile

    echo "   Input filename: {$testFileName}\n";

    // Test public_id generation
    $timestamp = time();
    $randomString = substr(md5(rand()), 0, 8);
    $publicId = "course_{$timestamp}_{$randomString}";

    echo "   Generated public_id: {$publicId}\n";
    echo "   Target folder: courses/\n";
    echo "   Expected final public_id: courses/{$publicId}\n";

    // Test URL generation with this public_id
    $expectedUrl = $cloudinaryService->getOptimizedUrl("courses/{$publicId}");
    echo "   Expected URL: {$expectedUrl}\n";

    echo "\n2. Test what should be saved to database:\n";
    echo "   image_url field should contain: courses/{$publicId}\n";
    echo "   NOT: livewire-tmp/...\n";

    echo "\n3. Test current database content:\n";
    $courses = DB::table('course_description')->select('id', 'title', 'image_url')->get();

    foreach($courses as $course) {
        echo "   Course {$course->id}: {$course->title}\n";
        echo "     Current image_url: {$course->image_url}\n";

        if(strpos($course->image_url, 'livewire-tmp') !== false) {
            echo "     ❌ Problem: Still using livewire-tmp path!\n";
        } elseif(strpos($course->image_url, 'courses/') !== false) {
            echo "     ✅ Good: Using courses path\n";
        }
        echo "\n";
    }

    echo "4. Recommendation:\n";
    echo "   - Fix uploadImageWithPublicId to return 'courses/...' not 'livewire-tmp/...'\n";
    echo "   - Fix Array to string conversion in getOptimizedUrl\n";
    echo "   - Ensure Filament saves the correct path to database\n";

} catch(\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== SIMULATION COMPLETED ===\n";
