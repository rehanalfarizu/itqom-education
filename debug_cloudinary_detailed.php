<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\CloudinaryService;
use Cloudinary\Api\ApiResponse;
use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Admin\AdminApi;

echo "=== CLOUDINARY DEBUGGING LENGKAP ===\n\n";

try {
    $cloudinaryService = app(CloudinaryService::class);
    
    echo "1. Test Koneksi Cloudinary:\n";
    
    // Test direct Cloudinary API
    $adminApi = new AdminApi();
    
    echo "\n2. Cari semua resources (tidak hanya folder courses):\n";
    try {
        $allResources = $adminApi->assets([
            'max_results' => 50,
            'resource_type' => 'image'
        ]);
        
        echo "   Total resources found: " . count($allResources['resources']) . "\n";
        
        foreach($allResources['resources'] as $resource) {
            echo "   - Public ID: " . $resource['public_id'] . "\n";
            echo "     Format: " . $resource['format'] . "\n";
            echo "     Size: " . $resource['bytes'] . " bytes\n";
            echo "     URL: " . $resource['secure_url'] . "\n";
            echo "     Created: " . $resource['created_at'] . "\n";
            echo "     ---\n";
        }
    } catch(\Exception $e) {
        echo "   Error getting all resources: " . $e->getMessage() . "\n";
    }
    
    echo "\n3. Cari dengan prefix 'courses':\n";
    try {
        $coursesResources = $adminApi->assets([
            'max_results' => 20,
            'prefix' => 'courses/',
            'resource_type' => 'image'
        ]);
        
        echo "   Resources dengan prefix 'courses/': " . count($coursesResources['resources']) . "\n";
        
        foreach($coursesResources['resources'] as $resource) {
            echo "   - " . $resource['public_id'] . " -> " . $resource['secure_url'] . "\n";
        }
    } catch(\Exception $e) {
        echo "   Error with courses prefix: " . $e->getMessage() . "\n";
    }
    
    echo "\n4. Cari dengan prefix 'itqom-platform':\n";
    try {
        $itqomResources = $adminApi->assets([
            'max_results' => 20,
            'prefix' => 'itqom-platform/',
            'resource_type' => 'image'
        ]);
        
        echo "   Resources dengan prefix 'itqom-platform/': " . count($itqomResources['resources']) . "\n";
        
        foreach($itqomResources['resources'] as $resource) {
            echo "   - " . $resource['public_id'] . " -> " . $resource['secure_url'] . "\n";
        }
    } catch(\Exception $e) {
        echo "   Error with itqom-platform prefix: " . $e->getMessage() . "\n";
    }
    
    echo "\n5. Cari tanpa prefix (root level):\n";
    try {
        $rootResources = $adminApi->assets([
            'max_results' => 20,
            'resource_type' => 'image'
        ]);
        
        echo "   Root level resources: " . count($rootResources['resources']) . "\n";
        
        foreach($rootResources['resources'] as $resource) {
            // Filter yang tidak mengandung slash (root level)
            if(strpos($resource['public_id'], '/') === false) {
                echo "   - " . $resource['public_id'] . " -> " . $resource['secure_url'] . "\n";
            }
        }
    } catch(\Exception $e) {
        echo "   Error getting root resources: " . $e->getMessage() . "\n";
    }
    
    echo "\n6. Test URL generation untuk files yang ditemukan:\n";
    
    // Test beberapa kemungkinan public_id berdasarkan screenshot
    $testPublicIds = [
        'courses/course_1754329000_6890efa8bee93.jpg',
        'course_1754329000_6890efa8bee93.jpg',
        'itqom-platform/course_1754329000_6890efa8bee93.jpg',
        'd0jiiAf5BbAPb0ADJTiTd12cgeSh3y',  // dari screenshot
        'courses/react-native.jpg',
        'react-native.jpg',
        'courses/ui-ux-design.jpg',
        'ui-ux-design.jpg'
    ];
    
    foreach($testPublicIds as $publicId) {
        $url = $cloudinaryService->getOptimizedUrl($publicId);
        echo "   Testing '{$publicId}' -> {$url}\n";
    }
    
} catch(\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== DEBUGGING SELESAI ===\n";
