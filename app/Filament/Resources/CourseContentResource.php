<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CourseContentResource\Pages;
use App\Models\CourseContent;
use App\Models\CourseDescription;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table as FilamentTable;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Get;
use Illuminate\Support\Str;

class CourseContentResource extends Resource
{
    protected static ?string $model = CourseContent::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationGroup = 'Courses';
    protected static ?string $navigationLabel = 'Course Materials';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Course Information')
                    ->description('Create course content with multiple materials')
                    ->schema([
                        Forms\Components\Select::make('course_description_id')
                            ->label('Course Description')
                            ->options(CourseDescription::pluck('title', 'id'))
                            ->searchable()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                if ($state) {
                                    $course = CourseDescription::find($state);
                                    if ($course) {
                                        $set('course_title', $course->title);
                                        // Auto generate slug from course title
                                        $set('slug', Str::slug($course->title));
                                    }
                                }
                            }),

                        Forms\Components\TextInput::make('course_title')
                            ->label('Course Title')
                            ->required()
                            ->disabled()
                            ->dehydrated(),

                        Forms\Components\TextInput::make('slug')
                            ->label('Course Slug')
                            ->required()
                            ->unique(CourseContent::class, 'slug', ignoreRecord: true)
                            ->helperText('Unique identifier for this course content'),
                    ])
                    ->columns(2),

                Section::make('Course Materials')
                    ->description('Add multiple materials for this course')
                    ->schema([
                        Repeater::make('materials')
                            ->label('Materials')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('judul')
                                            ->label('Material Title')
                                            ->required(),

                                        Forms\Components\TextInput::make('urutan')
                                            ->label('Order')
                                            ->numeric()
                                            ->default(function (Get $get) {
                                                $materials = $get('../../materials') ?? [];
                                                return count($materials) + 1;
                                            })
                                            ->helperText('Material order in the course'),
                                    ]),

                                Forms\Components\RichEditor::make('konten')
                                    ->label('Content')
                                    ->required()
                                    ->columnSpanFull()
                                    ->toolbarButtons([
                                        'attachFiles',
                                        'blockquote',
                                        'bold',
                                        'bulletList',
                                        'codeBlock',
                                        'h2',
                                        'h3',
                                        'italic',
                                        'link',
                                        'orderedList',
                                        'redo',
                                        'strike',
                                        'underline',
                                        'undo',
                                    ]),
                            ])
                            ->collapsed()
                            ->cloneable()
                            ->collapsible()
                            ->deleteAction(
                                fn (Forms\Components\Actions\Action $action) => $action->requiresConfirmation()
                            )
                            ->itemLabel(fn (array $state): ?string => $state['judul'] ?? 'New Material')
                            ->addActionLabel('Add New Material')
                            ->minItems(1)
                            ->maxItems(50)
                            ->grid(1)
                            ->orderColumn('urutan')
                    ])
            ]);
    }

    public static function table(FilamentTable $table): FilamentTable
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('courseDescription.title')
                    ->label('Course')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('course_title')
                    ->label('Course Title')
                    ->searchable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('materials_count')
                    ->label('Materials Count')
                    ->getStateUsing(function ($record) {
                        $materials = is_string($record->materials)
                            ? json_decode($record->materials, true)
                            : $record->materials;
                        return is_array($materials) ? count($materials) : 0;
                    })
                    ->alignCenter()
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('course_description_id')
                    ->label('Course')
                    ->options(CourseDescription::pluck('title', 'id'))
                    ->searchable(),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCourseContents::route('/'),
            'create' => Pages\CreateCourseContent::route('/create'),
            'edit' => Pages\EditCourseContent::route('/{record}/edit'),
            'view' => Pages\ViewCourseContent::route('/{record}'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'primary';
    }
}
