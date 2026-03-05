<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOwnerPayoutMethodRequest;
use App\Http\Requests\UpdateOwnerPayoutMethodRequest;
use App\Http\Resources\OwnerPayoutMethodResource;
use App\Models\OwnerPayoutMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class OwnerPayoutMethodController extends Controller
{
    public function index(Request $request)
    {
        if (!Gate::allows('viewAny', OwnerPayoutMethod::class)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $user = $request->user();
        $query = OwnerPayoutMethod::with('owner');

        if ($user->role === 'owner') {
            $query->where('owner_id', $user->id);
        }

        $methods = $query->paginate(15);
        return OwnerPayoutMethodResource::collection($methods);
    }

    public function store(StoreOwnerPayoutMethodRequest $request)
    {
        $data = $request->validated();

        // Si es default, quitar default de otros métodos del mismo owner
        if ($data['is_default'] ?? false) {
            OwnerPayoutMethod::where('owner_id', $data['owner_id'])
                ->update(['is_default' => false]);
        }

        $method = OwnerPayoutMethod::create($data);
        $method->load('owner');

        return new OwnerPayoutMethodResource($method);
    }

    public function show(OwnerPayoutMethod $ownerPayoutMethod)
    {
        if (!Gate::allows('view', $ownerPayoutMethod)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $ownerPayoutMethod->load('owner');
        return new OwnerPayoutMethodResource($ownerPayoutMethod);
    }

    public function update(UpdateOwnerPayoutMethodRequest $request, OwnerPayoutMethod $ownerPayoutMethod)
    {
        $data = $request->validated();

        // Si se marca como default, quitar default de otros métodos del mismo owner
        if (($data['is_default'] ?? false) && !$ownerPayoutMethod->is_default) {
            OwnerPayoutMethod::where('owner_id', $ownerPayoutMethod->owner_id)
                ->where('id', '!=', $ownerPayoutMethod->id)
                ->update(['is_default' => false]);
        }

        $ownerPayoutMethod->update($data);
        $ownerPayoutMethod->load('owner');

        return new OwnerPayoutMethodResource($ownerPayoutMethod);
    }

    public function destroy(OwnerPayoutMethod $ownerPayoutMethod)
    {
        if (!Gate::allows('delete', $ownerPayoutMethod)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $ownerPayoutMethod->delete();
        return response()->json(null, 204);
    }
}