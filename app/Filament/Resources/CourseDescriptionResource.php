<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CourseDescriptionResource\Pages;
use App\Filament\Resources\CourseDescriptionResource\RelationManagers;
use App\Models\CourseDescription;
use App\Services\CloudinaryService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class CourseDescriptionResource extends Resource
{
    protected static ?string $model = CourseDescription::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationLabel = 'Courses';

    protected static ?string $modelLabel = 'Course';

    protected static ?string $pluralModelLabel = 'Courses';

    protected static ?string $navigationGroup = 'Course Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Course Basic Information
                Forms\Components\Section::make('Course Information')
                    ->schema([
                        TextInput::make('title')
                            ->label('Course Title')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        TextInput::make('tag')
                            ->label('Category/Tag')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('instructor_name')
                            ->label('Instructor Name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('instructor_position')
                            ->label('Instructor Position')
                            ->maxLength(255),

                        Textarea::make('overview')
                            ->label('Course Overview')
                            ->required()
                            ->rows(4)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                // Course Media
                Forms\Components\Section::make('Course Media')
                    ->schema([
                        FileUpload::make('temp_image_upload')
                            ->label('Course Image')
                            ->image()
                            ->disk('public')
                            ->directory('course-images')
                            ->visibility('public')
                            ->acceptedFileTypes(['image/jpeg', 'image/jpg', 'image/png', 'image/webp'])
                            ->maxSize(5120) // 5MB
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('16:9')
                            ->imageResizeTargetWidth('800')
                            ->imageResizeTargetHeight('450')
                            ->helperText('Upload course image with 16:9 aspect ratio. Max: 5MB')
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    // Show immediate feedback
                                    $set('image_url', 'Uploading...');

                                    try {
                                        // ALWAYS upload to courses folder with proper naming
                                        $cloudinaryService = app(CloudinaryService::class);
                                        
                                        // Generate a proper filename for courses
                                        $originalName = $state->getClientOriginalName();
                                        $extension = $state->getClientOriginalExtension();
                                        $timestamp = time();
                                        $randomString = bin2hex(random_bytes(8));
                                        $fileName = "course_{$timestamp}_{$randomString}";
                                        
                                        // Upload directly to courses folder with our custom public_id
                                        $imagePath = $cloudinaryService->uploadImageWithPublicId($state, $fileName, 'courses');
                                        $set('image_url', $imagePath);
                                        
                                        // Clear the temp upload to reduce form size
                                        $set('temp_image_upload', null);
                                        Log::info('Course image uploaded successfully to: courses/' . $fileName);
                                    } catch (\Exception $e) {
                                        Log::error('Course image upload failed: ' . $e->getMessage());
                                        $set('image_url', 'Upload failed - please try again');
                                    }
                                }
                            })
                            ->dehydrated(false) // Don't save this field to database
                            ->saveUploadedFileUsing(function () {
                                // Prevent default file saving to reduce processing
                                return null;
                            }),

                        Hidden::make('image_url'), // Store the URL but don't display it

                        FileUpload::make('instructor_image_url')
                            ->label('Instructor Photo')
                            ->image()
                            ->disk('public')
                            ->directory('instructor-images')
                            ->visibility('public')
                            ->acceptedFileTypes(['image/jpeg', 'image/jpg', 'image/png', 'image/webp'])
                            ->maxSize(2048) // 2MB
                            ->imageEditor()
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('1:1')
                            ->imageResizeTargetWidth('300')
                            ->imageResizeTargetHeight('300')
                            ->helperText('Upload instructor photo with 1:1 aspect ratio. Max: 2MB')
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    try {
                                        $cloudinaryService = app(CloudinaryService::class);
                                        
                                        // Generate proper filename for instructor images
                                        $timestamp = time();
                                        $randomString = bin2hex(random_bytes(8));
                                        $fileName = "instructor_{$timestamp}_{$randomString}";
                                        
                                        // Upload to instructors folder
                                        $imagePath = $cloudinaryService->uploadImageWithPublicId($state, $fileName, 'instructors');
                                        $set('instructor_image_url', $imagePath);
                                        Log::info('Instructor image uploaded successfully to: instructors/' . $fileName);
                                    } catch (\Exception $e) {
                                        Log::error('Instructor image upload failed: ' . $e->getMessage());
                                        $set('instructor_image_url', 'Upload failed - please try again');
                                    }
                                }
                            }),
                    ])
                    ->columns(2),

                // Course Details
                Forms\Components\Section::make('Course Details')
                    ->schema([
                        TextInput::make('video_count')
                            ->label('Number of Videos')
                            ->numeric()
                            ->required()
                            ->minValue(1),

                        TextInput::make('duration')
                            ->label('Course Duration')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., 10 hours'),

                        TextInput::make('price')
                            ->label('Original Price')
                            ->numeric()
                            ->prefix('Rp')
                            ->required()
                            ->minValue(0),

                        TextInput::make('price_discount')
                            ->label('Discounted Price')
                            ->numeric()
                            ->prefix('Rp')
                            ->minValue(0),

                        Forms\Components\Repeater::make('features')
                            ->label('Course Features')
                            ->schema([
                                TextInput::make('feature')
                                    ->label('Feature')
                                    ->required()
                                    ->maxLength(255),
                            ])
                            ->addActionLabel('Add Feature')
                            ->collapsible()
                            ->collapsed()
                            ->cloneable()
                            ->reorderable()
                            ->maxItems(10)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_url')
                    ->label('Course Image')
                    ->disk('public')
                    ->size(60),

                TextColumn::make('title')
                    ->label('Course Title')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                TextColumn::make('tag')
                    ->label('Category')
                    ->searchable()
                    ->badge()
                    ->color('primary'),

                TextColumn::make('instructor_name')
                    ->label('Instructor')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('video_count')
                    ->label('Videos')
                    ->sortable()
                    ->alignCenter()
                    ->suffix(' videos'),

                TextColumn::make('duration')
                    ->label('Duration')
                    ->sortable(),

                TextColumn::make('price')
                    ->label('Price')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('price_discount')
                    ->label('Discount Price')
                    ->money('IDR')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('courses_count')
                    ->label('Course Entries')
                    ->counts('courses')
                    ->alignCenter()
                    ->badge()
                    ->color('success'),

                TextColumn::make('user_courses_count')
                    ->label('Enrollments')
                    ->counts('userCourses')
                    ->alignCenter()
                    ->badge()
                    ->color('info'),

                TextColumn::make('created_at')
                    ->label('Created')
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
            'index' => Pages\ListCourseDescription::route('/'),
            'create' => Pages\CreateCourseDescription::route('/create'),
            'edit' => Pages\EditCourseDescription::route('/{record}/edit'),
        ];
    }
}
