<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CourseDescriptionResource\Pages;
use App\Models\CourseDescriptions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CourseDescriptionResource extends Resource
{
    protected static ?string $model = CourseDescriptions::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Descriptions';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Course Information')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Title')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('tag')
                            ->label('Category / Tag')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\RichEditor::make('overview')
                            ->label('Overview')
                            ->required()
                            ->columnSpanFull(),

                        // FIXED: Use Cloudinary for cover image
                        Forms\Components\FileUpload::make('image_url')
                            ->label('Cover Image')
                            ->image()
                            ->disk('cloudinary') // Use Cloudinary disk
                            ->directory('course-covers')
                            ->visibility('public')
                            ->acceptedFileTypes(['image/jpeg', 'image/jpg', 'image/png', 'image/webp'])
                            ->maxSize(5120) // 5MB max
                            ->nullable()
                            ->enableDownload()
                            ->enableOpen()
                            ->imageEditor()  // Enable image editing
                            ->imageCropAspectRatio('16:9') // Set aspect ratio for consistency
                            ->imageResizeTargetWidth('1920')
                            ->imageResizeTargetHeight('1080'),

                        Forms\Components\FileUpload::make('thumbnail')
                            ->label('Thumbnail')
                            ->image()
                            ->disk('cloudinary') // Use Cloudinary disk
                            ->directory('course-thumbnails')
                            ->visibility('public')
                            ->acceptedFileTypes(['image/jpeg', 'image/jpg', 'image/png', 'image/webp'])
                            ->maxSize(2048) // 2MB max
                            ->nullable()
                            ->enableDownload()
                            ->enableOpen()
                            ->imageEditor()
                            ->imageCropAspectRatio('4:3')
                            ->imageResizeTargetWidth('800')
                            ->imageResizeTargetHeight('600'),
                    ])->columns(2),

                Forms\Components\Section::make('Pricing')
                    ->schema([
                        Forms\Components\TextInput::make('price')
                            ->label('Price')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->prefix('Rp'),
                        Forms\Components\TextInput::make('price_discount')
                            ->label('Discounted Price')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('Rp')
                            ->nullable(),
                    ])->columns(2),

                Forms\Components\Section::make('Instructor')
                    ->schema([
                        Forms\Components\TextInput::make('instructor_name')
                            ->label('Instructor Name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('instructor_position')
                            ->label('Instructor Position')
                            ->required()
                            ->maxLength(255),

                        // FIXED: Use Cloudinary for instructor image
                        Forms\Components\FileUpload::make('instructor_image_url')
                            ->label('Instructor Photo')
                            ->image()
                            ->disk('cloudinary') // Use Cloudinary disk
                            ->directory('instructors')
                            ->visibility('public')
                            ->acceptedFileTypes(['image/jpeg', 'image/jpg', 'image/png', 'image/webp'])
                            ->maxSize(2048) // 2MB max
                            ->nullable()
                            ->enableDownload()
                            ->enableOpen()
                            ->imageEditor()
                            ->imageCropAspectRatio('1:1') // Square for profile photos
                            ->imageResizeTargetWidth('400')
                            ->imageResizeTargetHeight('400'),
                    ])->columns(3),

                Forms\Components\Section::make('Details')
                    ->schema([
                        Forms\Components\TextInput::make('video_count')
                            ->label('Video Count')
                            ->numeric()
                            ->minValue(0)
                            ->nullable(),
                        Forms\Components\TextInput::make('duration')
                            ->label('Duration')
                            ->placeholder('e.g., 10h 30m')
                            ->maxLength(255)
                            ->nullable(),

                        // FIXED: Features repeater
                        Forms\Components\Repeater::make('features')
                            ->label('Features')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Feature')
                                    ->required()
                                    ->maxLength(255),
                            ])
                            ->columnSpanFull()
                            ->collapsible()
                            ->cloneable()
                            ->nullable()
                            ->default([])
                            ->addActionLabel('Add Feature'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // FIXED: Display images from Cloudinary
                Tables\Columns\ImageColumn::make('thumbnail')
                    ->label('Thumbnail')
                    ->disk('cloudinary')
                    ->defaultImageUrl(asset('images/default-course.png'))
                    ->size(60)
                    ->circular(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    }),
                Tables\Columns\TextColumn::make('instructor_name')
                    ->label('Instructor')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('price')
                    ->label('Price')
                    ->money('IDR', locale: 'id')
                    ->sortable(),
                Tables\Columns\TextColumn::make('price_discount')
                    ->label('Discount Price')
                    ->money('IDR', locale: 'id')
                    ->sortable()
                    ->placeholder('No discount'),
                Tables\Columns\TextColumn::make('tag')
                    ->label('Category')
                    ->badge()
                    ->searchable()
                    ->color(fn (string $state): string => match ($state) {
                        'web' => 'success',
                        'mobile' => 'info',
                        'design' => 'warning',
                        'data' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tag')
                    ->label('Category')
                    ->options([
                        'web' => 'Web Development',
                        'mobile' => 'Mobile Development',
                        'design' => 'Design',
                        'data' => 'Data Science',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }


        // Ubah bagian getPages() menjadi:
public static function getPages(): array
{
    return [
        'index' => Pages\ListCourseDescription::route('/'),
        'create' => Pages\CreateCourseDescription::route('/create'),
        'edit' => Pages\EditCourseDescription::route('/{record}/edit'),
    ];

    }
}
