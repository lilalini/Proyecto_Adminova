<?php

namespace App\Observers;

use App\Models\Booking;
use App\Models\Notification;

class BookingObserver
{
    public function created(Booking $booking): void
    {
        Notification::create([
            'notifiable_type' => 'App\\Models\\User',
            'notifiable_id' => $booking->guest->user_id,
            'title' => 'Reserva confirmada',
            'body' => "Tu reserva en {$booking->accommodation->title} ha sido confirmada",
            'type' => 'booking_confirmed',
            'is_read' => false
        ]);
    }

    public function updated(Booking $booking): void
    {
        if ($booking->wasChanged('status')) {
            $this->handleStatusChange($booking);
        }
    }

    private function handleStatusChange(Booking $booking): void
    {
        $messages = [
            'checked_in' => [
                'title' => 'Check-in realizado',
                'body' => "Has realizado el check-in en {$booking->accommodation->title}"
            ],
            'checked_out' => [
                'title' => 'Check-out realizado', 
                'body' => "Gracias por tu estancia en {$booking->accommodation->title}"
            ],
            'cancelled' => [
                'title' => 'Reserva cancelada',
                'body' => "Tu reserva en {$booking->accommodation->title} ha sido cancelada"
            ]
        ];

        if (isset($messages[$booking->status])) {
            Notification::create([
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => $booking->guest->user_id,
                'title' => $messages[$booking->status]['title'],
                'body' => $messages[$booking->status]['body'],
                'type' => $booking->status,
                'is_read' => false
            ]);
        }
    }

     private function generateInvoice(Booking $booking): void
    {
        try {
            $pdfService = app(PdfService::class);
            $pdfService->generateInvoice($booking);
        } catch (\Exception $e) {
            \Log::error('Error generando factura automática: ' . $e->getMessage());
        }
    }

}