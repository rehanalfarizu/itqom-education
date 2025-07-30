<?php

namespace App\Filament\Resources\CourseDescriptionResource\Pages; // Sesuaikan namespace

use App\Filament\Resources\CourseDescriptionResource; // Sesuaikan resource
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCourseDescriptions extends ListRecords // Sesuaikan nama kelas
{
    protected static string $resource = CourseDescriptionResource::class; // Sesuaikan resource

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
