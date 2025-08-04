<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('fix:cloudinary-images {--dry-run : Show what would be fixed without making changes}', function () {
    $dryRun = $this->option('dry-run');
    $cloudinaryService = app(\App\Services\CloudinaryService::class);

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
    $courses = \App\Models\CourseDescription::all();

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
        }
    }

    if ($dryRun) {
        $this->info("\n🏁 Dry run completed. Use --no-dry-run to make actual changes.");
    } else {
        $this->info("\n🏁 Fix completed!");
    }

    return 0;
})->purpose('Fix and verify Cloudinary image URLs for course descriptions');
