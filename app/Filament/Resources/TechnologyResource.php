<?php

namespace Alison\ProjectManagementAssistant\Filament\Resources;

use Alison\ProjectManagementAssistant\Filament\Resources\TechnologyResource\Pages;
use Alison\ProjectManagementAssistant\Filament\Resources\TechnologyResource\RelationManagers;
use Alison\ProjectManagementAssistant\Models\Technology;
use Exception;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class TechnologyResource extends Resource
{
    protected static ?string $model = Technology::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-cpu-chip';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Управління проектами';
    }

    public static function getNavigationSort(): int
    {
        return 5;
    }

    public static function getModelLabel(): string
    {
        return 'Технологія';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Технології';
    }

    /**
     * @throws Exception
     */
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

                TextColumn::make('link')
                    ->label('Посилання')
                    ->url(fn (Technology $record): string => $record->link)
                    ->openUrlInNewTab(),

                ImageColumn::make('image')
                    ->disk(env('FILAMENT_FILESYSTEM_DISK', 's3'))
                    ->label('Зображення')
                    ->circular(),

                TextColumn::make('projects_count')
                    ->label('Кількість проектів')
                    ->counts('projects')
                    ->sortable(),

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
                Filter::make('with_link')
                    ->label('З посиланням')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('link')->where('link', '!=', '')),

                Filter::make('with_projects')
                    ->label('З проектами')
                    ->query(fn (Builder $query): Builder => $query->has('projects')),
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
            RelationManagers\ProjectsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTechnologies::route('/'),
            'create' => Pages\CreateTechnology::route('/create'),
            'edit' => Pages\EditTechnology::route('/{record}/edit'),
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
                            ->maxLength(128)
                            ->unique(Technology::class, 'slug', ignoreRecord: true),

                        TextInput::make('link')
                            ->label('Посилання')
                            ->url()
                            ->maxLength(2048),
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
                            ->visibility('public')
                            ->label('Зображення')
                            ->image()
                            ->directory('technologies')
                            ->maxSize(2048)
                            ->columnSpanFull(),
                    ]),
            ])->columns(1);
    }
}
