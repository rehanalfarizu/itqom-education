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
    Route::post('/payment/callback', [PaymentController::class, 'midtransCallback']);
    Route::get('/payment/finish', [PaymentController::class, 'paymentFinish']);
    Route::get('/payment/unfinish', [PaymentController::class, 'paymentUnfinish']);
    Route::get('/payment/error', [PaymentController::class, 'paymentError']);

    // Protected Routes (require authentication)
    Route::middleware('auth:sanctum')->group(function () {
        // User info
        Route::get('/user', function (Request $request) {
            return $request->user();
        });
        Route::get('/my-courses', [CourseController::class, 'myCourses']);

        // User Profile Routes
        Route::get('/profile', [UserProfileController::class, 'show']);
        Route::put('/profile', [UserProfileController::class, 'update']);
        Route::post('/profile', [UserProfileController::class, 'update']);
        Route::get('/user/profile', [UserProfileController::class, 'show']);
        Route::put('/user/profile', [UserProfileController::class, 'update']);
        Route::post('/user/profile', [UserProfileController::class, 'update']);
        Route::delete('/user/profile/avatar', [UserProfileController::class, 'removeAvatar']);
        Route::post('/user/change-password', [UserProfileController::class, 'changePassword']);
        Route::get('/user/profile-for-payment', [UserProfileController::class, 'getProfileForPayment']);

        // Payment Routes (Protected) - FIXED: Semua dalam satu grup
        Route::prefix('payment')->group(function () {
            Route::post('/create-snap-token', [PaymentController::class, 'createSnapToken']);
            Route::get('/status/{orderId}', [PaymentController::class, 'checkPaymentStatus']);
            Route::get('/user-payments', [PaymentController::class, 'getUserPayments']);
            Route::post('/check-course-purchase', [PaymentController::class, 'checkCoursePurchase']); // MOVED to protected
            Route::post('/expire-old-pending', [PaymentController::class, 'expireOldPendingPayments']);
            Route::post('/cleanup-expired', [PaymentController::class, 'cleanupExpiredPayments']);
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

        // Protected Course Content Routes
        Route::prefix('protected/course-content')->group(function () {
            Route::get('/course/{id}', [CourseContentController::class, 'getByCourseDescription']);
            Route::post('/progress/{courseId}', function (Request $request, $courseId) {
                $user = $request->user();
                $progress = $request->input('progress', []);

                return response()->json([
                    'success' => true,
                    'message' => 'Progress saved successfully'
                ]);
            });

            Route::get('/progress/{courseId}', function (Request $request, $courseId) {
                $user = $request->user();

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
    Route::post('/profile/change-password', [UserProfileController::class, 'changePassword']);


    Route::get('/test-midtrans-config', function() {
        try {
            return response()->json([
                'server_key_exists' => !empty(config('services.midtrans.server_key')),
                'client_key_exists' => !empty(config('services.midtrans.client_key')),
                'server_key_prefix' => substr(config('services.midtrans.server_key'), 0, 15) . '...',
                'client_key_prefix' => substr(config('services.midtrans.client_key'), 0, 15) . '...',
                'is_production' => config('services.midtrans.is_production'),
                'config_files_check' => [
                    'services.midtrans' => config('services.midtrans'),
                    'midtrans' => config('midtrans')
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    });

        // Debug routes - MOVED to protected
        Route::prefix('debug')->group(function () {
            Route::get('/database', [CourseController::class, 'debugDatabase']);
            Route::get('/my-courses', [CourseController::class, 'myCourses']);
            Route::get('/raw-payments', function(Request $request) {
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
            Route::get('/table-structure', function() {
                return response()->json([
                    'payments' => DB::select("SHOW COLUMNS FROM payments"),
                    'courses' => DB::select("SHOW COLUMNS FROM courses"),
                    'course_descriptions' => DB::select("SHOW COLUMNS FROM course_descriptions")
                ]);
            });
        });
    });

