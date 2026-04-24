<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\AvailabilityCalendar;
use Carbon\Carbon;

class CalendarService
{
    /**
     * Verifica si un alojamiento está disponible en un rango de fechas
     */
    public function isAvailable($accommodationId, $checkIn, $checkOut, $excludeBookingId = null): bool
    {
        $query = Booking::where('accommodation_id', $accommodationId)
            ->where('status', '!=', 'cancelled')
            ->where(function($q) use ($checkIn, $checkOut) {
                $q->whereBetween('check_in', [$checkIn, $checkOut])
                  ->orWhereBetween('check_out', [$checkIn, $checkOut])
                  ->orWhere(function($sub) use ($checkIn, $checkOut) {
                      $sub->where('check_in', '<=', $checkIn)
                          ->where('check_out', '>=', $checkOut);
                  });
            });
        
        if ($excludeBookingId) {
            $query->where('id', '!=', $excludeBookingId);
        }
        
        return !$query->exists();
    }

    /**
     * Marca las fechas como ocupadas en el calendario
     */
    public function markAsBooked(Booking $booking): void
    {
        $start = Carbon::parse($booking->check_in);
        $end = Carbon::parse($booking->check_out);
        
        for ($date = $start->copy(); $date < $end; $date->addDay()) {
            AvailabilityCalendar::updateOrCreate(
                [
                    'accommodation_id' => $booking->accommodation_id,
                    'date' => $date->format('Y-m-d')
                ],
                [
                    'status' => 'booked',
                    'user_id' => $booking->guest_id,
                    'price' => $booking->price_per_night
                ]
            );
        }
    }

    /**
     * Libera las fechas del calendario
     */
    public function markAsAvailable(Booking $booking): void
    {
        $start = Carbon::parse($booking->check_in);
        $end = Carbon::parse($booking->check_out);
        
        for ($date = $start->copy(); $date < $end; $date->addDay()) {
            AvailabilityCalendar::where('accommodation_id', $booking->accommodation_id)
                ->where('date', $date->format('Y-m-d'))
                ->where('status', 'booked')
                ->update(['status' => 'available']);
        }
    }
}