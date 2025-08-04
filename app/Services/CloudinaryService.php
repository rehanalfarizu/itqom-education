<?php

namespace App\Services;

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Cloudinary\Configuration\Configuration;
use Cloudinary\Cloudinary as CloudinaryApi;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class CloudinaryService
{
    private $cloudinary;

    public function __construct()
    {
        // Initialize Cloudinary with manual configuration
        if ($this->shouldUseCloudinary()) {
            try {
                // Pastikan konfigurasi tersedia
                $cloudName = config('cloudinary.cloud.cloud_name');
                $apiKey = config('cloudinary.cloud.api_key');
                $apiSecret = config('cloudinary.cloud.api_secret');

                if (!$cloudName || !$apiKey || !$apiSecret) {
                    Log::warning('Cloudinary credentials not available, using fallback');
                    $this->cloudinary = null;
                    return;
                }

                Configuration::instance([
                    'cloud' => [
                        'cloud_name' => $cloudName,
                        'api_key' => $apiKey,
                        'api_secret' => $apiSecret,
                        'url' => ['secure' => true]
                    ]
                ]);
                $this->cloudinary = new CloudinaryApi();
                Log::info('Cloudinary initialized successfully');
            } catch (\Exception $e) {
                Log::warning('Cloudinary initialization failed: ' . $e->getMessage());
                $this->cloudinary = null;
            }
        } else {
            Log::info('Using local storage (not production environment)');
            $this->cloudinary = null;
        }
    }
    /**
     * Upload image with environment-based storage
     */
    public function uploadImage(UploadedFile $file, ?string $folder = null): string
    {
        if ($this->shouldUseCloudinary()) {
            return $this->uploadToCloudinary($file, $folder);
        } else {
            return $this->storeImageLocally($file, $folder);
        }
    }

    /**
     * Upload image with public ID (for Cloudinary) or custom name (for local)
     */
    public function uploadImageWithPublicId(UploadedFile $file, string $publicId, ?string $folder = null): string
    {
        if ($this->shouldUseCloudinary()) {
            return $this->uploadToCloudinaryWithPublicId($file, $publicId, $folder);
        } else {
            return $this->storeImageLocally($file, $folder, $publicId);
        }
    }

    /**
     * Get optimized URL for image
     */
    public function getOptimizedUrl(string $publicIdOrPath, array $transformations = []): string
    {
        if (empty($publicIdOrPath)) {
            return '/images/default-course.jpg';
        }

        // Jika production dan cloudinary available
        if ($this->shouldUseCloudinary() && $this->cloudinary) {
            try {
                $defaultTransformations = [
                    'quality' => 'auto',
                    'fetch_format' => 'auto'
                ];

                $transformations = array_merge($defaultTransformations, $transformations);

                // Build transformation string
                $transformString = [];
                if (isset($transformations['width'])) $transformString[] = 'w_' . $transformations['width'];
                if (isset($transformations['height'])) $transformString[] = 'h_' . $transformations['height'];
                if (isset($transformations['crop'])) $transformString[] = 'c_' . $transformations['crop'];
                if (isset($transformations['quality'])) $transformString[] = 'q_' . $transformations['quality'];
                if (isset($transformations['fetch_format'])) $transformString[] = 'f_' . $transformations['fetch_format'];

                $cloudName = config('cloudinary.cloud.cloud_name');
                if (!$cloudName) {
                    throw new \Exception('Cloudinary cloud name not configured');
                }

                $baseUrl = "https://res.cloudinary.com/{$cloudName}/image/upload/";

                if (!empty($transformString)) {
                    $baseUrl .= implode(',', $transformString) . '/';
                }

                return $baseUrl . $publicIdOrPath;
            } catch (\Exception $e) {
                Log::warning('Cloudinary URL generation failed: ' . $e->getMessage());
                // Fallback to default image
                return '/images/default-course.jpg';
            }
        }

        // If it's a local storage path (contains file extension) and we're not using cloudinary
        if (str_contains($publicIdOrPath, '.') || str_starts_with($publicIdOrPath, 'courses/')) {
            return $this->getLocalImageUrl($publicIdOrPath);
        }

        // Final fallback
        return '/images/default-course.jpg';
    }

    /**
     * Delete image from appropriate storage
     */
    public function deleteImage(string $publicIdOrPath): bool
    {
        if ($this->shouldUseCloudinary() && !str_contains($publicIdOrPath, '.')) {
            // Delete from Cloudinary
            try {
                $result = Cloudinary::destroy($publicIdOrPath);
                return isset($result['result']) && $result['result'] === 'ok';
            } catch (\Exception $e) {
                return false;
            }
        } else {
            // Delete from local storage
            try {
                if (Storage::disk('public')->exists($publicIdOrPath)) {
                    return Storage::disk('public')->delete($publicIdOrPath);
                }
                return true;
            } catch (\Exception $e) {
                return false;
            }
        }
    }

    /**
     * Determine if should use Cloudinary based on environment
     */
    private function shouldUseCloudinary(): bool
    {
        // Always use Cloudinary on Heroku (production)
        if (app()->environment('production') || isset($_ENV['DYNO']) || env('APP_ENV') === 'production') {
            return true;
        }

        // Use Cloudinary if explicitly enabled
        if (config('app.use_cloudinary', false) || env('USE_CLOUDINARY', false)) {
            return true;
        }

        // Use Cloudinary if FILESYSTEM_DISK is set to cloudinary
        if (env('FILESYSTEM_DISK') === 'cloudinary') {
            return true;
        }

        // Check if Cloudinary credentials are available
        if (config('cloudinary.cloud.cloud_name') && config('cloudinary.cloud.api_key') && config('cloudinary.cloud.api_secret')) {
            return true;
        }

        // Default to local storage for development
        return false;
    }

    /**
     * Upload to Cloudinary
     */
    private function uploadToCloudinary(UploadedFile $file, ?string $folder = null): string
    {
        $folder = $folder ?? config('cloudinary.folder', 'itqom-platform');

        $result = Cloudinary::upload($file->getRealPath(), [
            'folder' => $folder,
            'resource_type' => 'image',
            'transformation' => [
                'quality' => 'auto',
                'fetch_format' => 'auto'
            ]
        ]);

        // Return public_id for Cloudinary
        return $result->getPublicId();
    }

    /**
     * Upload to Cloudinary with specific public ID
     */
    private function uploadToCloudinaryWithPublicId(UploadedFile $file, string $publicId, ?string $folder = null): string
    {
        $folder = $folder ?? config('cloudinary.folder', 'itqom-platform');

        $result = Cloudinary::upload($file->getRealPath(), [
            'folder' => $folder,
            'public_id' => $publicId,
            'resource_type' => 'image',
            'overwrite' => true,
            'transformation' => [
                'quality' => 'auto',
                'fetch_format' => 'auto'
            ]
        ]);

        // Return public_id for Cloudinary
        return $result->getPublicId();
    }

    /**
     * Store file locally and return path
     */
    public function storeImageLocally(UploadedFile $file, ?string $folder = 'courses', ?string $customName = null): string
    {
        // Ensure folder has a default value
        $folder = $folder ?? 'courses';
        
        if ($customName) {
            $filename = $customName . '.' . $file->getClientOriginalExtension();
        } else {
            $filename = 'course_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        }

        $path = $file->storeAs($folder, $filename, 'public');

        return $path; // Returns something like 'courses/course_1659123456_abc123.jpg'
    }

    /**
     * Get local image URL
     */
    public function getLocalImageUrl(string $path): string
    {
        if (empty($path)) {
            return '/images/default-course.jpg';
        }

        // If it's already a full URL, return as is
        if (str_starts_with($path, 'http')) {
            return $path;
        }

        // Convert path to storage URL
        return Storage::url($path);
    }

    /**
     * Upload image with hybrid approach (primary + backup)
     */
    public function uploadImageHybrid(UploadedFile $file, ?string $folder = null): string
    {
        // Set default folder if null
        $folder = $folder ?? 'courses';
        
        $primaryPath = null;
        $backupPath = null;

        try {
            if ($this->shouldUseCloudinary()) {
                // Primary: Cloudinary, Backup: Local (if possible)
                $primaryPath = $this->uploadToCloudinary($file, $folder);

                // Try to store locally as backup (will fail on Heroku, but that's OK)
                try {
                    $this->storeImageLocally($file, $folder);
                } catch (\Exception $e) {
                    // Silent fail - expected on Heroku
                }

                return $primaryPath;
            } else {
                // Primary: Local, Backup: Cloudinary (optional)
                $primaryPath = $this->storeImageLocally($file, $folder);

                // Optional: Store to Cloudinary as backup/CDN
                if (env('ENABLE_CLOUDINARY_BACKUP', false)) {
                    try {
                        $this->uploadToCloudinary($file, $folder);
                    } catch (\Exception $e) {
                        // Silent fail - backup is optional
                        Log::info('Cloudinary backup failed: ' . $e->getMessage());
                    }
                }

                return $primaryPath;
            }
        } catch (\Exception $e) {
            Log::error('Primary storage failed: ' . $e->getMessage());

            // Fallback strategy
            if ($this->shouldUseCloudinary() && !$primaryPath) {
                // Try local as fallback
                try {
                    return $this->storeImageLocally($file, $folder ?? 'courses');
                } catch (\Exception $fallbackError) {
                    throw new \Exception('All storage methods failed');
                }
            } else {
                // Try Cloudinary as fallback
                try {
                    return $this->uploadToCloudinary($file, $folder);
                } catch (\Exception $fallbackError) {
                    throw new \Exception('All storage methods failed');
                }
            }
        }
    }

    /**
     * Get current storage type being used
     */
    public function getStorageType(): string
    {
        if (isset($_ENV['DYNO'])) {
            return 'cloudinary (heroku)';
        }
        return $this->shouldUseCloudinary() ? 'cloudinary' : 'local';
    }

    /**
     * Check if image exists in both storages
     */
    public function checkImageAvailability(string $publicIdOrPath): array
    {
        $result = [
            'local' => false,
            'cloudinary' => false,
            'primary' => null,
            'fallback' => null
        ];

        // Check local storage
        if (str_contains($publicIdOrPath, '.') || str_starts_with($publicIdOrPath, 'courses/')) {
            $result['local'] = Storage::disk('public')->exists($publicIdOrPath);
        }

        // Check Cloudinary
        try {
            $cloudinaryUrl = Cloudinary::getUrl($publicIdOrPath);
            if ($cloudinaryUrl) {
                $result['cloudinary'] = true;
            }
        } catch (\Exception $e) {
            $result['cloudinary'] = false;
        }

        // Determine primary and fallback
        if ($this->shouldUseCloudinary()) {
            $result['primary'] = $result['cloudinary'] ? 'cloudinary' : null;
            $result['fallback'] = $result['local'] ? 'local' : null;
        } else {
            $result['primary'] = $result['local'] ? 'local' : null;
            $result['fallback'] = $result['cloudinary'] ? 'cloudinary' : null;
        }

        return $result;
    }

    /**
     * Get best available image URL with fallback
     */
    public function getBestImageUrl(string $publicIdOrPath, array $transformations = []): string
    {
        if (empty($publicIdOrPath)) {
            return '/images/default-course.jpg';
        }

        $availability = $this->checkImageAvailability($publicIdOrPath);

        // Try primary storage first
        if ($availability['primary'] === 'cloudinary') {
            try {
                return $this->getOptimizedUrl($publicIdOrPath, $transformations);
            } catch (\Exception $e) {
                // Fall through to fallback
            }
        } elseif ($availability['primary'] === 'local') {
            try {
                return $this->getLocalImageUrl($publicIdOrPath);
            } catch (\Exception $e) {
                // Fall through to fallback
            }
        }

        // Try fallback storage
        if ($availability['fallback'] === 'cloudinary') {
            try {
                return $this->getOptimizedUrl($publicIdOrPath, $transformations);
            } catch (\Exception $e) {
                // Fall through to default
            }
        } elseif ($availability['fallback'] === 'local') {
            try {
                return $this->getLocalImageUrl($publicIdOrPath);
            } catch (\Exception $e) {
                // Fall through to default
            }
        }

        // Final fallback
        return '/images/default-course.jpg';
    }
}
