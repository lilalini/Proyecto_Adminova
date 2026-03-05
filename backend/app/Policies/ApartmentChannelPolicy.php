<?php

namespace App\Policies;

use App\Models\ApartmentChannel;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ApartmentChannelPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
         return in_array($user->role, ['admin', 'owner']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ApartmentChannel $apartmentChannel): bool
    {
        if ($user->role === 'admin') return true;
        return $user->id === $apartmentChannel->accommodation->owner_id;
    }

    /**
     * Determine whether the user can create models.
     */
     public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'owner']);
    }

    public function update(User $user, ApartmentChannel $apartmentChannel): bool
    {
        if ($user->role === 'admin') return true;
        return $user->id === $apartmentChannel->accommodation->owner_id;
    }

    public function delete(User $user, ApartmentChannel $apartmentChannel): bool
    {
        if ($user->role === 'admin') return true;
        return $user->id === $apartmentChannel->accommodation->owner_id;
    }
    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ApartmentChannel $apartmentChannel): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ApartmentChannel $apartmentChannel): bool
    {
        return false;
    }
}
