<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CloudinaryService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class TestHybridStorage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:hybrid-storage {--test-cloudinary} {--test-local} {--test-fallback}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test hybrid storage system functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $cloudinaryService = app(CloudinaryService::class);

        $this->info('🔍 Testing Hybrid Storage System');
        $this->newLine();

        // Test environment detection
        $this->info('📍 Environment Detection:');
        $this->line('- Current Environment: ' . app()->environment());
        $this->line('- Should Use Cloudinary: ' . ($this->shouldUseCloudinary($cloudinaryService) ? '✅ Yes' : '❌ No'));
        $this->line('- Heroku Detected: ' . (isset($_ENV['DYNO']) ? '✅ Yes' : '❌ No'));
        $this->newLine();

        // Test Cloudinary configuration
        if ($this->option('test-cloudinary') || !$this->hasTestOption()) {
            $this->testCloudinaryConfig($cloudinaryService);
        }

        // Test local storage
        if ($this->option('test-local') || !$this->hasTestOption()) {
            $this->testLocalStorage();
        }

        // Test fallback mechanism
        if ($this->option('test-fallback') || !$this->hasTestOption()) {
            $this->testFallbackMechanism($cloudinaryService);
        }

        $this->newLine();
        $this->info('✨ Hybrid Storage Test Completed!');
    }

    private function hasTestOption(): bool
    {
        return $this->option('test-cloudinary') ||
               $this->option('test-local') ||
               $this->option('test-fallback');
    }

    private function shouldUseCloudinary(CloudinaryService $service): bool
    {
        // Create public method to access private shouldUseCloudinary
        return isset($_ENV['DYNO']) ||
               app()->environment('production') ||
               config('app.force_cloudinary', false);
    }

    private function testCloudinaryConfig(CloudinaryService $service): void
    {
        $this->info('☁️ Testing Cloudinary Configuration:');

        $cloudName = config('cloudinary.cloud.cloud_name');
        $apiKey = config('cloudinary.cloud.api_key');
        $apiSecret = config('cloudinary.cloud.api_secret');

        $this->line('- Cloud Name: ' . ($cloudName ? '✅ Set (' . $cloudName . ')' : '❌ Missing'));
        $this->line('- API Key: ' . ($apiKey ? '✅ Set (' . substr($apiKey, 0, 6) . '...)' : '❌ Missing'));
        $this->line('- API Secret: ' . ($apiSecret ? '✅ Set (' . substr($apiSecret, 0, 6) . '...)' : '❌ Missing'));

        if ($cloudName && $apiKey && $apiSecret) {
            try {
                // Test dengan sample transformation
                $testUrl = $service->getOptimizedUrl('sample', [
                    'width' => 300,
                    'height' => 200,
                    'crop' => 'fill'
                ]);
                $this->line('- Sample URL Generation: ✅ Success');
                $this->line('  URL: ' . $testUrl);
            } catch (\Exception $e) {
                $this->line('- Sample URL Generation: ❌ Failed');
                $this->line('  Error: ' . $e->getMessage());
            }
        }

        $this->newLine();
    }

    private function testLocalStorage(): void
    {
        $this->info('📁 Testing Local Storage:');

        // Test public disk
        $publicDisk = Storage::disk('public');
        $this->line('- Public Disk Available: ' . ($publicDisk ? '✅ Yes' : '❌ No'));

        // Test directory creation
        try {
            $testDir = 'test-hybrid-' . time();
            $publicDisk->makeDirectory($testDir);
            $this->line('- Directory Creation: ✅ Success');

            // Test file writing
            $testFile = $testDir . '/test.txt';
            $publicDisk->put($testFile, 'Hybrid storage test');
            $this->line('- File Writing: ✅ Success');

            // Test file reading
            $content = $publicDisk->get($testFile);
            $this->line('- File Reading: ' . ($content === 'Hybrid storage test' ? '✅ Success' : '❌ Failed'));

            // Cleanup
            $publicDisk->deleteDirectory($testDir);
            $this->line('- Cleanup: ✅ Complete');

        } catch (\Exception $e) {
            $this->line('- Local Storage Test: ❌ Failed');
            $this->line('  Error: ' . $e->getMessage());
        }

        $this->newLine();
    }

    private function testFallbackMechanism(CloudinaryService $service): void
    {
        $this->info('🔄 Testing Fallback Mechanism:');

        // Test image availability check
        $this->line('- Testing image availability check...');

        // Test dengan path yang tidak ada
        $nonExistentLocal = 'courses/non-existent-image.jpg';
        $localAvailable = $service->checkImageAvailability($nonExistentLocal);
        $this->line('  Non-existent local image: ' . ($localAvailable ? '❌ False positive' : '✅ Correctly unavailable'));

        // Test best URL resolution
        $this->line('- Testing best URL resolution...');

        try {
            $bestUrl = $service->getBestImageUrl('sample', [
                'width' => 300,
                'height' => 200
            ]);

            $this->line('  Best URL for "sample": ✅ Success');
            $this->line('  Resolved URL: ' . $bestUrl);
        } catch (\Exception $e) {
            $this->line('  Best URL resolution: ❌ Failed');
            $this->line('  Error: ' . $e->getMessage());
        }

        $this->newLine();
    }
}
