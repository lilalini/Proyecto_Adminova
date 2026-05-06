<?php

namespace App\Policies;

use App\Models\Owner;
use App\Models\User;

class OwnerPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function view(User $user, Owner $owner): bool
    {
        if ($user->role === 'admin') return true;
        // Comparar por email mientras Owner y User sean tablas separadas
        return $user->role === 'owner' && $user->email === $owner->email;
    }

    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function update(User $user, Owner $owner): bool
    {
        if ($user->role === 'admin') return true;
        return $user->role === 'owner' && $user->email === $owner->email;
    }

    public function delete(User $user, Owner $owner): bool
    {
        return $user->role === 'admin';
    }
}