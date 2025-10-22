<?php

namespace Alison\ProjectManagementAssistant\Filament\Pages;

use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Cache;

class ColorSettings extends Page
{
    protected static ?string $title = 'Налаштування кольорів';

    protected static ?string $navigationLabel = 'Кольори';

    protected static ?int $navigationSort = 100;

    public ?array $data = [];

    protected string $view = 'filament.pages.color-settings';

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-paint-brush';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Налаштування';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([Select::make('primary_color')
                ->label('Основний колір адмін панелі')
                ->options(['slate' => 'Сланцевий',
                    'gray' => 'Сірий',
                    'zinc' => 'Цинковий',
                    'neutral' => 'Нейтральний',
                    'stone' => 'Кам\'яний',
                    'red' => 'Червоний',
                    'orange' => 'Помаранчевий',
                    'amber' => 'Бурштиновий',
                    'yellow' => 'Жовтий',
                    'lime' => 'Лаймовий',
                    'green' => 'Зелений',
                    'emerald' => 'Смарагдовий',
                    'teal' => 'Бірюзовий',
                    'cyan' => 'Блакитний',
                    'sky' => 'Небесний',
                    'blue' => 'Синій',
                    'indigo' => 'Індиго',
                    'violet' => 'Фіолетовий',
                    'purple' => 'Пурпурний',
                    'fuchsia' => 'Фуксія',
                    'pink' => 'Рожевий',
                    'rose' => 'Троянда',])
                ->default('amber')
                ->required()
                ->helperText('Перезавантажте сторінку після збереження для застосування змін'),])
            ->statePath('data');
    }

    public function mount(): void
    {
        $this->form->fill([
            'primary_color' => Cache::get('admin_primary_color', 'amber'),
        ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        Cache::put('admin_primary_color', $data['primary_color'], now()->addYear());

        Notification::make()
            ->title('Налаштування збережено')
            ->body('Колірна схема була успішно оновлена. Перезавантажте сторінку для застосування змін.')
            ->success()
            ->send();
    }

    public function resetColors(): void
    {
        Cache::forget('admin_primary_color');

        $this->form->fill([
            'primary_color' => 'amber',
        ]);

        Notification::make()
            ->title('Налаштування скинуто')
            ->body('Колірна схема була скинута до значень за замовчуванням.')
            ->success()
            ->send();
    }
}
