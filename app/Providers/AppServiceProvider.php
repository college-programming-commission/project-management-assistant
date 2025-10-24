<?php

namespace Alison\ProjectManagementAssistant\Providers;

use Alison\ProjectManagementAssistant\Services\MarkdownService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(MarkdownService::class, function ($app) {
            return new MarkdownService;
        });

        $this->app->singleton(\Alison\ProjectManagementAssistant\Services\CacheService::class, function ($app) {
            return new \Alison\ProjectManagementAssistant\Services\CacheService;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::unguard();
        // Model::shouldBeStrict();

        Paginator::useTailwind();

        if ($this->app->environment('production')) {
            URL::forceScheme('https');
            URL::forceRootUrl(config('app.url'));
        } else {
            URL::forceScheme('http');
            URL::forceRootUrl(config('app.url'));
        }
    }
}
