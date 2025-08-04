<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Cloudinary\Configuration\Configuration;
use Illuminate\Support\Facades\Log;

class CloudinaryConfigurationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Configure Cloudinary globally for Laravel filesystem
        if ($this->shouldConfigureCloudinary()) {
            try {
                $this->configureCloudinary();
            } catch (\Exception $e) {
                Log::error('Failed to configure Cloudinary globally: ' . $e->getMessage());
            }
        }
    }

    /**
     * Check if we should configure Cloudinary
     */
    private function shouldConfigureCloudinary(): bool
    {
        // Configure on production or when explicitly enabled
        return app()->environment('production') || 
               env('FILESYSTEM_DISK') === 'cloudinary' ||
               config('app.use_cloudinary', false);
    }

    /**
     * Configure Cloudinary with multiple fallback methods
     */
    private function configureCloudinary(): void
    {
        // Try multiple configuration sources
        $cloudName = config('cloudinary.cloud.cloud_name') ?: 
                   config('filesystems.disks.cloudinary.cloud_name') ?: 
                   env('CLOUDINARY_CLOUD_NAME');
        $apiKey = config('cloudinary.cloud.api_key') ?: 
                config('filesystems.disks.cloudinary.api_key') ?: 
                env('CLOUDINARY_API_KEY');
        $apiSecret = config('cloudinary.cloud.api_secret') ?: 
                   config('filesystems.disks.cloudinary.api_secret') ?: 
                   env('CLOUDINARY_API_SECRET');

        // Fallback to parsing CLOUDINARY_URL
        if ((!$cloudName || !$apiKey || !$apiSecret) && env('CLOUDINARY_URL')) {
            $cloudinaryUrl = env('CLOUDINARY_URL');
            if (preg_match('/cloudinary:\/\/(\d+):([^@]+)@(.+)/', $cloudinaryUrl, $matches)) {
                $apiKey = $matches[1];
                $apiSecret = $matches[2];
                $cloudName = $matches[3];
                Log::info('Cloudinary: Parsed credentials from CLOUDINARY_URL');
            }
        }

        if ($cloudName && $apiKey && $apiSecret) {
            // Set global Cloudinary configuration
            Configuration::instance([
                'cloud' => [
                    'cloud_name' => $cloudName,
                    'api_key' => $apiKey,
                    'api_secret' => $apiSecret,
                    'url' => ['secure' => true]
                ]
            ]);

            Log::info('Cloudinary: Global configuration set successfully for cloud: ' . $cloudName);
        } else {
            Log::warning('Cloudinary: Could not configure - missing credentials');
        }
    }
}
