<?php

namespace Alison\ProjectManagementAssistant\Filament\Resources;

use Alison\ProjectManagementAssistant\Filament\Resources\SupervisorResource\Pages;
use Alison\ProjectManagementAssistant\Filament\Resources\SupervisorResource\RelationManagers;
use Alison\ProjectManagementAssistant\Models\Supervisor;
use Alison\ProjectManagementAssistant\Models\User;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SupervisorResource extends Resource
{
    protected static ?string $model = Supervisor::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-academic-cap';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Управління проектами';
    }

    public static function getNavigationSort(): int
    {
        return 2;
    }

    public static function getModelLabel(): string
    {
        return 'Керівник';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Керівники';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('event.name')
                    ->label('Подія')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.full_name')
                    ->label('Користувач')
                    ->sortable()
                    ->getStateUsing(fn ($record) => $record->user?->full_name),

                TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('slot_count')
                    ->label('Кількість місць')
                    ->sortable(),

                TextColumn::make('note')
                    ->label('Примітка')
                    ->limit(50),

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
                SelectFilter::make('event')
                    ->label('Подія')
                    ->relationship('event', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('user')
                    ->label('Користувач')
                    ->relationship('user', 'id')
                    ->getOptionLabelFromRecordUsing(fn (User $record) => $record->full_name)
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('active_event')
                    ->label('Активні події')
                    ->query(fn (Builder $query): Builder => $query->whereHas('event', function ($q) {
                        $q->where('end_date', '>=', now());
                    })),

                Filter::make('slot_count')
                    ->label('Кількість місць')
                    ->schema([
                        TextInput::make('min_slot_count')
                            ->label('Мінімум місць')
                            ->numeric(),
                        TextInput::make('max_slot_count')
                            ->label('Максимум місць')
                            ->numeric(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['min_slot_count'],
                                fn (Builder $query, $min): Builder => $query->where('slot_count', '>=', $min),
                            )
                            ->when(
                                $data['max_slot_count'],
                                fn (Builder $query, $max): Builder => $query->where('slot_count', '<=', $max),
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
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSupervisors::route('/'),
            'create' => Pages\CreateSupervisor::route('/create'),
            'edit' => Pages\EditSupervisor::route('/{record}/edit'),
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Основна інформація')
                    ->schema([
                        Select::make('event_id')
                            ->label('Подія')
                            ->relationship('event', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('user_id')
                            ->label('Користувач')
                            ->relationship('user', 'id')
                            ->getOptionLabelFromRecordUsing(fn (User $record) => $record->full_name)
                            ->required()
                            ->searchable()
                            ->preload(),

                        TextInput::make('slot_count')
                            ->label('Кількість місць')
                            ->numeric()
                            ->minValue(1)
                            ->required(),
                    ])
                    ->columns(2),

                Section::make('Додаткова інформація')
                    ->schema([
                        MarkdownEditor::make('note')
                            ->label('Примітка')
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ]),
            ])->columns(1);
    }
}
