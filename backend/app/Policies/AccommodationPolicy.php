<?php

namespace App\Policies;

use App\Models\Accommodation;
use App\Models\User;

class AccommodationPolicy
{
    /**
     * Ver si un usuario puede ver un alojamiento
     */
    public function view(User $user, Accommodation $accommodation): bool
    {
        // Admin ve todo
        if ($user->role === 'admin') {
            return true;
        }
        
        // Owner solo ve sus propiedades
        if ($user->role === 'owner') {
            return $user->id === $accommodation->owner_id;
        }
        
        // Guest solo ve propiedades publicadas
        return $accommodation->status === 'published';
    }

    /**
     * Ver si un usuario puede crear alojamientos
     */
    public function create(User $user): bool
    {
        // Solo admin y owners pueden crear
        return in_array($user->role, ['admin', 'owner']);
    }

    /**
     * Ver si un usuario puede actualizar un alojamiento
     */
    public function update(User $user, Accommodation $accommodation): bool
    {
        // Admin puede actualizar todo
        if ($user->role === 'admin') {
            return true;
        }
        
        // Owner solo puede actualizar sus propiedades
        return $user->role === 'owner' && $user->id === $accommodation->owner_id;
    }

    /**
     * Ver si un usuario puede eliminar un alojamiento
     */
    public function delete(User $user, Accommodation $accommodation): bool
    {
        // Misma lógica que update
        if ($user->role === 'admin') {
            return true;
        }
        
        return $user->role === 'owner' && $user->id === $accommodation->owner_id;
    }
}