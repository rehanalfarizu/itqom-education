<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // <-- Untuk mengambil data user
// use App\Models\Course; // <-- Contoh jika Anda punya model Course
// use App\Models\UserProgress; // <-- Contoh jika Anda punya model Progress

class DashboardController extends Controller
{
    /**
     * Menyediakan data untuk halaman dashboard pengguna.
     */
    public function getData(Request $request)
    {
        // 1. Dapatkan user yang sedang login
        $user = Auth::user();

        // 2. Ambil data real dari UserCourse dan CourseContent
        $userCourses = $user->userCourses;
        $totalCourses = $userCourses->count();
        $completedCourses = $userCourses->where('progress_percentage', 100)->count();
        
        // Alternatif: hitung dari course content yang sudah diselesaikan
        $totalModules = 0;
        $completedModules = 0;
        
        foreach ($userCourses as $userCourse) {
            $courseContents = $userCourse->course ? $userCourse->course->courseDescription->courseContents : collect();
            $totalModules += $courseContents->count();
            $completedModules += floor($courseContents->count() * ($userCourse->progress_percentage / 100));
        }
        
        // Fallback jika tidak ada data
        if ($totalModules == 0) {
            $totalModules = 1; // Hindari division by zero
            $completedModules = 0;
        }
        $activeCourse = $user->activeCourse()->first(); // Misal: kursus yang sedang aktif
        $nextSession = $user->nextMentoringSession()->first(); // Misal: jadwal mentoring
        $notifications = $user->unreadNotifications()->take(3)->get(); // Misal: 3 notifikasi terbaru
        $leaderboard = $this->getWeeklyLeaderboard($user); // Panggil fungsi lain untuk leaderboard


        // 3. Susun data dalam format JSON yang mudah dibaca oleh Vue
        return response()->json([
            // Info User Dasar
            'user' => [
                'name' => $user->name,
                'level' => $user->level ?? 'Level 3', // ?? 'Level 3' adalah fallback
                'xp' => $user->xp ?? 1200,
                'level_progress' => $user->level_progress ?? 60,
            ],

            // Data untuk Kartu Progres
            'progress' => [
                'completed_modules' => $completedModules,
                'total_modules' => $totalModules,
                'percentage' => $totalModules > 0 ? round(($completedModules / $totalModules) * 100) : 0,
            ],

            // Data untuk Kartu Program Aktif
            'active_course' => [
                'title' => $activeCourse->title ?? 'Belajar Laravel & Vue 3',
                'start_date' => $activeCourse->start_date->format('j F Y') ?? '1 Juli 2025',
                'status' => 'On Fire!',
            ],

            // Data untuk Kartu Sesi Selanjutnya
            'next_session' => [
                'schedule' => $nextSession->schedule->format('l, j F Y - H:i') . ' WIB' ?? 'Rabu, 23 Juli 2025 - 19:00 WIB',
                'mentor_name' => $nextSession->mentor->name ?? 'Tim Dunia Coding',
            ],

            // Data untuk Notifikasi
            'notifications' => $notifications,

            // Data untuk Leaderboard
            'leaderboard' => $leaderboard,
        ]);
    }

    /**
     * Contoh fungsi privat untuk mengambil data leaderboard.
     *
     * @param \App\Models\User $currentUser
     * @return array
     */
    private function getWeeklyLeaderboard($currentUser)
    {
        // Logika untuk mengambil 2 user teratas + data user saat ini
        // Ini hanya contoh sederhana
        return [
            ['name' => $currentUser->name, 'xp' => $currentUser->xp ?? 1200, 'rank' => 1],
            ['name' => 'Bregas', 'xp' => 1100, 'rank' => 2],
            ['name' => 'Yafa', 'xp' => 950, 'rank' => 3],
        ];
    }
}

