<?php

namespace App\Http\Controllers;

use App\Models\CourseDescription;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CourseController extends Controller
{
    protected $cloudinaryService;

    public function __construct(CloudinaryService $cloudinaryService)
    {
        $this->cloudinaryService = $cloudinaryService;
    }

    public function index()
    {
        try {
            Log::info('CourseController@index called - using CourseDescription as single source');

            // Periksa koneksi database
            try {
                DB::connection()->getPdo();
                Log::info('Database connection successful');
            } catch (\Exception $e) {
                Log::error('Database connection failed: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'Database connection failed',
                    'error' => config('app.debug') ? $e->getMessage() : 'Database connection error'
                ], 500);
            }

            // Ambil data dari CourseDescription sebagai satu-satunya sumber data
            $courseDescriptions = CourseDescription::all();
            Log::info('Found ' . $courseDescriptions->count() . ' course descriptions');

            if ($courseDescriptions->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'message' => 'No courses found',
                    'source' => 'course_description'
                ]);
            }

            $transformedCourses = $courseDescriptions->map(function($courseDesc) {
                return [
                    'id' => $courseDesc->id,
                    'title' => $courseDesc->title,
                    'instructor' => $courseDesc->instructor_name,
                    'video_count' => $courseDesc->video_count . ' video',
                    'duration' => $courseDesc->duration, // integer sesuai migrasi
                    'original' => number_format((float)$courseDesc->price_discount, 0, ',', '.'),
                    'price' => number_format((float)$courseDesc->price, 0, ',', '.'),
                    'image' => $courseDesc->image_url ?: '/images/default.jpg', // menggunakan accessor
                    'thumbnail' => $courseDesc->thumbnail_url ?: '/images/default-thumb.jpg', // menggunakan accessor
                    'category' => $courseDesc->tag, // sesuai field migrasi
                    'description' => $courseDesc->title,
                    'overview' => $courseDesc->overview,
                    'features' => $courseDesc->features ?? [], // json field sesuai migrasi
                    'instructor_name' => $courseDesc->instructor_name,
                    'instructor_position' => $courseDesc->instructor_position,
                    'instructor_image_url' => $courseDesc->instructor_image_url,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $transformedCourses->values(),
                'source' => 'course_description'
            ]);

        } catch (\Exception $e) {
            Log::error('Error in CourseController@index: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Error loading courses',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            // Ambil course description langsung sebagai satu-satunya sumber data
            $courseDescription = CourseDescription::find($id);

            if (!$courseDescription) {
                return response()->json([
                    'success' => false,
                    'message' => 'Course not found'
                ], 404);
            }

            $courseData = [
                'id' => $courseDescription->id,
                'title' => $courseDescription->title,
                'instructor' => $courseDescription->instructor_name,
                'video_count' => $courseDescription->video_count, // integer sesuai migrasi
                'duration' => $courseDescription->duration, // integer sesuai migrasi
                'original_price' => $courseDescription->price_discount, // decimal sesuai migrasi
                'price' => $courseDescription->price, // decimal sesuai migrasi
                'image' => $courseDescription->image_url, // menggunakan accessor
                'thumbnail' => $courseDescription->thumbnail_url, // menggunakan accessor
                'category' => $courseDescription->tag, // sesuai field migrasi
                'description' => $courseDescription->title,
                'overview' => $courseDescription->overview,
                'features' => $courseDescription->features ?? [], // json field
                'instructor_name' => $courseDescription->instructor_name,
                'instructor_position' => $courseDescription->instructor_position,
                'instructor_image_url' => $courseDescription->instructor_image_url,
            ];

            return response()->json([
                'success' => true,
                'data' => $courseData,
                'source' => 'course_description'
            ]);

        } catch (\Exception $e) {
            Log::error('Error in CourseController@show: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading course',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get courses by category using CourseDescription
     */
    public function byCategory($category)
    {
        try {
            $courseDescriptions = CourseDescription::where('tag', $category)->get();

            $transformedCourses = $courseDescriptions->map(function($course) {
                return [
                    'id' => $course->id,
                    'title' => $course->title,
                    'instructor' => $course->instructor_name,
                    'video_count' => $course->video_count, // integer sesuai migrasi
                    'duration' => $course->duration, // integer sesuai migrasi
                    'price' => $course->price, // decimal sesuai migrasi
                    'image' => $course->image_url, // menggunakan accessor
                    'thumbnail' => $course->thumbnail_url, // menggunakan accessor
                    'category' => $course->tag, // sesuai field migrasi
                    'overview' => $course->overview,
                    'features' => $course->features ?? [], // json field
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $transformedCourses,
                'source' => 'course_description'
            ]);

        } catch (\Exception $e) {
            Log::error('Error in CourseController@byCategory: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading courses by category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get popular courses using CourseDescription
     */
    public function popular()
    {
        try {
            // Menggunakan CourseDescription dengan sorting by created_at
            $courseDescriptions = CourseDescription::orderBy('created_at', 'desc')
                ->take(8)
                ->get();

            $transformedCourses = $courseDescriptions->map(function($course) {
                return [
                    'id' => $course->id,
                    'title' => $course->title,
                    'instructor' => $course->instructor_name,
                    'video_count' => $course->video_count, // integer sesuai migrasi
                    'duration' => $course->duration, // integer sesuai migrasi
                    'price' => $course->price, // decimal sesuai migrasi
                    'enrollment_count' => 0, // Bisa dikembangkan untuk menghitung dari payments
                    'image' => $course->image_url, // menggunakan accessor
                    'thumbnail' => $course->thumbnail_url, // menggunakan accessor
                    'category' => $course->tag, // sesuai field migrasi
                    'overview' => $course->overview,
                    'features' => $course->features ?? [], // json field
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $transformedCourses,
                'source' => 'course_description'
            ]);

        } catch (\Exception $e) {
            Log::error('Error in CourseController@popular: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading popular courses',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * COMPLETELY FIXED VERSION - My Courses Method
     */
    public function myCourses(Request $request)
    {
        try {
            $user = $request->user();

            Log::info("=== MY COURSES FIXED VERSION START ===");
            Log::info("User ID: " . $user->id);
            Log::info("User Email: " . $user->email);

            // Step 1: Get user_profile_id from users_profile table
            $userProfile = DB::table('users_profile')->where('user_id', $user->id)->first();

            if (!$userProfile) {
                // Try to find by email if not found by user_id
                $userProfile = DB::table('users_profile')->where('email', $user->email)->first();
            }

            if (!$userProfile) {
                Log::info("No user profile found for user");
                return response()->json([
                    'success' => true,
                    'courses' => [],
                    'message' => 'No user profile found',
                    'debug_info' => [
                        'user_id' => $user->id,
                        'method' => 'no_user_profile'
                    ]
                ]);
            }

            // Step 2: Get successful payments for this user_profile_id
            $successfulPayments = DB::table('payments')
                ->where('user_profile_id', $userProfile->id)
                ->where('status', 'success')
                ->orderBy('created_at', 'desc')
                ->get();

            Log::info("Successful payments count: " . $successfulPayments->count());

            if ($successfulPayments->isEmpty()) {
                Log::info("No successful payments found for user");
                return response()->json([
                    'success' => true,
                    'courses' => [],
                    'message' => 'No purchased courses found',
                    'debug_info' => [
                        'user_id' => $user->id,
                        'payments_count' => 0,
                        'method' => 'no_payments_found'
                    ]
                ]);
            }

            // Step 2: Build courses array from payments
            $purchasedCourses = [];

            foreach ($successfulPayments as $payment) {
                Log::info("Processing payment:", [
                    'payment_id' => $payment->id,
                    'course_id' => $payment->course_id,
                    'order_id' => $payment->order_id
                ]);

                // Menggunakan CourseDescription model sebagai satu-satunya sumber data
                $courseDescription = CourseDescription::find($payment->course_id);

                if ($courseDescription) {
                    $courseData = [
                        'id' => $payment->course_id,
                        'title' => $courseDescription->title,
                        'image_url' => $courseDescription->image_url ?? '/images/default-course.jpg',
                        'instructor_name' => $courseDescription->instructor_name ?? 'Unknown Instructor',
                        'duration' => $courseDescription->duration ?? 0,
                        'tag' => $courseDescription->tag ?? 'General',
                        'purchased_at' => $payment->created_at,
                        'payment_status' => $payment->status,
                        'order_id' => $payment->order_id,
                        'price' => $courseDescription->price,
                        'video_count' => $courseDescription->video_count,
                        'overview' => $courseDescription->overview,
                        'features' => $courseDescription->features ?? []
                    ];

                    $purchasedCourses[] = $courseData;
                    Log::info("Added course to array:", ['title' => $courseData['title']]);
                } else {
                    Log::warning("Could not find course description for course_id: " . $payment->course_id);

                    // Add course with minimal data jika tidak ditemukan
                    $purchasedCourses[] = [
                        'id' => $payment->course_id,
                        'title' => 'Course #' . $payment->course_id,
                        'image_url' => '/images/default-course.jpg',
                        'instructor_name' => 'Unknown Instructor',
                        'duration' => 0,
                        'tag' => 'General',
                        'purchased_at' => $payment->created_at,
                        'payment_status' => $payment->status,
                        'order_id' => $payment->order_id
                    ];
                }
            }

            Log::info("Final courses count: " . count($purchasedCourses));
            Log::info("=== MY COURSES FIXED VERSION END ===");

            return response()->json([
                'success' => true,
                'courses' => $purchasedCourses,
                'debug_info' => [
                    'user_id' => $user->id,
                    'course_count' => count($purchasedCourses),
                    'payments_count' => $successfulPayments->count(),
                    'method' => 'fixed_version',
                    'timestamp' => now()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error in myCourses (FIXED): ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Error loading courses: ' . $e->getMessage(),
                'courses' => [],
                'debug_info' => [
                    'error' => $e->getMessage(),
                    'user_id' => $request->user()->id ?? null,
                    'method' => 'error_catch'
                ]
            ], 500);
        }
    }

    /**
     * Debug method - Enhanced version
     */
    public function debugDatabase(Request $request)
    {
        try {
            $user = $request->user();

            // Check what tables exist
            $tables = DB::select("SHOW TABLES");
            $tableNames = array_map(function($table) {
                return array_values((array)$table)[0];
            }, $tables);

            // Get payments info
            $paymentsInfo = [];
            try {
                $paymentsInfo = [
                    'total_payments' => DB::table('payments')->count(),
                    'user_payments' => DB::table('payments')->where('user_profile_id', $user->id)->count(),
                    'successful_user_payments' => DB::table('payments')
                        ->where('user_profile_id', $user->id)
                        ->where('status', 'success')
                        ->count(),
                    'sample_payment' => DB::table('payments')
                        ->where('user_profile_id', $user->id)
                        ->first()
                ];
            } catch (\Exception $e) {
                $paymentsInfo['error'] = $e->getMessage();
            }

            // Check course tables
            $courseTablesInfo = [];
            foreach(['courses', 'course_description', 'course_descriptions'] as $tableName) {
                try {
                    if (in_array($tableName, $tableNames)) {
                        $courseTablesInfo[$tableName] = [
                            'exists' => true,
                            'count' => DB::table($tableName)->count(),
                            'columns' => DB::getSchemaBuilder()->getColumnListing($tableName),
                            'sample' => DB::table($tableName)->first()
                        ];
                    } else {
                        $courseTablesInfo[$tableName] = ['exists' => false];
                    }
                } catch (\Exception $e) {
                    $courseTablesInfo[$tableName] = ['error' => $e->getMessage()];
                }
            }

            return response()->json([
                'user_id' => $user->id,
                'existing_tables' => $tableNames,
                'payments_info' => $paymentsInfo,
                'course_tables_info' => $courseTablesInfo,
                'timestamp' => now()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Show course by CourseDescription ID (for frontend)
     */
    public function showByDescription($id)
    {
        try {
            // Get course description directly
            $courseDescription = CourseDescription::find($id);

            if (!$courseDescription) {
                return response()->json([
                    'success' => false,
                    'message' => 'Course not found'
                ], 404);
            }

            // Format response to match frontend expectations
            $courseData = [
                'id' => $courseDescription->id,
                'title' => $courseDescription->title,
                'tag' => $courseDescription->tag,
                'overview' => $courseDescription->overview,
                'image_url' => $courseDescription->image_url,
                'thumbnail' => $courseDescription->thumbnail,
                'price' => $courseDescription->price,
                'price_discount' => $courseDescription->price_discount,
                'instructor_name' => $courseDescription->instructor_name,
                'instructor_position' => $courseDescription->instructor_position,
                'instructor_image_url' => $courseDescription->instructor_image_url,
                'video_count' => $courseDescription->video_count,
                'duration' => $courseDescription->duration,
                'features' => $courseDescription->features ?? [],
                'created_at' => $courseDescription->created_at,
                'updated_at' => $courseDescription->updated_at,
            ];

            return response()->json([
                'success' => true,
                'data' => $courseData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving course',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
