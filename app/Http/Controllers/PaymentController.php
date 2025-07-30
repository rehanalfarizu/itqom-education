<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Midtrans\Snap;
use Midtrans\Config;
use Midtrans\Notification;
use Midtrans\Transaction;
use App\Models\Payment;
use App\Models\CourseDescriptions;
use App\Models\UserProfile;
use Exception;

class PaymentController extends Controller
{
    public function __construct()
    {
        // Set Midtrans configuration
        \Midtrans\Config::$serverKey = config('services.midtrans.server_key');
        \Midtrans\Config::$isProduction = config('services.midtrans.is_production');
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;
    }

    /**
     * Create Snap Token for payment - DENGAN VALIDASI DUPLIKAT
     */
    public function createSnapToken(Request $request)
    {
        try {
            // Validate request
            $request->validate([
                'course_id' => 'required|exists:course_description,id',
                'user_profile_id' => 'required|exists:users_profile,id',
                'amount' => 'required|numeric|min:1'
            ]);

            $courseId = $request->course_id;
            $userProfileId = $request->user_profile_id;
            $amount = $request->amount;

            // ======= VALIDASI PEMBELIAN DUPLIKAT =======
            $existingSuccessfulPayment = DB::table('payments')
                ->where('user_profile_id', $userProfileId)
                ->where('course_id', $courseId)
                ->where('status', 'success')
                ->first();

            if ($existingSuccessfulPayment) {
                Log::warning('Attempted duplicate course purchase', [
                    'user_profile_id' => $userProfileId,
                    'course_id' => $courseId,
                    'existing_order_id' => $existingSuccessfulPayment->order_id
                ]);

                return response()->json([
                    'success' => false,
                    'error' => 'Anda sudah membeli kursus ini sebelumnya.',
                    'message' => 'Course sudah dibeli',
                    'error_code' => 'DUPLICATE_PURCHASE'
                ], 400);
            }

            // Cek juga apakah ada pembayaran pending untuk course yang sama
            $existingPendingPayment = DB::table('payments')
                ->where('user_profile_id', $userProfileId)
                ->where('course_id', $courseId)
                ->where('status', 'pending')
                ->where('created_at', '>', now()->subSeconds(5)) // Hanya cek pending yang dibuat dalam 30 menit terakhir
                ->first();

            if ($existingPendingPayment) {
                Log::warning('Attempted purchase while pending payment exists', [
                    'user_profile_id' => $userProfileId,
                    'course_id' => $courseId,
                    'pending_order_id' => $existingPendingPayment->order_id
                ]);

                return response()->json([
                    'success' => false,
                    'error' => 'Anda masih memiliki pembayaran yang sedang diproses untuk kursus ini. Silakan selesaikan pembayaran sebelumnya atau tunggu beberapa saat.',
                    'message' => 'Pembayaran pending masih ada',
                    'error_code' => 'PENDING_PAYMENT_EXISTS',
                    'pending_order_id' => $existingPendingPayment->order_id
                ], 400);
            }
            // ======= END VALIDASI DUPLIKAT =======

            // Get course data
            $course = CourseDescriptions::findOrFail($courseId);
            $userProfile = UserProfile::findOrFail($userProfileId);

            // Generate unique order ID
            $orderId = 'ORDER-' . $courseId . '-' . $userProfileId . '-' . uniqid();

            // Save transaction pending to database
            Payment::create([
                'order_id' => $orderId,
                'user_profile_id' => $userProfileId,
                'course_id' => $courseId,
                'amount' => $amount,
                'status' => 'pending',
            ]);

            // Prepare transaction details for Midtrans
            $transactionDetails = [
                'order_id' => $orderId,
                'gross_amount' => (int) $amount
            ];

            $itemDetails = [
                [
                    'id' => $courseId,
                    'price' => (int) $amount,
                    'quantity' => 1,
                    'name' => $course->title ?? 'Course',
                    'category' => 'Education'
                ]
            ];

            $customerDetails = [
                'first_name' => $userProfile->fullname ?? 'Customer',
                'email' => $userProfile->email,
                'phone' => $userProfile->phone ?? '08123456789'
            ];

            // Prepare transaction array
            $transactionArray = [
                'transaction_details' => $transactionDetails,
                'item_details' => $itemDetails,
                'customer_details' => $customerDetails,
                'enabled_payments' => [
                    'gopay', 'bank_transfer', 'credit_card', 'cstore', 'bca_va',
                    'bni_va', 'bri_va', 'other_va', 'qris'
                ],
                'callbacks' => [
                    'finish' => url('/payment/finish'),
                    'unfinish' => url('/payment/unfinish'),
                    'error' => url('/payment/error')
                ]
            ];

            Log::info('Creating Midtrans transaction', [
                'order_id' => $orderId,
                'amount' => $amount,
                'user_profile_id' => $userProfileId,
                'course_id' => $courseId
            ]);

            // Create snap token
            $snapToken = \Midtrans\Snap::getSnapToken($transactionArray);

            return response()->json([
                'success' => true,
                'snap_token' => $snapToken,
                'order_id' => $orderId,
                'message' => 'Snap token created successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error creating snap token', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to create payment token: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle Midtrans notification - DENGAN VALIDASI DUPLIKAT
     */
    public function handleNotification(Request $request)
    {
        try {
            Log::info('Received Midtrans notification', $request->all());

            // Initialize notification
            $notif = new \Midtrans\Notification();

            $transactionStatus = $notif->transaction_status;
            $orderId = $notif->order_id;
            $fraudStatus = $notif->fraud_status ?? null;
            $transactionTime = $notif->transaction_time ?? null;
            $paymentType = $notif->payment_type ?? null;
            $grossAmount = $notif->gross_amount ?? null;
            $transactionId = $notif->transaction_id ?? null;

            Log::info('Midtrans notification processed', [
                'order_id' => $orderId,
                'transaction_status' => $transactionStatus,
                'fraud_status' => $fraudStatus,
                'transaction_id' => $transactionId
            ]);

            // Get payment record
            $payment = DB::table('payments')->where('order_id', $orderId)->first();

            if (!$payment) {
                Log::error('Payment not found for notification', ['order_id' => $orderId]);
                return response()->json(['status' => 'error', 'message' => 'Payment not found'], 404);
            }

            // ======= VALIDASI DUPLIKAT SAAT NOTIFICATION =======
            if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
                $existingSuccessfulPayment = DB::table('payments')
                    ->where('user_profile_id', $payment->user_profile_id)
                    ->where('course_id', $payment->course_id)
                    ->where('status', 'success')
                    ->where('order_id', '!=', $orderId) // Exclude current payment
                    ->first();

                if ($existingSuccessfulPayment) {
                    Log::warning('Duplicate successful payment detected in notification', [
                        'current_order_id' => $orderId,
                        'existing_order_id' => $existingSuccessfulPayment->order_id,
                        'user_profile_id' => $payment->user_profile_id,
                        'course_id' => $payment->course_id
                    ]);

                    // Update current payment as failed due to duplicate
                    DB::table('payments')
                        ->where('order_id', $orderId)
                        ->update([
                            'status' => 'failed',
                            'transaction_id' => $transactionId,
                            'payment_type' => $paymentType,
                            'transaction_status' => $transactionStatus,
                            'failure_reason' => 'Duplicate purchase - user already owns this course',
                            'updated_at' => now(),
                        ]);

                    return response()->json(['status' => 'duplicate_prevented']);
                }
            }
            // ======= END VALIDASI DUPLIKAT =======

            // Map payment status
            $mappedStatus = $this->mapPaymentStatus($transactionStatus);

            // Update payment in database
            $updated = DB::table('payments')
                ->where('order_id', $orderId)
                ->update([
                    'status' => $mappedStatus,
                    'transaction_id' => $transactionId,
                    'payment_type' => $paymentType,
                    'transaction_status' => $transactionStatus,
                    'updated_at' => now(),
                ]);

            Log::info('Payment status updated in DB', [
                'order_id' => $orderId,
                'new_status' => $mappedStatus,
                'rows_affected' => $updated
            ]);

            // Grant course access if payment is successful
            if ($mappedStatus === 'success') {
                Log::info('Granting course access', [
                    'user_profile_id' => $payment->user_profile_id,
                    'course_id' => $payment->course_id
                ]);

                $this->grantCourseAccess(
                    $payment->user_profile_id,
                    $payment->course_id
                );
            }

            return response()->json(['status' => 'success']);

        } catch (Exception $e) {
            Log::error('Error handling Midtrans notification', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json(['status' => 'error'], 500);
        }
    }

    /**
     * Check payment status directly from Midtrans - DENGAN VALIDASI DUPLIKAT
     */
    public function checkPaymentStatus(Request $request, $orderId)
    {
        try {
            Log::info('Manual payment status check', ['order_id' => $orderId]);

            // Get payment record first
            $payment = DB::table('payments')->where('order_id', $orderId)->first();

            if (!$payment) {
                return response()->json([
                    'error' => 'Payment not found',
                    'order_id' => $orderId
                ], 404);
            }

            // Check status from Midtrans
            $status = \Midtrans\Transaction::status($orderId);

            Log::info('Payment status from Midtrans', [
                'order_id' => $orderId,
                'status' => $status->transaction_status,
                'payment_type' => $status->payment_type ?? null
            ]);

            $mappedStatus = $this->mapPaymentStatus($status->transaction_status);

            // ======= VALIDASI DUPLIKAT SAAT MANUAL CHECK =======
            if ($mappedStatus === 'success') {
                $existingSuccessfulPayment = DB::table('payments')
                    ->where('user_profile_id', $payment->user_profile_id)
                    ->where('course_id', $payment->course_id)
                    ->where('status', 'success')
                    ->where('order_id', '!=', $orderId)
                    ->first();

                if ($existingSuccessfulPayment) {
                    Log::warning('Duplicate successful payment detected in manual check', [
                        'current_order_id' => $orderId,
                        'existing_order_id' => $existingSuccessfulPayment->order_id,
                        'user_profile_id' => $payment->user_profile_id,
                        'course_id' => $payment->course_id
                    ]);

                    // Update current payment as failed due to duplicate
                    DB::table('payments')
                        ->where('order_id', $orderId)
                        ->update([
                            'status' => 'failed',
                            'transaction_id' => $status->transaction_id ?? null,
                            'payment_type' => $status->payment_type ?? null,
                            'transaction_status' => $status->transaction_status,
                            'failure_reason' => 'Duplicate purchase - user already owns this course',
                            'updated_at' => now(),
                        ]);

                    return response()->json([
                        'order_id' => $orderId,
                        'status' => $status->transaction_status,
                        'payment_status' => 'failed',
                        'error' => 'Duplicate purchase detected - you already own this course',
                        'existing_order_id' => $existingSuccessfulPayment->order_id
                    ]);
                }
            }
            // ======= END VALIDASI DUPLIKAT =======

            // Update status in database
            $updated = DB::table('payments')
                ->where('order_id', $orderId)
                ->update([
                    'status' => $mappedStatus,
                    'transaction_id' => $status->transaction_id ?? null,
                    'payment_type' => $status->payment_type ?? null,
                    'transaction_status' => $status->transaction_status,
                    'updated_at' => now(),
                ]);

            Log::info('Payment status updated via manual check', [
                'order_id' => $orderId,
                'new_status' => $mappedStatus,
                'rows_affected' => $updated
            ]);

            // Grant access if payment is successful
            if ($mappedStatus === 'success') {
                $this->grantCourseAccess(
                    $payment->user_profile_id,
                    $payment->course_id
                );
            }

            return response()->json([
                'order_id' => $orderId,
                'status' => $status->transaction_status,
                'payment_status' => $mappedStatus,
                'amount' => $status->gross_amount ?? null,
                'payment_type' => $status->payment_type ?? null,
                'transaction_time' => $status->transaction_time ?? null
            ]);

        } catch (Exception $e) {
            Log::error('Error checking payment status', [
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'error' => 'Failed to check payment status',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Grant course access to user - DENGAN VALIDASI DUPLIKAT
     */
    protected function grantCourseAccess($userId, $courseId)
    {
        try {
            // Check if access already granted
            $existingAccess = DB::table('user_courses')
                               ->where('user_id', $userId)
                               ->where('course_id', $courseId)
                               ->exists();

            if (!$existingAccess) {
                DB::table('user_courses')->insert([
                    'user_id' => $userId,
                    'course_id' => $courseId,
                    'enrolled_at' => now(),
                    'progress' => 0,
                    'completed' => false,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                Log::info('Course access granted', [
                    'user_id' => $userId,
                    'course_id' => $courseId
                ]);
            } else {
                Log::info('Course access already exists', [
                    'user_id' => $userId,
                    'course_id' => $courseId
                ]);
            }
        } catch (Exception $e) {
            Log::error('Error granting course access', [
                'user_id' => $userId,
                'course_id' => $courseId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Map Midtrans status to simpler status
     */
    protected function mapPaymentStatus($midtransStatus)
    {
        $statusMap = [
            'capture' => 'success',
            'settlement' => 'success',
            'pending' => 'pending',
            'deny' => 'failed',
            'expire' => 'expired',
            'cancel' => 'cancelled',
            'failure' => 'failed'
        ];

        return $statusMap[$midtransStatus] ?? $midtransStatus;
    }

    /**
     * Get payment history for user - DENGAN INFO DUPLIKAT
     */
    public function getUserPayments(Request $request)
    {
        try {
            $userProfileId = $request->input('user_profile_id');

            if (!$userProfileId) {
                return response()->json(['error' => 'User profile ID is required'], 400);
            }

            $payments = DB::table('payments')
                          ->leftJoin('course_description', 'payments.course_id', '=', 'course_description.id')
                          ->where('payments.user_profile_id', $userProfileId)
                          ->select('payments.*', 'course_description.title as course_title')
                          ->orderBy('payments.created_at', 'desc')
                          ->get();

            // Group by course to identify duplicates
            $groupedPayments = $payments->groupBy('course_id');
            $processedPayments = [];

            foreach ($groupedPayments as $courseId => $coursePayments) {
                $successfulPayments = $coursePayments->where('status', 'success');

                foreach ($coursePayments as $payment) {
                    $paymentArray = (array) $payment;

                    // Add duplicate information
                    if ($successfulPayments->count() > 1) {
                        $paymentArray['is_duplicate_course'] = true;
                        $paymentArray['successful_purchases_count'] = $successfulPayments->count();
                    } else {
                        $paymentArray['is_duplicate_course'] = false;
                        $paymentArray['successful_purchases_count'] = $successfulPayments->count();
                    }

                    $processedPayments[] = $paymentArray;
                }
            }

            return response()->json([
                'success' => true,
                'payments' => $processedPayments,
                'message' => 'Payment history fetched successfully.'
            ]);

        } catch (Exception $e) {
            Log::error('Error fetching user payments', [
                'error' => $e->getMessage()
            ]);

            return response()->json(['error' => 'Failed to fetch payments'], 500);
        }
    }

    /**
     * Check if user already purchased a course - ENDPOINT BARU
     */
    public function checkCoursePurchase(Request $request)
    {
        try {
            $request->validate([
                'user_profile_id' => 'required|exists:users_profile,id',
                'course_id' => 'required|exists:course_description,id'
            ]);

            $userProfileId = $request->user_profile_id;
            $courseId = $request->course_id;

            $existingPayment = DB::table('payments')
                ->where('user_profile_id', $userProfileId)
                ->where('course_id', $courseId)
                ->where('status', 'success')
                ->first();

            return response()->json([
                'has_purchased' => !!$existingPayment,
                'payment_details' => $existingPayment ? [
                    'order_id' => $existingPayment->order_id,
                    'purchased_at' => $existingPayment->updated_at,
                    'amount' => $existingPayment->amount
                ] : null
            ]);

        } catch (Exception $e) {
            Log::error('Error checking course purchase', [
                'error' => $e->getMessage()
            ]);

            return response()->json(['error' => 'Failed to check course purchase'], 500);
        }
    }

    /**
     * Alternative callback method (jika webhook tidak bekerja)
     */
    public function midtransCallback(Request $request)
    {
        try {
            $serverKey = config('services.midtrans.server_key');
            $orderId = $request->order_id;
            $statusCode = $request->status_code;
            $grossAmount = $request->gross_amount;
            $signatureKey = $request->signature_key;

            // Verify signature
            $hashed = hash("sha512", $orderId.$statusCode.$grossAmount.$serverKey);

            if ($hashed === $signatureKey) {
                Log::info('Midtrans callback verified', [
                    'order_id' => $orderId,
                    'transaction_status' => $request->transaction_status
                ]);

                if ($request->transaction_status == 'settlement' || $request->transaction_status == 'capture') {
                    // Get payment record
                    $payment = DB::table('payments')->where('order_id', $orderId)->first();

                    if ($payment) {
                        // Check for duplicates
                        $existingSuccessfulPayment = DB::table('payments')
                            ->where('user_profile_id', $payment->user_profile_id)
                            ->where('course_id', $payment->course_id)
                            ->where('status', 'success')
                            ->where('order_id', '!=', $orderId)
                            ->first();

                        if ($existingSuccessfulPayment) {
                            Log::warning('Duplicate payment detected in callback', [
                                'current_order_id' => $orderId,
                                'existing_order_id' => $existingSuccessfulPayment->order_id
                            ]);

                            // Mark as failed due to duplicate
                            DB::table('payments')
                                ->where('order_id', $orderId)
                                ->update([
                                    'status' => 'failed',
                                    'transaction_id' => $request->transaction_id,
                                    'payment_type' => $request->payment_type ?? null,
                                    'failure_reason' => 'Duplicate purchase',
                                    'updated_at' => now()
                                ]);
                        } else {
                            // Update payment status as success
                            DB::table('payments')
                                ->where('order_id', $orderId)
                                ->update([
                                    'status' => 'success',
                                    'transaction_id' => $request->transaction_id,
                                    'payment_type' => $request->payment_type ?? null,
                                    'updated_at' => now()
                                ]);

                            // Grant course access
                            $this->grantCourseAccess($payment->user_profile_id, $payment->course_id);
                        }
                    }
                }
            } else {
                Log::warning('Invalid signature from Midtrans callback', [
                    'order_id' => $orderId,
                    'expected_hash' => $hashed,
                    'received_signature' => $signatureKey
                ]);
            }

            return response('OK');

        } catch (Exception $e) {
            Log::error('Error in Midtrans callback', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response('ERROR', 500);
        }
    }
}
