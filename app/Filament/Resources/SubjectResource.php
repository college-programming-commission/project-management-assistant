<?php

namespace Alison\ProjectManagementAssistant\Filament\Resources;

use Alison\ProjectManagementAssistant\Filament\Resources\SubjectResource\Pages;
use Alison\ProjectManagementAssistant\Filament\Resources\SubjectResource\RelationManagers;
use Alison\ProjectManagementAssistant\Models\Subject;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class SubjectResource extends Resource
{
    protected static ?string $model = Subject::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-academic-cap';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Управління навчанням';
    }

    public static function getNavigationSort(): int
    {
        return 3;
    }

    public static function getModelLabel(): string
    {
        return 'Предмет';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Предмети';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Назва')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('course_number')
                    ->label('Курс')
                    ->formatStateUsing(fn (int $state): string => "{$state} курс")
                    ->sortable(),

                TextColumn::make('categories.name')
                    ->label('Категорії')
                    ->badge()
                    ->searchable(),

                ImageColumn::make('image')
                    ->label('Зображення')
                    ->circular(),

                TextColumn::make('created_at')
                    ->label('Створено')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Оновлено')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('course_number')
                    ->label('Курс')
                    ->options([
                        1 => '1 курс',
                        2 => '2 курс',
                        3 => '3 курс',
                        4 => '4 курс',
                    ]),

                SelectFilter::make('categories')
                    ->label('Категорія')
                    ->relationship('categories', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\CategoriesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubjects::route('/'),
            'create' => Pages\CreateSubject::route('/create'),
            'edit' => Pages\EditSubject::route('/{record}/edit'),
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Основна інформація')
                    ->schema([
                        TextInput::make('name')
                            ->label('Назва')
                            ->required()
                            ->maxLength(128)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $state, Set $set) => $set('slug', Str::slug($state))),

                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(72)
                            ->unique(Subject::class, 'slug', ignoreRecord: true),

                        Select::make('course_number')
                            ->label('Курс')
                            ->options([
                                1 => '1 курс',
                                2 => '2 курс',
                                3 => '3 курс',
                                4 => '4 курс',
                            ])
                            ->required(),
                    ])
                    ->columns(2),

                Section::make('Додаткова інформація')
                    ->schema([
                        MarkdownEditor::make('description')
                            ->label('Опис')
                            ->maxLength(65535)
                            ->columnSpanFull(),

                        FileUpload::make('image')
                            ->disk(env('FILAMENT_FILESYSTEM_DISK', 's3'))
                            ->label('Зображення')
                            ->image()
                            ->directory('subjects')
                            ->maxSize(2048)
                            ->columnSpanFull(),
                    ]),

                Section::make('Категорії')
                    ->schema([
                        CheckboxList::make('categories')
                            ->label('Категорії')
                            ->relationship('categories', 'name')
                            ->searchable()
                            ->columnSpanFull(),
                    ]),
            ])->columns(1);
    }
}
