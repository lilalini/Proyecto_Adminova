<?php

namespace App\Jobs;

use App\Mail\BookingReminder;
use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendBookingReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Booking $booking)
    {
        //
    }

    public function handle(): void
    {
        Mail::to($this->booking->guest->email)
            ->send(new BookingReminder($this->booking));
    }
}