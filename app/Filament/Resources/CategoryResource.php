<?php

namespace Alison\ProjectManagementAssistant\Filament\Resources;

use Alison\ProjectManagementAssistant\Filament\Resources\CategoryResource\Pages;
use Alison\ProjectManagementAssistant\Filament\Resources\CategoryResource\RelationManagers;
use Alison\ProjectManagementAssistant\Models\Category;
use Exception;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-tag';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Управління навчанням';
    }

    public static function getNavigationSort(): int
    {
        return 2;
    }

    public static function getModelLabel(): string
    {
        return 'Категорія';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Категорії';
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

                TextColumn::make('course_number')
                    ->label('Курс')
                    ->formatStateUsing(fn (int $state): string => "{$state} курс")
                    ->sortable(),

                TextColumn::make('freezing_period')
                    ->label('Період заморожування')
                    ->formatStateUsing(fn (int $state): string => "{$state} днів")
                    ->sortable(),

                TextColumn::make('period')
                    ->label('Період')
                    ->formatStateUsing(fn (int $state): string => "{$state} днів")
                    ->sortable(),

                TextColumn::make('subjects.name')
                    ->label('Предмети')
                    ->badge()
                    ->searchable(),

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

                Filter::make('freezing_period')
                    ->label('Період заморожування')
                    ->schema([
                        TextInput::make('min_freezing_period')
                            ->label('Мінімум днів')
                            ->numeric(),
                        TextInput::make('max_freezing_period')
                            ->label('Максимум днів')
                            ->numeric(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['min_freezing_period'],
                                fn (Builder $query, $min): Builder => $query->where('freezing_period', '>=', $min),
                            )
                            ->when(
                                $data['max_freezing_period'],
                                fn (Builder $query, $max): Builder => $query->where('freezing_period', '<=', $max),
                            );
                    }),

                Filter::make('period')
                    ->label('Період')
                    ->schema([
                        TextInput::make('min_period')
                            ->label('Мінімум днів')
                            ->numeric(),
                        TextInput::make('max_period')
                            ->label('Максимум днів')
                            ->numeric(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['min_period'],
                                fn (Builder $query, $min): Builder => $query->where('period', '>=', $min),
                            )
                            ->when(
                                $data['max_period'],
                                fn (Builder $query, $max): Builder => $query->where('period', '<=', $max),
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
            RelationManagers\SubjectsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
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
                            ->maxLength(32),

                        Select::make('course_number')
                            ->label('Курс')
                            ->options([
                                1 => '1 курс',
                                2 => '2 курс',
                                3 => '3 курс',
                                4 => '4 курс',
                            ])
                            ->required(),

                        TextInput::make('freezing_period')
                            ->label('Період заморожування (днів)')
                            ->numeric()
                            ->minValue(1)
                            ->required(),

                        TextInput::make('period')
                            ->label('Період (днів)')
                            ->numeric()
                            ->minValue(1)
                            ->required(),
                    ])
                    ->columns(2),

                Section::make('Додаткова інформація')
                    ->schema([
                        Repeater::make('attachments')
                            ->label('Додатки')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Назва')
                                    ->required(),

                                TextInput::make('url')
                                    ->label('URL')
                                    ->url()
                                    ->required(),
                            ])
                            ->columns(2),
                    ]),

                Section::make('Предмети')
                    ->schema([
                        CheckboxList::make('subjects')
                            ->label('Предмети')
                            ->relationship('subjects', 'name')
                            ->searchable()
                            ->columnSpanFull(),
                    ]),
            ])->columns(1);
    }
}
