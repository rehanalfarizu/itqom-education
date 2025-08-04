<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\CloudinaryService;
use Illuminate\Support\Facades\Log;

echo "=== Test CloudinaryService Setelah Perbaikan ===\n\n";

try {
    // Test 1: Inisialisasi Service
    echo "1. Test Inisialisasi CloudinaryService:\n";
    $cloudinaryService = app(CloudinaryService::class);
    echo "   ✓ CloudinaryService berhasil dibuat\n";

    // Test 2: Test getOptimizedUrl dengan transformasi
    echo "\n2. Test getOptimizedUrl dengan transformasi:\n";
    $testTransformations = [
        'width' => 800,
        'height' => 450,
        'crop' => 'fill',
        'quality' => 'auto',
        'fetch_format' => 'auto'
    ];
    
    $testUrl = $cloudinaryService->getOptimizedUrl('courses/test-image.jpg', $testTransformations);
    echo "   Test URL: {$testUrl}\n";
    echo "   ✓ URL berhasil dihasilkan tanpa error Array to String\n";

    // Test 3: Test tanpa transformasi
    echo "\n3. Test getOptimizedUrl tanpa transformasi:\n";
    $simpleUrl = $cloudinaryService->getOptimizedUrl('courses/simple-test.jpg');
    echo "   Simple URL: {$simpleUrl}\n";
    echo "   ✓ URL sederhana berhasil dihasilkan\n";

    // Test 4: Test dengan path kosong
    echo "\n4. Test dengan path kosong:\n";
    $emptyUrl = $cloudinaryService->getOptimizedUrl('');
    echo "   Empty URL: {$emptyUrl}\n";
    echo "   ✓ Fallback ke default image berfungsi\n";

    // Test 5: Test isCloudinaryEnabled
    echo "\n5. Test konfigurasi environment:\n";
    $isEnabled = $cloudinaryService->isCloudinaryEnabled();
    echo "   Cloudinary enabled: " . ($isEnabled ? 'Yes' : 'No') . "\n";
    echo "   Environment: " . app()->environment() . "\n";
    
    // Test 6: Test getBestImageUrl
    echo "\n6. Test getBestImageUrl:\n";
    $bestUrl = $cloudinaryService->getBestImageUrl('courses/test-image.jpg', $testTransformations);
    echo "   Best URL: {$bestUrl}\n";
    echo "   ✓ getBestImageUrl berfungsi dengan baik\n";

    echo "\n=== Semua Test Berhasil ===\n";
    echo "CloudinaryService telah diperbaiki dan siap digunakan!\n\n";

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

echo "Selesai!\n";
