<x-mail::message>
# ¡Tu estancia se acerca!

Hola **{{ $booking->guest->first_name }}**,

Te recordamos que tu reserva comienza en **2 días**. ¡Prepárate!

<x-mail::panel>
**Referencia:** {{ $booking->booking_reference }}  
**Alojamiento:** {{ $booking->accommodation->title }}  
**Check-in:** {{ \Carbon\Carbon::parse($booking->check_in)->format('d/m/Y') }} a las {{ $booking->accommodation->check_in_time }}  
**Check-out:** {{ \Carbon\Carbon::parse($booking->check_out)->format('d/m/Y') }} a las {{ $booking->accommodation->check_out_time }}  
**Dirección:** {{ $booking->accommodation->address }}, {{ $booking->accommodation->city }}
</x-mail::panel>

<x-mail::button :url="env('FRONTEND_URL') . '/bookings/' . $booking->id">
Ver mi reserva
</x-mail::button>

Gracias por elegir Adminova,<br>
{{ config('app.name') }}
</x-mail::message>
