<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\PdfService;
use App\Services\CalendarService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
        public function register(): void
        {
            $this->app->singleton(PdfService::class, function ($app) {
            return new PdfService();
        });

        // Registrar CalendarService
            $this->app->singleton(CalendarService::class, function ($app) {
                return new CalendarService();
            });
        }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
