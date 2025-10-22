<?php

namespace Alison\ProjectManagementAssistant\Filament\Resources\EventResource\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SupervisorsRelationManager extends RelationManager
{
    protected static string $relationship = 'supervisors';

    protected static ?string $title = 'Керівники';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->label('Користувач')
                    ->relationship('user', 'id')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->full_name)
                    ->required()
                    ->searchable()
                    ->preload(),

                TextInput::make('slot_count')
                    ->label('Кількість місць')
                    ->numeric()
                    ->minValue(1)
                    ->required(),

                MarkdownEditor::make('note')
                    ->label('Примітка')
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('user.full_name')
            ->columns([
                TextColumn::make('user.full_name')
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
            ])
            ->filters([
                SelectFilter::make('user')
                    ->label('Користувач')
                    ->relationship('user', 'id')
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
