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
        'fraud_status',
        'failure_reason'
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
     * FIXED: Reference to correct table/model
     */
    public function course()
    {
        // If you have a Course model that uses 'courses' table
        return $this->belongsTo(Course::class, 'course_id');

        // OR if you want to keep using CourseDescriptions but fix the table reference
        // Make sure CourseDescriptions model uses the correct table name
        // return $this->belongsTo(CourseDescriptions::class, 'course_id');
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

    /**
     * Check if payment is for duplicate course purchase
     */
    public function isDuplicatePurchase()
    {
        return self::where('user_profile_id', $this->user_profile_id)
                  ->where('course_id', $this->course_id)
                  ->where('status', 'success')
                  ->where('id', '!=', $this->id)
                  ->exists();
    }
}
