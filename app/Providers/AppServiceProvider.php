<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(\App\Services\ProjectPasswordService::class);
        $this->app->singleton(\App\Services\DynamicWidgetRenderer::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (!app()->runningInConsole()) {
            header('X-Powered-By: VGTCRM');
        }

        // Configure Livewire to use project.web middleware
        Livewire::setUpdateRoute(function ($handle) {
            return Route::post('/livewire/update', $handle)
                ->middleware('project.web');
        });

        // Register Blade directives for widgets
        Blade::directive('widgetArea', function ($expression) {
            return "<?php echo app(\App\Services\DynamicWidgetRenderer::class)->renderArea($expression); ?>";
        });

        Blade::directive('widget', function ($expression) {
            return "<?php echo app(\App\Services\DynamicWidgetRenderer::class)->renderById($expression); ?>";
        });
    }
}

