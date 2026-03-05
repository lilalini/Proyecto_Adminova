<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGuestPaymentMethodRequest;
use App\Http\Requests\UpdateGuestPaymentMethodRequest;
use App\Http\Resources\GuestPaymentMethodResource;
use App\Models\GuestPaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class GuestPaymentMethodController extends Controller
{
    public function index(Request $request)
    {
        if (!Gate::allows('viewAny', GuestPaymentMethod::class)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $user = $request->user();
        $query = GuestPaymentMethod::with('guest');

        if ($user->role === 'guest') {
            $query->where('guest_id', $user->id);
        }

        $methods = $query->paginate(15);
        return GuestPaymentMethodResource::collection($methods);
    }

    public function store(StoreGuestPaymentMethodRequest $request)
    {
        $data = $request->validated();

        // Si es default, quitar default de otros métodos del mismo guest
        if ($data['is_default'] ?? false) {
            GuestPaymentMethod::where('guest_id', $data['guest_id'])
                ->update(['is_default' => false]);
        }

        $method = GuestPaymentMethod::create($data);
        $method->load('guest');

        return new GuestPaymentMethodResource($method);
    }

    public function show(GuestPaymentMethod $guestPaymentMethod)
    {
        if (!Gate::allows('view', $guestPaymentMethod)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $guestPaymentMethod->load('guest');
        return new GuestPaymentMethodResource($guestPaymentMethod);
    }

    public function update(UpdateGuestPaymentMethodRequest $request, GuestPaymentMethod $guestPaymentMethod)
    {
        $data = $request->validated();

        // Si se marca como default, quitar default de otros métodos del mismo guest
        if (($data['is_default'] ?? false) && !$guestPaymentMethod->is_default) {
            GuestPaymentMethod::where('guest_id', $guestPaymentMethod->guest_id)
                ->where('id', '!=', $guestPaymentMethod->id)
                ->update(['is_default' => false]);
        }

        $guestPaymentMethod->update($data);
        $guestPaymentMethod->load('guest');

        return new GuestPaymentMethodResource($guestPaymentMethod);
    }

    public function destroy(GuestPaymentMethod $guestPaymentMethod)
    {
        if (!Gate::allows('delete', $guestPaymentMethod)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $guestPaymentMethod->delete();
        return response()->json(null, 204);
    }
}