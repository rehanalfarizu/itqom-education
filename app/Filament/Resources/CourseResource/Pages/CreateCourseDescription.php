<?php

namespace App\Filament\Resources\CourseDescriptionResource\Pages; // Sesuaikan namespace

use App\Filament\Resources\CourseDescriptionResource; // Sesuaikan resource
use Filament\Resources\Pages\CreateRecord;

class CreateCourseDescription extends CreateRecord // Sesuaikan nama kelas
{
    protected static string $resource = CourseDescriptionResource::class; // Sesuaikan resource

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
