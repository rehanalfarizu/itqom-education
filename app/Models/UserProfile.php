<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property string|null $fullname
 * @property string|null $username
 * @property \Illuminate\Support\Carbon|null $dob
 * @property string $email
 * @property string|null $bio
 * @property array<array-key, mixed>|null $hobbies
 * @property string|null $avatar
 * @property array<array-key, mixed>|null $badges
 * @property int $level
 * @property int $progress
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile whereBadges($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile whereBio($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile whereDob($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile whereFullname($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile whereHobbies($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile whereProgress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserProfile whereUsername($value)
 * @mixin \Eloquent
 */
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

