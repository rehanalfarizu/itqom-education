<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use HasFactory;

    protected $table = 'users_profile'; // Nama tabel yang benar

    protected $fillable = [
        'user_id',
        'fullname',
        'username',
        'dob',
        'email',
        'bio',
        'hobbies',
        'avatar',
        'badges',
        'level',
        'progress',
    ];

    protected $casts = [
        'hobbies' => 'array', // Otomatis casting ke array
        'badges' => 'array',  // Otomatis casting ke array
        'dob' => 'date',      // Otomatis casting ke Carbon date object
    ];

    /**
     * Get the user that owns the profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

