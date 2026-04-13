@extends('pdfs.layout')

@section('content')
    <div style="text-align: center; margin-bottom: 20px;">
        <h1>CONFIRMACIÓN DE RESERVA</h1>
        <p>Nº Confirmación: {{ $booking->booking_reference }}</p>
        <p>Fecha de emisión: {{ now()->format('d/m/Y') }}</p>
        <p><strong>Este documento no es una factura fiscal</strong></p>
    </div>

    <div style="margin-bottom: 20px;">
        <strong>Cliente:</strong><br>
        {{ $booking->guest_name }}<br>
        {{ $booking->guest_email }}<br>
        {{ $booking->guest_phone }}
    </div>

    <div style="margin-bottom: 20px;">
        <strong>Alojamiento:</strong> {{ $booking->accommodation->title }}<br>
        <strong>Dirección:</strong> {{ $booking->accommodation->address }}, {{ $booking->accommodation->city }}<br>
        <strong>Fechas:</strong> {{ \Carbon\Carbon::parse($booking->check_in)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($booking->check_out)->format('d/m/Y') }}<br>
        <strong>Noches:</strong> {{ $booking->nights }}
    </div>

    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
        <thead>
            <tr style="background-color: #dee2e6;">
                <th style="border: 1px solid #cfd2dc; padding: 8px;">Concepto</th>
                <th style="border: 1px solid #cfd2dc; padding: 8px; text-align: right;">Cantidad</th>
                <th style="border: 1px solid #cfd2dc; padding: 8px; text-align: right;">Precio unitario</th>
                <th style="border: 1px solid #cfd2dc; padding: 8px; text-align: right;">Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="border: 1px solid #cfd2dc; padding: 8px;">Alojamiento</td>
                <td style="border: 1px solid #cfd2dc; padding: 8px; text-align: right;">{{ $booking->nights }} noches</td>
                <td style="border: 1px solid #cfd2dc; padding: 8px; text-align: right;">{{ number_format($booking->price_per_night, 2) }} €</td>
                <td style="border: 1px solid #cfd2dc; padding: 8px; text-align: right;">{{ number_format($booking->base_price, 2) }} €</td>
            </tr>
            @if($booking->cleaning_fee > 0)
            <tr>
                <td style="border: 1px solid #cfd2dc; padding: 8px;">Limpieza</td>
                <td style="border: 1px solid #cfd2dc; padding: 8px; text-align: right;">1</td>
                <td style="border: 1px solid #cfd2dc; padding: 8px; text-align: right;">{{ number_format($booking->cleaning_fee, 2) }} €</td>
                <td style="border: 1px solid #cfd2dc; padding: 8px; text-align: right;">{{ number_format($booking->cleaning_fee, 2) }} €</td>
            </tr>
            @endif
            <tr style="font-weight: bold; background-color: #f8f9fa;">
                <td colspan="3" style="border: 1px solid #cfd2dc; padding: 8px; text-align: right;">TOTAL</td>
                <td style="border: 1px solid #cfd2dc; padding: 8px; text-align: right;">{{ number_format($booking->total_amount, 2) }} €</td>
            </tr>
        </tbody>
    </table>

    <div style="margin-top: 30px; font-size: 10px; text-align: center;">
        <strong>Condiciones:</strong> Esta confirmación no es una factura fiscal.<br>
        La factura fiscal se generará automáticamente después del check-out.
    </div>
@endsection