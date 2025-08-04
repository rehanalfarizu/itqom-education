<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CloudinaryService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class TestCloudinaryCommand extends Command
{
    protected $signature = 'test:cloudinary';
    protected $description = 'Test Cloudinary configuration and service';

    public function handle()
    {
        $this->info('=== Cloudinary Configuration Test ===');
        
        // Test environment variables
        $cloudName = env('CLOUDINARY_CLOUD_NAME');
        $apiKey = env('CLOUDINARY_API_KEY');
        $apiSecret = env('CLOUDINARY_API_SECRET');
        
        $this->info('Environment Variables:');
        $this->line('Cloud Name: ' . ($cloudName ? 'SET (' . $cloudName . ')' : 'MISSING'));
        $this->line('API Key: ' . ($apiKey ? 'SET (' . $apiKey . ')' : 'MISSING'));
        $this->line('API Secret: ' . ($apiSecret ? 'SET' : 'MISSING'));
        
        // Test config values
        $this->info('');
        $this->info('Config Values:');
        $this->line('Cloud Name: ' . config('cloudinary.cloud.cloud_name', 'NOT SET'));
        $this->line('API Key: ' . config('cloudinary.cloud.api_key', 'NOT SET'));
        $this->line('API Secret: ' . (config('cloudinary.cloud.api_secret') ? 'SET' : 'NOT SET'));
        
        // Test CloudinaryService
        $this->info('');
        $this->info('CloudinaryService Test:');
        try {
            $service = new CloudinaryService();
            $this->info('✓ CloudinaryService instantiated successfully');
            
            $storageType = $service->getStorageType();
            $this->line('Storage Type: ' . $storageType);
            
        } catch (\Exception $e) {
            $this->error('✗ CloudinaryService failed: ' . $e->getMessage());
        }
        
        // Test environment detection
        $this->info('');
        $this->info('Environment Detection:');
        $this->line('APP_ENV: ' . env('APP_ENV'));
        $this->line('Production check: ' . (app()->environment('production') ? 'YES' : 'NO'));
        $this->line('DYNO check: ' . (isset($_ENV['DYNO']) ? 'YES' : 'NO'));
        
        return 0;
    }
}
