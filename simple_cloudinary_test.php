<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\CloudinaryService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

echo "=== Cloudinary Connection Test (Simplified) ===\n";

try {
    // Print configuration
    $cloudName = config('cloudinary.cloud.cloud_name');
    $apiKey = config('cloudinary.cloud.api_key');
    
    echo "Cloud Name: {$cloudName}\n";
    echo "API Key: " . (strlen($apiKey) > 4 ? substr($apiKey, 0, 4) . '...' : 'MISSING') . "\n";
    echo "API Secret: " . (config('cloudinary.cloud.api_secret') ? 'SET' : 'NOT SET') . "\n\n";
    
    // Get CloudinaryService instance
    echo "Initializing CloudinaryService...\n";
    $cloudinaryService = app(CloudinaryService::class);
    echo "CloudinaryService initialized\n\n";
    
    // Create a test file to upload
    $testFile = tempnam(sys_get_temp_dir(), 'cloudinary_test');
    file_put_contents($testFile, 'Test file for Cloudinary upload');
    echo "Test file created at {$testFile}\n\n";
    
    // Create an UploadedFile instance
    $uploadedFile = new \Illuminate\Http\UploadedFile(
        $testFile, 
        'test.txt', 
        'text/plain', 
        null, 
        true
    );
    
    // Test local storage first
    echo "Testing local storage...\n";
    $localPath = Storage::disk('public')->putFile('tests', $uploadedFile);
    echo "Local storage test successful: {$localPath}\n\n";
    
    // Test Cloudinary upload
    echo "Testing Cloudinary upload (this may take a moment)...\n";
    $uploadResult = $cloudinaryService->uploadImageHybrid($uploadedFile, 'tests');
    echo "Upload result: {$uploadResult}\n\n";
    
    // Clean up
    unlink($testFile);
    echo "Test file deleted\n";
    
    echo "=== Test completed successfully ===\n";
} catch (\Exception $e) {
    echo "\n\nERROR: " . $e->getMessage() . "\n";
    echo "Error code: " . $e->getCode() . "\n";
    echo "File: " . $e->getFile() . " (Line: " . $e->getLine() . ")\n";
}
