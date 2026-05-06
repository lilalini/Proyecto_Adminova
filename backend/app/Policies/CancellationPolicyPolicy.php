<?php

namespace App\Policies;

use App\Models\CancellationPolicy;
use App\Models\User;

class CancellationPolicyPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'owner']);
    }

    public function view(User $user, CancellationPolicy $cancellationPolicy): bool
    {
        return in_array($user->role, ['admin', 'owner']);
    }

    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function update(User $user, CancellationPolicy $cancellationPolicy): bool
    {
        return $user->role === 'admin';
    }

    public function delete(User $user, CancellationPolicy $cancellationPolicy): bool
    {
        return $user->role === 'admin';
    }
}