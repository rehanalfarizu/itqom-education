<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\Course;
use App\Models\UserCourse;
use App\Models\CourseDescription;
use Illuminate\Support\Facades\Cache;

class CourseOverviewWidget extends Widget
{
    protected static string $view = 'filament.widgets.course-overview';
    
    protected int | string | array $columnSpan = 'full';
    
    protected static ?int $sort = 1;
    
    public bool $isLoading = true;
    
    public function mount(): void
    {
        // Simulate loading delay for demonstration
        $this->isLoading = true;
    }
    
    public function getViewData(): array
    {
        // Use cache to improve performance
        $data = Cache::remember('course_overview_data', 300, function () {
            // Get basic statistics
            $totalCourses = Course::count();
            $totalEnrollments = UserCourse::count();
            $completedCourses = UserCourse::where('is_completed', true)->count();
            $activeEnrollments = UserCourse::where('is_completed', false)->count();
            
            // Get recent enrollments with course details
            $recentEnrollments = UserCourse::with(['user', 'course'])
                ->latest('enrolled_at')
                ->take(6)
                ->get()
                ->map(function ($enrollment) {
                    return [
                        'id' => $enrollment->id,
                        'user_name' => $enrollment->user->name ?? 'Unknown User',
                        'user_email' => $enrollment->user->email ?? '',
                        'course_title' => $enrollment->course->title ?? 'Unknown Course',
                        'course_description' => $enrollment->course->description ?? 'No description',
                        'progress_percentage' => $enrollment->progress_percentage ?? 0,
                        'enrolled_at' => $enrollment->enrolled_at,
                        'last_accessed_at' => $enrollment->last_accessed_at,
                        'is_completed' => $enrollment->is_completed,
                        'completed_at' => $enrollment->completed_at,
                    ];
                });
            
            // Get popular courses (most enrolled)
            $popularCourses = UserCourse::selectRaw('course_id, COUNT(*) as enrollment_count')
                ->with('course')
                ->groupBy('course_id')
                ->orderBy('enrollment_count', 'desc')
                ->take(4)
                ->get()
                ->map(function ($item) {
                    return [
                        'course_title' => $item->course->title ?? 'Unknown Course',
                        'course_description' => $item->course->description ?? 'No description',
                        'enrollment_count' => $item->enrollment_count,
                        'course_id' => $item->course_id,
                    ];
                });
                
            // Calculate monthly statistics
            $thisMonth = UserCourse::whereMonth('enrolled_at', now()->month)
                ->whereYear('enrolled_at', now()->year)
                ->count();
            $lastMonth = UserCourse::whereMonth('enrolled_at', now()->subMonth()->month)
                ->whereYear('enrolled_at', now()->subMonth()->year)
                ->count();
            
            $growthRate = $lastMonth > 0 ? round((($thisMonth - $lastMonth) / $lastMonth) * 100, 1) : 100;
            
            return [
                'totalCourses' => $totalCourses,
                'totalEnrollments' => $totalEnrollments,
                'completedCourses' => $completedCourses,
                'activeEnrollments' => $activeEnrollments,
                'recentEnrollments' => $recentEnrollments,
                'popularCourses' => $popularCourses,
                'enrollmentStats' => [
                    'this_month' => $thisMonth,
                    'last_month' => $lastMonth,
                    'growth_rate' => $growthRate,
                    'completion_rate' => $totalEnrollments > 0 ? round(($completedCourses / $totalEnrollments) * 100, 1) : 0,
                ]
            ];
        });
        
        return $data;
    }
    
    public function loadData()
    {
        $this->isLoading = false;
        $this->dispatch('dataLoaded');
    }
}
