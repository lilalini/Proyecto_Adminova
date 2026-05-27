<?php

namespace App\Jobs;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class UpdateCheckedOutBookings implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        $count = Booking::where('status', 'confirmed')
            ->where('check_out', '<', Carbon::today())
            ->update(['status' => 'checked_out']);

        \Log::info("Actualizados {$count} bookings a checked_out");
    }
}