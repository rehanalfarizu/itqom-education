<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CourseResource\Pages;
use App\Models\Course;
use App\Models\CourseDescription;
use App\Services\CloudinaryService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;


class CourseResource extends Resource
{
    protected static ?string $model = Course::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'Course Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Select::make('course_description_id')
                            ->label('Course Description')
                            ->options(CourseDescription::all()->pluck('title', 'id'))
                            ->searchable()
                            ->required(),

                        TextInput::make('instructor')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('video_count')
                            ->numeric()
                            ->required()
                            ->minValue(1),

                        TextInput::make('duration')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., 10 hours'),

                        TextInput::make('category')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('original_price')
                            ->numeric()
                            ->prefix('Rp')
                            ->required()
                            ->minValue(0),

                        TextInput::make('price')
                            ->numeric()
                            ->prefix('Rp')
                            ->required()
                            ->minValue(0),

                        FileUpload::make('image')
                            ->label('Course Image')
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '16:9',
                                '4:3',
                                '1:1',
                            ])
                            ->directory('courses')
                            ->disk('public')
                            ->visibility('public')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(5120) // 5MB
                            ->columnSpanFull()
                            ->helperText('Upload course thumbnail image. Recommended size: 1280x720 pixels')
                            ->getUploadedFileNameForStorageUsing(
                                fn (UploadedFile $file): string => (string) str($file->getClientOriginalName())
                                    ->prepend('course_' . time() . '_'),
                            )
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    // Use hybrid upload when file is uploaded
                                    $cloudinaryService = app(\App\Services\CloudinaryService::class);
                                    try {
                                        $hybridResult = $cloudinaryService->uploadImageHybrid($state);
                                        if ($hybridResult['success']) {
                                            $set('image', $hybridResult['path']);
                                            Log::info('Hybrid upload successful: ' . $hybridResult['path']);
                                        }
                                    } catch (\Exception $e) {
                                        Log::warning('Hybrid upload failed, using local path: ' . $e->getMessage());
                                        // Continue with local path if hybrid fails
                                    }
                                }
                            }),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('Image')
                    ->disk('public')
                    ->size(60)
                    ->square(),

                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                TextColumn::make('courseDescription.title')
                    ->label('Description')
                    ->searchable()
                    ->limit(20),

                TextColumn::make('instructor')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('category')
                    ->searchable()
                    ->badge()
                    ->color('primary'),

                TextColumn::make('video_count')
                    ->label('Videos')
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('duration')
                    ->sortable(),

                TextColumn::make('price')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('original_price')
                    ->label('Original Price')
                    ->money('IDR')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->options([
                        'Programming' => 'Programming',
                        'Design' => 'Design',
                        'Marketing' => 'Marketing',
                        'Business' => 'Business',
                        'Photography' => 'Photography',
                        'Music' => 'Music',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCourses::route('/'),
            'create' => Pages\CreateCourse::route('/create'),
            'edit' => Pages\EditCourse::route('/{record}/edit'),
        ];
    }
}
