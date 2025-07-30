<?php

namespace App\Filament\Resources\CourseContentResource\Pages;

use App\Filament\Resources\CourseContentResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Support\Enums\FontWeight;

class ViewCourseContent extends ViewRecord
{
    protected static string $resource = CourseContentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Course Information')
                    ->schema([
                        TextEntry::make('courseDescription.title')
                            ->label('Course Title')
                            ->weight(FontWeight::Bold),

                        TextEntry::make('course_title')
                            ->label('Internal Course Title'),

                        TextEntry::make('slug')
                            ->label('Slug')
                            ->copyable(),

                        TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime(),

                        TextEntry::make('updated_at')
                            ->label('Last Updated')
                            ->dateTime(),
                    ])
                    ->columns(2),

                Section::make('Course Materials')
                    ->schema([
                        RepeatableEntry::make('materials')
                            ->label('')
                            ->schema([
                                TextEntry::make('judul')
                                    ->label('Title')
                                    ->weight(FontWeight::Bold),

                                TextEntry::make('urutan')
                                    ->label('Order')
                                    ->badge(),

                                TextEntry::make('konten')
                                    ->label('Content')
                                    ->html()
                                    ->columnSpanFull()
                                    ->limit(200),
                            ])
                            ->columns(2)
                            ->contained(false)
                            ->grid(1)
                    ]),

                Section::make('Materials Summary')
                    ->schema([
                        TextEntry::make('materials_summary')
                            ->label('')
                            ->getStateUsing(function ($record) {
                                $materials = is_string($record->materials)
                                    ? json_decode($record->materials, true)
                                    : $record->materials;

                                if (!is_array($materials) || empty($materials)) {
                                    return '<div class="text-gray-500">No materials found</div>';
                                }

                                $sortedMaterials = collect($materials)->sortBy('urutan');
                                $summary = '<div class="space-y-2">';
                                $summary .= '<div class="font-medium text-gray-900">Total Materials: ' . count($materials) . '</div>';
                                $summary .= '<div class="grid gap-2">';

                                foreach ($sortedMaterials as $material) {
                                    $summary .= '<div class="p-3 bg-gray-50 rounded border">';
                                    $summary .= '<div class="flex justify-between items-start">';
                                    $summary .= '<span class="font-medium">' . ($material['urutan'] ?? 0) . '. ' . ($material['judul'] ?? 'Untitled') . '</span>';
                                    $summary .= '<span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded">Order: ' . ($material['urutan'] ?? 0) . '</span>';
                                    $summary .= '</div>';
                                    $contentPreview = strip_tags($material['konten'] ?? '');
                                    $contentPreview = strlen($contentPreview) > 100 ? substr($contentPreview, 0, 100) . '...' : $contentPreview;
                                    $summary .= '<div class="text-sm text-gray-600 mt-2">' . $contentPreview . '</div>';
                                    $summary .= '</div>';
                                }

                                $summary .= '</div>';
                                $summary .= '</div>';

                                return $summary;
                            })
                            ->html(),
                    ]),
            ]);
    }
}


