<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\CourseDescription;
use App\Services\CloudinaryService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

echo "=== Perbaikan Komprehensif Cloudinary ===\n\n";

// Initialize Cloudinary configuration
try {
    $cloudinaryService = app(CloudinaryService::class);
    $cloudName = config('cloudinary.cloud.cloud_name') ?: 
                 config('filesystems.disks.cloudinary.cloud_name') ?: 
                 env('CLOUDINARY_CLOUD_NAME');
    
    echo "Cloud Name: {$cloudName}\n\n";
} catch (\Exception $e) {
    echo "Error: Tidak dapat menginisialisasi Cloudinary: {$e->getMessage()}\n";
    exit(1);
}

// Step 1: Fix any Livewire temp paths in the database
echo "Langkah 1: Memperbaiki path Livewire sementara\n";
$courses = CourseDescription::whereRaw("image_url LIKE '%livewire-tmp%'")->get();
echo "  Ditemukan " . count($courses) . " course dengan path livewire-tmp\n";

foreach ($courses as $course) {
    echo "  Course ID: {$course->id} - {$course->title}\n";
    echo "    Path asli: {$course->getRawOriginal('image_url')}\n";
    
    // Generate a standardized path
    $newPath = "courses/course_{$course->id}_" . time() . ".jpg";
    $course->image_url = $newPath;
    $course->save();
    
    echo "    ✓ Path diperbarui: {$newPath}\n";
}

// Step 2: Ensure all paths start with 'courses/'
echo "\nLangkah 2: Standarisasi semua path ke folder 'courses/'\n";
$courses = CourseDescription::whereRaw("image_url NOT LIKE 'courses/%'")->get();
echo "  Ditemukan " . count($courses) . " course dengan path yang tidak standar\n";

foreach ($courses as $course) {
    echo "  Course ID: {$course->id} - {$course->title}\n";
    echo "    Path asli: {$course->getRawOriginal('image_url')}\n";
    
    $originalPath = $course->getRawOriginal('image_url');
    $filename = basename($originalPath);
    $newPath = "courses/" . $filename;
    
    $course->image_url = $newPath;
    $course->save();
    
    echo "    ✓ Path diperbarui: {$newPath}\n";
}

// Step 3: Display a summary of what needs to be uploaded to Cloudinary
echo "\nLangkah 3: Daftar gambar yang perlu diunggah ke Cloudinary\n";
$courses = CourseDescription::all();
echo "  Ditemukan " . count($courses) . " course di database\n\n";

// Function to check if image exists in Cloudinary
function doesImageExistInCloudinary($cloudName, $path) {
    $url = "https://res.cloudinary.com/{$cloudName}/image/upload/{$path}";
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return $responseCode == 200;
}

// Create a list of images that need to be uploaded
$uploadNeeded = [];

foreach ($courses as $course) {
    $imagePath = $course->getRawOriginal('image_url');
    $exists = doesImageExistInCloudinary($cloudName, $imagePath);
    
    if (!$exists) {
        $uploadNeeded[] = [
            'course_id' => $course->id,
            'title' => $course->title,
            'path' => $imagePath
        ];
    }
}

if (count($uploadNeeded) > 0) {
    echo "  PERHATIAN: " . count($uploadNeeded) . " gambar perlu diunggah ke Cloudinary\n\n";
    
    foreach ($uploadNeeded as $item) {
        echo "  Course ID: {$item['course_id']} - {$item['title']}\n";
        echo "    Path yang diperlukan: {$item['path']}\n";
        echo "    URL yang akan diakses: https://res.cloudinary.com/{$cloudName}/image/upload/{$item['path']}\n\n";
    }
    
    echo "  Petunjuk upload:\n";
    echo "  1. Buka dashboard Cloudinary: https://console.cloudinary.com/\n";
    echo "  2. Navigasi ke folder 'courses'\n";
    echo "  3. Upload gambar dengan nama file yang sama persis seperti yang tercantum di atas\n";
    echo "  4. Pastikan gambar diunggah ke folder 'courses/'\n\n";
} else {
    echo "  ✓ Semua gambar sudah tersedia di Cloudinary\n\n";
}

// Final step: Fix CloudinaryService if needed
echo "Langkah 4: Memperbaiki CloudinaryService untuk handling transformasi\n";
$cloudinaryServicePath = app_path('Services/CloudinaryService.php');
$fileContent = File::get($cloudinaryServicePath);

$searchPattern = 'return $this->cloudinary->image($publicId)->toUrl($options);';
$replacementPattern = 'return $this->cloudinary->image($publicId)->toUrl([\'transformation\' => $options]);';

if (str_contains($fileContent, $searchPattern)) {
    // Create backup
    $backupPath = $cloudinaryServicePath . '.backup.' . time();
    File::put($backupPath, $fileContent);
    
    // Replace the code
    $newContent = str_replace($searchPattern, $replacementPattern, $fileContent);
    File::put($cloudinaryServicePath, $newContent);
    
    echo "  ✓ CloudinaryService diperbarui untuk menangani transformasi dengan benar\n";
    echo "  ✓ Backup disimpan di: {$backupPath}\n";
} else {
    echo "  ✓ CloudinaryService sudah diperbarui sebelumnya\n";
}

echo "\n=== Ringkasan ===\n";
echo "1. " . count($courses) . " total course diperiksa\n";
echo "2. " . count($uploadNeeded) . " gambar perlu diunggah ke Cloudinary\n";
echo "3. Semua path di database sudah distandarisasi ke folder 'courses/'\n";
echo "4. CloudinaryService sudah diperbarui untuk menangani transformasi dengan benar\n\n";

echo "=== Langkah Selanjutnya ===\n";
echo "1. Unggah gambar yang diperlukan ke Cloudinary\n";
echo "2. Jalankan 'php artisan cache:clear' untuk membersihkan cache\n";
echo "3. Periksa kembali dengan 'php check_cloudinary_images.php'\n\n";

echo "Selesai!\n";
