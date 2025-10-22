<?php

namespace Alison\ProjectManagementAssistant\Filament\Resources\EventResource\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SubeventsRelationManager extends RelationManager
{
    protected static string $relationship = 'subevents';

    protected static ?string $title = 'Підподії';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Назва')
                    ->required()
                    ->maxLength(255),

                DateTimePicker::make('start_date')
                    ->label('Дата початку'),

                DateTimePicker::make('end_date')
                    ->label('Дата завершення'),

                MarkdownEditor::make('description')
                    ->label('Опис')
                    ->maxLength(65535),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Назва')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('start_date')
                    ->label('Дата початку')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),

                TextColumn::make('end_date')
                    ->label('Дата завершення')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Filter::make('active')
                    ->label('Активні')
                    ->query(fn (Builder $query) => $query->where('end_date', '>=', now())),

                Filter::make('past')
                    ->label('Завершені')
                    ->query(fn (Builder $query) => $query->where('end_date', '<', now())),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }
}
