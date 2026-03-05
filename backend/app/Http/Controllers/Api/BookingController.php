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
        $validated['booking_reference'] = 'BKG-' . strtoupper(Str::random(8));
        
        $booking = Booking::create($validated);
        $booking->load(['accommodation', 'guest', 'channel']);

        return new BookingResource($booking);
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
}
