<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CourseDescription;

class CourseDescriptionSeeder extends Seeder
{
    public function run()
    {
        $cd = CourseDescription::create([
            'title' => 'Complete Web Development Bootcamp 2025',
            'tag' => 'Development',
            'overview' => 'Learn to code and become a full-stack web developer with HTML, CSS, JavaScript, Node.js, Express, MongoDB, and more!',
            'price' => 999000,
            'price_discount' => 799000,
            'instructor_name' => 'Angela Yu',
            'instructor_position' => 'Lead Instructor at App Brewery',
            'image_url' => 'images/webdev2025.jpg',
            'thumbnail' => 'thumbs/webdev2025-thumb.jpg',
            'video_count' => 85,
            'duration' => 45,
            'features' => [
                'Full Lifetime Access',
                'Certificate of Completion',
                'Downloadable Resources'
            ]
        ]);

        // Create additional course data untuk testing
        CourseDescription::create([
            'title' => 'React Native Mobile Development',
            'tag' => 'Mobile Development',
            'overview' => 'Build cross-platform mobile apps with React Native. Learn to create iOS and Android apps from a single codebase.',
            'price' => 899000,
            'price_discount' => 699000,
            'instructor_name' => 'Maximilian Schwarzmüller',
            'instructor_position' => 'React & React Native Expert',
            'image_url' => 'images/react-native.jpg',
            'thumbnail' => 'thumbs/react-native-thumb.jpg',
            'video_count' => 62,
            'duration' => 38,
            'features' => [
                'Build Real Apps',
                'iOS & Android Publishing',
                'State Management'
            ]
        ]);

        CourseDescription::create([
            'title' => 'UI/UX Design Masterclass',
            'tag' => 'Design',
            'overview' => 'Master the art of UI/UX design with Figma, Adobe XD, and design thinking principles.',
            'price' => 799000,
            'price_discount' => 599000,
            'instructor_name' => 'Sarah Johnson',
            'instructor_position' => 'Senior UI/UX Designer at Google',
            'image_url' => 'images/ui-ux-design.jpg',
            'thumbnail' => 'thumbs/ui-ux-thumb.jpg',
            'video_count' => 45,
            'duration' => 32,
            'features' => [
                'Design System Creation',
                'User Research Methods',
                'Prototype Development'
            ]
        ]);
    }
}
