<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Filament Path
    |--------------------------------------------------------------------------
    |
    | The default is `admin` but you can change it to whatever you want.
    |
    */

    'path' => env('FILAMENT_PATH', 'admin'),

    /*
    |--------------------------------------------------------------------------
    | Filament Core Path
    |--------------------------------------------------------------------------
    |
    | The default is `filament` but you can change it to whatever you want.
    |
    */

    'core_path' => env('FILAMENT_CORE_PATH', 'filament'),

    /*
    |--------------------------------------------------------------------------
    | Filament Domain
    |--------------------------------------------------------------------------
    |
    | You may change the domain where Filament should be active.
    | If the domain is empty, all domains will be valid.
    |
    */

    'domain' => env('FILAMENT_DOMAIN'),

    /*
    |--------------------------------------------------------------------------
    | Homepage URL
    |--------------------------------------------------------------------------
    |
    | This is the URL that will be used for the homepage of the Filament admin
    | panel, and for the button on the "404" and "403" pages.
    |
    */

    'home_url' => '/',

    /*
    |--------------------------------------------------------------------------
    | Auth
    |--------------------------------------------------------------------------
    */

    'auth' => [
        'guard' => env('FILAMENT_AUTH_GUARD', 'web'),
        'pages' => [
            'login' => \Filament\Http\Livewire\Auth\Login::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Auto detect icons
    |--------------------------------------------------------------------------
    |
    | Automatically detect icons from your project.
    |
    */

    'icons' => [
        'aliases' => [
            // Define your icon aliases here
        ],
        'default' => 'heroicon',
    ],

    /*
    |--------------------------------------------------------------------------
    | Database notifications
    |--------------------------------------------------------------------------
    */

    'database_notifications' => [
        'enabled' => env('FILAMENT_DATABASE_NOTIFICATIONS_ENABLED', true),
        'polling_interval' => env('FILAMENT_DATABASE_NOTIFICATIONS_POLLING_INTERVAL', '30s'),
        'database_connection' => env('FILAMENT_DATABASE_NOTIFICATIONS_CONNECTION'),
        'database_table' => env('FILAMENT_DATABASE_NOTIFICATIONS_TABLE', 'notifications'),
        'model' => env('FILAMENT_DATABASE_NOTIFICATIONS_MODEL', \Filament\Notifications\Models\DatabaseNotification::class),
    ],

    /*
    |--------------------------------------------------------------------------
    | Broadcasting
    |--------------------------------------------------------------------------
    */

    'broadcasting' => [
        'enabled' => env('FILAMENT_BROADCASTING_ENABLED', true),
        'echo' => [
            'broadcaster' => env('FILAMENT_BROADCASTING_BROADCASTER', 'pusher'),
            'key' => env('FILAMENT_BROADCASTING_KEY'),
            'cluster' => env('FILAMENT_BROADCASTING_CLUSTER', 'mt1'),
            'wsHost' => env('FILAMENT_BROADCASTING_HOST'),
            'wsPort' => env('FILAMENT_BROADCASTING_PORT', 6001),
            'wssPort' => env('FILAMENT_BROADCASTING_PORT', 443),
            'forceTLS' => env('FILAMENT_BROADCASTING_SCHEME', 'https') === 'https',
            'enableClientSideInterceptors' => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Dark mode
    |--------------------------------------------------------------------------
    */

    'dark_mode' => true,

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    */

    'default_filesystem_disk' => env('FILAMENT_FILESYSTEM_DRIVER', 'public'),

    /*
    |--------------------------------------------------------------------------
    | Default Avatar Provider
    |--------------------------------------------------------------------------
    */

    'default_avatar_provider' => \Filament\AvatarProviders\UiAvatarsProvider::class,

    /*
    |--------------------------------------------------------------------------
    | Default File Provider
    |--------------------------------------------------------------------------
    */

    'default_file_provider' => \Filament\FileProviders\Local::class,

    /*
    |--------------------------------------------------------------------------
    | Default Rich Editor Provider
    |--------------------------------------------------------------------------
    */

    'default_rich_editor_provider' => \Filament\RichEditorProviders\TinyMce::class,

    /*
    |--------------------------------------------------------------------------
    | Default Password Reset Provider
    |--------------------------------------------------------------------------
    */

    'default_password_reset_provider' => \Filament\PasswordResetProviders\Database::class,

    /*
    |--------------------------------------------------------------------------
    | Health Panel
    |--------------------------------------------------------------------------
    */

    'panels' => [
        'default' => [
            'database_notifications' => [
                'enabled' => true,
                'polling_interval' => '30s',
            ],
            'resources' => [
                'registration' => true,
                'discover' => true,
                'paths' => [
                    app_path('Filament/Resources'),
                ],
            ],
            'pages' => [
                'registration' => true,
                'discover' => true,
                'paths' => [
                    app_path('Filament/Pages'),
                ],
            ],
            'widgets' => [
                'registration' => true,
                'discover' => true,
                'paths' => [
                    app_path('Filament/Widgets'),
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Global Search
    |--------------------------------------------------------------------------
    */

    'global_search' => [
        'enabled' => true,
        'timeout' => 5,
    ],
];
