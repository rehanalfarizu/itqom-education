<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CourseDescription;

/**
 * @property int $id
 * @property int $user_id
 * @property int $course_id
 * @property \Illuminate\Support\Carbon|null $enrolled_at
 * @property numeric $progress_percentage
 * @property \Illuminate\Support\Carbon|null $last_accessed_at
 * @property array<array-key, mixed>|null $completed_materials
 * @property bool $is_completed
 * @property \Illuminate\Support\Carbon|null $completed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read CourseDescription $courseDescription
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCourse newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCourse newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCourse query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCourse whereCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCourse whereCompletedMaterials($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCourse whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCourse whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCourse whereEnrolledAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCourse whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCourse whereIsCompleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCourse whereLastAccessedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCourse whereProgressPercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCourse whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserCourse whereUserId($value)
 * @mixin \Eloquent
 */
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
        return $this->belongsTo(CourseDescription::class, 'course_id');
    }

    /**
     * Alias for courseDescription for backward compatibility
     */
    public function course()
    {
        return $this->courseDescription();
    }
}
