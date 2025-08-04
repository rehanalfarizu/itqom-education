<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\CloudinaryService;

class CourseDescription extends Model
{
    use HasFactory;

    protected $table = 'course_description';

    protected $fillable = [
        'title',
        'tag',
        'overview',
        'image_url',
        'thumbnail',
        'price',
        'price_discount',
        'instructor_name',
        'instructor_position',
        'instructor_image_url',
        'video_count',
        'duration',
        'features',
    ];

    protected $casts = [
        'features' => 'array',
        'price' => 'decimal:2',
        'price_discount' => 'decimal:2',
    ];

    protected $appends = ['image_url_optimized', 'thumbnail_url'];

    /**
     * Relasi ke Course (one-to-many) - for purchase/enrollment bridge
     */
    public function courses()
    {
        return $this->hasMany(Course::class, 'course_description_id');
    }

    /**
     * Relasi ke UserCourse untuk tracking enrollment langsung
     */
    public function userCourses()
    {
        return $this->hasMany(UserCourse::class, 'course_id');
    }

    /**
     * Relasi ke CourseContent
     */
    public function courseContents()
    {
        return $this->hasMany(CourseContent::class, 'course_description_id');
    }

    /**
     * Method untuk membuat course entry otomatis setelah course description dibuat
     */
    protected static function booted()
    {
        static::created(function ($courseDescription) {
            // Auto-create course entry untuk sistem pembelian
            Course::create([
                'course_description_id' => $courseDescription->id,
                'title' => $courseDescription->title,
                'instructor' => $courseDescription->instructor_name,
                'video_count' => $courseDescription->video_count,
                'duration' => $courseDescription->duration . ' minutes',
                'original_price' => $courseDescription->price,
                'price' => $courseDescription->price_discount ?? $courseDescription->price,
                'image' => $courseDescription->image_url,
                'category' => $courseDescription->tag,
            ]);
        });

        static::updated(function ($courseDescription) {
            // Update course entry yang terkait
            $courseDescription->courses()->update([
                'title' => $courseDescription->title,
                'instructor' => $courseDescription->instructor_name,
                'video_count' => $courseDescription->video_count,
                'duration' => $courseDescription->duration . ' minutes',
                'original_price' => $courseDescription->price,
                'price' => $courseDescription->price_discount ?? $courseDescription->price,
                'image' => $courseDescription->image_url,
                'category' => $courseDescription->tag,
            ]);
        });

        static::deleted(function ($courseDescription) {
            // Cascade delete course entries
            $courseDescription->courses()->delete();
        });
    }

    /**
     * Accessor untuk mendapatkan URL gambar yang sudah dioptimasi
     * Backend akan menangani Cloudinary secara internal
     */
    public function getImageUrlAttribute($value): ?string
    {
        if (!$value) {
            return '/images/default-course.jpg';
        }

        // Jika sudah berupa URL lengkap (Cloudinary atau external), return as is
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }

        // Untuk production (Heroku), gunakan Cloudinary
        if (app()->environment('production') || config('app.use_cloudinary', false)) {
            try {
                $cloudinaryService = app(CloudinaryService::class);
                $optimizedUrl = $cloudinaryService->getOptimizedUrl($value, [
                    'width' => 800,
                    'height' => 450,
                    'crop' => 'fill',
                    'quality' => 'auto',
                    'format' => 'auto'
                ]);

                // Only log in debug mode to reduce overhead
                if (config('app.debug')) {
                    \Illuminate\Support\Facades\Log::info('Cloudinary URL generated', [
                        'original' => $value,
                        'optimized' => $optimizedUrl
                    ]);
                }

                return $optimizedUrl;
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning('Cloudinary failed for image: ' . $e->getMessage(), [
                    'image' => $value,
                    'error' => $e->getMessage()
                ]);

                // Fallback: jika Cloudinary gagal, coba sebagai path lokal
                if (str_starts_with($value, '/storage/') || str_starts_with($value, 'storage/')) {
                    return url($value);
                }

                // Ultimate fallback
                return '/images/default-course.jpg';
            }
        }

        // Untuk development, gunakan storage lokal
        if (str_starts_with($value, '/storage/') || str_starts_with($value, 'storage/')) {
            return url($value);
        }

        // Jika path relatif, tambahkan storage prefix
        if (!str_starts_with($value, '/') && !str_starts_with($value, 'http')) {
            return url('/storage/' . $value);
        }

        // Default fallback
        return '/images/default-course.jpg';
    }

    /**
     * Accessor untuk mendapatkan URL gambar yang sudah dioptimasi dengan nama yang berbeda
     */
    public function getImageUrlOptimizedAttribute(): ?string
    {
        return $this->getImageUrlAttribute($this->attributes['image_url'] ?? null);
    }

    /**
     * Accessor untuk mendapatkan URL thumbnail yang sudah dioptimasi
     */
    public function getThumbnailUrlAttribute(): ?string
    {
        $imageUrl = $this->attributes['image_url'] ?? null;

        if (!$imageUrl) {
            return '/images/default-course-thumb.jpg';
        }

        // Jika sudah berupa URL lengkap, return as is untuk thumbnail
        if (filter_var($imageUrl, FILTER_VALIDATE_URL)) {
            return $imageUrl;
        }

        // Untuk production (Heroku), gunakan Cloudinary
        if (app()->environment('production') || isset($_ENV['DYNO'])) {
            try {
                $cloudinaryService = app(CloudinaryService::class);
                return $cloudinaryService->getOptimizedUrl($imageUrl, [
                    'width' => 300,
                    'height' => 200,
                    'crop' => 'fill'
                ]);
            } catch (\Exception $e) {
                return '/images/default-course-thumb.jpg';
            }
        }

        // Fallback untuk development
        return url('/storage/' . ltrim($imageUrl, '/'));
    }

    /**
     * Accessor untuk instructor image
     */
    public function getInstructorImageUrlAttribute($value): ?string
    {
        if (!$value) {
            return '/images/default-instructor.jpg';
        }

        // Jika sudah berupa URL lengkap
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }

        // Untuk production (Heroku), gunakan Cloudinary
        if (app()->environment('production') || isset($_ENV['DYNO'])) {
            try {
                $cloudinaryService = app(CloudinaryService::class);
                return $cloudinaryService->getOptimizedUrl($value, [
                    'width' => 150,
                    'height' => 150,
                    'crop' => 'fill'
                ]);
            } catch (\Exception $e) {
                return '/images/default-instructor.jpg';
            }
        }

        // Fallback untuk development
        if (str_starts_with($value, '/storage/') || str_starts_with($value, 'storage/')) {
            return url($value);
        }

        return url('/storage/' . ltrim($value, '/'));
    }
}
