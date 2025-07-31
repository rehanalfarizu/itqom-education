<?php

namespace App\Filament\Resources\CourseDescriptionsResource\Pages; // Sesuaikan namespace

use App\Filament\Resources\CourseDescriptionsResource; // Sesuaikan resource
use Filament\Resources\Pages\CreateRecord;

class CreateCourseDescription extends CreateRecord // Sesuaikan nama kelas
{
    protected static string $resource = CourseDescriptionsResource::class; // Sesuaikan resource

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
