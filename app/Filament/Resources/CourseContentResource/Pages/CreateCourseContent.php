<?php

namespace App\Filament\Resources\CourseContentResource\Pages;

use App\Filament\Resources\CourseContentResource;
use App\Models\CourseContent;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;

class CreateCourseContent extends CreateRecord
{
    protected static string $resource = CourseContentResource::class;

    protected static ?string $title = 'Create Course Content';

    protected function handleRecordCreation(array $data): Model
    {
        // Validate that we have materials
        if (empty($data['materials'])) {
            throw new \Exception('At least one material is required');
        }

        // Sort materials by urutan
        $materials = collect($data['materials'])->sortBy('urutan')->values()->toArray();

        // Create single course content record with materials as JSON
        $courseContent = CourseContent::create([
            'course_description_id' => $data['course_description_id'],
            'course_title' => $data['course_title'],
            'slug' => $data['slug'],
            'materials' => $materials, // This will be cast to JSON by the model
        ]);

        // Send success notification
        Notification::make()
            ->title('Course Content Created Successfully')
            ->body('Course content with ' . count($materials) . ' materials has been created.')
            ->success()
            ->send();

        return $courseContent;
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Ensure materials have proper ordering if not set
        if (isset($data['materials'])) {
            foreach ($data['materials'] as $index => &$material) {
                if (empty($material['urutan'])) {
                    $material['urutan'] = $index + 1;
                }
            }
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
