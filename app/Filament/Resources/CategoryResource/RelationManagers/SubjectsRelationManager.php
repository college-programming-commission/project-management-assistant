<?php

namespace Alison\ProjectManagementAssistant\Filament\Resources\CategoryResource\RelationManagers;

use Alison\ProjectManagementAssistant\Models\Subject;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class SubjectsRelationManager extends RelationManager
{
    protected static string $relationship = 'subjects';

    protected static ?string $title = 'Предмети';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([TextInput::make('name')
                ->label('Назва')
                ->required()
                ->maxLength(128)
                ->live(onBlur: true)
                ->afterStateUpdated(fn (string $state, Set $set) => $set('slug', Str::slug($state))),

                TextInput::make('slug')
                    ->label('Slug')
                    ->required()
                    ->maxLength(72)
                    ->unique(Subject::class, 'slug', ignoreRecord: true),

                Select::make('course_number')
                    ->label('Курс')
                    ->options([1 => '1 курс',
                        2 => '2 курс',
                        3 => '3 курс',
                        4 => '4 курс', ])
                    ->required(),

                MarkdownEditor::make('description')
                    ->label('Опис')
                    ->maxLength(65535), ]);
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

                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('course_number')
                    ->label('Курс')
                    ->formatStateUsing(fn (int $state): string => "{$state} курс")
                    ->sortable(),

                ImageColumn::make('image')
                    ->disk(env('FILAMENT_FILESYSTEM_DISK', 's3'))
                    ->label('Зображення')
                    ->circular(),
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
