<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\CloudinaryService;
use App\Models\CourseDescription;

echo "=== SINKRONISASI DATABASE DENGAN CLOUDINARY ===\n\n";

$cloudinaryService = app(CloudinaryService::class);

try {
    // 1. Ambil semua files dari Cloudinary folder courses
    echo "1. Mengambil semua files dari Cloudinary folder 'courses':\n";
    $cloudinaryFiles = $cloudinaryService->listFiles('courses');
    
    echo "   Files di Cloudinary:\n";
    foreach($cloudinaryFiles as $index => $file) {
        echo "   " . ($index + 1) . ". {$file['public_id']} ({$file['format']}) - {$file['bytes']} bytes\n";
    }
    
    // 2. Ambil semua courses dari database
    echo "\n2. Data courses di database:\n";
    $courses = CourseDescription::all();
    
    foreach($courses as $course) {
        echo "   Course ID {$course->id}: {$course->title}\n";
        echo "     Current image_url: {$course->image_url}\n";
        
        // Cek apakah image_url di database cocok dengan file di Cloudinary
        $currentImagePath = str_replace('courses/', '', $course->image_url);
        $found = false;
        
        foreach($cloudinaryFiles as $file) {
            $cloudinaryFileName = basename($file['public_id']);
            if (strpos($file['public_id'], $currentImagePath) !== false || 
                strpos($currentImagePath, $cloudinaryFileName) !== false) {
                echo "     ✓ Found match in Cloudinary: {$file['public_id']}\n";
                $found = true;
                break;
            }
        }
        
        if (!$found) {
            echo "     ❌ No match found in Cloudinary\n";
            
            // Cari file yang mirip berdasarkan pattern
            foreach($cloudinaryFiles as $file) {
                if (strpos($file['public_id'], 'courses/') === 0) {
                    echo "     🔍 Available: {$file['public_id']}\n";
                }
            }
        }
        echo "\n";
    }
    
    // 3. Suggestion untuk fix
    echo "3. Rekomendasi Perbaikan:\n";
    echo "   a. Update database dengan public_id yang benar dari Cloudinary\n";
    echo "   b. Atau upload ulang gambar dengan nama yang konsisten\n";
    echo "   c. Atau buat mapping antara database dan Cloudinary\n\n";
    
} catch(\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

echo "=== SELESAI ===\n";
