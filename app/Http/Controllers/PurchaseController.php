<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseDescription;
use App\Models\UserCourse;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    /**
     * Initiate course purchase
     */
    public function initiatePurchase(Request $request)
    {
        $request->validate([
            'course_description_id' => 'required|exists:course_description,id',
        ]);

        $user = Auth::user();
        $courseDescriptionId = $request->course_description_id;

        // Get course description
        $courseDescription = CourseDescription::findOrFail($courseDescriptionId);

        // Check if user already enrolled
        $existingEnrollment = UserCourse::where('user_id', $user->id)
            ->where('course_id', $courseDescriptionId)
            ->first();

        if ($existingEnrollment) {
            return response()->json([
                'success' => false,
                'message' => 'You are already enrolled in this course'
            ], 400);
        }

        // Get or create course bridge entry for this purchase
        $course = Course::where('course_description_id', $courseDescriptionId)->first();

        if (!$course) {
            // Auto-create if doesn't exist (backup mechanism)
            $course = Course::create([
                'course_description_id' => $courseDescriptionId,
                'title' => $courseDescription->title,
                'instructor' => $courseDescription->instructor_name,
                'video_count' => $courseDescription->video_count,
                'duration' => $courseDescription->duration . ' minutes',
                'original_price' => $courseDescription->price,
                'price' => $courseDescription->price_discount ?? $courseDescription->price,
                'image' => $courseDescription->image_url,
                'category' => $courseDescription->tag,
            ]);
        }

        try {
            DB::beginTransaction();

            // Create payment record
            $payment = Payment::create([
                'user_id' => $user->id,
                'course_id' => $courseDescriptionId, // Reference to course_description
                'amount' => $course->price,
                'original_amount' => $course->original_price,
                'payment_method' => $request->payment_method ?? 'pending',
                'status' => 'pending',
                'transaction_id' => 'TXN-' . time() . '-' . $user->id,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Purchase initiated successfully',
                'data' => [
                    'payment_id' => $payment->id,
                    'transaction_id' => $payment->transaction_id,
                    'amount' => $payment->amount,
                    'course' => [
                        'id' => $courseDescription->id,
                        'title' => $courseDescription->title,
                        'instructor' => $courseDescription->instructor_name,
                        'price' => $course->price,
                        'original_price' => $course->original_price,
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to initiate purchase: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Complete course purchase (after payment verification)
     */
    public function completePurchase(Request $request)
    {
        $request->validate([
            'payment_id' => 'required|exists:payments,id',
            'transaction_status' => 'required|in:success,failed',
        ]);

        $payment = Payment::findOrFail($request->payment_id);

        // Verify payment belongs to authenticated user
        if ($payment->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to payment'
            ], 403);
        }

        try {
            DB::beginTransaction();

            if ($request->transaction_status === 'success') {
                // Update payment status
                $payment->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                ]);

                // Create user course enrollment
                $userCourse = UserCourse::create([
                    'user_id' => $payment->user_id,
                    'course_id' => $payment->course_id, // This references course_description.id
                    'enrolled_at' => now(),
                    'progress_percentage' => 0,
                    'last_accessed_at' => now(),
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Course purchased successfully! You are now enrolled.',
                    'data' => [
                        'enrollment_id' => $userCourse->id,
                        'course_id' => $payment->course_id,
                        'enrolled_at' => $userCourse->enrolled_at,
                    ]
                ]);

            } else {
                // Payment failed
                $payment->update([
                    'status' => 'failed',
                    'failed_at' => now(),
                ]);

                DB::commit();

                return response()->json([
                    'success' => false,
                    'message' => 'Payment failed. Please try again.'
                ], 400);
            }

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to complete purchase: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's purchased courses
     */
    public function getUserCourses()
    {
        $user = Auth::user();

        $userCourses = UserCourse::with(['courseDescription'])
            ->where('user_id', $user->id)
            ->orderBy('enrolled_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $userCourses->map(function ($userCourse) {
                return [
                    'enrollment_id' => $userCourse->id,
                    'course' => [
                        'id' => $userCourse->courseDescription->id,
                        'title' => $userCourse->courseDescription->title,
                        'instructor' => $userCourse->courseDescription->instructor_name,
                        'image_url' => $userCourse->courseDescription->image_url,
                        'video_count' => $userCourse->courseDescription->video_count,
                        'duration' => $userCourse->courseDescription->duration,
                    ],
                    'progress' => $userCourse->progress_percentage,
                    'is_completed' => $userCourse->is_completed,
                    'enrolled_at' => $userCourse->enrolled_at,
                    'last_accessed_at' => $userCourse->last_accessed_at,
                ];
            })
        ]);
    }
}
