<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateBookingRequest;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use App\Models\Guest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\AvailabilityCalendar;
use Carbon\CarbonPeriod;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Si no hay usuario autenticado, error 401
        if (!$user) {
            return response()->json(['message' => 'No autenticado'], 401);
        }
        
        $query = Booking::with(['accommodation', 'guest', 'channel']);

        if ($user->role === 'guest') {
            $query->where('guest_id', $user->id);
        } elseif ($user->role === 'owner') {
            $query->whereHas('accommodation', fn($q) => $q->where('owner_id', $user->id));
        }

        $bookings = $query->paginate(15);
        return BookingResource::collection($bookings);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBookingRequest $request)
    {
        $validated = $request->validated();
        
        // Si hay usuario autenticado, lo usamos
        $user = $request->user();
        
        // Si NO hay usuario autenticado (guest checkout)
        if (!$user) {
            // Buscar si ya existe un guest con este email
            $user = User::where('email', $validated['guest_email'])->first();
            
            if (!$user) {
                // Crear usuario guest automáticamente
                $user = User::create([
                    'first_name' => explode(' ', $validated['guest_name'])[0],
                    'last_name' => explode(' ', $validated['guest_name'])[1] ?? '',
                    'email' => $validated['guest_email'],
                    'phone' => $validated['guest_phone'] ?? '',
                    'role' => 'guest',
                    'is_guest' => true,
                    'password' => Hash::make(Str::random(16))
                ]);
            }
            
            // Autenticar al guest para esta petición
            Auth::login($user);
        }
        
        // Buscar o crear guest asociado en tabla guests
        $guest = Guest::where('email', $user->email)->first();
        if (!$guest) {
            $guest = Guest::create([
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'phone' => $user->phone ?? '',
                'source' => 'direct'
            ]);
        }
        
        $validated['guest_id'] = $guest->id;
        $validated['booking_reference'] = 'BKG-' . strtoupper(Str::random(8));
        
        $booking = Booking::create($validated);
        $booking->load(['accommodation', 'guest']);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'data' => new BookingResource($booking),
            'token' => $token
        ], 201);
}

    /**
     * Display the specified resource.
     */
    public function show(Booking $booking)
    {
        if (!Gate::allows('view', $booking)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $booking->load(['accommodation', 'guest', 'channel']);
        return new BookingResource($booking);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBookingRequest $request, Booking $booking)
    {
        if (!Gate::allows('update', $booking)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $booking->update($request->validated());
        $booking->load(['accommodation', 'guest', 'channel']);

        return new BookingResource($booking);
    }

    /**
     * Remove the specified resource from storage.
     */
     public function destroy(Booking $booking)
    {
        if (!Gate::allows('delete', $booking)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $booking->delete();
        return response()->json(null, 204);
    }

    /**
 * Get bookings for the authenticated guest.
 */
    public function myBookings(Request $request)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json(['message' => 'No autenticado'], 401);
        }

        // Buscar guest asociado al usuario
        $guest = Guest::where('email', $user->email)->first();
        
        $query = Booking::with(['accommodation'])
                        ->where('guest_email', $user->email);
        
        if ($guest) {
            $query->orWhere('guest_id', $guest->id);
        }

        $bookings = $query->latest()->paginate(10);
        
        return BookingResource::collection($bookings);
    }

        public function confirmPayment(Booking $booking)
    {
        $booking->update([
            'status' => 'confirmed',
            'payment_status' => 'paid',
            'confirmed_at' => now()
        ]);
        
        return new BookingResource($booking);
    }

    public function cancelByGuest(Booking $booking, Request $request)
    {
        $user = $request->user();
        
        // Verificar que la reserva pertenece al guest
        if ($booking->guest_email !== $user->email) {
            return response()->json(['message' => 'No autorizado'], 403);
        }
        
        // Verificar que la reserva puede cancelarse (no pasada, no ya cancelada)
        if ($booking->status === 'cancelled') {
            return response()->json(['message' => 'La reserva ya está cancelada'], 400);
        }
        
        if ($booking->check_in < now()) {
            return response()->json(['message' => 'No se puede cancelar una estancia pasada'], 400);
        }
        
        // Obtener política de cancelación
        $policy = $booking->accommodation->cancellationPolicy;
        $daysBeforeCheckin = now()->diffInDays($booking->check_in, false);
        
        // Calcular reembolso según política
        $refundAmount = 0;
        $penaltyAmount = 0;
        
        if ($daysBeforeCheckin >= $policy->free_cancellation_days) {
            // Cancelación gratuita
            $refundAmount = $booking->total_amount;
        } else {
            // Aplicar penalización
            $penaltyAmount = ($booking->total_amount * $policy->penalty_percentage) / 100;
            $refundAmount = $booking->total_amount - $penaltyAmount;
        }
        
        // Actualizar estado
        $booking->status = 'cancelled';
        $booking->cancelled_at = now();
        $booking->cancellation_reason = $request->reason ?? 'Cancelado por el huésped';
        $booking->save();
        
        // Liberar disponibilidad en el calendario
        $this->releaseAvailability($booking);
        
        return response()->json([
            'message' => 'Reserva cancelada correctamente',
            'refund_amount' => $refundAmount,
            'penalty' => $penaltyAmount
        ]);
    }

    private function releaseAvailability(Booking $booking)
    {
        // Liberar las fechas en availability_calendars
        $start = $booking->check_in;               
        $end = $booking->check_out->copy()->subDay(); // ← copy() para no modificar original
        
        $dates = CarbonPeriod::create($start, $end);
        
        foreach ($dates as $date) {
            AvailabilityCalendar::where('accommodation_id', $booking->accommodation_id)
                ->where('date', $date->format('Y-m-d'))
                ->update(['status' => 'available']);
        }
    }
}
