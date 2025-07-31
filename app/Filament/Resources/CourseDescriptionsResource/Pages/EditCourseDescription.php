<?php

namespace App\Filament\Resources\CourseDescriptionsResource\Pages; // Sesuaikan namespace

use App\Filament\Resources\CourseDescriptionsResource; // Sesuaikan resource
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCourseDescription extends EditRecord // Sesuaikan nama kelas
{
    protected static string $resource = CourseDescriptionsResource::class; // Sesuaikan resource

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
