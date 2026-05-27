<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Jobs\SendBookingReminder;
use App\Models\Booking;
use App\Jobs\UpdateCheckedOutBookings;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('geocode:addresses')->daily();

Schedule::call(function () {
    Booking::with(['guest', 'accommodation'])
        ->where('status', 'confirmed')
        ->whereDate('check_in', now()->addDays(2))
        ->each(function ($booking) {
            SendBookingReminder::dispatch($booking);
        });
})->daily();

Schedule::job(new UpdateCheckedOutBookings)->daily();


