<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAvailabilityRequest;
use App\Http\Requests\UpdateAvailabilityRequest;
use App\Http\Resources\AvailabilityCalendarResource;
use App\Models\AvailabilityCalendar;
use App\Models\Accommodation;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Services\CalendarService;

class AvailabilityCalendarController extends Controller
{

    public function __construct(protected CalendarService $calendarService)
    {
    }
    public function index(Request $request)
    {
        $user = $request->user();
        $query = AvailabilityCalendar::query();

        // Control de acceso por rol
        if ($user->role === 'owner') {
            $query->whereHas('accommodation', fn($q) => $q->where('owner_id', $user->id));
        } elseif ($user->role === 'guest') {
            // Guests solo ven disponibilidad de alojamientos publicados
            $query->whereHas('accommodation', fn($q) => $q->where('status', 'published'))
                  ->where('status', 'available');
        }

        if ($request->has('accommodation_id')) {
            $query->where('accommodation_id', $request->accommodation_id);
        }

        if ($request->has('from')) {
            $query->where('date', '>=', $request->from);
        }

        if ($request->has('to')) {
            $query->where('date', '<=', $request->to);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        return AvailabilityCalendarResource::collection($query->orderBy('date')->paginate(31));
    }

    public function store(StoreAvailabilityRequest $request)
    {
        $this->authorize('create', AvailabilityCalendar::class);

        $exists = AvailabilityCalendar::where('accommodation_id', $request->accommodation_id)
            ->where('date', $request->date)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Ya existe un registro para esta fecha'
            ], 422);
        }

        $availability = AvailabilityCalendar::create($request->validated());
        return new AvailabilityCalendarResource($availability);
    }

    public function show(AvailabilityCalendar $availabilityCalendar)
    {
        return new AvailabilityCalendarResource($availabilityCalendar);
    }

    public function update(UpdateAvailabilityRequest $request, AvailabilityCalendar $availabilityCalendar)
    {
        $this->authorize('update', $availabilityCalendar);
        $availabilityCalendar->update($request->validated());
        return new AvailabilityCalendarResource($availabilityCalendar->refresh());
    }

    public function destroy(AvailabilityCalendar $availabilityCalendar)
    {
        $this->authorize('delete', $availabilityCalendar);
        $availabilityCalendar->delete();
        return response()->json(null, 204);
    }

    public function updateRange(Request $request)
    {
        $request->validate([
            'accommodation_id' => 'required|exists:accommodations,id',
            'from' => 'required|date',
            'to' => 'required|date|after:from',
            'status' => 'required|in:available,blocked,maintenance',
            'price' => 'nullable|numeric|min:0',
            'min_nights' => 'nullable|integer|min:1',
            'max_nights' => 'nullable|integer|min:1',
        ]);

        $accommodation = Accommodation::findOrFail($request->accommodation_id);
        $this->authorize('update', $accommodation);

        $current = Carbon::parse($request->from);
        $end = Carbon::parse($request->to);

        while ($current <= $end) {
            AvailabilityCalendar::updateOrCreate(
                [
                    'accommodation_id' => $request->accommodation_id,
                    'date' => $current->format('Y-m-d'),
                ],
                [
                    'status' => $request->status,
                    'price' => $request->price,
                    'min_nights' => $request->min_nights,
                    'max_nights' => $request->max_nights,
                ]
            );
            $current->addDay();
        }

        return response()->json(['message' => 'Calendario actualizado correctamente']);
    }

    public function publicIndex($accommodationId)
    {
        $availability = AvailabilityCalendar::where('accommodation_id', $accommodationId)
            ->where('date', '>=', now())
            ->where('status', 'available') // solo disponibilidad pública, sin datos sensibles
            ->select(['date', 'status', 'price', 'min_nights', 'max_nights', 'closed_to_arrival', 'closed_to_departure'])
            ->limit(60)
            ->get();

        return response()->json(['data' => $availability]);
    }


    public function check(Request $request)
    {
        $request->validate([
            'accommodation_id' => 'required|exists:accommodations,id',
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
        ]);

        $available = $this->calendarService->isAvailable(
            $request->accommodation_id,
            $request->check_in,
            $request->check_out
        );

        return response()->json(['available' => $available]);
    }
}