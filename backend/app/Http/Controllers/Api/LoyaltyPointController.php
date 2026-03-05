<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateLoyaltyPointRequest;
use App\Http\Resources\LoyaltyPointResource;
use App\Models\LoyaltyPoint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class LoyaltyPointController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (!Gate::allows('viewAny', LoyaltyPoint::class)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $query = LoyaltyPoint::with(['guest', 'booking', 'redeemedBooking', 'adjustedBy']);

        if ($request->has('guest_id')) {
            $query->where('guest_id', $request->guest_id);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        $points = $query->paginate(15);
        return LoyaltyPointResource::collection($points);
    }

    public function show(LoyaltyPoint $loyaltyPoint)
    {
        if (!Gate::allows('view', $loyaltyPoint)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $loyaltyPoint->load(['guest', 'booking', 'redeemedBooking', 'adjustedBy']);
        return new LoyaltyPointResource($loyaltyPoint);
    }

    public function update(UpdateLoyaltyPointRequest $request, LoyaltyPoint $loyaltyPoint)
    {
        $loyaltyPoint->update($request->validated());
        $loyaltyPoint->load(['guest', 'booking', 'redeemedBooking', 'adjustedBy']);

        return new LoyaltyPointResource($loyaltyPoint);
    }

    public function destroy(LoyaltyPoint $loyaltyPoint)
    {
        if (!Gate::allows('delete', $loyaltyPoint)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $loyaltyPoint->delete();
        return response()->json(null, 204);
    }

    // Método para obtener total de puntos de un guest
    public function balance(Request $request, $guestId)
    {
        $total = LoyaltyPoint::where('guest_id', $guestId)
            ->where('expiry_date', '>', now())
            ->sum('points');

        return response()->json([
            'guest_id' => (int) $guestId,
            'balance' => (int) $total
        ]);
    }
}
