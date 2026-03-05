<?php

namespace App\Policies;

use App\Models\OwnerPayoutMethod;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OwnerPayoutMethodPolicy
{
    /**
     * Determine whether the user can view any models.
     */
public function viewAny(User $user): bool
    {
        // Admin puede ver todos, owner solo los suyos
        return in_array($user->role, ['admin', 'owner']);
    }

    public function view(User $user, OwnerPayoutMethod $ownerPayoutMethod): bool
    {
        if ($user->role === 'admin') return true;
        return $user->id === $ownerPayoutMethod->owner_id;
    }

    public function create(User $user): bool
    {
        // Admin y owner pueden crear
        return in_array($user->role, ['admin', 'owner']);
    }

    public function update(User $user, OwnerPayoutMethod $ownerPayoutMethod): bool
    {
        if ($user->role === 'admin') return true;
        return $user->id === $ownerPayoutMethod->owner_id;
    }

    public function delete(User $user, OwnerPayoutMethod $ownerPayoutMethod): bool
    {
        if ($user->role === 'admin') return true;
        return $user->id === $ownerPayoutMethod->owner_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, OwnerPayoutMethod $ownerPayoutMethod): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, OwnerPayoutMethod $ownerPayoutMethod): bool
    {
        return false;
    }
}
