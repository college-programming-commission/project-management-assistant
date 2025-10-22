<?php

namespace Alison\ProjectManagementAssistant\Providers\Filament;

use Alison\ProjectManagementAssistant\Filament\Pages\ColorSettings;
use Alison\ProjectManagementAssistant\Http\Middleware\CheckAdminAccess;
use Filament\Actions\Action;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->spa(true, true) // Enable SPA mode with prefetching
            ->login()
            ->colors([
                'primary' => $this->getPrimaryColor(),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'Alison\\ProjectManagementAssistant\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'Alison\\ProjectManagementAssistant\\Filament\\Pages')
            ->pages([
                Dashboard::class,
                ColorSettings::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'Alison\\ProjectManagementAssistant\\Filament\\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                CheckAdminAccess::class,
            ])
            ->userMenuItems([
                'profile' => fn (Action $action): Action => $action
                    ->label(fn (): string => auth()->user()->full_name)
                    ->url(fn (): string => route('profile.show'))
                    ->icon('heroicon-m-user-circle'),
                Action::make('dashboard')
                    ->label('Головна сторінка')
                    ->url(fn (): string => route('dashboard'))
                    ->icon('heroicon-m-home')
                    ->openUrlInNewTab(),
                Action::make('colors')
                    ->label('Налаштування кольорів')
                    ->url(fn (): string => ColorSettings::getUrl())
                    ->icon('heroicon-m-paint-brush'),
                Action::make('separator')
                    ->label('---')
                    ->disabled()
                    ->url('#'),
            ]);
    }

    protected function getPrimaryColor(): array
    {
        $colorName = Cache::get('admin_primary_color', 'amber');

        $colors = [
            'slate' => Color::Slate,
            'gray' => Color::Gray,
            'zinc' => Color::Zinc,
            'neutral' => Color::Neutral,
            'stone' => Color::Stone,
            'red' => Color::Red,
            'orange' => Color::Orange,
            'amber' => Color::Amber,
            'yellow' => Color::Yellow,
            'lime' => Color::Lime,
            'green' => Color::Green,
            'emerald' => Color::Emerald,
            'teal' => Color::Teal,
            'cyan' => Color::Cyan,
            'sky' => Color::Sky,
            'blue' => Color::Blue,
            'indigo' => Color::Indigo,
            'violet' => Color::Violet,
            'purple' => Color::Purple,
            'fuchsia' => Color::Fuchsia,
            'pink' => Color::Pink,
            'rose' => Color::Rose,
        ];

        return $colors[$colorName] ?? Color::Amber;
    }
}
