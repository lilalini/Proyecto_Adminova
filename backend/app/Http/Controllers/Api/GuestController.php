<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGuestRequest;
use App\Http\Requests\UpdateGuestRequest;
use App\Http\Resources\GuestResource;
use App\Models\Guest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class GuestController extends Controller
{
    public function index(Request $request)
    {
        if (!Gate::allows('viewAny', Guest::class)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $guests = Guest::paginate(15);
        return GuestResource::collection($guests);
    }

    public function store(StoreGuestRequest $request)
    {
        if (!Gate::allows('create', Guest::class)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $guest = Guest::create($request->validated());
        return new GuestResource($guest);
    }

    public function show(Guest $guest)
    {
        if (!Gate::allows('view', $guest)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        return new GuestResource($guest);
    }

    public function update(UpdateGuestRequest $request, Guest $guest)
    {
        if (!Gate::allows('update', $guest)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $guest->update($request->validated());
        return new GuestResource($guest);
    }

    public function destroy(Guest $guest)
    {
        if (!Gate::allows('delete', $guest)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $guest->delete();
        return response()->json(null, 204);
    }
}