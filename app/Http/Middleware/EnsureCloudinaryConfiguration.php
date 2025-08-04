<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Cloudinary\Configuration\Configuration;
use Illuminate\Support\Facades\Log;

class EnsureCloudinaryConfiguration
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Only configure in production or when using cloudinary disk
        if (app()->environment('production') || env('FILESYSTEM_DISK') === 'cloudinary') {
            $this->ensureCloudinaryConfiguration();
        }

        return $next($request);
    }

    /**
     * Ensure Cloudinary is properly configured
     */
    private function ensureCloudinaryConfiguration(): void
    {
        static $configured = false;

        if ($configured) {
            return;
        }

        try {
            // Check if already configured by checking the current instance
            $existingConfig = Configuration::instance();
            if ($existingConfig && isset($existingConfig->cloud->cloudName) && $existingConfig->cloud->cloudName) {
                Log::info('EnsureCloudinaryConfiguration: Already configured for cloud: ' . $existingConfig->cloud->cloudName);
                $configured = true;
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
                // Pattern: cloudinary://api_key:api_secret@cloud_name
                if (preg_match('/cloudinary:\/\/([^:]+):([^@]+)@(.+)/', $cloudinaryUrl, $matches)) {
                    $apiKey = $matches[1];
                    $apiSecret = $matches[2];
                    $cloudName = $matches[3];
                    Log::info('EnsureCloudinaryConfiguration: Parsed credentials from CLOUDINARY_URL for cloud: ' . $cloudName);
                }
            }

            if ($cloudName && $apiKey && $apiSecret) {
                // Set the global configuration
                Configuration::instance([
                    'cloud' => [
                        'cloud_name' => $cloudName,
                        'api_key' => $apiKey,
                        'api_secret' => $apiSecret,
                        'url' => ['secure' => true]
                    ]
                ]);

                Log::info('EnsureCloudinaryConfiguration: Successfully configured for cloud: ' . $cloudName);
                $configured = true;
            } else {
                Log::warning('EnsureCloudinaryConfiguration: Missing credentials - cloud_name: ' . ($cloudName ? 'set' : 'missing') .
                           ', api_key: ' . ($apiKey ? 'set' : 'missing') .
                           ', api_secret: ' . ($apiSecret ? 'set' : 'missing'));
            }
        } catch (\Exception $e) {
            Log::error('EnsureCloudinaryConfiguration: Failed to configure: ' . $e->getMessage());
        }
    }
}
