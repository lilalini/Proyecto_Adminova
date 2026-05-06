<?php

namespace App\Policies;

use App\Models\GuestPaymentMethod;
use App\Models\User;

class GuestPaymentMethodPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'guest']);
    }

    public function view(User $user, GuestPaymentMethod $guestPaymentMethod): bool
    {
        if ($user->role === 'admin') return true;
        return $user->guest?->id === $guestPaymentMethod->guest_id;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'guest']);
    }

    public function update(User $user, GuestPaymentMethod $guestPaymentMethod): bool
    {
        if ($user->role === 'admin') return true;
        return $user->guest?->id === $guestPaymentMethod->guest_id;
    }

    public function delete(User $user, GuestPaymentMethod $guestPaymentMethod): bool
    {
        if ($user->role === 'admin') return true;
        return $user->guest?->id === $guestPaymentMethod->guest_id;
    }

    public function restore(User $user, GuestPaymentMethod $guestPaymentMethod): bool
    {
        return false;
    }

    public function forceDelete(User $user, GuestPaymentMethod $guestPaymentMethod): bool
    {
        return false;
    }
}