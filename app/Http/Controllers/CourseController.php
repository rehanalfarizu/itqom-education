<?php

namespace App\Http\Controllers;

use App\Models\Course;
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
            // Ambil dari Course model yang sudah terintegrasi dengan Cloudinary
            $courses = Course::with('courseDescription')->get();

            // Transform data untuk frontend dengan Cloudinary URLs
            $transformedCourses = $courses->map(function($course) {
                return [
                    'id' => $course->id,
                    'title' => $course->title,
                    'instructor' => $course->instructor,
                    'video_count' => $course->video_count . ' video',
                    'duration' => $course->duration,
                    'original' => number_format((float)$course->original_price, 0, ',', '.'),
                    'price' => number_format((float)$course->price, 0, ',', '.'),
                    'image' => $course->image_url ?: '/images/default.jpg', // Menggunakan accessor Cloudinary
                    'thumbnail' => $course->thumbnail_url, // Thumbnail yang sudah dioptimasi
                    'category' => $course->category,
                    'description' => $course->courseDescription?->title,
                    'overview' => $course->courseDescription?->overview,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $transformedCourses->values()
            ]);

        } catch (\Exception $e) {
            Log::error('Error in CourseController@index: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading courses',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            // Ambil course dengan relasi
            $course = Course::with('courseDescription')->find($id);

            if (!$course) {
                return response()->json([
                    'success' => false,
                    'message' => 'Course not found'
                ], 404);
            }

            $courseData = [
                'id' => $course->id,
                'title' => $course->title,
                'instructor' => $course->instructor,
                'video_count' => $course->video_count,
                'duration' => $course->duration,
                'original_price' => $course->original_price,
                'price' => $course->price,
                'image' => $course->image_url, // URL Cloudinary yang sudah dioptimasi
                'thumbnail' => $course->thumbnail_url,
                'category' => $course->category,
                'description' => $course->courseDescription?->title,
                'overview' => $course->courseDescription?->overview,
                'features' => $course->courseDescription?->features ?? [],
            ];

            return response()->json([
                'success' => true,
                'data' => $courseData
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
     * Get courses by category with Cloudinary integration
     */
    public function byCategory($category)
    {
        try {
            $courses = Course::with('courseDescription')
                ->byCategory($category)
                ->get();

            $transformedCourses = $courses->map(function($course) {
                return [
                    'id' => $course->id,
                    'title' => $course->title,
                    'instructor' => $course->instructor,
                    'video_count' => $course->video_count,
                    'duration' => $course->duration,
                    'price' => $course->price,
                    'image' => $course->image_url,
                    'thumbnail' => $course->thumbnail_url,
                    'category' => $course->category,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $transformedCourses
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
     * Get popular courses with Cloudinary integration
     */
    public function popular()
    {
        try {
            $courses = Course::with('courseDescription')
                ->popular(8)
                ->get();

            $transformedCourses = $courses->map(function($course) {
                return [
                    'id' => $course->id,
                    'title' => $course->title,
                    'instructor' => $course->instructor,
                    'enrollment_count' => $course->user_courses_count,
                    'image' => $course->image_url,
                    'thumbnail' => $course->thumbnail_url,
                    'price' => $course->price,
                    'category' => $course->category,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $transformedCourses
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

                // Try to get course description from different possible table names
                $courseDescription = null;

                // Method 1: Try course_descriptions table
                try {
                    $courseDescription = DB::table('course_descriptions')
                        ->where('id', $payment->course_id)
                        ->orWhere('course_id', $payment->course_id)
                        ->first();

                    if ($courseDescription) {
                        Log::info("Found course in course_descriptions table");
                    }
                } catch (\Exception $e) {
                    Log::info("course_descriptions table not found or error: " . $e->getMessage());
                }

                // Method 2: Try course_description table (singular)
                if (!$courseDescription) {
                    try {
                        $courseDescription = DB::table('course_description')
                            ->where('id', $payment->course_id)
                            ->orWhere('course_id', $payment->course_id)
                            ->first();

                        if ($courseDescription) {
                            Log::info("Found course in course_description table");
                        }
                    } catch (\Exception $e) {
                        Log::info("course_description table not found or error: " . $e->getMessage());
                    }
                }

                // Method 3: Try courses table directly
                if (!$courseDescription) {
                    try {
                        $courseDescription = DB::table('courses')
                            ->where('id', $payment->course_id)
                            ->first();

                        if ($courseDescription) {
                            Log::info("Found course in courses table");
                        }
                    } catch (\Exception $e) {
                        Log::info("courses table error: " . $e->getMessage());
                    }
                }

                // If we found course data, add it to the array
                if ($courseDescription) {
                    $courseData = [
                        'id' => $payment->course_id,
                        'title' => $courseDescription->title ?? 'Course #' . $payment->course_id,
                        'image_url' => $courseDescription->image_url ?? '/images/default-course.jpg',
                        'instructor_name' => $courseDescription->instructor_name ?? 'Unknown Instructor',
                        'duration' => $courseDescription->duration ?? 0,
                        'tag' => $courseDescription->tag ?? 'General',
                        'purchased_at' => $payment->created_at,
                        'payment_status' => $payment->status,
                        'order_id' => $payment->order_id
                    ];

                    $purchasedCourses[] = $courseData;
                    Log::info("Added course to array:", ['title' => $courseData['title']]);
                } else {
                    Log::warning("Could not find course description for course_id: " . $payment->course_id);

                    // Add course with minimal data
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
}
