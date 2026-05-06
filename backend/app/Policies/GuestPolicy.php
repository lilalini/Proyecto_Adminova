<?php

namespace App\Policies;

use App\Models\Guest;
use App\Models\User;

class GuestPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'owner']);
    }

    public function view(User $user, Guest $guest): bool
    {
        if ($user->role === 'admin') return true;

        // Owner puede ver guests — filtro en controller, no en policy
        if ($user->role === 'owner') return true;

        // Guest solo puede verse a sí mismo
        return $user->role === 'guest' && $user->id === $guest->user_id;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'owner', 'guest']);
    }

    public function update(User $user, Guest $guest): bool
    {
        if ($user->role === 'admin') return true;
        return $user->role === 'guest' && $user->id === $guest->user_id;
    }

    public function delete(User $user, Guest $guest): bool
    {
        return $user->role === 'admin';
    }
}