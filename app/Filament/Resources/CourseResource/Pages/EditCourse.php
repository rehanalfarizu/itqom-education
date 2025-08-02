<?php

namespace App\Filament\Resources\CourseResource\Pages;

use App\Filament\Resources\CourseResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

class EditCourse extends EditRecord
{
    protected static string $resource = CourseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Handle local storage image update
        if (isset($data['image']) && $data['image'] && is_string($data['image'])) {
            // Check if old image exists and is different from new one
            if ($this->record->image && $this->record->image !== $data['image']) {
                // Delete old image file if it exists
                if (Storage::disk('public')->exists($this->record->image)) {
                    Storage::disk('public')->delete($this->record->image);
                }
            }
        }

        return $data;
    }
}
