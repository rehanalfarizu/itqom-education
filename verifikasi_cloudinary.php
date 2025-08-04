<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Verifikasi Konfigurasi Cloudinary di Heroku ===\n\n";

// Check environment variables
echo "1. Memeriksa Environment Variables:\n";
$cloudinaryUrl = env('CLOUDINARY_URL');
$cloudName = env('CLOUDINARY_CLOUD_NAME');
$apiKey = env('CLOUDINARY_API_KEY');
$apiSecret = env('CLOUDINARY_API_SECRET');

echo "   CLOUDINARY_URL: " . ($cloudinaryUrl ? "✓ Ada (length: " . strlen($cloudinaryUrl) . ")" : "✗ Tidak ada") . "\n";
echo "   CLOUDINARY_CLOUD_NAME: " . ($cloudName ? "✓ Ada ($cloudName)" : "✗ Tidak ada") . "\n";
echo "   CLOUDINARY_API_KEY: " . ($apiKey ? "✓ Ada (" . strlen($apiKey) . " chars)" : "✗ Tidak ada") . "\n";
echo "   CLOUDINARY_API_SECRET: " . ($apiSecret ? "✓ Ada (" . strlen($apiSecret) . " chars)" : "✗ Tidak ada") . "\n";

// Parse CLOUDINARY_URL if available
if ($cloudinaryUrl) {
    echo "\n2. Parsing CLOUDINARY_URL:\n";
    if (preg_match('/cloudinary:\/\/(\d+):([^@]+)@(.+)/', $cloudinaryUrl, $matches)) {
        $parsedApiKey = $matches[1];
        $parsedApiSecret = $matches[2];
        $parsedCloudName = $matches[3];
        
        echo "   Parsed API Key: $parsedApiKey\n";
        echo "   Parsed Cloud Name: $parsedCloudName\n";
        echo "   Parsed API Secret: " . substr($parsedApiSecret, 0, 8) . "...\n";
        
        // Use parsed values if individual env vars are missing
        if (!$cloudName) $cloudName = $parsedCloudName;
        if (!$apiKey) $apiKey = $parsedApiKey;
        if (!$apiSecret) $apiSecret = $parsedApiSecret;
    } else {
        echo "   ✗ Format CLOUDINARY_URL tidak valid\n";
    }
}

// Check config values
echo "\n3. Memeriksa Config Values:\n";
$configCloudName = config('cloudinary.cloud.cloud_name');
$configApiKey = config('cloudinary.cloud.api_key');
$configApiSecret = config('cloudinary.cloud.api_secret');

echo "   config('cloudinary.cloud.cloud_name'): " . ($configCloudName ? "✓ Ada ($configCloudName)" : "✗ Tidak ada") . "\n";
echo "   config('cloudinary.cloud.api_key'): " . ($configApiKey ? "✓ Ada" : "✗ Tidak ada") . "\n";
echo "   config('cloudinary.cloud.api_secret'): " . ($configApiSecret ? "✓ Ada" : "✗ Tidak ada") . "\n";

// Final resolved values
echo "\n4. Final Resolved Values:\n";
$finalCloudName = $configCloudName ?: $cloudName;
$finalApiKey = $configApiKey ?: $apiKey;
$finalApiSecret = $configApiSecret ?: $apiSecret;

echo "   Final Cloud Name: " . ($finalCloudName ? "✓ $finalCloudName" : "✗ Tidak ada") . "\n";
echo "   Final API Key: " . ($finalApiKey ? "✓ Ada" : "✗ Tidak ada") . "\n";
echo "   Final API Secret: " . ($finalApiSecret ? "✓ Ada" : "✗ Tidak ada") . "\n";

// Test Cloudinary initialization
echo "\n5. Test Inisialisasi Cloudinary:\n";
try {
    if ($finalCloudName && $finalApiKey && $finalApiSecret) {
        \Cloudinary\Configuration\Configuration::instance([
            'cloud' => [
                'cloud_name' => $finalCloudName,
                'api_key' => $finalApiKey,
                'api_secret' => $finalApiSecret,
                'url' => ['secure' => true]
            ]
        ]);
        
        $cloudinary = new \Cloudinary\Cloudinary();
        echo "   ✓ Cloudinary berhasil diinisialisasi\n";
        
        // Test simple API call
        try {
            $result = \CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary::admin()->ping();
            echo "   ✓ Ping ke Cloudinary API berhasil\n";
        } catch (\Exception $e) {
            echo "   ✗ Ping ke Cloudinary API gagal: " . $e->getMessage() . "\n";
        }
        
    } else {
        echo "   ✗ Tidak dapat menginisialisasi - kredensial tidak lengkap\n";
    }
} catch (\Exception $e) {
    echo "   ✗ Error saat inisialisasi: " . $e->getMessage() . "\n";
}

// Check CloudinaryService
echo "\n6. Test CloudinaryService:\n";
try {
    $service = app(\App\Services\CloudinaryService::class);
    echo "   ✓ CloudinaryService berhasil dibuat\n";
    
    if ($service->isCloudinaryEnabled()) {
        echo "   ✓ Cloudinary enabled\n";
    } else {
        echo "   ✗ Cloudinary disabled\n";
    }
    
    // Test URL generation
    $testUrl = $service->getOptimizedUrl('courses/test-image.jpg', [
        'width' => 800,
        'height' => 450,
        'crop' => 'fill'
    ]);
    echo "   Test URL: $testUrl\n";
    
} catch (\Exception $e) {
    echo "   ✗ Error CloudinaryService: " . $e->getMessage() . "\n";
}

// Environment info
echo "\n7. Environment Info:\n";
echo "   APP_ENV: " . env('APP_ENV') . "\n";
echo "   Environment detected: " . app()->environment() . "\n";
echo "   Is Production: " . (app()->environment('production') ? 'Yes' : 'No') . "\n";
echo "   DYNO detected: " . (isset($_ENV['DYNO']) ? 'Yes (' . $_ENV['DYNO'] . ')' : 'No') . "\n";

echo "\n=== Selesai ===\n";
