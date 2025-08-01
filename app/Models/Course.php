<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CourseDescription;

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
        return $this->belongsTo(CourseDescription::class, 'course_description_id');
    }

    // Relasi ke UserCourse untuk mendapatkan data enrollment
    public function userCourses()
    {
        return $this->hasMany(UserCourse::class, 'course_id', 'course_description_id');
    }

    // Relasi ke CourseContent
    public function courseContents()
    {
        return $this->hasMany(CourseContent::class, 'course_id', 'course_description_id');
    }

    // Accessor untuk mendapatkan jumlah students
    public function getStudentsCountAttribute()
    {
        return $this->userCourses()->count();
    }
}
