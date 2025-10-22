<?php

namespace Alison\ProjectManagementAssistant\Filament\Resources;

use Alison\ProjectManagementAssistant\Filament\Resources\SubeventResource\Pages;
use Alison\ProjectManagementAssistant\Models\Subevent;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SubeventResource extends Resource
{
    protected static ?string $model = Subevent::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-calendar-days';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Управління подіями';
    }

    public static function getNavigationSort(): int
    {
        return 2;
    }

    public static function getModelLabel(): string
    {
        return 'Підподія';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Підподії';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                return $query->with(['event', 'dependsOn']);
            })
            ->columns([
                TextColumn::make('name')
                    ->label('Назва')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('event.name')
                    ->label('Подія')
                    ->sortable(),

                TextColumn::make('dependsOn.name')
                    ->label('Залежить від')
                    ->sortable()
                    ->placeholder('Немає залежності'),

                TextColumn::make('start_date')
                    ->label('Дата початку')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),

                TextColumn::make('end_date')
                    ->label('Дата завершення')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),

                ColorColumn::make('bg_color')
                    ->label('Колір фону'),

                TextColumn::make('description')
                    ->label('Опис')
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }

                        return $state;
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

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

                SelectFilter::make('depends_on')
                    ->label('Залежить від')
                    ->relationship('dependsOn', 'name')
                    ->searchable()
                    ->preload(),

                Filter::make('start_date')
                    ->label('Дата початку')
                    ->schema([
                        DatePicker::make('start_from')
                            ->label('Від'),
                        DatePicker::make('start_until')
                            ->label('До'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['start_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('start_date', '>=', $date),
                            )
                            ->when(
                                $data['start_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('start_date', '<=', $date),
                            );
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ])
            ->defaultSort('start_date', 'asc');
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
            'index' => Pages\ListSubevents::route('/'),
            'create' => Pages\CreateSubevent::route('/create'),
            'edit' => Pages\EditSubevent::route('/{record}/edit'),
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

                        TextInput::make('name')
                            ->label('Назва')
                            ->required()
                            ->maxLength(255),

                        Textarea::make('description')
                            ->label('Опис')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Дати та час')
                    ->schema([
                        DateTimePicker::make('start_date')
                            ->label('Дата початку')
                            ->required()
                            ->native(false),

                        DateTimePicker::make('end_date')
                            ->label('Дата завершення')
                            ->required()
                            ->native(false)
                            ->after('start_date'),
                    ])
                    ->columns(2),

                Section::make('Залежності та стилізація')
                    ->schema([
                        Select::make('depends_on')
                            ->label('Залежить від підподії')
                            ->relationship('dependsOn', 'name')
                            ->searchable()
                            ->preload(),

                        ColorPicker::make('bg_color')
                            ->label('Колір фону')
                            ->default('#3b82f6'),

                        ColorPicker::make('fg_color')
                            ->label('Колір тексту')
                            ->default('#ffffff'),
                    ])
                    ->columns(3),
            ])->columns(1);
    }
}
