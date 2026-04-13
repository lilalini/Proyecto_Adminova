<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGuestRequest;
use App\Http\Requests\UpdateGuestRequest;
use App\Http\Resources\GuestResource;
use App\Models\Guest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\UpdateGuestByUserIdRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\LoyaltyPoint;

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

    public function findByUserId($userId)
    {
        $guest = Guest::where('user_id', $userId)->first();
        if (!$guest) {
            return response()->json(['message' => 'Guest no encontrado'], 404);
        }
        return new GuestResource($guest);
    }

    public function isProfileComplete($userId)
    {
        $guest = Guest::where('user_id', $userId)->first();
        
        if (!$guest) {
            return response()->json([
                'complete' => false,
                'message' => 'Perfil no encontrado'
            ], 404);
        }
        
        $requiredFields = [
            'first_name', 'last_name', 'email', 'phone',
            'document_type', 'document_number', 'nationality',
            'address', 'city', 'country'
        ];
        
        $missingFields = [];
        foreach ($requiredFields as $field) {
            if (empty($guest->$field)) {
                $missingFields[] = $field;
            }
        }
        
        return response()->json([
            'complete' => empty($missingFields),
            'missing_fields' => $missingFields
        ]);
    }

  

    public function updateByUserId(UpdateGuestByUserIdRequest $request, $userId)
    {
        $guest = Guest::where('user_id', $userId)->first();
        
        if (!$guest) {
            return response()->json(['message' => 'Guest no encontrado'], 404);
        }
        
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($user->id != $userId) {
            return response()->json(['message' => 'No autorizado'], 403);
        }
        
        $oldNewsletter = $guest->accepts_newsletter;
        
        $guest->update($request->validated());
        
        // Si acaba de aceptar el boletín, dar puntos de bienvenida
        if (!$oldNewsletter && $request->accepts_newsletter) {
            LoyaltyPoint::create([
                'guest_id' => $guest->id,
                'points' => 300,
                'type' => 'earned',
                'description' => 'Bono de bienvenida por suscripción al boletín',
                'expiry_date' => now()->addYear(),
            ]);
        }
        
        return new GuestResource($guest);
    }

}