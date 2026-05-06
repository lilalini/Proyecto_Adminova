<?php

namespace App\Policies;

use App\Models\OwnerPayoutMethod;
use App\Models\User;

class OwnerPayoutMethodPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'owner']);
    }

    public function view(User $user, OwnerPayoutMethod $ownerPayoutMethod): bool
    {
        if ($user->role === 'admin') return true;
        return $user->role === 'owner' &&
               $user->email === $ownerPayoutMethod->owner()->value('email');
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'owner']);
    }

    public function update(User $user, OwnerPayoutMethod $ownerPayoutMethod): bool
    {
        if ($user->role === 'admin') return true;
        return $user->role === 'owner' &&
               $user->email === $ownerPayoutMethod->owner()->value('email');
    }

    public function delete(User $user, OwnerPayoutMethod $ownerPayoutMethod): bool
    {
        if ($user->role === 'admin') return true;
        return $user->role === 'owner' &&
               $user->email === $ownerPayoutMethod->owner()->value('email');
    }

    public function restore(User $user, OwnerPayoutMethod $ownerPayoutMethod): bool
    {
        return false;
    }

    public function forceDelete(User $user, OwnerPayoutMethod $ownerPayoutMethod): bool
    {
        return false;
    }
}