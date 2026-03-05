<?php

namespace App\Policies;

use App\Models\GuestPaymentMethod;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class GuestPaymentMethodPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Admin puede ver todos, guest solo los suyos
        return in_array($user->role, ['admin', 'guest']);
    }

    /**
     * Determine whether the user can view the model.
     */
     public function view(User $user, GuestPaymentMethod $guestPaymentMethod): bool
    {
        if ($user->role === 'admin') return true;
        return $user->id === $guestPaymentMethod->guest_id;
    }

    public function create(User $user): bool
    {
        // Admin y guest pueden crear
        return in_array($user->role, ['admin', 'guest']);
    }

    public function update(User $user, GuestPaymentMethod $guestPaymentMethod): bool
    {
        if ($user->role === 'admin') return true;
        return $user->id === $guestPaymentMethod->guest_id;
    }

    public function delete(User $user, GuestPaymentMethod $guestPaymentMethod): bool
    {
        if ($user->role === 'admin') return true;
        return $user->id === $guestPaymentMethod->guest_id;
    }
    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, GuestPaymentMethod $guestPaymentMethod): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, GuestPaymentMethod $guestPaymentMethod): bool
    {
        return false;
    }
}
