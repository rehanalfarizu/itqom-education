<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CourseResource\Pages;
use App\Models\Course;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;

class CourseResource extends Resource
{
    protected static ?string $model = Course::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationLabel = 'Course Bridge Data';

    protected static ?string $modelLabel = 'Course Bridge';

    protected static ?string $pluralModelLabel = 'Course Bridge Data';

    protected static ?string $navigationGroup = 'Course Management';

    protected static ?int $navigationSort = 2;

    // Read-only resource - no create/edit allowed
    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->disabled(),
                Forms\Components\TextInput::make('instructor')
                    ->disabled(),
                Forms\Components\TextInput::make('price')
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('courseDescription.title')
                    ->label('Source Course Description')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->color('primary'),

                TextColumn::make('title')
                    ->label('Bridge Title')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('instructor')
                    ->label('Instructor')
                    ->searchable(),

                TextColumn::make('category')
                    ->label('Category')
                    ->badge()
                    ->color('success'),

                TextColumn::make('price')
                    ->label('Final Price')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('original_price')
                    ->label('Original Price')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('userCourses_count')
                    ->label('Enrollments')
                    ->counts('userCourses')
                    ->alignCenter()
                    ->badge()
                    ->color('info'),

                TextColumn::make('created_at')
                    ->label('Auto-Created')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Last Sync')
                    ->dateTime()
                    ->sortable()
                    ->since(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                // No bulk actions for read-only data
            ])
            ->emptyStateHeading('No Course Bridge Data')
            ->emptyStateDescription('Course bridge data is automatically created when you add Course Descriptions.')
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCourses::route('/'),
            'view' => Pages\ViewCourse::route('/{record}'),
        ];
    }
}
