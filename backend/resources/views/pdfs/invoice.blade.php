@extends('pdfs.layout')

@section('doc-title')
    <h1 style="margin: 0 0 8px 0;">FACTURA</h1>
    <p style="margin: 4px 0;">Nº Factura: INV-{{ $booking->id }}-{{ date('Y') }}</p>
    <p style="margin: 4px 0;">Fecha: {{ \Carbon\Carbon::parse($booking->checked_out_at ?? now())->format('d/m/Y') }}</p>
@endsection

@section('content')

    <div style="margin-bottom: 20px;">
        <strong>Cliente:</strong><br>
        {{ $booking->guest_name }}<br>
        {{ $booking->guest_email }}<br>
        {{ $booking->guest_phone }}
    </div>

    <div style="margin-bottom: 20px;">
        <strong>Reserva:</strong> {{ $booking->booking_reference }}<br>
        <strong>Alojamiento:</strong> {{ $booking->accommodation->title }}<br>
        <strong>Dirección:</strong> {{ $booking->accommodation->address }}, {{ $booking->accommodation->city }}<br>
        <strong>Fechas:</strong> {{ \Carbon\Carbon::parse($booking->check_in)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($booking->check_out)->format('d/m/Y') }}<br>
        <strong>Noches:</strong> {{ $booking->nights }}
    </div>

     <table>
        <thead>
            <tr>
                <th>Concepto</th>
                <th class="text-right">Cantidad</th>
                <th class="text-right">Precio unitario</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Alojamiento</td>
                <td class="text-right">{{ $booking->nights }} noches</td>
                <td class="text-right">{{ number_format($booking->price_per_night, 2) }} €</td>
                <td class="text-right">{{ number_format($booking->base_price, 2) }} €</td>
            </tr>
            @if($booking->cleaning_fee > 0)
            <tr>
                <td>Limpieza</td>
                <td class="text-right">1</td>
                <td class="text-right">{{ number_format($booking->cleaning_fee, 2) }} €</td>
                <td class="text-right">{{ number_format($booking->cleaning_fee, 2) }} €</td>
            </tr>
            @endif
            <tr class="total">
                <td colspan="3" class="text-right"><strong>TOTAL</strong></td>
                <td class="text-right"><strong>{{ number_format($booking->total_amount, 2) }} €</strong></td>
            </tr>
        </tbody>
    </table>

    <div style="margin-top: 30px; font-size: 10px; text-align: center;">
        Documento generado electrónicamente. No requiere firma.
    </div>
@endsection