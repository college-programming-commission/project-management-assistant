<?php

namespace Alison\ProjectManagementAssistant\Filament\Resources;

use Alison\ProjectManagementAssistant\Filament\Resources\ProjectResource\Pages;
use Alison\ProjectManagementAssistant\Filament\Resources\ProjectResource\RelationManagers;
use Alison\ProjectManagementAssistant\Models\Project;
use Alison\ProjectManagementAssistant\Models\Supervisor;
use Alison\ProjectManagementAssistant\Models\User;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-document-text';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Управління проектами';
    }

    public static function getNavigationSort(): int
    {
        return 4;
    }

    public static function getModelLabel(): string
    {
        return 'Проект';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Проекти';
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

                TextColumn::make('event.name')
                    ->label('Подія')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('supervisor.user.full_name')
                    ->label('Керівник')
                    ->sortable()
                    ->getStateUsing(fn ($record) => $record->supervisor?->user?->full_name),

                TextColumn::make('assignedTo.full_name')
                    ->label('Призначено')
                    ->sortable()
                    ->getStateUsing(fn ($record) => $record->assignedTo?->full_name),

                TextColumn::make('technologies.name')
                    ->label('Технології')
                    ->badge()
                    ->searchable(),

                TextColumn::make('offers_count')
                    ->label('Кількість заявок')
                    ->counts('offers')
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
                SelectFilter::make('event')
                    ->label('Подія')
                    ->relationship('event', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('supervisor')
                    ->label('Керівник')
                    ->relationship('supervisor.user', 'id')
                    ->getOptionLabelFromRecordUsing(fn (User $record) => $record->full_name)
                    ->searchable()
                    ->preload(),

                SelectFilter::make('assigned_to')
                    ->label('Призначено')
                    ->relationship('assignedTo', 'id')
                    ->getOptionLabelFromRecordUsing(fn (User $record) => $record->full_name)
                    ->searchable()
                    ->preload(),

                SelectFilter::make('technologies')
                    ->label('Технологія')
                    ->relationship('technologies', 'name')
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
            RelationManagers\TechnologiesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
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
                            ->maxLength(248)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $state, Set $set) => $set('slug', Str::slug($state))),

                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(128)
                            ->unique(Project::class, 'slug', ignoreRecord: true),

                        Select::make('event_id')
                            ->label('Подія')
                            ->relationship('event', 'name')
                            ->searchable()
                            ->preload(),

                        Select::make('supervisor_id')
                            ->label('Керівник')
                            ->relationship('supervisor', 'id')
                            ->getOptionLabelFromRecordUsing(fn (Supervisor $record) => $record->user->full_name)
                            ->searchable()
                            ->preload(),

                        Select::make('assigned_to')
                            ->label('Призначено')
                            ->relationship('assignedTo', 'id')
                            ->getOptionLabelFromRecordUsing(fn (User $record) => $record->full_name)
                            ->searchable()
                            ->preload(),
                    ])
                    ->columns(2),

                Section::make('Додаткова інформація')
                    ->schema([
                        Textarea::make('appendix')
                            ->label('Додаток')
                            ->maxLength(512),

                        MarkdownEditor::make('body')
                            ->label('Опис')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Технології')
                    ->schema([
                        CheckboxList::make('technologies')
                            ->label('Технології')
                            ->relationship('technologies', 'name')
                            ->searchable()
                            ->columnSpanFull(),
                    ]),
            ])->columns(1);
    }
}
