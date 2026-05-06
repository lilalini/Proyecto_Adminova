<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateLoyaltyPointRequest;
use App\Http\Resources\LoyaltyPointResource;
use App\Models\LoyaltyPoint;
use Illuminate\Http\Request;

class LoyaltyPointController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', LoyaltyPoint::class);

        $user = $request->user();
        $query = LoyaltyPoint::with(['guest', 'booking', 'redeemedBooking', 'adjustedBy']);

        // Guest solo ve sus propios puntos
        if ($user->role === 'guest') {
            $query->where('guest_id', $user->guest?->id);
        } elseif ($request->has('guest_id')) {
            $query->where('guest_id', $request->guest_id);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        return LoyaltyPointResource::collection($query->paginate(15));
    }

    public function show(LoyaltyPoint $loyaltyPoint)
    {
        $this->authorize('view', $loyaltyPoint);
        $loyaltyPoint->load(['guest', 'booking', 'redeemedBooking', 'adjustedBy']);
        return new LoyaltyPointResource($loyaltyPoint);
    }

    public function update(UpdateLoyaltyPointRequest $request, LoyaltyPoint $loyaltyPoint)
    {
        $this->authorize('update', $loyaltyPoint);
        $loyaltyPoint->update($request->validated());
        $loyaltyPoint->load(['guest', 'booking', 'redeemedBooking', 'adjustedBy']);
        return new LoyaltyPointResource($loyaltyPoint);
    }

    public function destroy(LoyaltyPoint $loyaltyPoint)
    {
        $this->authorize('delete', $loyaltyPoint);
        $loyaltyPoint->delete();
        return response()->json(null, 204);
    }

    public function balance(Request $request, $guestId)
    {
        return response()->json([
            'guest_id' => (int) $guestId,
            'balance' => (int) LoyaltyPoint::getBalance($guestId),
        ]);
    }
}