<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    // Relasi ke CourseDescription
    public function courseDescription()
    {
        return $this->belongsTo(CourseDescriptions::class, 'course_description_id');
    }
}
