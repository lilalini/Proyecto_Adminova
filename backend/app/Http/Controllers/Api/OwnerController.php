<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOwnerRequest;
use App\Http\Requests\UpdateOwnerRequest;
use App\Http\Resources\OwnerResource;
use App\Models\Owner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;

class OwnerController extends Controller
{
    public function index(Request $request)
    {
        if (!Gate::allows('viewAny', Owner::class)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $owners = Owner::paginate(15);
        return OwnerResource::collection($owners);
    }

    public function store(StoreOwnerRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        
        $owner = Owner::create($data);
        return new OwnerResource($owner);
    }

    public function show(Owner $owner)
    {
        if (!Gate::allows('view', $owner)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        return new OwnerResource($owner);
    }

    public function update(UpdateOwnerRequest $request, Owner $owner)
    {
        $data = $request->validated();
        
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        
        $owner->update($data);
        return new OwnerResource($owner);
    }

    public function destroy(Owner $owner)
    {
        if (!Gate::allows('delete', $owner)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $owner->delete();
        return response()->json(null, 204);
    }
}