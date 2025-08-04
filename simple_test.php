<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== SIMPLE CLOUDINARY TEST ===\n\n";

try {
    // Test 1: Environment Check
    echo "1. Environment Check:\n";
    $cloudName = env('CLOUDINARY_CLOUD_NAME');
    echo "   Cloud Name: " . ($cloudName ?: 'Not Set') . "\n";
    echo "   Environment: " . app()->environment() . "\n";
    
    // Test 2: Manual URL Generation
    echo "\n2. Manual URL Generation:\n";
    $testImage = 'courses/test-image.jpg';
    $baseUrl = "https://res.cloudinary.com/{$cloudName}/image/upload";
    $transformations = "q_auto,f_auto,w_800,h_450,c_fill";
    $fullUrl = "{$baseUrl}/{$transformations}/{$testImage}";
    
    echo "   Base URL: {$baseUrl}\n";
    echo "   Test Image: {$testImage}\n";
    echo "   Full URL: {$fullUrl}\n";
    
    // Test 3: Check Database
    echo "\n3. Database Sample:\n";
    try {
        $courses = \App\Models\CourseDescription::select('id', 'title', 'image')
            ->whereNotNull('image')
            ->take(3)
            ->get();
            
        foreach ($courses as $course) {
            echo "   ID: {$course->id} | Title: " . substr($course->title, 0, 30) . "... | Image: {$course->image}\n";
        }
    } catch (\Exception $e) {
        echo "   Database Error: " . $e->getMessage() . "\n";
    }
    
    // Test 4: Test Image URL Construction
    echo "\n4. Image URL Construction Test:\n";
    $sampleImages = [
        'courses/course_123.jpg',
        'courses/sample.png',
        'livewire-tmp/abc123.jpg' // This should be converted
    ];
    
    foreach ($sampleImages as $image) {
        // Simulate the logic that should happen
        if (str_contains($image, 'livewire-tmp/')) {
            $converted = 'courses/' . basename($image);
            echo "   '{$image}' -> '{$converted}' (converted)\n";
        } else {
            echo "   '{$image}' -> '{$image}' (unchanged)\n";
        }
        
        $url = "{$baseUrl}/q_auto,f_auto/{$image}";
        echo "     Final URL: {$url}\n";
    }
    
    echo "\n✓ Test completed successfully!\n";
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
