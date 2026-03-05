<?php

namespace App\Policies;

use App\Models\Guest;
use App\Models\User;

class GuestPolicy
{
    /**
     * Determine whether the user can view any guests.
     */
    public function viewAny(User $user): bool
    {
        // Admin y owner pueden ver listado de guests
        return in_array($user->role, ['admin', 'owner']);
    }

    /**
     * Determine whether the user can view the guest.
     */
    public function view(User $user, Guest $guest): bool
    {
        // Admin puede ver cualquier guest
        if ($user->role === 'admin') return true;
        
        // Owner puede ver guests de sus bookings
        if ($user->role === 'owner') {
            return $guest->bookings()
                ->whereHas('accommodation', fn($q) => $q->where('owner_id', $user->id))
                ->exists();
        }
        
        // Guest solo puede verse a sí mismo
        return $user->role === 'guest' && $user->id === $guest->id;
    }

    /**
     * Determine whether the user can create guests.
     */
    public function create(User $user): bool
    {
        // Admin, owner y el propio guest pueden crear (registro)
        return in_array($user->role, ['admin', 'owner', 'guest']);
    }

    /**
     * Determine whether the user can update the guest.
     */
    public function update(User $user, Guest $guest): bool
    {
        // Admin puede actualizar cualquier guest
        if ($user->role === 'admin') return true;
        
        // Guest solo puede actualizarse a sí mismo
        return $user->role === 'guest' && $user->id === $guest->id;
    }

    /**
     * Determine whether the user can delete the guest.
     */
    public function delete(User $user, Guest $guest): bool
    {
        // Solo admin puede eliminar guests (soft delete)
        return $user->role === 'admin';
    }
}