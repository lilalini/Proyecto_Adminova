<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateCommissionRequest;
use App\Http\Resources\CommissionResource;
use App\Models\Commission;
use Illuminate\Http\Request;

class CommissionController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Commission::class);

        $user = $request->user();
        $query = Commission::with(['booking', 'channel', 'accommodation', 'owner']);

        // Owner solo ve sus comisiones
        if ($user->role === 'owner') {
            $query->whereHas('owner', fn($q) => $q->where('email', $user->email));
        }

        if ($request->has('owner_id')) {
            $query->where('owner_id', $request->owner_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('from')) {
            $query->where('created_at', '>=', $request->from);
        }

        if ($request->has('to')) {
            $query->where('created_at', '<=', $request->to);
        }

        return CommissionResource::collection($query->paginate(15));
    }

    public function show(Commission $commission)
    {
        $this->authorize('view', $commission);
        $commission->load(['booking', 'channel', 'accommodation', 'owner']);
        return new CommissionResource($commission);
    }

    public function update(UpdateCommissionRequest $request, Commission $commission)
    {
        $commission->update($request->validated());
        $commission->load(['booking', 'channel', 'accommodation', 'owner']);
        return new CommissionResource($commission);
    }

    public function destroy(Commission $commission)
    {
        $this->authorize('delete', $commission);
        $commission->delete();
        return response()->json(null, 204);
    }

    public function markAsPaid(Request $request, Commission $commission)
    {
        $this->authorize('update', $commission);
        $commission->markAsPaid(); // usando método del modelo
        return new CommissionResource($commission->fresh());
    }
}