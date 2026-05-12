<x-mail::message>
# ¡Reserva confirmada!

Hola **{{ $booking->guest->first_name }}**,

Tu reserva ha sido creada correctamente. Aquí tienes el resumen:

<x-mail::panel>
**Referencia:** {{ $booking->booking_reference }}  
**Alojamiento:** {{ $booking->accommodation->title }}  
**Check-in:** {{ \Carbon\Carbon::parse($booking->check_in)->format('d/m/Y') }}  
**Check-out:** {{ \Carbon\Carbon::parse($booking->check_out)->format('d/m/Y') }}  
**Total:** {{ number_format($booking->total_amount, 2) }} €
</x-mail::panel>

Por favor, realiza el pago para confirmar tu reserva.

<x-mail::button :url="config('app.url')">
Ver mi reserva
</x-mail::button>

Gracias por elegir Adminova,<br>
{{ config('app.name') }}
</x-mail::message>