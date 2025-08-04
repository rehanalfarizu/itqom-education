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
    private static $initialized = false;

    /**
     * Static method to ensure Cloudinary is initialized globally
     */
    public static function ensureInitialized(): void
    {
        if (!self::$initialized && (app()->environment('production') || env('FILESYSTEM_DISK') === 'cloudinary')) {
            try {
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
                    }
                }

                if ($cloudName && $apiKey && $apiSecret) {
                    // Initialize Cloudinary with proper configuration
                    Configuration::instance([
                        'cloud' => [
                            'cloud_name' => $cloudName,
                            'api_key' => $apiKey,
                            'api_secret' => $apiSecret,
                        ],
                        'url' => [
                            'secure' => true
                        ]
                    ]);
                    
                    // Also set global Cloudinary configuration for Laravel package
                    config([
                        'cloudinary.cloud.cloud_name' => $cloudName,
                        'cloudinary.cloud.api_key' => $apiKey,
                        'cloudinary.cloud.api_secret' => $apiSecret,
                        'cloudinary.url.secure' => true
                    ]);
                    
                    self::$initialized = true;
                    Log::info('CloudinaryService: Static initialization successful for cloud: ' . $cloudName);
                }
            } catch (\Exception $e) {
                Log::error('CloudinaryService: Static initialization failed: ' . $e->getMessage());
            }
        }
    }

    public function __construct()
    {
        // Ensure static initialization is called
        self::ensureInitialized();
        
        // Initialize Cloudinary instance if we should use it
        if ($this->shouldUseCloudinary()) {
            try {
                // Get credentials from environment
                $cloudName = config('cloudinary.cloud.cloud_name') ?: env('CLOUDINARY_CLOUD_NAME');
                $apiKey = config('cloudinary.cloud.api_key') ?: env('CLOUDINARY_API_KEY');
                $apiSecret = config('cloudinary.cloud.api_secret') ?: env('CLOUDINARY_API_SECRET');

                // Parse from CLOUDINARY_URL if individual vars not available
                if ((!$cloudName || !$apiKey || !$apiSecret) && env('CLOUDINARY_URL')) {
                    $cloudinaryUrl = env('CLOUDINARY_URL');
                    if (preg_match('/cloudinary:\/\/(\d+):([^@]+)@(.+)/', $cloudinaryUrl, $matches)) {
                        $apiKey = $matches[1];
                        $apiSecret = $matches[2];
                        $cloudName = $matches[3];
                    }
                }

                if ($cloudName && $apiKey && $apiSecret) {
                    // Create Cloudinary instance
                    $this->cloudinary = new CloudinaryApi([
                        'cloud' => [
                            'cloud_name' => $cloudName,
                            'api_key' => $apiKey,
                            'api_secret' => $apiSecret,
                        ],
                        'url' => ['secure' => true]
                    ]);
                    
                    Log::info('Cloudinary instance created successfully for cloud: ' . $cloudName);
                } else {
                    Log::error('Failed to get complete Cloudinary credentials');
                    $this->cloudinary = null;
                }
            } catch (\Exception $e) {
                Log::error('Cloudinary instance creation failed: ' . $e->getMessage());
                $this->cloudinary = null;
            }
        } else {
            $this->cloudinary = null;
        }
            // Reuse existing configuration
            $this->cloudinary = new CloudinaryApi();
        } else {
            // Only log once per request
            if (!self::$initialized) {
                Log::info('Using local storage (not production environment)');
                self::$initialized = true;
            }
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

        // Jika sudah berupa URL lengkap, return as is
        if (filter_var($publicIdOrPath, FILTER_VALIDATE_URL)) {
            return $publicIdOrPath;
        }

        // Jika production dan cloudinary available
        if ($this->shouldUseCloudinary() && $this->cloudinary) {
            try {
                $defaultTransformations = [
                    'quality' => 'auto',
                    'fetch_format' => 'auto'
                ];

                $transformations = array_merge($defaultTransformations, $transformations);

                // Clean the public ID - remove any leading slashes or storage paths
                $publicId = $publicIdOrPath;
                $publicId = ltrim($publicId, '/');
                $publicId = str_replace('storage/', '', $publicId);
                
                // Check if we're dealing with a Cloudinary URL or ID that may have been modified
                if (str_contains($publicId, 'res.cloudinary.com') || 
                    str_contains($publicId, '/upload/')) {
                    // Extract just the public ID from a full Cloudinary URL if that's what we received
                    $parts = explode('/upload/', $publicId);
                    if (count($parts) > 1) {
                        $publicId = end($parts);
                    }
                }

                // Add folder prefix if not already present
                $folder = config('cloudinary.folder', 'itqom-platform');
                if (!str_starts_with($publicId, $folder . '/') && !str_contains($publicId, '/')) {
                    $publicId = $folder . '/' . $publicId;
                }

                // Menggunakan Cloudinary SDK jika tersedia untuk membangun URL yang benar
                if (class_exists('\\Cloudinary\\Cloudinary') && $this->cloudinary) {
                    try {
                        // Build transformations array properly for Cloudinary SDK
                        $transformArray = [];
                        if (isset($transformations['width'])) $transformArray['width'] = $transformations['width'];
                        if (isset($transformations['height'])) $transformArray['height'] = $transformations['height'];
                        if (isset($transformations['crop'])) $transformArray['crop'] = $transformations['crop'];
                        if (isset($transformations['quality'])) $transformArray['quality'] = $transformations['quality'];
                        if (isset($transformations['fetch_format'])) $transformArray['fetch_format'] = $transformations['fetch_format'];
                        
                        // Use Cloudinary SDK with proper format
                        if (!empty($transformArray)) {
                            // Pass transformations as individual parameters, not nested array
                            return $this->cloudinary->image($publicId)->toUrl($transformArray);
                        } else {
                            return $this->cloudinary->image($publicId)->toUrl();
                        }
                    } catch (\Exception $e) {
                        // Fallback ke metode manual jika SDK gagal
                        Log::warning('Cloudinary SDK URL generation failed: ' . $e->getMessage() . ' - falling back to manual URL construction');
                    }
                }

                // Manual URL construction as fallback
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

                $finalUrl = $baseUrl . $publicId;
                // Reduced logging to improve performance
                if (app()->environment('local')) {
                    Log::debug('Cloudinary URL generated: ' . $finalUrl);
                }

                return $finalUrl;
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
     * Check if we should use Cloudinary (public method)
     */
    public function isCloudinaryEnabled(): bool
    {
        return $this->shouldUseCloudinary();
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

        // Check if all Cloudinary credentials are available
        $cloudName = config('cloudinary.cloud.cloud_name') ?: env('CLOUDINARY_CLOUD_NAME');
        $apiKey = config('cloudinary.cloud.api_key') ?: env('CLOUDINARY_API_KEY');
        $apiSecret = config('cloudinary.cloud.api_secret') ?: env('CLOUDINARY_API_SECRET');

        if ($cloudName && $apiKey && $apiSecret) {
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
        // Double check configuration before attempting upload
        if (!$this->cloudinary) {
            throw new \Exception('Cloudinary not properly initialized - check credentials');
        }

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
     * Ensure Cloudinary is properly initialized
     */
    private function ensureCloudinaryInitialized(): void
    {
        if ($this->shouldUseCloudinary() && !$this->cloudinary) {
            try {
                // Get credentials
                $cloudName = config('cloudinary.cloud.cloud_name') ?: env('CLOUDINARY_CLOUD_NAME');
                $apiKey = config('cloudinary.cloud.api_key') ?: env('CLOUDINARY_API_KEY');
                $apiSecret = config('cloudinary.cloud.api_secret') ?: env('CLOUDINARY_API_SECRET');

                // Parse from CLOUDINARY_URL if needed
                if ((!$cloudName || !$apiKey || !$apiSecret) && env('CLOUDINARY_URL')) {
                    $cloudinaryUrl = env('CLOUDINARY_URL');
                    if (preg_match('/cloudinary:\/\/(\d+):([^@]+)@(.+)/', $cloudinaryUrl, $matches)) {
                        $apiKey = $matches[1];
                        $apiSecret = $matches[2];
                        $cloudName = $matches[3];
                    }
                }

                if ($cloudName && $apiKey && $apiSecret) {
                    $this->cloudinary = new CloudinaryApi([
                        'cloud' => [
                            'cloud_name' => $cloudName,
                            'api_key' => $apiKey,
                            'api_secret' => $apiSecret,
                        ],
                        'url' => ['secure' => true]
                    ]);
                    Log::info('Cloudinary re-initialized successfully');
                } else {
                    Log::error('Failed to re-initialize Cloudinary: missing credentials');
                }
            } catch (\Exception $e) {
                Log::error('Cloudinary re-initialization failed: ' . $e->getMessage());
            }
        }
    }

    /**
     * Upload image with hybrid approach (primary + backup)
     */
    public function uploadImageHybrid(UploadedFile $file, ?string $folder = null): string
    {
        // Set default folder if null
        $folder = $folder ?? 'courses';

        // Ensure CloudinaryService is properly initialized
        $this->ensureCloudinaryInitialized();

        // Fast check - if we should use Cloudinary but it's not initialized, fail fast
        if ($this->shouldUseCloudinary() && !$this->cloudinary) {
            Log::error('Cloudinary should be used but not initialized - falling back to local storage');
            // Use local storage as immediate fallback on configuration error
            try {
                return $this->storeImageLocally($file, $folder);
            } catch (\Exception $e) {
                throw new \Exception('All storage methods failed: Configuration invalid and local storage unavailable: ' . $e->getMessage());
            }
        }

        $primaryPath = null;

        try {
            if ($this->shouldUseCloudinary() && $this->cloudinary) {
                // Primary: Cloudinary only (no backup to reduce processing time)
                $primaryPath = $this->uploadToCloudinary($file, $folder);
                Log::info('Cloudinary upload successful: ' . $primaryPath);
                return $primaryPath;
            } else {
                // Primary: Local storage only
                $primaryPath = $this->storeImageLocally($file, $folder);
                Log::info('Local storage upload successful: ' . $primaryPath);
                return $primaryPath;
            }
        } catch (\Exception $e) {
            Log::error('Primary storage failed: ' . $e->getMessage());

            // Fast fallback strategy
            if ($this->shouldUseCloudinary() && !$primaryPath) {
                // Try local as fallback
                try {
                    $fallbackPath = $this->storeImageLocally($file, $folder ?? 'courses');
                    Log::info('Fallback to local storage successful: ' . $fallbackPath);
                    return $fallbackPath;
                } catch (\Exception $fallbackError) {
                    throw new \Exception('All storage methods failed: Primary Cloudinary failed and local fallback failed: ' . $fallbackError->getMessage());
                }
            } else {
                // Try Cloudinary as fallback (only if credentials are available)
                if ($this->cloudinary) {
                    try {
                        $fallbackPath = $this->uploadToCloudinary($file, $folder);
                        Log::info('Fallback to Cloudinary successful: ' . $fallbackPath);
                        return $fallbackPath;
                    } catch (\Exception $fallbackError) {
                        throw new \Exception('All storage methods failed: Primary local failed and Cloudinary fallback failed: ' . $fallbackError->getMessage());
                    }
                } else {
                    throw new \Exception('All storage methods failed: No fallback available');
                }
            }
        }
    }

    /**
     * Ensure Cloudinary is properly initialized
     */
    private function ensureCloudinaryInitialized(): void
    {
        if ($this->shouldUseCloudinary() && !$this->cloudinary) {
            try {
                // Get credentials from multiple sources for better compatibility
                $cloudName = config('cloudinary.cloud.cloud_name') ?:
                           config('filesystems.disks.cloudinary.cloud_name') ?:
                           env('CLOUDINARY_CLOUD_NAME');
                $apiKey = config('cloudinary.cloud.api_key') ?:
                        config('filesystems.disks.cloudinary.api_key') ?:
                        env('CLOUDINARY_API_KEY');
                $apiSecret = config('cloudinary.cloud.api_secret') ?:
                           config('filesystems.disks.cloudinary.api_secret') ?:
                           env('CLOUDINARY_API_SECRET');

                // Try to parse from CLOUDINARY_URL as fallback
                if ((!$cloudName || !$apiKey || !$apiSecret) && env('CLOUDINARY_URL')) {
                    $cloudinaryUrl = env('CLOUDINARY_URL');
                    if (preg_match('/cloudinary:\/\/(\d+):([^@]+)@(.+)/', $cloudinaryUrl, $matches)) {
                        $apiKey = $matches[1];
                        $apiSecret = $matches[2];
                        $cloudName = $matches[3];
                        Log::info('Parsed Cloudinary credentials from CLOUDINARY_URL');
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
                    $this->cloudinary = new CloudinaryApi();
                    Log::info('Cloudinary re-initialized successfully for cloud: ' . $cloudName);
                } else {
                    Log::error('Cannot initialize Cloudinary - missing credentials');
                    $this->cloudinary = null;
                }
            } catch (\Exception $e) {
                Log::error('Cloudinary re-initialization failed: ' . $e->getMessage());
                $this->cloudinary = null;
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
     * Debug method to check if file exists in Cloudinary
     */
    public function checkFileExists(string $publicId): bool
    {
        if (!$this->shouldUseCloudinary() || !$this->cloudinary) {
            return false;
        }

        try {
            $result = $this->cloudinary->adminApi()->asset($publicId);
            return !empty($result['public_id']);
        } catch (\Exception $e) {
            Log::warning('File check failed: ' . $e->getMessage(), ['public_id' => $publicId]);
            return false;
        }
    }

    /**
     * List all files in a folder for debugging
     */
    public function listFiles(string $folder = null): array
    {
        if (!$this->shouldUseCloudinary() || !$this->cloudinary) {
            return [];
        }

        try {
            $folder = $folder ?? config('cloudinary.folder', 'itqom-platform');
            $result = $this->cloudinary->adminApi()->assets([
                'type' => 'upload',
                'prefix' => $folder,
                'max_results' => 100
            ]);

            return array_map(function($asset) {
                return $asset['public_id'];
            }, $result['resources'] ?? []);
        } catch (\Exception $e) {
            Log::error('Failed to list Cloudinary files: ' . $e->getMessage());
            return [];
        }
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
