<?php

namespace App\Filament\Resources\CourseContentResource\Pages;

use App\Filament\Resources\CourseContentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditCourseContent extends EditRecord
{
    protected static string $resource = CourseContentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Decode materials from JSON for editing
        if (isset($data['materials']) && is_string($data['materials'])) {
            $data['materials'] = json_decode($data['materials'], true) ?? [];
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Ensure materials have proper ordering
        if (isset($data['materials'])) {
            foreach ($data['materials'] as $index => &$material) {
                if (empty($material['urutan'])) {
                    $material['urutan'] = $index + 1;
                }
            }
            // Sort materials by urutan
            $data['materials'] = collect($data['materials'])->sortBy('urutan')->values()->toArray();
        }

        return $data;
    }

    protected function getUpdatedNotificationTitle(): ?string
    {
        return 'Course content updated successfully';
    }

    protected function afterSave(): void
    {
        $materialsCount = is_array($this->record->materials) ? count($this->record->materials) : 0;

        Notification::make()
            ->title('Course Content Updated')
            ->body("Course content with {$materialsCount} materials has been updated.")
            ->success()
            ->send();
    }
}
