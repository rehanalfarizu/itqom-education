<?php

namespace App\Providers;

use CloudinaryLabs\CloudinaryLaravel\CloudinaryServiceProvider as BaseCloudinaryServiceProvider;
use CloudinaryLabs\CloudinaryLaravel\CloudinaryStorageAdapter;
use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;
use Illuminate\Support\Facades\Log;
use Illuminate\Filesystem\FilesystemAdapter;
use League\Flysystem\Filesystem;

class CustomCloudinaryServiceProvider extends BaseCloudinaryServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // First, ensure our global configuration is set
        $this->ensureGlobalConfiguration();

        // Register the extended filesystem driver
        $this->app['filesystem']->extend('cloudinary', function ($app, $config) {
            try {
                // Try to use existing global configuration first
                $globalConfig = Configuration::instance();
                if ($globalConfig && $globalConfig->cloud->cloudName) {
                    Log::info('CustomCloudinaryServiceProvider: Using existing global configuration for cloud: ' . $globalConfig->cloud->cloudName);
                    $cloudinary = new Cloudinary();
                } else {
                    // Create new instance with config
                    Log::info('CustomCloudinaryServiceProvider: Creating new Cloudinary instance with disk config');
                    $cloudinary = $this->createCloudinaryFromConfig($config);
                }

                $adapter = new CloudinaryStorageAdapter($cloudinary);
                return new FilesystemAdapter(new Filesystem($adapter, $config), $adapter, $config);

            } catch (\Exception $e) {
                Log::error('CustomCloudinaryServiceProvider: Failed to create Cloudinary adapter: ' . $e->getMessage());
                
                // Emergency fallback - try to create with environment variables
                $cloudinary = $this->createCloudinaryFromEnv();
                if ($cloudinary) {
                    $adapter = new CloudinaryStorageAdapter($cloudinary);
                    return new FilesystemAdapter(new Filesystem($adapter, $config), $adapter, $config);
                }
                
                throw $e;
            }
        });

        // Load views and publish config like parent
        $this->loadViewsFrom(__DIR__.'/../../vendor/cloudinary-labs/cloudinary-laravel/views', 'cloudinary');

        $this->publishes([
            __DIR__.'/../../vendor/cloudinary-labs/cloudinary-laravel/config/cloudinary.php' => config_path('cloudinary.php'),
        ], 'cloudinary-config');
    }

    /**
     * Create Cloudinary instance from config
     */
    private function createCloudinaryFromConfig($config): Cloudinary
    {
        if (isset($config['url'])) {
            return new Cloudinary($config['url']);
        }

        return new Cloudinary([
            'cloud' => [
                'cloud_name' => $config['cloud_name'] ?? $config['cloud'],
                'api_key' => $config['api_key'] ?? $config['key'],
                'api_secret' => $config['api_secret'] ?? $config['secret'],
                'url' => $config['url'] ?? ['secure' => true]
            ]
        ]);
    }

    /**
     * Create Cloudinary instance from environment variables
     */
    private function createCloudinaryFromEnv(): ?Cloudinary
    {
        $cloudName = env('CLOUDINARY_CLOUD_NAME');
        $apiKey = env('CLOUDINARY_API_KEY');
        $apiSecret = env('CLOUDINARY_API_SECRET');
        
        if ($cloudName && $apiKey && $apiSecret) {
            return new Cloudinary([
                'cloud' => [
                    'cloud_name' => $cloudName,
                    'api_key' => $apiKey,
                    'api_secret' => $apiSecret,
                    'url' => ['secure' => true]
                ]
            ]);
        }

        return null;
    }

    /**
     * Ensure global Cloudinary configuration is set
     */
    private function ensureGlobalConfiguration(): void
    {
        if (app()->environment('production') || env('FILESYSTEM_DISK') === 'cloudinary') {
            try {
                // Check if already configured
                $existingConfig = Configuration::instance();
                if ($existingConfig && $existingConfig->cloud->cloudName) {
                    Log::info('CustomCloudinaryServiceProvider: Global configuration already exists for cloud: ' . $existingConfig->cloud->cloudName);
                    return;
                }

                // Get credentials with multiple fallbacks
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
                    Log::info('CustomCloudinaryServiceProvider: Global configuration set successfully for cloud: ' . $cloudName);
                }
            } catch (\Exception $e) {
                Log::error('CustomCloudinaryServiceProvider: Failed to ensure global configuration: ' . $e->getMessage());
            }
        }
    }
}
