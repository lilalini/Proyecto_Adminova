<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGuestRequest;
use App\Http\Requests\UpdateGuestRequest;
use App\Http\Resources\GuestResource;
use App\Models\Guest;
use Illuminate\Http\Request;
use App\Http\Requests\UpdateGuestByUserIdRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\LoyaltyPoint;

class GuestController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Guest::class);

        $user = $request->user();
        $query = Guest::query();

        // Owner solo ve guests de sus alojamientos
        if ($user->role === 'owner') {
            $query->whereHas('bookings.accommodation', fn($q) => $q->where('owner_id', $user->id));
        }

        return GuestResource::collection($query->paginate(15));
    }

    public function store(StoreGuestRequest $request)
    {
        $this->authorize('create', Guest::class);
        $guest = Guest::create($request->validated());
        return new GuestResource($guest);
    }

    public function show(Guest $guest)
    {
        $this->authorize('view', $guest);
        return new GuestResource($guest);
    }

    public function update(UpdateGuestRequest $request, Guest $guest)
    {
        $this->authorize('update', $guest);
        $guest->update($request->validated());
        return new GuestResource($guest);
    }

    public function destroy(Guest $guest)
    {
        $this->authorize('delete', $guest);
        $guest->delete();
        return response()->json(null, 204);
    }

    public function findByUserId(Request $request, $userId)
    {
        // Solo el propio usuario o admin pueden consultar
        $authUser = $request->user();
        if ($authUser->id != $userId && $authUser->role !== 'admin') {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $guest = Guest::where('user_id', $userId)->first();
        if (!$guest) {
            return response()->json(['message' => 'Guest no encontrado'], 404);
        }

        return new GuestResource($guest);
    }

    public function findByEmail(Request $request)
    {
        $email = $request->query('email');
        
        if (!$email) {
            return response()->json(['data' => null]);
        }
        
        $guest = Guest::where('email', $email)->first();
        
        if (!$guest) {
            return response()->json(['data' => null]);
        }
        
        // Verificar que el usuario autenticado puede ver este guest
        $user = $request->user();
        
        if ($user->role !== 'admin' && $user->email !== $email) {
            return response()->json(['data' => null], 403);
        }
        
        return response()->json(['data' => new GuestResource($guest)]);
    }

    public function isProfileComplete($userId)
    {
        $guest = Guest::where('user_id', $userId)->first();

        if (!$guest) {
            return response()->json([
                'complete' => false,
                'message' => 'Perfil no encontrado',
            ], 404);
        }

        $requiredFields = [
            'first_name', 'last_name', 'email', 'phone',
            'document_type', 'document_number', 'nationality',
            'address', 'city', 'country',
        ];

        $missingFields = array_filter($requiredFields, fn($field) => empty($guest->$field));

        return response()->json([
            'complete' => empty($missingFields),
            'missing_fields' => array_values($missingFields),
        ]);
    }

    public function updateByUserId(UpdateGuestByUserIdRequest $request, $userId)
    {
        $authUser = Auth::user();

        if ($authUser->id != $userId && $authUser->role !== 'admin') {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $guest = Guest::where('user_id', $userId)->first();
        if (!$guest) {
            return response()->json(['message' => 'Guest no encontrado'], 404);
        }

        $oldNewsletter = $guest->accepts_newsletter;
        $guest->update($request->validated());

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