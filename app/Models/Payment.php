<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
     * Get the course associated with the payment.
     */
    public function course()
    {
        return $this->belongsTo(CourseDescription::class, 'course_id');
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
