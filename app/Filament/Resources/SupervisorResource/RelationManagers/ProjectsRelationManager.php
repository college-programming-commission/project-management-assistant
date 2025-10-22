<?php

namespace Alison\ProjectManagementAssistant\Filament\Resources\SupervisorResource\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ProjectsRelationManager extends RelationManager
{
    protected static string $relationship = 'projects';

    protected static ?string $title = 'Проекти';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('assigned_to')
                    ->label('Призначено')
                    ->relationship('assignedTo', 'id')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->full_name)
                    ->searchable()
                    ->preload(),

                Textarea::make('appendix')
                    ->label('Додаток')
                    ->maxLength(512),
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

                TextColumn::make('assignedTo.full_name')
                    ->label('Призначено')
                    ->sortable()
                    ->getStateUsing(fn ($record) => $record->assignedTo?->full_name),

                TextColumn::make('offers_count')
                    ->label('Кількість заявок')
                    ->counts('offers')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('assigned_to')
                    ->label('Призначено')
                    ->relationship('assignedTo', 'id')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->full_name)
                    ->searchable()
                    ->preload(),
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
