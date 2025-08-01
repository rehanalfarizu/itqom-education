<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\Course;
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
            return [
                'totalCourses' => Course::count(),
                'activeCourses' => Course::where('is_active', true)->count(),
                'totalStudents' => Course::withCount('userCourses')->get()->sum('user_courses_count'),
                'recentCourses' => Course::with(['courseDescriptions', 'userCourses'])
                    ->latest()
                    ->take(6)
                    ->get()
                    ->map(function ($course) {
                        return [
                            'id' => $course->id,
                            'title' => $course->title,
                            'description' => $course->courseDescriptions->first()?->description ?? 'No description',
                            'thumbnail' => $course->thumbnail_url,
                            'students_count' => $course->userCourses->count(),
                            'duration' => $this->calculateCourseDuration($course),
                            'level' => $course->level ?? 'Beginner',
                            'price' => $course->price,
                            'rating' => $this->calculateCourseRating($course),
                            'created_at' => $course->created_at,
                            'updated_at' => $course->updated_at,
                        ];
                    }),
                'popularCourses' => Course::withCount('userCourses')
                    ->orderBy('user_courses_count', 'desc')
                    ->take(4)
                    ->get(),
                'courseStats' => [
                    'this_month' => Course::whereMonth('created_at', now()->month)->count(),
                    'last_month' => Course::whereMonth('created_at', now()->subMonth()->month)->count(),
                    'growth_rate' => $this->calculateGrowthRate(),
                ]
            ];
        });
        
        return $data;
    }
    
    private function calculateCourseDuration(Course $course): string
    {
        // Calculate total course duration from course contents
        $totalMinutes = $course->courseContents->sum('duration_minutes') ?? 0;
        
        if ($totalMinutes < 60) {
            return $totalMinutes . ' min';
        }
        
        $hours = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;
        
        return $hours . 'h ' . ($minutes > 0 ? $minutes . 'm' : '');
    }
    
    private function calculateCourseRating(Course $course): float
    {
        // Simulate rating calculation - implement your actual rating logic
        return round(rand(35, 50) / 10, 1);
    }
    
    private function calculateGrowthRate(): float
    {
        $thisMonth = Course::whereMonth('created_at', now()->month)->count();
        $lastMonth = Course::whereMonth('created_at', now()->subMonth()->month)->count();
        
        if ($lastMonth == 0) return 100;
        
        return round((($thisMonth - $lastMonth) / $lastMonth) * 100, 1);
    }
    
    public function loadData()
    {
        $this->isLoading = false;
        $this->dispatch('dataLoaded');
    }
}
