<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run()
    {
        Course::create([
            'course_description_id' => 1, // Wajib ada
            'title' => 'Full Stack Mobile Development',
            'instructor' => 'John Doe',
            'duration' => '10 jam 30 menit • 25 video',
            'original_price' => 150000, // Ganti 'original' ke 'original_price'
            'price' => 120000,
            'image' => '/image/devfest-stockholm.png',
            'category' => 'Fullstack Development'
        ]);

        Course::create([
            'course_description_id' => 1, // Wajib ada
            'title' => 'Fundamental ReactJS',
            'instructor' => 'Jane Smith',
            'duration' => '6 jam 15 menit • 15 video',
            'original_price' => 100000, // Ganti 'original' ke 'original_price'
            'price' => 80000,
            'image' => '/image/devfest-stockholm.png',
            'category' => 'Web Programming'
        ]);
    }
}
