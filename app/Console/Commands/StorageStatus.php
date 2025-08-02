<?php

namespace App\Console\Commands;

use App\Models\Course;
use App\Services\CloudinaryService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class StorageStatus extends Command
{
    protected $signature = 'storage:status';
    protected $description = 'Check current storage configuration and status';

    public function handle()
    {
        $cloudinaryService = app(CloudinaryService::class);

        $this->info('=== Storage Configuration Status ===');
        $this->line('');

        // Environment info
        $this->info('Environment: ' . app()->environment());
        $this->info('Storage Type: ' . $cloudinaryService->getStorageType());
        $this->info('USE_CLOUDINARY: ' . (config('app.use_cloudinary') ? 'true' : 'false'));
        $this->info('FILESYSTEM_DISK: ' . env('FILESYSTEM_DISK', 'local'));

        $this->line('');
        $this->info('=== Database Status ===');

        // Count courses with images
        $totalCourses = Course::count();
        $coursesWithImages = Course::whereNotNull('image')->count();
        $coursesWithLocalImages = Course::whereNotNull('image')
            ->where('image', 'like', 'courses/%')
            ->count();
        $coursesWithCloudinaryImages = Course::whereNotNull('image')
            ->where('image', 'not like', 'courses/%')
            ->where('image', 'not like', 'http%')
            ->count();
        $coursesWithUrlImages = Course::whereNotNull('image')
            ->where('image', 'like', 'http%')
            ->count();

        $this->table(
            ['Metric', 'Count'],
            [
                ['Total Courses', $totalCourses],
                ['Courses with Images', $coursesWithImages],
                ['Local Storage Paths', $coursesWithLocalImages],
                ['Cloudinary Paths', $coursesWithCloudinaryImages],
                ['URL Paths', $coursesWithUrlImages],
            ]
        );

        $this->line('');
        $this->info('=== Storage Directory Status ===');

        // Check local storage
        $storageExists = Storage::disk('public')->exists('courses');
        $this->info('Courses directory exists: ' . ($storageExists ? 'Yes' : 'No'));

        if ($storageExists) {
            $files = Storage::disk('public')->files('courses');
            $this->info('Files in courses directory: ' . count($files));

            if (count($files) > 0) {
                $this->line('');
                $this->info('Recent files:');
                foreach (array_slice($files, -5) as $file) {
                    $size = Storage::disk('public')->size($file);
                    $this->line('  - ' . basename($file) . ' (' . $this->formatBytes($size) . ')');
                }
            }
        }

        $this->line('');
        $this->info('=== Recommendations ===');

        if (app()->environment('production')) {
            if ($cloudinaryService->getStorageType() === 'local') {
                $this->warn('⚠️  You are in production but using local storage.');
                $this->warn('   Consider switching to Cloudinary for better reliability.');
                $this->info('   Set USE_CLOUDINARY=true in your .env file');
            } else {
                $this->info('✅ Production setup with Cloudinary - Good!');
            }
        } else {
            if ($cloudinaryService->getStorageType() === 'local') {
                $this->info('✅ Development setup with local storage - Good!');
            } else {
                $this->info('ℹ️  Development environment using Cloudinary');
            }
        }

        return 0;
    }

    private function formatBytes($size, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }

        return round($size, $precision) . ' ' . $units[$i];
    }
}
