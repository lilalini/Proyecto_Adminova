<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BookingPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Booking $booking): bool
    {
        if ($user->role === 'admin') return true;
        if ($user->role === 'owner') {
            return $user->id === $booking->accommodation->owner_id;
        }
        return $user->id === $booking->guest_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
     public function update(User $user, Booking $booking): bool
    {
        /*// Admin puede actualizar cualquier booking
        if ($user->role === 'admin') return true;
        
        // Owner solo puede actualizar bookings de sus propiedades
        if ($user->role === 'owner') {
            return $user->id === $booking->accommodation->owner_id;
        }
        
        // Guest no puede actualizar (solo ver)
        return false;
        */
            return match ($user->role) {
                'admin' => true,
                'owner' => $user->id === $booking->accommodation->owner_id,
                default => false,
            };
        
    }


    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Booking $booking): bool
    {
        // Solo admin puede eliminar bookings
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Booking $booking): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Booking $booking): bool
    {
        return false;
    }
}
