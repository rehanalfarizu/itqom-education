<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\CourseDescription;

/**
 * @property int $id
 * @property int $user_profile_id
 * @property int $course_id
 * @property string $order_id
 * @property numeric $amount
 * @property string $status
 * @property string|null $snap_token
 * @property string|null $payment_type
 * @property string|null $transaction_id
 * @property \Illuminate\Support\Carbon|null $transaction_time
 * @property string|null $transaction_status
 * @property string|null $fraud_status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read CourseDescription|null $courseDescription
 * @property-read \App\Models\UserProfile $userProfile
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment failed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment pending()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment successful()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereFraudStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment wherePaymentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereSnapToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereTransactionStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereTransactionTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereUserProfileId($value)
 * @mixin \Eloquent
 */
class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_profile_id',
        'course_id',
        'order_id',
        'amount',
        'status',
        'snap_token',
        'payment_type',
        'transaction_id',
        'transaction_time',
        'transaction_status',
        'fraud_status'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_time' => 'datetime'
    ];

    /**
     * Get the user profile that owns the payment.
     */
    public function userProfile()
    {
        return $this->belongsTo(UserProfile::class, 'user_profile_id');
    }

        /**
     * Get the course description associated with the payment.
     */
    public function courseDescription(): BelongsTo
    {
        return $this->belongsTo(CourseDescription::class, 'course_description_id');
    }

    /**
     * Scope for successful payments
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    /**
     * Scope for pending payments
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for failed payments
     */
    public function scopeFailed($query)
    {
        return $query->whereIn('status', ['failed', 'cancelled', 'expired']);
    }
}
