<?php

namespace App\Policies;

use App\Models\LoyaltyPoint;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LoyaltyPointPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'guest']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, LoyaltyPoint $loyaltyPoint): bool
    {
        if ($user->role === 'admin') return true;
        return $user->id === $loyaltyPoint->guest_id;
    }

    public function create(User $user): bool
    {
        return false; // Los puntos se generan automáticamente
    }

    public function update(User $user, LoyaltyPoint $loyaltyPoint): bool
    {
        return $user->role === 'admin'; // Solo admin puede ajustar puntos
    }

    public function delete(User $user, LoyaltyPoint $loyaltyPoint): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, LoyaltyPoint $loyaltyPoint): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, LoyaltyPoint $loyaltyPoint): bool
    {
        return false;
    }
}
