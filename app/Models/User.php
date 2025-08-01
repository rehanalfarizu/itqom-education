<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // Pastikan ini ada

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get the user profile associated with the user.
     */
    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    /**
     * Get the user's enrolled courses.
     */
    public function userCourses()
    {
        return $this->hasMany(UserCourse::class);
    }

    /**
     * Get the user's purchased courses through payments.
     */
    public function purchasedCourses()
    {
        return $this->hasManyThrough(
            CourseDescription::class,
            Payment::class,
            'user_profile_id', // Foreign key on payments table
            'id',              // Foreign key on course_description table
            'id',              // Local key on users table
            'course_id'        // Local key on payments table
        )->where('payments.status', 'success');
    }
}
