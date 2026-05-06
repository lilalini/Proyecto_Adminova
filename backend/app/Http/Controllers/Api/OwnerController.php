<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOwnerRequest;
use App\Http\Requests\UpdateOwnerRequest;
use App\Http\Resources\OwnerResource;
use App\Models\Owner;
use Illuminate\Http\Request;

class OwnerController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Owner::class);
        return OwnerResource::collection(Owner::paginate(15));
    }

    public function store(StoreOwnerRequest $request)
    {
        // Sin Hash::make — el cast 'hashed' lo hace automáticamente
        $owner = Owner::create($request->validated());
        return new OwnerResource($owner);
    }

    public function show(Owner $owner)
    {
        $this->authorize('view', $owner);
        return new OwnerResource($owner);
    }

    public function update(UpdateOwnerRequest $request, Owner $owner)
    {
        // Sin Hash::make — el cast 'hashed' lo hace automáticamente
        $owner->update($request->validated());
        return new OwnerResource($owner);
    }

    public function destroy(Owner $owner)
    {
        $this->authorize('delete', $owner);
        $owner->delete();
        return response()->json(null, 204);
    }
}