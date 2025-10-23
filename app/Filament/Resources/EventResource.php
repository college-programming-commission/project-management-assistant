<?php

namespace Alison\ProjectManagementAssistant\Filament\Resources;

use Alison\ProjectManagementAssistant\Filament\Resources\EventResource\Pages;
use Alison\ProjectManagementAssistant\Filament\Resources\EventResource\RelationManagers;
use Alison\ProjectManagementAssistant\Models\Event;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-calendar';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Управління подіями';
    }

    public static function getNavigationSort(): int
    {
        return 1;
    }

    public static function getModelLabel(): string
    {
        return 'Подія';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Події';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Назва')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('category.name')
                    ->label('Категорія')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('start_date')
                    ->label('Початок')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),

                TextColumn::make('end_date')
                    ->label('Завершення')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(),

                ColorColumn::make('bg_color')
                    ->label('Колір фону'),

                ColorColumn::make('fg_color')
                    ->label('Колір тексту')
                    ->toggleable(isToggledHiddenByDefault: true),

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
                SelectFilter::make('category_id')
                    ->label('Категорія')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),

                Filter::make('active')
                    ->label('Активні')
                    ->query(fn (Builder $query): Builder => $query->where('end_date', '>=', now())),

                Filter::make('past')
                    ->label('Минулі')
                    ->query(fn (Builder $query): Builder => $query->where('end_date', '<', now())),

                Filter::make('upcoming')
                    ->label('Майбутні')
                    ->query(fn (Builder $query): Builder => $query->where('start_date', '>', now())),

                Filter::make('date_range')
                    ->label('Діапазон дат')
                    ->schema([
                        DatePicker::make('start_date')
                            ->label('З')
                            ->default(now()->subMonth()),
                        DatePicker::make('end_date')
                            ->label('По')
                            ->default(now()->addMonth()),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['start_date'],
                                fn (Builder $query, $date): Builder => $query->whereDate('start_date', '>=', $date),
                            )
                            ->when(
                                $data['end_date'],
                                fn (Builder $query, $date): Builder => $query->whereDate('start_date', '<=', $date),
                            );
                    }),
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
            RelationManagers\SupervisorsRelationManager::class,
            RelationManagers\SubeventsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            'edit' => Pages\EditEvent::route('/{record}/edit'),
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
                            ->maxLength(128),

                        Select::make('category_id')
                            ->label('Категорія')
                            ->relationship('category', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),

                        DateTimePicker::make('start_date')
                            ->label('Дата початку')
                            ->required(),

                        DateTimePicker::make('end_date')
                            ->label('Дата завершення')
                            ->after('start_date'),
                    ])
                    ->columns(2),

                Section::make('Додаткова інформація')
                    ->schema([
                        MarkdownEditor::make('description')
                            ->label('Опис')
                            ->maxLength(512)
                            ->columnSpanFull(),

                        ColorPicker::make('bg_color')
                            ->label('Колір фону')
                            ->rgba(),

                        ColorPicker::make('fg_color')
                            ->label('Колір тексту')
                            ->rgba(),

                        FileUpload::make('image')
                            ->disk(env('FILAMENT_FILESYSTEM_DISK', 's3'))
                            ->visibility('public')
                            ->label('Зображення')
                            ->image()
                            ->directory('events')
                            ->maxSize(2048)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ])->columns(1);
    }
}
