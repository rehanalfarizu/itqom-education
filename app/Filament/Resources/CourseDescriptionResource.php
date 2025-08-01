<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CourseDescriptionResource\Pages;
use App\Filament\Resources\CourseDescriptionResource\RelationManagers;
use App\Models\CourseDescription;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CourseDescriptionResource extends Resource
{
    protected static ?string $model = CourseDescription::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Course Descriptions';

    protected static ?string $modelLabel = 'Course Description';

    protected static ?string $pluralModelLabel = 'Course Descriptions';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('tag')
                    ->label('Category/Tag')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('overview')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('image_url')
                    ->label('Course Image')
                    ->image()
                    ->disk('cloudinary')
                    ->directory('courses')
                    ->imageEditor()
                    ->imageEditorAspectRatios([
                        '16:9',
                        '4:3',
                        '1:1',
                    ])
                    ->maxSize(5120) // 5MB
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->previewable(true)
                    ->downloadable(false)
                    ->openable(false)
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('thumbnail')
                    ->label('Thumbnail Image')
                    ->image()
                    ->disk('cloudinary')
                    ->directory('courses/thumbnails')
                    ->imageEditor()
                    ->imageEditorAspectRatios([
                        '16:9',
                        '4:3',
                        '1:1',
                    ])
                    ->maxSize(2048) // 2MB
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->previewable(true)
                    ->downloadable(false)
                    ->openable(false),
                Forms\Components\TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->step(1000),
                Forms\Components\TextInput::make('price_discount')
                    ->numeric()
                    ->prefix('Rp')
                    ->step(1000),
                Forms\Components\TextInput::make('instructor_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('instructor_position')
                    ->maxLength(255),
                Forms\Components\FileUpload::make('instructor_image_url')
                    ->label('Instructor Photo')
                    ->image()
                    ->disk('cloudinary')
                    ->directory('instructors')
                    ->imageEditor()
                    ->imageEditorAspectRatios([
                        '1:1',
                    ])
                    ->maxSize(1024) // 1MB
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->previewable(true)
                    ->downloadable(false)
                    ->openable(false),
                Forms\Components\TextInput::make('video_count')
                    ->required()
                    ->numeric()
                    ->minValue(1),
                Forms\Components\TextInput::make('duration')
                    ->required()
                    ->numeric()
                    ->suffix('hours')
                    ->minValue(0.5)
                    ->step(0.5),
                Forms\Components\Repeater::make('features')
                    ->schema([
                        Forms\Components\TextInput::make('feature')
                            ->required()
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_url')
                    ->label('Image')
                    ->circular()
                    ->size(50),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tag')
                    ->label('Category')
                    ->searchable()
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('instructor_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('price_discount')
                    ->money('IDR')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('video_count')
                    ->numeric()
                    ->sortable()
                    ->suffix(' videos'),
                Tables\Columns\TextColumn::make('duration')
                    ->suffix(' hours')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListCourseDescriptions::route('/'),
            'create' => Pages\CreateCourseDescription::route('/create'),
            'edit' => Pages\EditCourseDescription::route('/{record}/edit'),
        ];
    }
}
