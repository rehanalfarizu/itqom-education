<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== COMPREHENSIVE CLOUDINARY TEST ===\n\n";

try {
    // Test 1: Check Environment Variables
    echo "1. Environment Variables Check:\n";
    $cloudName = env('CLOUDINARY_CLOUD_NAME');
    $apiKey = env('CLOUDINARY_API_KEY');
    $apiSecret = env('CLOUDINARY_API_SECRET');
    $cloudinaryUrl = env('CLOUDINARY_URL');
    
    echo "   CLOUDINARY_CLOUD_NAME: " . ($cloudName ? "✓ Set" : "✗ Missing") . "\n";
    echo "   CLOUDINARY_API_KEY: " . ($apiKey ? "✓ Set" : "✗ Missing") . "\n";
    echo "   CLOUDINARY_API_SECRET: " . ($apiSecret ? "✓ Set" : "✗ Missing") . "\n";
    echo "   CLOUDINARY_URL: " . ($cloudinaryUrl ? "✓ Set" : "✗ Missing") . "\n";
    
    // Test 2: Check CloudinaryService existence
    echo "\n2. CloudinaryService Class Check:\n";
    if (class_exists('App\Services\CloudinaryService')) {
        echo "   ✓ CloudinaryService class exists\n";
        
        $service = app('App\Services\CloudinaryService');
        echo "   ✓ Service can be instantiated\n";
        
        // Test method existence
        $methods = ['getOptimizedUrl', 'uploadImage', 'isCloudinaryEnabled'];
        foreach ($methods as $method) {
            if (method_exists($service, $method)) {
                echo "   ✓ Method {$method} exists\n";
            } else {
                echo "   ✗ Method {$method} missing\n";
            }
        }
    } else {
        echo "   ✗ CloudinaryService class not found\n";
    }
    
    // Test 3: Test Database Connection and Course Images
    echo "\n3. Database and Course Images Check:\n";
    try {
        $courses = \App\Models\CourseDescription::select('id', 'image')->take(5)->get();
        echo "   ✓ Database connection successful\n";
        echo "   Found " . $courses->count() . " courses\n";
        
        foreach ($courses as $course) {
            echo "   Course ID {$course->id}: {$course->image}\n";
        }
    } catch (\Exception $e) {
        echo "   ✗ Database error: " . $e->getMessage() . "\n";
    }
    
    // Test 4: Test Cloudinary Configuration
    echo "\n4. Cloudinary Configuration Test:\n";
    try {
        \Cloudinary\Configuration\Configuration::instance([
            'cloud' => [
                'cloud_name' => $cloudName,
                'api_key' => $apiKey,
                'api_secret' => $apiSecret
            ]
        ]);
        
        $api = new \Cloudinary\Api\Admin\AdminApi();
        $result = $api->usage();
        echo "   ✓ Cloudinary API connection successful\n";
        echo "   Usage: " . json_encode($result) . "\n";
    } catch (\Exception $e) {
        echo "   ✗ Cloudinary ping failed: " . $e->getMessage() . "\n";
    }
    
    // Test 5: Test URL Generation
    echo "\n5. URL Generation Test:\n";
    $testPaths = [
        'courses/test-image.jpg',
        'courses/sample.png',
        ''
    ];
    
    foreach ($testPaths as $path) {
        $url = "https://res.cloudinary.com/{$cloudName}/image/upload/q_auto,f_auto/{$path}";
        echo "   Path: '{$path}' -> {$url}\n";
    }
    
    echo "\n=== TEST COMPLETED ===\n";
    
} catch (\Exception $e) {
    echo "FATAL ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
