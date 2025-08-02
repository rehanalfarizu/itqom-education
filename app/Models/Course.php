<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CourseDescription;
use App\Services\CloudinaryService;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_description_id',
        'title',
        'instructor',
        'video_count',
        'duration',
        'original_price',
        'price',
        'image',
        'category'
    ];

    protected $casts = [
        'original_price' => 'decimal:2',
        'price' => 'decimal:2',
    ];

    protected $appends = ['image_url', 'thumbnail_url'];

    // Relasi ke CourseDescription
    public function courseDescription()
    {
        return $this->belongsTo(CourseDescription::class, 'course_description_id');
    }

    // Relasi ke UserCourse untuk tracking enrollment
    public function userCourses()
    {
        return $this->hasMany(UserCourse::class);
    }

    // Accessor untuk mendapatkan URL gambar yang sudah dioptimasi
    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) {
            return '/images/default-course.jpg';
        }

        $cloudinaryService = app(CloudinaryService::class);

        // Use hybrid approach - best available source with fallback
        return $cloudinaryService->getBestImageUrl($this->image, [
            'width' => 800,
            'height' => 450,
            'crop' => 'fill',
            'quality' => 'auto',
            'fetch_format' => 'auto'
        ]);
    }

    // Accessor untuk thumbnail kecil
    public function getThumbnailUrlAttribute(): ?string
    {
        if (!$this->image) {
            return '/images/default-course.jpg';
        }

        $cloudinaryService = app(CloudinaryService::class);

        // Use hybrid approach for thumbnails
        return $cloudinaryService->getBestImageUrl($this->image, [
            'width' => 300,
            'height' => 200,
            'crop' => 'fill',
            'quality' => 'auto',
            'fetch_format' => 'auto'
        ]);
    }

    // Scope untuk mencari course berdasarkan kategori
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    // Scope untuk course yang populer (berdasarkan enrollment)
    public function scopePopular($query, $limit = 10)
    {
        return $query->withCount('userCourses')
                    ->orderBy('user_courses_count', 'desc')
                    ->limit($limit);
    }
}
