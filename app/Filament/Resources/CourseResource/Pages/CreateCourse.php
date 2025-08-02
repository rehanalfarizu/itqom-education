<?php

namespace App\Filament\Resources\CourseResource\Pages;

use App\Filament\Resources\CourseResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCourse extends CreateRecord
{
    protected static string $resource = CourseResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Image sudah tersimpan di storage/app/public/courses oleh Filament
        // Tidak perlu processing tambahan, path sudah benar

        return $data;
    }
}
