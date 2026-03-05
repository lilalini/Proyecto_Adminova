<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateCommissionRequest;
use App\Http\Resources\CommissionResource;
use App\Models\Commission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CommissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (!Gate::allows('viewAny', Commission::class)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $query = Commission::with(['booking', 'channel', 'accommodation', 'owner']);

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

        $commissions = $query->paginate(15);
        return CommissionResource::collection($commissions);
    }

    public function show(Commission $commission)
    {
        if (!Gate::allows('view', $commission)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

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
        if (!Gate::allows('delete', $commission)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $commission->delete();
        return response()->json(null, 204);
    }

    public function markAsPaid(Request $request, Commission $commission)
    {
        if (!Gate::allows('update', $commission)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $commission->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        return new CommissionResource($commission);
    }
}
