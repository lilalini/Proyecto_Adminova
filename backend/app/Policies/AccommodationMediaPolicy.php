<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Accommodation;

class AccommodationMediaPolicy
{
    public function create(User $user, Accommodation $accommodation)
    {
        // Admin puede subir a cualquier alojamiento
        if ($user->role === 'admin') {
            return true;
        }
        
        // Owner solo puede subir a sus propios alojamientos
        if ($user->role === 'owner' && $accommodation->owner_id === $user->id) {
            return true;
        }
        
        return false;
    }
    
    public function delete(User $user, Accommodation $accommodation)
    {
        // Misma lógica
        if ($user->role === 'admin') {
            return true;
        }
        
        if ($user->role === 'owner' && $accommodation->owner_id === $user->id) {
            return true;
        }
        
        return false;
    }
    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Accommodation $accommodation): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Accommodation $accommodation): bool
    {
        return false;
    }
}
