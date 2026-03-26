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
        // Admin puede ver todo
        if ($user->role === 'admin') {
            return true;
        }
        
        // Owner puede ver reservas de sus alojamientos
        if ($user->role === 'owner') {
            return $user->id === $booking->accommodation->owner_id;
        }
        
        // Guest puede ver sus propias reservas
        if ($user->role === 'guest') {
            // Buscar guest asociado al user por email
            $guest = \App\Models\Guest::where('email', $user->email)->first();
            
            return $booking->guest_id === $guest?->id 
                || $booking->guest_email === $user->email;
        }
        
        return false;
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
        return match ($user->role) {
            'admin' => true,
            'owner' => $user->id === $booking->accommodation->owner_id,
            default => false, // Guest no puede actualizar
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
