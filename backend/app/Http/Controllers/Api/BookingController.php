<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateBookingRequest;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use Illuminate\Http\Request;
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
use App\Models\Notification;
use App\Models\Owner;

class BookingController extends Controller
{
    public function __construct(
        protected CalendarService $calendarService
    ) {}

    public function index(Request $request)
    {
        $user = $request->user();
        $query = Booking::with(['accommodation', 'guest', 'channel']);

        if ($user->role === 'guest') {
            $query->where('guest_id', $user->id);
        } elseif ($user->role === 'owner') {
            $owner = Owner::where('user_id', $user->id)->first();
            if ($owner) {
                $query->whereHas('accommodation', fn($q) => $q->where('owner_id', $owner->id));
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        return BookingResource::collection($query->orderBy('id', 'desc')->paginate(15));
    }

    public function store(StoreBookingRequest $request)
    {
        $validated = $request->validated();

        if (!$this->calendarService->isAvailable($validated['accommodation_id'], $validated['check_in'], $validated['check_out'])) {
            return response()->json([
                'message' => 'El alojamiento no está disponible en las fechas seleccionadas'
            ], 422);
        }

        $user = $request->user();

        if (!$user) {
            $user = User::where('email', $validated['guest_email'])->first();

            if (!$user) {
                $user = User::create([
                    'first_name' => explode(' ', $validated['guest_name'])[0],
                    'last_name' => explode(' ', $validated['guest_name'])[1] ?? '',
                    'email' => $validated['guest_email'],
                    'phone' => $validated['guest_phone'] ?? '',
                    'role' => 'guest',
                    'is_guest' => true,
                    'password' => Hash::make(Str::random(16)),
                ]);
            }

            Auth::login($user);
        }

        $guest = Guest::where('email', $user->email)->first();
        if (!$guest) {
            $guest = Guest::create([
                'user_id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'phone' => $user->phone ?? '',
                'source' => 'direct',
            ]);

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
        $this->calendarService->markAsBooked($booking);

        Notification::create([
            'notifiable_type' => 'App\\Models\\User',
            'notifiable_id' => $user->id,
            'title' => 'Reserva creada',
            'body' => 'Tu reserva en ' . $booking->accommodation->title . ' ha sido creada correctamente. Por favor, realiza el pago para confirmarla.',
            'type' => 'booking_created',
            'is_read' => false
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'data' => new BookingResource($booking),
            'token' => $token,
        ], 201);
    }

    public function show(Booking $booking)
    {
        $this->authorize('view', $booking);
        $booking->load(['accommodation', 'guest', 'channel']);
        return new BookingResource($booking);
    }

    public function update(UpdateBookingRequest $request, Booking $booking)
    {
        // Autorización PRIMERO
        $this->authorize('update', $booking);

        // Luego verificar disponibilidad si cambian las fechas
        if ($request->has('check_in') || $request->has('check_out')) {
            $checkIn = $request->check_in ?? $booking->check_in;
            $checkOut = $request->check_out ?? $booking->check_out;

            if (!$this->calendarService->isAvailable($booking->accommodation_id, $checkIn, $checkOut, $booking->id)) {
                return response()->json([
                    'message' => 'Las nuevas fechas no están disponibles'
                ], 422);
            }

            $this->calendarService->markAsAvailable($booking);
            $booking->update($request->validated());
            $this->calendarService->markAsBooked($booking->fresh());
        } else {
            $booking->update($request->validated());
        }

        $booking->load(['accommodation', 'guest', 'channel']);
        return new BookingResource($booking);
    }

    public function destroy(Booking $booking)
    {
        $this->authorize('delete', $booking);
        $booking->delete();
        return response()->json(null, 204);
    }

    public function myBookings(Request $request)
    {
        $user = $request->user();
        $guest = Guest::where('email', $user->email)->first();

        $bookings = Booking::with(['accommodation'])
            ->where(function($q) use ($user, $guest) {
                $q->where('guest_email', $user->email)
                  ->orWhere('guest_id', $guest?->id);
            })
            ->latest()
            ->paginate(10);

        return BookingResource::collection($bookings);
    }

    public function confirmPayment(Request $request, Booking $booking)
    {
        $this->authorize('confirmPayment', $booking);

        Payment::create([
            'booking_id' => $booking->id,
            'guest_id' => $booking->guest_id,
            'payment_reference' => 'PAY-' . strtoupper(Str::random(10)),
            'amount' => $booking->total_amount,
            'currency' => 'EUR',
            'status' => 'completed',
            'payment_date' => now(),
            'payment_type' => 'full',
            'method' => $request->input('method', 'transfer'),
            'user_id' => Auth::id(),
        ]);

        $booking->update([
            'status' => 'confirmed',
            'payment_status' => 'paid',
            'confirmed_at' => now(),
        ]);

        // Crear notificación
        Notification::create([
            'notifiable_type' => 'App\\Models\\User',
            'notifiable_id' => $booking->guest->user_id,
            'title' => 'Pago confirmado',
            'body' => 'El pago de tu reserva #' . $booking->booking_reference . ' ha sido confirmado',
            'type' => 'payment_confirmed',
            'is_read' => false
        ]);

        return new BookingResource($booking->fresh());
    }

    public function cancelByGuest(Booking $booking, Request $request)
    {
        $user = $request->user();

        if ($booking->guest_email !== $user->email) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        if ($booking->status === 'cancelled') {
            return response()->json(['message' => 'La reserva ya está cancelada'], 400);
        }

        if ($booking->check_in < now()) {
            return response()->json(['message' => 'No se puede cancelar una estancia pasada'], 400);
        }

        $policy = $booking->accommodation->cancellationPolicy;
        $daysBeforeCheckin = now()->diffInDays($booking->check_in, false);

        $refundAmount = 0;
        $penaltyAmount = 0;

        if ($daysBeforeCheckin >= $policy->free_cancellation_days) {
            $refundAmount = $booking->total_amount;
        } else {
            $penaltyAmount = ($booking->total_amount * $policy->penalty_percentage) / 100;
            $refundAmount = $booking->total_amount - $penaltyAmount;
        }

        $booking->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $request->reason ?? 'Cancelado por el huésped',
        ]);

        $this->calendarService->markAsAvailable($booking);

        return response()->json([
            'message' => 'Reserva cancelada correctamente',
            'refund_amount' => $refundAmount,
            'penalty' => $penaltyAmount,
        ]);
    }

    public function downloadConfirmation(Booking $booking, PdfService $pdfService)
    {
        $this->authorize('view', $booking);
        $pdf = $pdfService->render('pdfs.booking_confirmation', compact('booking'));

        return response($pdf)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="confirmacion-' . $booking->booking_reference . '.pdf"');
    }

    public function generateInvoice(Booking $booking, PdfService $pdfService)
    {
        $this->authorize('view', $booking);

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
            'document' => new DocumentResource($document),
        ]);
    }

    public function downloadInvoice(Booking $booking, PdfService $pdfService)
    {
        $this->authorize('view', $booking);
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
            $owner = \App\Models\Owner::where('user_id', $user->id)->first();
            if ($owner) {
                $query->whereHas('accommodation', fn($q) => $q->where('owner_id', $owner->id));
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        $currentMonth = (clone $query)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $previousMonth = (clone $query)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();

        $percentage = $previousMonth > 0
            ? round(($currentMonth - $previousMonth) / $previousMonth * 100)
            : ($currentMonth > 0 ? 100 : 0);

        return response()->json([
            'percentage' => abs($percentage),
            'trend' => $percentage >= 0 ? 'up' : 'down',
            'current_month' => $currentMonth,
            'previous_month' => $previousMonth,
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

        $currentAvg = (clone $query)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->avg('total_amount') ?? 0;

        $previousAvg = (clone $query)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->avg('total_amount') ?? 0;

        $percentage = $previousAvg > 0
            ? round(($currentAvg - $previousAvg) / $previousAvg * 100)
            : ($currentAvg > 0 ? 100 : 0);

        return response()->json([
            'percentage' => abs($percentage),
            'trend' => $percentage >= 0 ? 'up' : 'down',
            'current_month' => round($currentAvg),
            'previous_month' => round($previousAvg),
        ]);
    }
}