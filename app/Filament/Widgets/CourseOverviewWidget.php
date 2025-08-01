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
                'activeCourses' => Course::whereNotNull('title')->count(), // Courses yang memiliki title sebagai active
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
                            'thumbnail' => $course->image, // Gunakan field image yang ada
                            'students_count' => $course->userCourses->count(),
                            'duration' => $this->calculateCourseDuration($course),
                            'level' => $course->category ?? 'General', // Gunakan category sebagai level
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
        // Gunakan field duration yang sudah ada di tabel courses
        if ($course->duration) {
            return $course->duration;
        }

        // Fallback: hitung berdasarkan jumlah materials jika ada
        $totalContents = $course->courseContents->count() ?? 0;

        if ($totalContents == 0) {
            return '0 min';
        }

        // Estimasi 5 menit per content
        $estimatedMinutes = $totalContents * 5;

        if ($estimatedMinutes < 60) {
            return $estimatedMinutes . ' min';
        }

        $hours = floor($estimatedMinutes / 60);
        $minutes = $estimatedMinutes % 60;

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
