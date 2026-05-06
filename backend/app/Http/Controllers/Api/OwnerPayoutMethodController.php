<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOwnerPayoutMethodRequest;
use App\Http\Requests\UpdateOwnerPayoutMethodRequest;
use App\Http\Resources\OwnerPayoutMethodResource;
use App\Models\OwnerPayoutMethod;
use Illuminate\Http\Request;

class OwnerPayoutMethodController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', OwnerPayoutMethod::class);

        $user = $request->user();
        $query = OwnerPayoutMethod::with('owner');

        if ($user->role === 'owner') {
            $query->whereHas('owner', fn($q) => $q->where('email', $user->email));
        }

        return OwnerPayoutMethodResource::collection($query->paginate(15));
    }

    public function store(StoreOwnerPayoutMethodRequest $request)
    {
        $data = $request->validated();

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
        $this->authorize('view', $ownerPayoutMethod);
        $ownerPayoutMethod->load('owner');
        return new OwnerPayoutMethodResource($ownerPayoutMethod);
    }

    public function update(UpdateOwnerPayoutMethodRequest $request, OwnerPayoutMethod $ownerPayoutMethod)
    {
        $this->authorize('update', $ownerPayoutMethod);

        $data = $request->validated();

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
        $this->authorize('delete', $ownerPayoutMethod);
        $ownerPayoutMethod->delete();
        return response()->json(null, 204);
    }
}