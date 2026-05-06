<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\PdfService;
use App\Services\CalendarService;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Models\Booking;
use App\Observers\BookingObserver;

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
        Media::created(function ($media) {
            if (PHP_OS_FAMILY !== 'Windows') {
                @chmod(storage_path('app/public/' . $media->getPath()), 0755);
            }
        });

        // Registrar el observer para Booking
        Booking::observe(BookingObserver::class);
    }
}