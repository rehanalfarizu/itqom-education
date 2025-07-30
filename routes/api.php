<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseContentController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\PaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public Authentication Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Public Course Routes
Route::get('/courses', [CourseController::class, 'index']);
Route::get('/courses/{id}', [CourseController::class, 'show']);
Route::get('/course-description/{id}', [CourseController::class, 'showByDescription']);

// Public Course Content Routes
Route::prefix('course-content')->group(function () {
    Route::get('/course/{id}', [CourseContentController::class, 'getByCourseDescription']);
    Route::get('/slug/{slug}', [CourseContentController::class, 'getBySlug']);
    Route::get('/navigation/{id}', [CourseContentController::class, 'getNavigation']);
    Route::get('/prev-next/{slug}', [CourseContentController::class, 'getPrevNext']);
    Route::get('/search', [CourseContentController::class, 'search']);
    Route::get('/all', [CourseContentController::class, 'index']);
});

// Public Payment Routes (untuk Midtrans callback)
Route::post('/payment/notification', [PaymentController::class, 'handleNotification']);
Route::get('/payment/finish', [PaymentController::class, 'paymentFinish']);

// Protected Routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    // User info
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::middleware('auth:sanctum')->get('/my-courses', [CourseController::class, 'myCourses']);

    // User Profile Routes
    Route::get('/profile', [UserProfileController::class, 'show']); // Alias untuk /api/profile
    Route::put('/profile', [UserProfileController::class, 'update']); // Alias untuk PUT /api/profile
    Route::post('/profile', [UserProfileController::class, 'update']); // Alias untuk POST /api/profile
    Route::get('/user/profile', [UserProfileController::class, 'show']);
    Route::put('/user/profile', [UserProfileController::class, 'update']);
    Route::post('/user/profile', [UserProfileController::class, 'update']); // Support both PUT & POST
    Route::delete('/user/profile/avatar', [UserProfileController::class, 'removeAvatar']); // Hapus avatar
    Route::post('/user/change-password', [UserProfileController::class, 'changePassword']);

    // Method khusus untuk payment (jika diperlukan)
    Route::get('/user/profile-for-payment', [UserProfileController::class, 'getProfileForPayment']);

    // Payment Routes (Protected)
    Route::prefix('payment')->group(function () {
        Route::post('/create-snap-token', [PaymentController::class, 'createSnapToken']);
        Route::get('/status/{orderId}', [PaymentController::class, 'checkPaymentStatus']);
        Route::get('/user-payments', [PaymentController::class, 'getUserPayments']);
            Route::post('/notification', [PaymentController::class, 'handleNotification']);

    });

    // Logout
    Route::post('/logout', [AuthController::class, 'logout']);

    // Chat Routes
    Route::prefix('chat')->group(function () {
        Route::get('/messages', [\App\Http\Controllers\ChatController::class, 'getMessages']);
        Route::post('/send', [\App\Http\Controllers\ChatController::class, 'sendMessage']);
        Route::post('/mark-read', [\App\Http\Controllers\ChatController::class, 'markAsRead']);
        Route::get('/unread-count', [\App\Http\Controllers\ChatController::class, 'getUnreadCount']);
    });

    // Protected Course Content Routes (jika diperlukan autentikasi untuk akses konten)
    Route::prefix('protected/course-content')->group(function () {
        Route::get('/course/{id}', [CourseContentController::class, 'getByCourseDescription']);
        Route::post('/progress/{courseId}', function (Request $request, $courseId) {
            // Save user progress
            $user = $request->user();
            $progress = $request->input('progress', []);

            // Logic to save progress to database
            // You can create a UserProgress model for this

            return response()->json([
                'success' => true,
                'message' => 'Progress saved successfully'
            ]);
        });

        Route::get('/progress/{courseId}', function (Request $request, $courseId) {
            // Get user progress
            $user = $request->user();

            // Logic to retrieve progress from database

            return response()->json([
                'success' => true,
                'data' => [
                    'completed_materials' => [],
                    'current_material' => null,
                    'progress_percentage' => 0
                ]
            ]);
        });
    });
});


// Tambahkan routes ini di api.php untuk debug

// Debug routes - hapus setelah masalah selesai
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/debug/database', [CourseController::class, 'debugDatabase']);
    Route::get('/debug/my-courses', [CourseController::class, 'myCourses']);

    // Route untuk cek raw data
    Route::get('/debug/raw-payments', function(Request $request) {
        $user = $request->user();
        return response()->json([
            'user_id' => $user->id,
            'all_payments' => DB::table('payments')->where('user_profile_id', $user->id)->get(),
            'successful_payments' => DB::table('payments')
                ->where('user_profile_id', $user->id)
                ->where('status', 'success')
                ->get()
        ]);
    });

    // Route untuk cek struktur tabel
    Route::get('/debug/table-structure', function() {
        return response()->json([
            'payments' => DB::select("SHOW COLUMNS FROM payments"),
            'courses' => DB::select("SHOW COLUMNS FROM courses"),
            'course_descriptions' => DB::select("SHOW COLUMNS FROM course_descriptions")
        ]);
    });
});


// Routes yang sudah ada (pastikan sudah ada):
Route::post('/payment/create-snap-token', [PaymentController::class, 'createSnapToken']);
Route::post('/payment/notification', [PaymentController::class, 'handleNotification']);
Route::get('/payment/status/{orderId}', [PaymentController::class, 'checkPaymentStatus']);
Route::post('/payment/check-course-purchase', [PaymentController::class, 'checkCoursePurchase']);

// Route baru yang perlu ditambahkan:
Route::post('/payment/expire-old-pending', [PaymentController::class, 'expireOldPendingPayments']);
Route::get('/payment/user-payments', [PaymentController::class, 'getUserPayments']);
Route::post('/payment/callback', [PaymentController::class, 'midtransCallback']);

// Route untuk cleanup otomatis (opsional, bisa dijadwalkan dengan scheduler)
Route::post('/payment/cleanup-expired', [PaymentController::class, 'cleanupExpiredPayments']);
