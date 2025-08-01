<?php

namespace App\Http\Controllers;

use App\Models\CourseContent;
use App\Models\CourseDescription;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Exception;

class CourseContentController extends Controller
{
    /**
     * Get course content by course description ID
     * Enhanced with better progress tracking and material count from CourseDescriptions
     */
    public function getByCourseDescription($courseDescriptionId): JsonResponse
    {
        try {
            Log::info('Searching for course description with ID: ' . $courseDescriptionId, [
                'id' => $courseDescriptionId,
                'type' => gettype($courseDescriptionId)
            ]);

            // Check if any course descriptions exist at all
            $totalCourses = CourseDescription::count();
            $allCourses = CourseDescription::select('id', 'title')->get();

            Log::info('Database check - Total courses: ' . $totalCourses, [
                'all_courses' => $allCourses->toArray()
            ]);

            // Validate ID
            if (!is_numeric($courseDescriptionId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID course tidak valid'
                ], 400);
            }

            $courseDescriptionId = (int) $courseDescriptionId;

            // Find course description with more details
            $courseDescription = CourseDescription::find($courseDescriptionId);

            if (!$courseDescription) {
                // Log available courses for debugging
                $availableIds = CourseDescription::select('id', 'title')->get();
                Log::warning('Course description not found', [
                    'requested_id' => $courseDescriptionId,
                    'available_courses' => $availableIds->toArray()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Course tidak ditemukan dengan ID: ' . $courseDescriptionId,
                    'debug_info' => [
                        'available_courses' => $availableIds->map(function($course) {
                            return [
                                'id' => $course->id,
                                'title' => $course->title
                            ];
                        })
                    ]
                ], 404);
            }

            // Get course content (should be single record with JSON materials)
            $courseContent = CourseContent::where('course_description_id', $courseDescriptionId)->first();

            Log::info('Found course content', [
                'course_id' => $courseDescriptionId,
                'content_found' => $courseContent ? true : false
            ]);

            $materis = collect([]);
            $totalMaterisFromCourseDescription = 0;

            // First, try to get materials from CourseContent JSON
            if ($courseContent && $courseContent->materials && is_array($courseContent->materials)) {
                // Extract materials from JSON and format them
                $materis = collect($courseContent->materials)
                    ->map(function ($material, $index) use ($courseContent, $courseDescription) {
                        return [
                            'id' => $index + 1, // Use index as ID since materials are in array
                            'slug' => $courseContent->slug . '-materi-' . ($material['urutan'] ?? ($index + 1)),
                            'judul' => $material['judul'] ?? 'Materi ' . ($index + 1),
                            'konten' => $material['konten'] ?? '<p>Konten akan segera tersedia.</p>',
                            'urutan' => $material['urutan'] ?? ($index + 1),
                            'course_title' => $courseContent->course_title ?: ($courseDescription->title ?? ''),
                            'course_description_id' => $courseContent->course_description_id
                        ];
                    })
                    ->sortBy('urutan')
                    ->values();
            }

            // If no materials in CourseContent, get count from CourseDescriptions
            if ($materis->isEmpty()) {
                // Try to get material count from course_description fields
                $totalMaterisFromCourseDescription = $courseDescription->video_count ??
                                                   $courseDescription->total_materials ??
                                                   $courseDescription->material_count ?? 0;

                // Generate placeholder materials based on course description
                if ($totalMaterisFromCourseDescription > 0) {
                    for ($i = 1; $i <= $totalMaterisFromCourseDescription; $i++) {
                        $materis[] = [
                            'id' => $i,
                            'slug' => 'course-' . $courseDescriptionId . '-materi-' . $i,
                            'judul' => 'Materi ' . $i . ' - ' . $courseDescription->title,
                            'konten' => '<p>Materi ini akan segera tersedia. Silakan tunggu update dari instruktur.</p>',
                            'urutan' => $i,
                            'course_title' => $courseDescription->title,
                            'course_description_id' => $courseDescriptionId
                        ];
                    }
                    $materis = collect($materis);
                }
            }

            // Calculate actual total materials
            $actualTotal = max($materis->count(), $totalMaterisFromCourseDescription);

            // Enhanced course description data
            $courseDescriptionData = [
                'id' => $courseDescription->id,
                'title' => $courseDescription->title ?: 'Untitled Course',
                'description' => $courseDescription->description ?? $courseDescription->overview ?? null,
                'overview' => $courseDescription->overview ?? $courseDescription->description ?? null,
                'instructor' => $courseDescription->instructor ?? $courseDescription->instructor_name ?? 'Unknown Instructor',
                'instructor_name' => $courseDescription->instructor_name ?? $courseDescription->instructor ?? 'Unknown Instructor',
                'duration' => $courseDescription->duration ?? 0,
                'level' => $courseDescription->level ?? 'beginner',
                'price' => $courseDescription->price ?? 0,
                'price_discount' => $courseDescription->price_discount ?? null,
                'thumbnail' => $courseDescription->thumbnail ?? $courseDescription->image_url ?? null,
                'image_url' => $courseDescription->image_url ?? $courseDescription->thumbnail ?? null,
                'is_active' => $courseDescription->is_active ?? true,
                'video_count' => $courseDescription->video_count ?? $actualTotal,
                'total_materials' => $actualTotal,
                'tag' => $courseDescription->tag ?? null
            ];

            // Prepare response with enhanced data
            $response = [
                'success' => true,
                'data' => [
                    'courseDescription' => $courseDescriptionData,
                    'materis' => $materis,
                    'totalMateris' => $actualTotal,
                    // Enhanced for dashboard integration
                    'course_stats' => [
                        'total_materials' => $actualTotal,
                        'has_content' => $courseContent ? true : false,
                        'materials_source' => $courseContent ? 'course_content' : 'course_description'
                    ]
                ],
                'message' => 'Data berhasil dimuat'
            ];

            Log::info('Response prepared successfully', [
                'course_title' => $courseDescription->title,
                'materis_count' => $actualTotal,
                'materials_source' => $courseContent ? 'course_content' : 'course_description'
            ]);

            return response()->json($response);

        } catch (Exception $e) {
            Log::error('Error in getByCourseDescription: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'course_id' => $courseDescriptionId ?? 'unknown'
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan internal server',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Enhanced search method for better material discovery
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $query = $request->get('q', '');
            $courseId = $request->get('course_id');

            $searchQuery = CourseContent::query();

            if ($courseId) {
                $searchQuery->where('course_description_id', $courseId);
            }

            $results = $searchQuery->get();

            $searchResults = [];
            foreach ($results as $content) {
                if ($content->materials && is_array($content->materials)) {
                    foreach ($content->materials as $material) {
                        if (stripos($material['judul'] ?? '', $query) !== false ||
                            stripos($material['konten'] ?? '', $query) !== false) {
                            $searchResults[] = [
                                'judul' => $material['judul'] ?? 'Untitled',
                                'konten' => $material['konten'] ?? '',
                                'course_title' => $content->course_title,
                                'course_description_id' => $content->course_description_id
                            ];
                        }
                    }
                }
            }

            return response()->json([
                'success' => true,
                'data' => $searchResults,
                'message' => 'Pencarian berhasil'
            ]);

        } catch (Exception $e) {
            Log::error('Error in search: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat pencarian',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get statistics for course progress
     */
    public function getProgressStats($courseDescriptionId): JsonResponse
    {
        try {
            $courseDescription = CourseDescription::find($courseDescriptionId);
            if (!$courseDescription) {
                return response()->json([
                    'success' => false,
                    'message' => 'Course tidak ditemukan'
                ], 404);
            }

            $courseContent = CourseContent::where('course_description_id', $courseDescriptionId)->first();

            // Calculate total materials
            $totalMaterials = 0;
            if ($courseContent && $courseContent->materials && is_array($courseContent->materials)) {
                $totalMaterials = count($courseContent->materials);
            } else {
                // Fallback to course description fields
                $totalMaterials = $courseDescription->video_count ??
                                $courseDescription->total_materials ??
                                $courseDescription->material_count ?? 0;
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'course_id' => $courseDescriptionId,
                    'course_title' => $courseDescription->title,
                    'total_materials' => $totalMaterials,
                    'has_content' => $courseContent ? true : false,
                    'materials_source' => $courseContent ? 'course_content' : 'course_description'
                ]
            ]);

        } catch (Exception $e) {
            Log::error('Error in getProgressStats: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat statistik progress',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function getBySlug($slug): JsonResponse
    {
        try {
            $courseContent = CourseContent::where('slug', $slug)->first();

            if (!$courseContent) {
                return response()->json([
                    'success' => false,
                    'message' => 'Konten tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $courseContent,
                'message' => 'Data berhasil dimuat'
            ]);

        } catch (Exception $e) {
            Log::error('Error in getBySlug: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan internal server',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function getNavigation($courseDescriptionId): JsonResponse
    {
        try {
            $courseContent = CourseContent::where('course_description_id', $courseDescriptionId)->first();

            if (!$courseContent || !$courseContent->materials) {
                return response()->json([
                    'success' => false,
                    'message' => 'Navigasi tidak ditemukan'
                ], 404);
            }

            $navigation = collect($courseContent->materials)
                ->map(function ($material, $index) {
                    return [
                        'id' => $index + 1,
                        'judul' => $material['judul'] ?? 'Materi ' . ($index + 1),
                        'urutan' => $material['urutan'] ?? ($index + 1),
                    ];
                })
                ->sortBy('urutan')
                ->values();

            return response()->json([
                'success' => true,
                'data' => $navigation,
                'message' => 'Navigasi berhasil dimuat'
            ]);

        } catch (Exception $e) {
            Log::error('Error in getNavigation: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan internal server',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function getPrevNext($slug): JsonResponse
    {
        try {
            // Extract course info from slug
            $slugParts = explode('-materi-', $slug);
            if (count($slugParts) < 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'Format slug tidak valid'
                ], 400);
            }

            $courseSlug = $slugParts[0];
            $materialNumber = (int) $slugParts[1];

            $courseContent = CourseContent::where('slug', $courseSlug)->first();

            if (!$courseContent || !$courseContent->materials) {
                return response()->json([
                    'success' => false,
                    'message' => 'Konten course tidak ditemukan'
                ], 404);
            }

            $materials = collect($courseContent->materials)->sortBy('urutan')->values();
            $currentIndex = $materialNumber - 1;

            $prevMaterial = null;
            $nextMaterial = null;

            if ($currentIndex > 0) {
                $prev = $materials[$currentIndex - 1];
                $prevMaterial = [
                    'slug' => $courseSlug . '-materi-' . $prev['urutan'],
                    'judul' => $prev['judul']
                ];
            }

            if ($currentIndex < $materials->count() - 1) {
                $next = $materials[$currentIndex + 1];
                $nextMaterial = [
                    'slug' => $courseSlug . '-materi-' . $next['urutan'],
                    'judul' => $next['judul']
                ];
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'prev' => $prevMaterial,
                    'next' => $nextMaterial
                ],
                'message' => 'Navigasi berhasil dimuat'
            ]);

        } catch (Exception $e) {
            Log::error('Error in getPrevNext: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan internal server',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function index(): JsonResponse
    {
        try {
            $contents = CourseContent::with('courseDescription')->get();

            $formattedContents = $contents->map(function ($content) {
                $materialCount = $content->materials ? count($content->materials) : 0;

                return [
                    'id' => $content->id,
                    'course_title' => $content->course_title,
                    'slug' => $content->slug,
                    'course_description_id' => $content->course_description_id,
                    'material_count' => $materialCount,
                    'course_info' => $content->courseDescription ? [
                        'title' => $content->courseDescription->title,
                        'instructor_name' => $content->courseDescription->instructor_name,
                        'price' => $content->courseDescription->price
                    ] : null
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $formattedContents,
                'message' => 'Semua konten berhasil dimuat'
            ]);

        } catch (Exception $e) {
            Log::error('Error in index: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan internal server',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}
