<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\CloudinaryService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Admin\AdminApi;

echo "=== Cloudinary Connection Test ===\n";

try {
    // Initialize Cloudinary service
    $cloudinaryService = app(CloudinaryService::class);
    
    echo "Cloudinary service initialized successfully\n";
    
    // Test API Connection
    echo "\nTesting API connection...\n";
    
    $cloudName = config('cloudinary.cloud.cloud_name');
    $apiKey = config('cloudinary.cloud.api_key');
    $apiSecret = config('cloudinary.cloud.api_secret');
    
    $config = Configuration::instance([
        'cloud' => [
            'cloud_name' => $cloudName,
            'api_key'    => $apiKey,
            'api_secret' => $apiSecret,
        ]
    ]);
    
    $admin = new AdminApi();
    $pingResult = $admin->ping();
    
    echo "API Connection: SUCCESS\n";
    echo "Ping Result: " . json_encode($pingResult) . "\n";
    
    // Create a test image file
    echo "\nCreating test image...\n";
    $testImagePath = __DIR__ . '/storage/test-image.jpg';
    
    // Check if test directory exists
    $testDir = __DIR__ . '/storage';
    if (!File::exists($testDir)) {
        File::makeDirectory($testDir, 0755, true);
    }
    
    // Generate a simple 100x100 red square image
    $image = imagecreatetruecolor(100, 100);
    $red = imagecolorallocate($image, 255, 0, 0);
    imagefill($image, 0, 0, $red);
    imagejpeg($image, $testImagePath);
    imagedestroy($image);
    
    echo "Test image created at: $testImagePath\n";
    
    // Create uploaded file from the test image
    $uploadedFile = new \Illuminate\Http\UploadedFile(
        $testImagePath,
        'test-image.jpg',
        'image/jpeg',
        null,
        true
    );
    
    // Perform upload test
    echo "\nTesting upload to Cloudinary...\n";
    $startTime = microtime(true);
    $uploadResult = $cloudinaryService->uploadImageHybrid($uploadedFile, 'tests');
    $endTime = microtime(true);
    
    echo "Upload completed in " . round(($endTime - $startTime), 2) . " seconds\n";
    echo "Upload result: $uploadResult\n";
    
    // Test URL generation
    echo "\nTesting URL optimization...\n";
    $optimizedUrl = $cloudinaryService->getOptimizedUrl($uploadResult, [
        'width' => 300,
        'height' => 200,
        'crop' => 'fill'
    ]);
    
    echo "Original URL: $uploadResult\n";
    echo "Optimized URL: $optimizedUrl\n";
    
    // Clean up test file
    File::delete($testImagePath);
    echo "\nTest image deleted\n";
    
    echo "\n=== Test completed successfully ===\n";
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
