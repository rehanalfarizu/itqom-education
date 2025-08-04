<?php

namespace App\Console\Commands;

use App\Models\CourseDescription;
use App\Services\CloudinaryService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class FixCloudinaryImages extends Command
{
    protected $signature = 'fix:cloudinary-images {--dry-run : Show what would be fixed without making changes}';
    protected $description = 'Fix and verify Cloudinary image URLs for course descriptions';

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $cloudinaryService = app(CloudinaryService::class);
        
        $this->info('🔍 Checking Cloudinary configuration...');
        $this->info('Storage type: ' . $cloudinaryService->getStorageType());
        
        if (!$cloudinaryService->isCloudinaryEnabled()) {
            $this->error('❌ Cloudinary is not configured for this environment');
            return 1;
        }

        $this->info('📋 Listing available files in Cloudinary...');
        $availableFiles = $cloudinaryService->listFiles();
        
        if (empty($availableFiles)) {
            $this->warn('⚠️  No files found in Cloudinary or unable to access');
        } else {
            $this->info('✅ Found ' . count($availableFiles) . ' files in Cloudinary');
            foreach ($availableFiles as $file) {
                $this->line('  - ' . $file);
            }
        }

        $this->info('🔧 Checking course descriptions...');
        $courses = CourseDescription::all();
        
        foreach ($courses as $course) {
            $this->info("\n📚 Course: {$course->title}");
            $originalImageUrl = $course->getAttributes()['image_url'] ?? null;
            
            if (!$originalImageUrl) {
                $this->warn("  ⚠️  No image URL stored");
                continue;
            }
            
            $this->line("  📁 Stored path: {$originalImageUrl}");
            
            // Generate the expected public ID
            $cleanPath = ltrim($originalImageUrl, '/');
            $cleanPath = str_replace('storage/', '', $cleanPath);
            
            if (!str_contains($cleanPath, '/') && !str_starts_with($cleanPath, 'courses/')) {
                $cleanPath = 'courses/' . $cleanPath;
            }
            
            $folder = config('cloudinary.folder', 'itqom-platform');
            if (!str_starts_with($cleanPath, $folder . '/')) {
                $expectedPublicId = $folder . '/' . $cleanPath;
            } else {
                $expectedPublicId = $cleanPath;
            }
            
            $this->line("  🔍 Expected public ID: {$expectedPublicId}");
            
            // Check if file exists
            $exists = $cloudinaryService->checkFileExists($expectedPublicId);
            
            if ($exists) {
                $this->info("  ✅ File exists in Cloudinary");
                
                // Generate the correct URL
                $optimizedUrl = $cloudinaryService->getOptimizedUrl($cleanPath, [
                    'width' => 800,
                    'height' => 450,
                    'crop' => 'fill',
                    'quality' => 'auto',
                    'format' => 'auto'
                ]);
                
                $this->line("  🌐 Generated URL: {$optimizedUrl}");
            } else {
                $this->error("  ❌ File NOT found in Cloudinary: {$expectedPublicId}");
                
                // Check if any similar files exist
                $similarFiles = array_filter($availableFiles, function($file) use ($cleanPath) {
                    $filename = basename($cleanPath);
                    return str_contains($file, $filename) || str_contains($file, pathinfo($filename, PATHINFO_FILENAME));
                });
                
                if (!empty($similarFiles)) {
                    $this->warn("  💡 Similar files found:");
                    foreach ($similarFiles as $similar) {
                        $this->line("    - {$similar}");
                    }
                    
                    if (!$dryRun) {
                        if ($this->confirm("  🔄 Update to use: {$similarFiles[0]}?")) {
                            $newPath = str_replace($folder . '/', '', $similarFiles[0]);
                            $course->update(['image_url' => $newPath]);
                            $this->info("  ✅ Updated course image path");
                        }
                    }
                }
            }
        }

        if ($dryRun) {
            $this->info("\n🏁 Dry run completed. Use --no-dry-run to make actual changes.");
        } else {
            $this->info("\n🏁 Fix completed!");
        }

        return 0;
    }
}
