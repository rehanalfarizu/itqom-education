<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\CloudinaryService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

echo "=== Cloudinary Diagnostic Tool ===\n\n";

// 1. Check environment and configuration
echo "Environment: " . app()->environment() . "\n";
echo "Filesystem Disk: " . config('filesystems.default') . "\n";

$cloudName = config('cloudinary.cloud.cloud_name');
$apiKey = config('cloudinary.cloud.api_key');
$apiSecret = config('cloudinary.cloud.api_secret');
$folder = config('cloudinary.folder', 'itqom-platform');

echo "Cloudinary Cloud Name: " . $cloudName . "\n";
echo "Cloudinary API Key: " . (strlen($apiKey) > 4 ? substr($apiKey, 0, 4) . '...' : 'NOT SET') . "\n";
echo "Cloudinary API Secret: " . ($apiSecret ? 'SET' : 'NOT SET') . "\n";
echo "Cloudinary Folder: " . $folder . "\n\n";

// 2. Check database for image URLs
echo "=== Database Check ===\n";
$courses = DB::table('course_description')->select(['id', 'title', 'image_url'])->get();
echo "Found " . count($courses) . " courses in database\n\n";

$imagePathPatterns = [
    'cloudinary_full' => 0,  // https://res.cloudinary.com/...
    'cloudinary_id' => 0,    // courses/filename.jpg
    'storage_path' => 0,     // storage/courses/...
    'no_path' => 0,          // filename.jpg
    'empty' => 0,            // empty
    'other' => 0             // other format
];

$folderStructure = [];

foreach ($courses as $course) {
    echo "Course ID: {$course->id} - {$course->title}\n";
    
    $imageUrl = $course->image_url;
    
    if (!$imageUrl) {
        echo "  Image URL: NOT SET\n";
        $imagePathPatterns['empty']++;
    }
    elseif (str_contains($imageUrl, 'cloudinary.com')) {
        echo "  Image URL: {$imageUrl}\n";
        $imagePathPatterns['cloudinary_full']++;
        
        // Extract folder structure
        if (preg_match('/cloudinary\.com\/[^\/]+\/image\/upload\/[^\/]+\/([^\/]+)\//', $imageUrl, $matches)) {
            $folder = $matches[1];
            $folderStructure[$folder] = ($folderStructure[$folder] ?? 0) + 1;
            echo "  Folder: {$folder}\n";
        }
    }
    elseif (str_starts_with($imageUrl, 'courses/') || str_starts_with($imageUrl, 'images/')) {
        echo "  Image URL: {$imageUrl}\n";
        $imagePathPatterns['cloudinary_id']++;
        
        // Extract folder
        if (strpos($imageUrl, '/') !== false) {
            $folder = explode('/', $imageUrl)[0];
            $folderStructure[$folder] = ($folderStructure[$folder] ?? 0) + 1;
            echo "  Folder: {$folder}\n";
        }
    }
    elseif (str_contains($imageUrl, 'storage/')) {
        echo "  Image URL: {$imageUrl}\n";
        $imagePathPatterns['storage_path']++;
    }
    elseif (str_contains($imageUrl, '.jpg') || str_contains($imageUrl, '.png') || str_contains($imageUrl, '.jpeg')) {
        echo "  Image URL: {$imageUrl}\n";
        $imagePathPatterns['no_path']++;
    }
    else {
        echo "  Image URL: {$imageUrl}\n";
        $imagePathPatterns['other']++;
    }
    
    echo "\n";
}

echo "=== URL Format Summary ===\n";
foreach ($imagePathPatterns as $type => $count) {
    echo "{$type}: {$count}\n";
}

echo "\n=== Folder Structure Summary ===\n";
foreach ($folderStructure as $folder => $count) {
    echo "{$folder}: {$count}\n";
}

// 3. Test CloudinaryService
echo "\n=== CloudinaryService Test ===\n";
try {
    $cloudinaryService = app(CloudinaryService::class);
    echo "CloudinaryService initialized successfully\n";
    
    // Test URL generation
    echo "\nTesting URL generation:\n";
    $testPaths = [
        'courses/test_image.jpg',
        'images/test_image.jpg',
        'test_image.jpg'
    ];
    
    foreach ($testPaths as $testPath) {
        try {
            $url = $cloudinaryService->getOptimizedUrl($testPath, [
                'width' => 800,
                'height' => 450,
                'crop' => 'fill'
            ]);
            echo "  Path: {$testPath}\n";
            echo "  Generated URL: {$url}\n";
        } catch (\Exception $e) {
            echo "  Path: {$testPath}\n";
            echo "  Error: " . $e->getMessage() . "\n";
        }
    }
    
    // Check if actual frontend URLs would work
    echo "\nTesting frontend URLs against database entries:\n";
    if (count($courses) > 0) {
        foreach ($courses as $course) {
            if (!$course->image_url) continue;
            
            $imageUrl = $course->image_url;
            echo "Course ID {$course->id}:\n";
            echo "  Original URL: {$imageUrl}\n";
            
            // Extract just the filename if it's a full Cloudinary URL
            $publicId = $imageUrl;
            if (str_contains($imageUrl, 'cloudinary.com')) {
                if (preg_match('/\/([^\/]+\/[^\/]+\.\w+)$/', $imageUrl, $matches)) {
                    $publicId = $matches[1];
                }
            }
            
            // Test both paths
            $testFolders = ['images/', 'courses/'];
            foreach ($testFolders as $folder) {
                $path = $folder . basename($publicId);
                try {
                    $generatedUrl = $cloudinaryService->getOptimizedUrl($path, [
                        'width' => 800,
                        'height' => 450,
                        'crop' => 'fill'
                    ]);
                    echo "  Testing {$path}: {$generatedUrl}\n";
                } catch (\Exception $e) {
                    echo "  Testing {$path}: Error - " . $e->getMessage() . "\n";
                }
            }
            echo "\n";
            
            // Only test the first course to avoid too much output
            break;
        }
    }
    
} catch (\Exception $e) {
    echo "Error initializing CloudinaryService: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

// 4. Check recent error logs
echo "\n=== Recent Error Logs ===\n";
$errorLogs = [];
try {
    // Try to get the last 10 error logs related to Cloudinary
    $errorLogs = DB::table('logs')
        ->where('level', 'error')
        ->where(function($query) {
            $query->where('message', 'like', '%cloudinary%')
                  ->orWhere('message', 'like', '%image%')
                  ->orWhere('message', 'like', '%upload%');
        })
        ->orderBy('created_at', 'desc')
        ->limit(10)
        ->get(['id', 'message', 'created_at']);
        
    if (count($errorLogs) > 0) {
        foreach ($errorLogs as $log) {
            echo "Log ID: {$log->id}\n";
            echo "Date: {$log->created_at}\n";
            echo "Message: {$log->message}\n";
            echo "--------------------\n";
        }
    } else {
        echo "No relevant error logs found in database.\n";
    }
} catch (\Exception $e) {
    echo "Could not fetch error logs from database: " . $e->getMessage() . "\n";
}

echo "\n=== Diagnostic Complete ===\n";
