<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Midtrans\Snap;
use Midtrans\Config;
use Midtrans\Notification;
use Midtrans\Transaction; // Tetap diimpor karena digunakan untuk Transaction::status()
use App\Models\Payment; // Pastikan model ini ada dan benar
use App\Models\CourseDescription; // Sesuaikan dengan nama model Anda. Jika nama filenya CourseDescriptions.php, maka nama kelasnya CourseDescriptions
use App\Models\UserProfile; // Pastikan model ini ada dan benar
use App\Models\User; // Tambahkan ini jika Anda merujuk ke tabel 'users' secara langsung
use Exception;
use Illuminate\Validation\ValidationException; // Penting untuk menangkap validasi error

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
     * Memperbaiki masalah transaction_id yang NULL dan relasi order_id
     */
    public function createSnapToken(Request $request)
    {
        try {
            // Log request data untuk debugging
            Log::info('Payment request received', $request->all());

            // Flexible validation - accept both user_id and user_profile_id
            $request->validate([
                'course_id' => 'required|exists:course_description,id', // Pastikan nama tabel di DB adalah 'course_description'
                'amount' => 'required|numeric|min:1'
            ]);

            // Handle user ID flexibility
            $userProfileId = $request->user_profile_id ?? $request->user_id;

            if (!$userProfileId) {
                return response()->json([
                    'success' => false,
                    'error' => 'User ID is required (user_profile_id or user_id)'
                ], 422);
            }

            // Verify user exists using Eloquent models for clarity and consistency
            // Coba cari di UserProfile dulu, jika tidak ada, coba di User
            $userProfileData = UserProfile::find($userProfileId);
            if (!$userProfileData) {
                $userProfileData = User::find($userProfileId); // Fallback ke model User
            }

            if (!$userProfileData) {
                return response()->json([
                    'success' => false,
                    'error' => 'User not found'
                ], 404);
            }

            // Dapatkan detail pengguna dari objek yang ditemukan
            $customerName = $userProfileData->fullname ?? $userProfileData->name ?? 'Customer';
            $customerEmail = $userProfileData->email ?? 'customer@example.com';
            $customerPhone = $userProfileData->phone ?? '08123456789';


            $courseId = $request->course_id;
            $amount = $request->amount;

            // ======= VALIDASI PEMBELIAN DUPLIKAT =======
            $existingSuccessfulPayment = Payment::where('user_profile_id', $userProfileId)
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
            $existingPendingPayment = Payment::where('user_profile_id', $userProfileId)
                ->where('course_id', $courseId)
                ->where('status', 'pending')
                ->where('created_at', '>', now()->subMinutes(30))
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

            // Get course data using Eloquent model
            $course = CourseDescription::find($courseId); // Menggunakan model CourseDescription
            if (!$course) {
                return response()->json([
                    'success' => false,
                    'error' => 'Course not found'
                ], 404);
            }

            // Generate unique order ID
            $orderId = 'ORDER-' . $courseId . '-' . $userProfileId . '-' . uniqid();

            // Save transaction pending to database
            // transaction_id akan diisi NULL karena kita sudah membuat kolomnya nullable
            // Menggunakan Eloquent create untuk konsistensi
            $payment = Payment::create([
                'order_id' => $orderId,
                'user_profile_id' => (int) $userProfileId,
                'course_id' => (int) $courseId,
                'amount' => (int) $amount,
                'status' => 'pending',
                'transaction_id' => null, // Eksplisit set null, meskipun sudah nullable di DB
                'payment_type' => null,
                'created_at' => now(),
                'updated_at' => now(),
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
                'first_name' => $customerName,
                'email' => $customerEmail,
                'phone' => $customerPhone
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
            $snapToken = Snap::getSnapToken($transactionArray);

            return response()->json([
                'success' => true,
                'snap_token' => $snapToken,
                'order_id' => $orderId,
                'message' => 'Snap token created successfully'
            ]);

        } catch (ValidationException $e) { // Tangkap ValidationException secara spesifik
            Log::error('Validation error in payment', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Validation failed',
                'messages' => $e->errors()
            ], 422);

        } catch (Exception $e) { // Tangkap Exception umum
            Log::error('Error creating snap token', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(), // Pertimbangkan untuk tidak mencatat trace di produksi
                'request_data' => $request->all()
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
            $notif = new Notification();

            $transactionStatus = $notif->transaction_status;
            $orderId = $notif->order_id;
            $fraudStatus = $notif->fraud_status ?? null;
            $transactionTime = $notif->transaction_time ?? null;
            $paymentType = $notif->payment_type ?? null;
            $grossAmount = $notif->gross_amount ?? null;
            $transactionId = $notif->transaction_id ?? null; // Midtrans transaction_id

            Log::info('Midtrans notification processed', [
                'order_id' => $orderId,
                'transaction_status' => $transactionStatus,
                'fraud_status' => $fraudStatus,
                'transaction_id' => $transactionId
            ]);

            // Get payment record using Eloquent
            $payment = Payment::where('order_id', $orderId)->first();

            if (!$payment) {
                Log::error('Payment not found for notification', ['order_id' => $orderId]);
                return response()->json(['status' => 'error', 'message' => 'Payment not found'], 404);
            }

            // ======= VALIDASI DUPLIKAT SAAT NOTIFICATION =======
            // Jika transaksi saat ini berhasil (capture/settlement)
            if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
                $existingSuccessfulPayment = Payment::where('user_profile_id', $payment->user_profile_id)
                    ->where('course_id', $payment->course_id)
                    ->where('status', 'success')
                    ->where('order_id', '!=', $orderId) // Kecualikan pembayaran saat ini
                    ->first();

                if ($existingSuccessfulPayment) {
                    Log::warning('Duplicate successful payment detected in notification', [
                        'current_order_id' => $orderId,
                        'existing_order_id' => $existingSuccessfulPayment->order_id,
                        'user_profile_id' => $payment->user_profile_id,
                        'course_id' => $payment->course_id
                    ]);

                    // Update current payment as failed due to duplicate using Eloquent
                    $payment->update([
                        'status' => 'failed',
                        'transaction_id' => $transactionId,
                        'payment_type' => $paymentType,
                        'transaction_status' => $transactionStatus,
                        'fraud_status' => $fraudStatus,
                        'failure_reason' => 'Duplicate purchase - user already owns this course',
                        'updated_at' => now(),
                    ]);

                    return response()->json(['status' => 'duplicate_prevented']);
                }
            }
            // ======= END VALIDASI DUPLIKAT =======

            // Map payment status
            $mappedStatus = $this->mapPaymentStatus($transactionStatus);

            // Update payment in database using Eloquent
            $updated = $payment->update([
                'status' => $mappedStatus,
                'transaction_id' => $transactionId, // Ini akan mengisi transaction_id
                'payment_type' => $paymentType,
                'transaction_status' => $transactionStatus,
                'fraud_status' => $fraudStatus,
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
                'trace' => $e->getTraceAsString(), // Pertimbangkan untuk tidak mencatat trace di produksi
                'request_data' => $request->all()
            ]);

            return response()->json(['status' => 'error'], 500);
        }
    }

    /**
     * Check payment status directly from Midtrans - IMPROVED VERSION
     */
    public function checkPaymentStatus(Request $request, $orderId)
    {
        try {
            Log::info('Manual payment status check', [
                'order_id' => $orderId,
                'user_id' => $request->user()->id ?? 'guest'
            ]);

            // Get payment record first using Eloquent
            $payment = Payment::where('order_id', $orderId)->first();

            if (!$payment) {
                Log::warning('Payment not found', ['order_id' => $orderId]);

                return response()->json([
                    'success' => false,
                    'error' => 'Payment not found',
                    'order_id' => $orderId,
                    'payment_status' => 'not_found'
                ], 404);
            }

            // Verify user ownership (if authenticated)
            if ($request->user() && $payment->user_profile_id != $request->user()->id) {
                Log::warning('Unauthorized payment status check', [
                    'order_id' => $orderId,
                    'payment_user_id' => $payment->user_profile_id,
                    'request_user_id' => $request->user()->id
                ]);

                return response()->json([
                    'success' => false,
                    'error' => 'Unauthorized access to payment',
                    'payment_status' => 'unauthorized'
                ], 403);
            }

            try {
                // Check status from Midtrans using Transaction class
                $status = Transaction::status($orderId); // Menggunakan Midtrans\Transaction

                Log::info('Payment status from Midtrans', [
                    'order_id' => $orderId,
                    'status' => $status->transaction_status,
                    'payment_type' => $status->payment_type ?? null
                ]);

                $mappedStatus = $this->mapPaymentStatus($status->transaction_status);

                // ======= VALIDASI DUPLIKAT SAAT MANUAL CHECK =======
                if ($mappedStatus === 'success') {
                    $existingSuccessfulPayment = Payment::where('user_profile_id', $payment->user_profile_id)
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

                        // Update current payment as failed due to duplicate using Eloquent
                        $payment->update([
                            'status' => 'failed',
                            'transaction_id' => $status->transaction_id ?? null,
                            'payment_type' => $status->payment_type ?? null,
                            'transaction_status' => $status->transaction_status,
                            'fraud_status' => $status->fraud_status ?? null,
                            'failure_reason' => 'Duplicate purchase - user already owns this course',
                            'updated_at' => now(),
                        ]);

                        return response()->json([
                            'success' => false,
                            'order_id' => $orderId,
                            'transaction_status' => $status->transaction_status,
                            'payment_status' => 'failed',
                            'error' => 'Duplicate purchase detected - you already own this course',
                            'existing_order_id' => $existingSuccessfulPayment->order_id,
                            'course_id' => $payment->course_id
                        ]);
                    }
                }
                // ======= END VALIDASI DUPLIKAT =======

                // Update status in database using Eloquent
                $updated = $payment->update([
                    'status' => $mappedStatus,
                    'transaction_id' => $status->transaction_id ?? null,
                    'payment_type' => $status->payment_type ?? null,
                    'transaction_status' => $status->transaction_status,
                    'fraud_status' => $status->fraud_status ?? null,
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

                // Get course title for response using Eloquent
                $course = CourseDescription::find($payment->course_id);

                return response()->json([
                    'success' => $mappedStatus === 'success',
                    'order_id' => $orderId,
                    'transaction_status' => $status->transaction_status,
                    'payment_status' => $mappedStatus,
                    'amount' => $status->gross_amount ?? $payment->amount,
                    'payment_type' => $status->payment_type ?? null,
                    'transaction_time' => $status->transaction_time ?? null,
                    'transaction_id' => $status->transaction_id ?? null,
                    'course_id' => $payment->course_id,
                    'course_title' => $course->title ?? null
                ]);

            } catch (Exception $midtransError) { // Tangkap kesalahan koneksi ke Midtrans
                Log::error('Error connecting to Midtrans for status check', [
                    'order_id' => $orderId,
                    'error' => $midtransError->getMessage()
                ]);

                // Return status from database if Midtrans fails
                return response()->json([
                    'success' => $payment->status === 'success',
                    'order_id' => $orderId,
                    'payment_status' => $payment->status,
                    'transaction_status' => $payment->transaction_status,
                    'amount' => $payment->amount,
                    'payment_type' => $payment->payment_type,
                    'transaction_id' => $payment->transaction_id,
                    'course_id' => $payment->course_id,
                    'error' => 'Could not verify with payment gateway, showing cached status',
                    'midtrans_error' => $midtransError->getMessage()
                ]);
            }

        } catch (Exception $e) { // Tangkap Exception umum di luar try-catch Midtrans
            Log::error('Error checking payment status', [
                'order_id' => $orderId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString() // Pertimbangkan untuk tidak mencatat trace di produksi
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to check payment status',
                'message' => $e->getMessage(),
                'payment_status' => 'error'
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
            'failure' => 'failed',
            'refund' => 'refunded', // Tambahkan jika ada status refund
            'partial_refund' => 'partial_refunded', // Tambahkan jika ada status partial refund
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

            // Menggunakan Eloquent join
            $payments = Payment::leftJoin('course_description', 'payments.course_id', '=', 'course_description.id')
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
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString() // Pertimbangkan untuk tidak mencatat trace di produksi
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
            // Get authenticated user
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'error' => 'User not authenticated'
                ], 401);
            }

            // Validate course_id
            $request->validate([
                'course_id' => 'required|exists:course_description,id'
            ]);

            $courseId = $request->course_id;
            $userProfileId = $user->id;

            Log::info('Checking course purchase', [
                'user_id' => $userProfileId,
                'course_id' => $courseId
            ]);

            // Check if user already purchased this course using Eloquent
            $existingPayment = Payment::where('user_profile_id', $userProfileId)
                ->where('course_id', $courseId)
                ->where('status', 'success')
                ->first();

            // Also check in user_courses table (if exists)
            $hasAccess = DB::table('user_courses')
                ->where('user_id', $userProfileId)
                ->where('course_id', $courseId)
                ->exists();

            return response()->json([
                'success' => true,
                'has_purchased' => !!$existingPayment,
                'has_access' => $hasAccess,
                'payment_details' => $existingPayment ? [
                    'order_id' => $existingPayment->order_id,
                    'purchased_at' => $existingPayment->updated_at,
                    'amount' => $existingPayment->amount
                ] : null
            ]);

        } catch (ValidationException $e) {
            Log::error('Validation error in checkCoursePurchase', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Validation failed',
                'messages' => $e->errors()
            ], 422);

        } catch (Exception $e) {
            Log::error('Error checking course purchase', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(), // Pertimbangkan untuk tidak mencatat trace di produksi
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to check course purchase',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle payment finish callback from Midtrans
     */
    public function paymentFinish(Request $request)
    {
        try {
            $orderId = $request->query('order_id');
            $statusCode = $request->query('status_code');
            $transactionStatus = $request->query('transaction_status');

            Log::info('Payment finish callback', [
                'order_id' => $orderId,
                'status_code' => $statusCode,
                'transaction_status' => $transactionStatus,
                'all_params' => $request->all()
            ]);

            if (!$orderId) {
                Log::warning('Payment finish called without order_id');
                return redirect('/courses?error=missing_order_id');
            }

            // Get payment record using Eloquent
            $payment = Payment::where('order_id', $orderId)->first();

            if (!$payment) {
                Log::warning('Payment not found in finish callback', ['order_id' => $orderId]);
                return redirect('/courses?error=payment_not_found');
            }

            // Get course info for redirect using Eloquent
            $course = CourseDescription::find($payment->course_id);

            // Redirect to payment result page with proper parameters
            $redirectUrl = '/payment/result?' . http_build_query([
                'order_id' => $orderId,
                'course_id' => $payment->course_id,
                'result' => ($transactionStatus === 'settlement' || $transactionStatus === 'capture') ? 'success' : 'pending'
            ]);

            Log::info('Redirecting to payment result', [
                'order_id' => $orderId,
                'redirect_url' => $redirectUrl
            ]);

            return redirect($redirectUrl);

        } catch (Exception $e) {
            Log::error('Error in payment finish callback', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);

            return redirect('/courses?error=payment_finish_error');
        }
    }

    /**
     * Handle payment unfinish callback from Midtrans
     */
    public function paymentUnfinish(Request $request)
    {
        try {
            $orderId = $request->query('order_id');

            Log::info('Payment unfinish callback', [
                'order_id' => $orderId,
                'all_params' => $request->all()
            ]);

            if ($orderId) {
                // Get payment record using Eloquent
                $payment = Payment::where('order_id', $orderId)->first();

                $redirectUrl = '/payment/result?' . http_build_query([
                    'order_id' => $orderId,
                    'course_id' => $payment->course_id ?? null,
                    'result' => 'unfinish'
                ]);

                return redirect($redirectUrl);
            }

            return redirect('/courses?error=payment_unfinished');

        } catch (Exception $e) {
            Log::error('Error in payment unfinish callback', [
                'error' => $e->getMessage()
            ]);

            return redirect('/courses?error=payment_unfinish_error');
        }
    }

    /**
     * Handle payment error callback from Midtrans
     */
    public function paymentError(Request $request)
    {
        try {
            $orderId = $request->query('order_id');

            Log::info('Payment error callback', [
                'order_id' => $orderId,
                'all_params' => $request->all()
            ]);

            if ($orderId) {
                // Get payment record using Eloquent
                $payment = Payment::where('order_id', $orderId)->first();

                $redirectUrl = '/payment/result?' . http_build_query([
                    'order_id' => $orderId,
                    'course_id' => $payment->course_id ?? null,
                    'result' => 'error'
                ]);

                return redirect($redirectUrl);
            }

            return redirect('/courses?error=payment_error');

        } catch (Exception $e) {
            Log::error('Error in payment error callback', [
                'error' => $e->getMessage()
            ]);

            return redirect('/courses?error=payment_callback_error');
        }
    }
}