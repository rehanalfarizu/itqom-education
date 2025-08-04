<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DEBUG UPLOAD PROCESS ===\n\n";

try {
    // Test 1: CloudinaryService method check
    echo "1. CloudinaryService methods available:\n";
    $cloudinaryService = app(\App\Services\CloudinaryService::class);

    $methods = get_class_methods($cloudinaryService);
    foreach(['uploadImageWithPublicId', 'uploadToCloudinaryWithPublicId', 'shouldUseCloudinary'] as $method) {
        $exists = method_exists($cloudinaryService, $method) ? '✓' : '❌';
        echo "   {$exists} {$method}\n";
    }

    echo "\n2. Test shouldUseCloudinary:\n";
    $shouldUse = app()->environment('production');
    echo "   Should use Cloudinary: " . ($shouldUse ? 'YES' : 'NO') . "\n";
    echo "   Environment: " . app()->environment() . "\n";

    echo "\n3. Test URL generation (courses folder):\n";
    $testUrls = [
        'courses/test.jpg',
        'livewire-tmp/test.jpg',
        'courses/course_123.jpg'
    ];

    foreach($testUrls as $path) {
        $url = $cloudinaryService->getOptimizedUrl($path);
        echo "   '{$path}' -> {$url}\n";
    }

    echo "\n4. Check Filament form fields:\n";
    echo "   Form should use: uploadImageWithPublicId()\n";
    echo "   Should save to database: public_id from Cloudinary\n";
    echo "   Should return: courses/filename\n";

} catch(\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== DEBUG COMPLETED ===\n";
