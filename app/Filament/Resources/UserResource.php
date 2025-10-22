<?php

namespace Alison\ProjectManagementAssistant\Filament\Resources;

use Alison\ProjectManagementAssistant\Filament\Resources\UserResource\Pages;
use Alison\ProjectManagementAssistant\Models\User;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-users';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Адміністрування';
    }

    public static function getNavigationSort(): int
    {
        return 1;
    }

    public static function getModelLabel(): string
    {
        return 'Користувач';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Користувачі';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name')
                    ->label('Повне ім\'я')
                    ->searchable(['first_name', 'last_name', 'middle_name'])
                    ->sortable(['last_name', 'first_name'])
                    ->getStateUsing(fn ($record) => $record->full_name),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('first_name')
                    ->label('Імя')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('last_name')
                    ->label('Прізвище')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('course_number')
                    ->label('Курс')
                    ->formatStateUsing(fn (?int $state): ?string => $state ? "{$state} курс" : null),

                TextColumn::make('roles.name')
                    ->label('Ролі')
                    ->badge()
                    ->searchable(),

                ImageColumn::make('avatar')
                    ->disk(env('FILAMENT_FILESYSTEM_DISK', 's3'))
                    ->label('Аватар')
                    ->circular(),

                TextColumn::make('email_verified_at')
                    ->label('Верифіковано')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
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
                SelectFilter::make('roles')
                    ->label('Роль')
                    ->relationship('roles', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('course_number')
                    ->label('Курс')
                    ->options([
                        1 => '1 курс',
                        2 => '2 курс',
                        3 => '3 курс',
                        4 => '4 курс',
                    ]),

                Filter::make('verified')
                    ->label('Верифіковані')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('email_verified_at')),

                Filter::make('unverified')
                    ->label('Неверифіковані')
                    ->query(fn (Builder $query): Builder => $query->whereNull('email_verified_at')),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Основна інформація')
                    ->schema([
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        TextInput::make('password')
                            ->label('Пароль')
                            ->password()
                            ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->required(fn (string $operation): bool => $operation === 'create'),
                    ])
                    ->columns(2),

                Section::make('Персональні дані')
                    ->schema([
                        TextInput::make('first_name')
                            ->label('Імя')
                            ->required()
                            ->maxLength(50),

                        TextInput::make('last_name')
                            ->label('Прізвище')
                            ->required()
                            ->maxLength(50),

                        TextInput::make('middle_name')
                            ->label('По батькові')
                            ->maxLength(50),

                        Select::make('course_number')
                            ->label('Курс')
                            ->options([
                                1 => '1 курс',
                                2 => '2 курс',
                                3 => '3 курс',
                                4 => '4 курс',
                            ]),
                    ])
                    ->columns(2),

                Section::make('Додаткова інформація')
                    ->schema([
                        Textarea::make('description')
                            ->label('Опис')
                            ->maxLength(512)
                            ->columnSpanFull(),

                        FileUpload::make('avatar')
                            ->disk(env('FILAMENT_FILESYSTEM_DISK', 's3'))
                            ->label('Аватар')
                            ->image()
                            ->directory('avatars')
                            ->maxSize(1024)
                            ->columnSpanFull(),
                    ]),

                Section::make('Ролі')
                    ->schema([
                        CheckboxList::make('roles')
                            ->label('Ролі')
                            ->relationship('roles', 'name')
                            ->searchable()
                            ->columnSpanFull(),
                    ]),
            ])->columns(1);
    }
}
