<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\AvailabilityCalendar;
use Carbon\Carbon;
use App\Models\Accommodation;

class CalendarService
{
public function isAvailable($accommodationId, $checkIn, $checkOut, $excludeBookingId = null): bool
{
    $accommodation = Accommodation::find($accommodationId);
    $bufferDays = $accommodation->cleaning_buffer_days ?? 1;

    $checkInDate  = Carbon::parse($checkIn);
    $checkOutDate = Carbon::parse($checkOut);

    // Expandir el rango con el buffer usando Carbon (sin SQL)
    $checkInWithBuffer  = $checkInDate->copy()->subDays($bufferDays);
    $checkOutWithBuffer = $checkOutDate->copy()->addDays($bufferDays);

    $query = Booking::where('accommodation_id', $accommodationId)
        ->where('status', '!=', 'cancelled')
        ->where(function($q) use ($checkInDate, $checkOutDate, $checkInWithBuffer, $checkOutWithBuffer) {
            // 1. Solapamiento directo
            $q->where(function($sub) use ($checkInDate, $checkOutDate) {
                $sub->where('check_in', '<', $checkOutDate)
                    ->where('check_out', '>', $checkInDate);
            })
            // 2. Nueva reserva entra en el buffer post check-out de una existente
            ->orWhere(function($sub) use ($checkInDate, $checkOutWithBuffer) {
                $sub->where('check_out', '>', $checkInDate)
                    ->where('check_out', '<=', $checkOutWithBuffer)
                    ->where('check_in', '<', $checkInDate);
            })
            // 3. Reserva existente entra en el buffer post check-out de la nueva
            ->orWhere(function($sub) use ($checkOutDate, $checkInWithBuffer, $checkOutWithBuffer) {
                $sub->where('check_in', '>=', $checkOutDate)
                    ->where('check_in', '<', $checkOutWithBuffer);
            });
        });

    if ($excludeBookingId) {
        $query->where('id', '!=', $excludeBookingId);
    }

    return !$query->exists();
}



    public function markAsBooked(Booking $booking): void
    {
        $start = Carbon::parse($booking->check_in);
        $end = Carbon::parse($booking->check_out);
        
        for ($date = $start->copy(); $date <= $end; $date->addDay()) {
            AvailabilityCalendar::updateOrCreate(
                [
                    'accommodation_id' => $booking->accommodation_id,
                    'date' => $date->format('Y-m-d')
                ],
                [
                    'status' => 'booked',
                    'user_id' => $booking->guest->user_id,
                    'price' => $booking->price_per_night
                ]
            );
        }
    }

    public function markAsAvailable(Booking $booking): void
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
                    'status' => 'available'
                ]
            );
        }
    }
}