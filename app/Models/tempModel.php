<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseDescriptions extends Model
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
        'features'
    ];

    protected $casts = [
        'features' => 'array',
        'price' => 'decimal:2',
        'price_discount' => 'decimal:2',
    ];

    public function course()
    {
        return $this->hasOne(Course::class, 'course_description_id');
    }
       public function courseContents()
    {
        return $this->hasMany(CourseContent::class, 'course_description_id');
    }

    protected static function booted()
    {
        static::created(function ($courseDescription) {
            $courseDescription->createOrUpdateCourse();
        });

        static::updated(function ($courseDescription) {
            $courseDescription->createOrUpdateCourse();
        });
    }

    public function createOrUpdateCourse()
    {
        $this->course()->updateOrCreate(
            ['course_description_id' => $this->id],
            [
                'title' => $this->title,
                'instructor' => $this->instructor_name,
                'duration' => $this->duration,
                'video_count' => $this->video_count,
                'original_price' => $this->price,
                'price' => $this->price_discount ?? $this->price,
                'image' => $this->image_url,
                'category' => $this->tag,
            ]
        );
    }
}
