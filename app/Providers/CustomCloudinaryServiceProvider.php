<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use CloudinaryLabs\CloudinaryLaravel\CloudinaryStorageAdapter;
use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;
use Illuminate\Support\Facades\Log;
use Illuminate\Filesystem\FilesystemAdapter;
use League\Flysystem\Filesystem;

class CustomCloudinaryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register the cloudinary filesystem driver with safe configuration
        $this->app['filesystem']->extend('cloudinary', function ($app, $config) {
            try {
                // First, ensure global configuration is available
                $this->ensureGlobalConfiguration($config);
                
                // Try to use existing global configuration
                $globalConfig = Configuration::instance();
                if ($globalConfig && $globalConfig->cloud->cloudName) {
                    Log::info('CustomCloudinaryServiceProvider: Using existing global configuration for cloud: ' . $globalConfig->cloud->cloudName);
                    $cloudinary = new Cloudinary();
                } else {
                    // Fallback: create new instance with safe config parsing
                    Log::info('CustomCloudinaryServiceProvider: Creating new Cloudinary instance');
                    $cloudinary = $this->createCloudinaryFromConfig($config);
                }

                $adapter = new CloudinaryStorageAdapter($cloudinary);
                return new FilesystemAdapter(new Filesystem($adapter, $config), $adapter, $config);

            } catch (\Exception $e) {
                Log::error('CustomCloudinaryServiceProvider: Error creating Cloudinary adapter: ' . $e->getMessage());
                
                // Emergency fallback with environment variables
                try {
                    $cloudinary = $this->createCloudinaryFromEnv();
                    $adapter = new CloudinaryStorageAdapter($cloudinary);
                    return new FilesystemAdapter(new Filesystem($adapter, $config), $adapter, $config);
                } catch (\Exception $fallbackError) {
                    Log::error('CustomCloudinaryServiceProvider: Fallback also failed: ' . $fallbackError->getMessage());
                    throw $e; // Throw original error
                }
            }
        });

        // Load views and publish config
        $this->loadViewsFrom(__DIR__.'/../../vendor/cloudinary-labs/cloudinary-laravel/views', 'cloudinary');

        $this->publishes([
            __DIR__.'/../../vendor/cloudinary-labs/cloudinary-laravel/config/cloudinary.php' => config_path('cloudinary.php'),
        ], 'cloudinary-config');
    }

    /**
     * Ensure global configuration is set
     */
    private function ensureGlobalConfiguration($config): void
    {
        try {
            // Check if already configured
            $existingConfig = Configuration::instance();
            if ($existingConfig && $existingConfig->cloud->cloudName) {
                return; // Already configured
            }

            // Get credentials with multiple fallbacks
            $cloudName = $config['cloud_name'] ?? $config['cloud'] ?? 
                        config('cloudinary.cloud.cloud_name') ??
                        config('filesystems.disks.cloudinary.cloud_name') ??
                        env('CLOUDINARY_CLOUD_NAME');
                        
            $apiKey = $config['api_key'] ?? $config['key'] ?? 
                     config('cloudinary.cloud.api_key') ??
                     config('filesystems.disks.cloudinary.api_key') ??
                     env('CLOUDINARY_API_KEY');
                     
            $apiSecret = $config['api_secret'] ?? $config['secret'] ?? 
                        config('cloudinary.cloud.api_secret') ??
                        config('filesystems.disks.cloudinary.api_secret') ??
                        env('CLOUDINARY_API_SECRET');

            // Fallback to parsing CLOUDINARY_URL
            if ((!$cloudName || !$apiKey || !$apiSecret) && env('CLOUDINARY_URL')) {
                $cloudinaryUrl = env('CLOUDINARY_URL');
                if (preg_match('/cloudinary:\/\/([^:]+):([^@]+)@(.+)/', $cloudinaryUrl, $matches)) {
                    $apiKey = $matches[1];
                    $apiSecret = $matches[2];
                    $cloudName = $matches[3];
                    Log::info('CustomCloudinaryServiceProvider: Parsed credentials from CLOUDINARY_URL');
                }
            }

            if ($cloudName && $apiKey && $apiSecret) {
                Configuration::instance([
                    'cloud' => [
                        'cloud_name' => $cloudName,
                        'api_key' => $apiKey,
                        'api_secret' => $apiSecret,
                        'url' => ['secure' => true]
                    ]
                ]);
                Log::info('CustomCloudinaryServiceProvider: Global configuration set for cloud: ' . $cloudName);
            }
        } catch (\Exception $e) {
            Log::error('CustomCloudinaryServiceProvider: Failed to ensure global configuration: ' . $e->getMessage());
        }
    }

    /**
     * Create Cloudinary instance from config with safe fallbacks
     */
    private function createCloudinaryFromConfig($config): Cloudinary
    {
        // Try URL format first
        if (isset($config['url']) && $config['url']) {
            return new Cloudinary($config['url']);
        }

        // Build configuration array
        $cloudName = $config['cloud_name'] ?? $config['cloud'] ?? env('CLOUDINARY_CLOUD_NAME');
        $apiKey = $config['api_key'] ?? $config['key'] ?? env('CLOUDINARY_API_KEY');
        $apiSecret = $config['api_secret'] ?? $config['secret'] ?? env('CLOUDINARY_API_SECRET');
        
        $cloudinaryConfig = [
            'cloud' => [
                'cloud_name' => $cloudName,
                'api_key' => $apiKey,
                'api_secret' => $apiSecret,
                'url' => ['secure' => $config['secure'] ?? true]
            ]
        ];

        // Validate we have required fields
        if (!$cloudName || !$apiKey || !$apiSecret) {
            throw new \Exception('Missing required Cloudinary credentials');
        }

        return new Cloudinary($cloudinaryConfig);
    }

    /**
     * Create Cloudinary instance from environment variables
     */
    private function createCloudinaryFromEnv(): Cloudinary
    {
        $cloudName = env('CLOUDINARY_CLOUD_NAME');
        $apiKey = env('CLOUDINARY_API_KEY');
        $apiSecret = env('CLOUDINARY_API_SECRET');

        // Try parsing CLOUDINARY_URL if individual vars not available
        if ((!$cloudName || !$apiKey || !$apiSecret) && env('CLOUDINARY_URL')) {
            $cloudinaryUrl = env('CLOUDINARY_URL');
            if (preg_match('/cloudinary:\/\/([^:]+):([^@]+)@(.+)/', $cloudinaryUrl, $matches)) {
                $apiKey = $matches[1];
                $apiSecret = $matches[2];
                $cloudName = $matches[3];
            }
        }

        if (!$cloudName || !$apiKey || !$apiSecret) {
            throw new \Exception('No valid Cloudinary credentials found in environment');
        }

        return new Cloudinary([
            'cloud' => [
                'cloud_name' => $cloudName,
                'api_key' => $apiKey,
                'api_secret' => $apiSecret,
                'url' => ['secure' => true]
            ]
        ]);
    }
}
