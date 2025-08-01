<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseDescription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CourseController extends Controller
{
    public function index()
    {
        // Ambil semua course descriptions
        $courseDescriptions = CourseDescription::all();

        // Transform data untuk sesuai dengan format yang diharapkan frontend
        $transformedCourses = $courseDescriptions->map(function($courseDesc) {
            return [
                'id' => $courseDesc->id,
                'title' => $courseDesc->title,
                'instructor' => $courseDesc->instructor_name,
                'video_count' => $courseDesc->video_count . ' video',
                'duration' => $courseDesc->duration . ' hours',
                'original' => number_format($courseDesc->price, 0, ',', '.'),
                'price' => number_format($courseDesc->price_discount ?? $courseDesc->price, 0, ',', '.'),
                'image' => $courseDesc->image_url ? '/storage/' . $courseDesc->image_url : '/images/default.jpg',
                'category' => $courseDesc->tag ?? 'Umum'
            ];
        });

        return response()->json($transformedCourses->values());
    }

    public function show($id)
    {
        $course = Course::with('courseDescription')->find($id);

        if (!$course || !$course->courseDescription) {
            return response()->json(['message' => 'Course not found'], 404);
        }

        $flattenedData = [
            'id' => $course->id,
            'title' => $course->courseDescription->title,
            'tag' => $course->courseDescription->tag,
            'overview' => $course->courseDescription->overview,
            'image_url' => $course->courseDescription->image_url,
            'thumbnail' => $course->courseDescription->thumbnail,
            'price' => $course->courseDescription->price_discount ?? $course->courseDescription->price,
            'price_discount' => $course->courseDescription->price_discount,
            'instructor_name' => $course->courseDescription->instructor_name,
            'instructor_position' => $course->courseDescription->instructor_position,
            'instructor_image_url' => $course->courseDescription->instructor_image_url,
            'video_count' => $course->courseDescription->video_count,
            'duration' => $course->courseDescription->duration,
            'features' => $course->courseDescription->features ?? [],
        ];

        return response()->json($flattenedData);
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

            // Step 1: Get successful payments for this user
            $successfulPayments = DB::table('payments')
                ->where('user_profile_id', $user->id)
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
