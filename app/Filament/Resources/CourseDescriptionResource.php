<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CourseDescriptionResource\Pages; // Sesuaikan namespace
use App\Models\CourseDescriptions; // Gunakan model CourseDescriptions
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CourseDescriptionResource extends Resource // Ubah nama kelas
{
    protected static ?string $model = CourseDescriptions::class; // Arahkan ke CourseDescriptions

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Descriptions'; // Label menu
    protected static ?int $navigationSort = 2; // Urutan menu

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Course Information')
                    ->schema([
                        Forms\Components\TextInput::make('title') // Langsung ke kolom di CourseDescriptions
                            ->label('Title')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('tag') // Langsung ke kolom di CourseDescriptions
                            ->label('Category / Tag')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\RichEditor::make('overview') // Langsung ke kolom di CourseDescriptions
                            ->label('Overview')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('image_url') // Langsung ke kolom di CourseDescriptions
                            ->label('Cover Image')
                            ->image()
                            ->directory('course-covers'),
                        Forms\Components\FileUpload::make('thumbnail') // Langsung ke kolom di CourseDescriptions
                            ->label('Thumbnail')
                            ->image()
                            ->directory('course-thumbnails'),
                    ])->columns(2),

                Forms\Components\Section::make('Pricing')
                    ->schema([
                        Forms\Components\TextInput::make('price') // Langsung ke kolom di CourseDescriptions
                            ->label('Price')
                            ->required()
                            ->numeric()
                            ->prefix('Rp'),
                        Forms\Components\TextInput::make('price_discount') // Langsung ke kolom di CourseDescriptions
                            ->label('Discounted Price')
                            ->numeric()
                            ->prefix('Rp'),
                    ])->columns(2),

                Forms\Components\Section::make('Instructor')
                    ->schema([
                        Forms\Components\TextInput::make('instructor_name') // Langsung ke kolom di CourseDescriptions
                            ->label('Instructor Name')
                            ->required(),
                        Forms\Components\TextInput::make('instructor_position') // Langsung ke kolom di CourseDescriptions
                            ->label('Instructor Position')
                            ->required(),
                        Forms\Components\FileUpload::make('instructor_image_url') // Langsung ke kolom di CourseDescriptions
                            ->label('Instructor Photo')
                            ->image()
                            ->directory('instructors'),
                    ])->columns(3),

                Forms\Components\Section::make('Details')
                    ->schema([
                        Forms\Components\TextInput::make('video_count') // Langsung ke kolom di CourseDescriptions
                            ->label('Video Count')
                            ->numeric(),
                        Forms\Components\TextInput::make('duration') // Langsung ke kolom di CourseDescriptions
                            ->label('Duration')
                            ->placeholder('e.g., 10h 30m'),
                        Forms\Components\Repeater::make('features') // Langsung ke kolom di CourseDescriptions
                            ->label('Features')
                            ->schema([
                                Forms\Components\TextInput::make('feature_name')->required(),
                            ])
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        // Tampilkan data dari CourseDescriptions
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('thumbnail')
                    ->label('Thumbnail'),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('instructor_name')
                    ->label('Instructor'),
                Tables\Columns\TextColumn::make('price')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('tag')
                    ->label('Category'),
            ])
            ->filters([
                //
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
            'index' => Pages\ListCourseDescriptions::route('/'), // Sesuaikan nama halaman
            'create' => Pages\CreateCourseDescription::route('/create'), // Sesuaikan nama halaman
            'edit' => Pages\EditCourseDescription::route('/{record}/edit'), // Sesuaikan nama halaman
        ];
    }
}
