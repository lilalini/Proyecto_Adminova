<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateBookingRequest;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\Payment;
use Illuminate\Support\Str;
use App\Models\Guest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Services\PdfService;
use App\Http\Resources\DocumentResource;
use App\Models\LoyaltyPoint;
use App\Services\CalendarService;

class BookingController extends Controller
{

    protected $calendarService;

    public function __construct(CalendarService $calendarService)
    {
        $this->calendarService = $calendarService;
    }

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

        $query->orderBy('id', 'desc');
        $bookings = $query->paginate(15);
        return BookingResource::collection($bookings);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBookingRequest $request)
    {
        $validated = $request->validated();

        // Verificar disponibilidad
            if (!$this->calendarService->isAvailable($validated['accommodation_id'], $validated['check_in'], $validated['check_out'])) {
                return response()->json([
                    'message' => 'El alojamiento no está disponible en las fechas seleccionadas'
                ], 422);
            }
    
        
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
                'user_id'    => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'phone' => $user->phone ?? '',
                'source' => 'direct'
            ]);

        // Bono de bienvenida
            LoyaltyPoint::create([
                'guest_id' => $guest->id,
                'points' => 300,
                'type' => 'earned',
                'description' => 'Bono de bienvenida',
                'expiry_date' => now()->addYear(),
            ]);
        }
        
        $validated['guest_id'] = $guest->id;
        $validated['booking_reference'] = 'BKG-' . strtoupper(Str::random(8));
        
        $booking = Booking::create($validated);
        $booking->load(['accommodation', 'guest']);
        //para marcar la reserva
        $this->calendarService->markAsBooked($booking);

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
        // Verificar disponibilidad (excluyendo esta reserva)
        if (!$this->calendarService->isAvailable($booking->accommodation_id, $request->check_in, $request->check_out, $booking->id)) {
            return response()->json([
                'message' => 'Las nuevas fechas no están disponibles'
            ], 422);
        }

        if (!Gate::allows('update', $booking)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        // Liberar antiguas y marcar nuevas
        $this->calendarService->markAsAvailable($booking);
        $booking->update($request->validated());
        $this->calendarService->markAsBooked($booking);
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

        public function confirmPayment(Request $request, Booking $booking)
    {
        /** @var int $bookingId */
        $bookingId = $booking->id;
        
        /** @var int $guestId */
        $guestId = $booking->guest_id;
        
        // Crear el pago
        $payment = Payment::create([
            'booking_id' => $bookingId,
            'guest_id' => $guestId,
            'payment_reference' => 'PAY-' . strtoupper(Str::random(10)),
            'amount' => $booking->total_amount,
            'currency' => 'EUR',
            'status' => 'completed',
            'payment_date' => now(),
            'payment_type' => 'full',
            'method' => $request->input('method', 'transfer'),
            'user_id' => Auth::id(),
        ]);

        // Actualizar la reserva
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
        $this->calendarService->markAsAvailable($booking);
        
        return response()->json([
            'message' => 'Reserva cancelada correctamente',
            'refund_amount' => $refundAmount,
            'penalty' => $penaltyAmount
        ]);
    }

    /**
 * Descarga confirmación de reserva (siempre disponible)
 * No guarda en documents, solo descarga directa
 */
    public function downloadConfirmation(Booking $booking, PdfService $pdfService)
    {
        if (!Gate::allows('view', $booking)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $pdf = $pdfService->render('pdfs.booking_confirmation', compact('booking'));

        return response($pdf)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="confirmacion-' . $booking->booking_reference . '.pdf"');
    }

        public function generateInvoice(Booking $booking, PdfService $pdfService)
    {
        if (!Gate::allows('view', $booking)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        // Solo permitir generar factura si la reserva está completada
        if ($booking->status !== 'checked_out') {
            return response()->json([
                'message' => 'La factura solo se puede generar después del check-out'
            ], 422);
        }

        $pdfContent = $pdfService->generateInvoice($booking);
        
        $document = $pdfService->saveAndRegister(
            $pdfContent,
            'invoice',
            $booking,
            'Factura reserva #' . $booking->booking_reference
        );

        return response()->json([
            'message' => 'Factura generada',
            'document' => new DocumentResource($document)
        ]);
    }

    public function downloadInvoice(Booking $booking, PdfService $pdfService)
    {
        if (!Gate::allows('view', $booking)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $pdfContent = $pdfService->generateInvoice($booking);
        
        return response($pdfContent)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="factura-' . $booking->booking_reference . '.pdf"');
    }


    public function bookingComparison(Request $request)
    {
        $user = $request->user();
        $query = Booking::query();
        
        if ($user->role === 'owner') {
            $query->whereHas('accommodation', fn($q) => $q->where('owner_id', $user->id));
        } elseif ($user->role === 'guest') {
            $query->where('guest_id', $user->id);
        }
        
        $currentMonth = (clone $query)->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        
        $previousMonth = (clone $query)->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();
        
        $percentage = $previousMonth > 0 
            ? round(($currentMonth - $previousMonth) / $previousMonth * 100) 
            : ($currentMonth > 0 ? 100 : 0);
        
        return response()->json([
            'percentage' => abs($percentage),
            'trend' => $percentage >= 0 ? 'up' : 'down',
            'current_month' => $currentMonth,
            'previous_month' => $previousMonth
        ]);
    }

        public function averageComparison(Request $request)
    {
        $user = $request->user();
        $query = Booking::query();
        
        if ($user->role === 'owner') {
            $query->whereHas('accommodation', fn($q) => $q->where('owner_id', $user->id));
        } elseif ($user->role === 'guest') {
            $query->where('guest_id', $user->id);
        }
        
        $currentAvg = (clone $query)->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->avg('total_amount') ?? 0;
        
        $previousAvg = (clone $query)->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->avg('total_amount') ?? 0;
        
        $percentage = $previousAvg > 0 
            ? round(($currentAvg - $previousAvg) / $previousAvg * 100) 
            : ($currentAvg > 0 ? 100 : 0);
        
        return response()->json([
            'percentage' => abs($percentage),
            'trend' => $percentage >= 0 ? 'up' : 'down',
            'current_month' => round($currentAvg),
            'previous_month' => round($previousAvg)
        ]);
    }

}
