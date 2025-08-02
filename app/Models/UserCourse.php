<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCourse extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'enrolled_at',
        'progress_percentage',
        'last_accessed_at',
        'completed_materials',
        'is_completed',
        'completed_at'
    ];

    protected $casts = [
        'enrolled_at' => 'datetime',
        'last_accessed_at' => 'datetime',
        'completed_at' => 'datetime',
        'completed_materials' => 'array',
        'progress_percentage' => 'decimal:2',
        'is_completed' => 'boolean'
    ];

    /**
     * Get the user that owns the course enrollment.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the course associated with the enrollment.
     * Using course_description table as primary course data source
     */
    public function courseDescription()
    {
        return $this->belongsTo(\App\Models\CourseDescription::class, 'course_id');
    }

    /**
     * Alias for courseDescription for backward compatibility
     */
    public function course()
    {
        return $this->courseDescription();
    }
}
