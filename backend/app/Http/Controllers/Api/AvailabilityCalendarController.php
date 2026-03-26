<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAvailabilityRequest;
use App\Http\Requests\UpdateAvailabilityRequest;
use App\Http\Resources\AvailabilityCalendarResource;
use App\Models\AvailabilityCalendar;
use App\Models\Accommodation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AvailabilityCalendarController extends Controller
{
    public function index(Request $request)
    {
        $query = AvailabilityCalendar::query();

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

        $availabilities = $query->orderBy('date')->paginate(31);
        return AvailabilityCalendarResource::collection($availabilities);
    }

    public function store(StoreAvailabilityRequest $request)
    {
        if (!Gate::allows('create', AvailabilityCalendar::class)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

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

   /* public function update(UpdateAvailabilityRequest $request, AvailabilityCalendar $availabilityCalendar)
    {
        // Forzar carga de la relación para la policy
        $availabilityCalendar->load('accommodation');
        
        if (!Gate::allows('update', $availabilityCalendar)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $availabilityCalendar->update($request->validated());
        $availabilityCalendar->refresh();
        return new AvailabilityCalendarResource($availabilityCalendar);
    }*/
public function update(UpdateAvailabilityRequest $request, $id)
{
    $availabilityCalendar = AvailabilityCalendar::find($id);
    
    if (!$availabilityCalendar) {
        return response()->json(['message' => 'Registro no encontrado'], 404);
    }
    
    $availabilityCalendar->load('accommodation');
    
    if (!Gate::allows('update', $availabilityCalendar)) {
        return response()->json(['message' => 'No autorizado'], 403);
    }

    $availabilityCalendar->update($request->validated());
    $availabilityCalendar->refresh();

    return new AvailabilityCalendarResource($availabilityCalendar);
}

    public function destroy(AvailabilityCalendar $availabilityCalendar)
    {
        $availabilityCalendar->load('accommodation');
        
        if (!Gate::allows('delete', $availabilityCalendar)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

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

        $accommodation = Accommodation::find($request->accommodation_id);
        
        if (!Gate::allows('update', $accommodation)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

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
                                            ->limit(60)
                                            ->get();
        return response()->json(['data' => $availability]);
    }
}