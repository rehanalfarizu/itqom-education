<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\CloudinaryService;
use Illuminate\Support\Facades\DB;

echo "=== AUTO CLEANUP LIVEWIRE-TMP FILES ===\n\n";

$cloudinaryService = app(CloudinaryService::class);

try {
    echo "1. Scanning livewire-tmp folder...\n";
    $result = $cloudinaryService->listFiles('livewire-tmp');
    $files = $result['resources'] ?? [];
    
    if(count($files) == 0) {
        echo "   ✅ No files to cleanup\n";
        exit;
    }
    
    echo "   Found " . count($files) . " files\n";
    
    $cleanedCount = 0;
    $now = new DateTime();
    
    echo "\n2. Cleaning up old files (older than 24 hours)...\n";
    
    foreach($files as $file) {
        $createdAt = new DateTime($file->created_at);
        $hoursDiff = $now->diff($createdAt)->h + ($now->diff($createdAt)->days * 24);
        
        if($hoursDiff > 24) {
            try {
                $deleted = $cloudinaryService->deleteImage($file->public_id);
                if($deleted) {
                    echo "   ✅ Deleted: {$file->public_id}\n";
                    $cleanedCount++;
                } else {
                    echo "   ❌ Failed to delete: {$file->public_id}\n";
                }
            } catch(\Exception $e) {
                echo "   ❌ Error deleting {$file->public_id}: " . $e->getMessage() . "\n";
            }
        } else {
            echo "   ⏳ Keeping recent file: {$file->public_id} ({$hoursDiff}h old)\n";
        }
    }
    
    echo "\n=== CLEANUP COMPLETED ===\n";
    echo "Cleaned up {$cleanedCount} old files\n";
    
} catch(\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\nDone!\n";
