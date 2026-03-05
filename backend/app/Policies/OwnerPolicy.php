<?php

namespace App\Policies;

use App\Models\Owner;
use App\Models\User;

class OwnerPolicy
{
    /**
     * Determine whether the user can view any owners.
     */
    public function viewAny(User $user): bool
    {
        // Solo admin puede ver listado de owners
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can view the owner.
     */
    public function view(User $user, Owner $owner): bool
    {
        // Admin puede ver cualquier owner
        if ($user->role === 'admin') return true;
        
        // Owner solo puede verse a sí mismo
        return $user->role === 'owner' && $user->id === $owner->id;
    }

    /**
     * Determine whether the user can create owners.
     */
    public function create(User $user): bool
    {
        // Solo admin puede crear owners
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can update the owner.
     */
    public function update(User $user, Owner $owner): bool
    {
        // Admin puede actualizar cualquier owner
        if ($user->role === 'admin') return true;
        
        // Owner solo puede actualizarse a sí mismo
        return $user->role === 'owner' && $user->id === $owner->id;
    }

    /**
     * Determine whether the user can delete the owner.
     */
    public function delete(User $user, Owner $owner): bool
    {
        // Solo admin puede eliminar owners (soft delete)
        return $user->role === 'admin';
    }
}