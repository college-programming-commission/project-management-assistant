<?php

namespace Alison\ProjectManagementAssistant\Filament\Resources\ProjectResource\RelationManagers;

use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TechnologiesRelationManager extends RelationManager
{
    protected static string $relationship = 'technologies';

    protected static ?string $title = 'Технології';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('technology_id')
                    ->label('Технологія')
                    ->relationship('technology', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('technology.name')
            ->columns([
                TextColumn::make('technology.name')
                    ->label('Назва')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('technology.link')
                    ->label('Посилання')
                    ->url(fn ($record) => $record->technology->link)
                    ->openUrlInNewTab(),
            ])
            ->filters([
                SelectFilter::make('technology')
                    ->label('Технологія')
                    ->relationship('technology', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->headerActions([
                AttachAction::make()
                    ->preloadRecordSelect(),
            ])
            ->recordActions([
                DetachAction::make(),
            ])
            ->toolbarActions([
                DetachBulkAction::make(),
            ]);
    }
}
