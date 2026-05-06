<?php

namespace App\Policies;

use App\Models\DistributionChannel;
use App\Models\User;

class DistributionChannelPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // todos pueden ver canales
    }

    public function view(User $user, DistributionChannel $distributionChannel): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function update(User $user, DistributionChannel $distributionChannel): bool
    {
        return $user->role === 'admin';
    }

    public function delete(User $user, DistributionChannel $distributionChannel): bool
    {
        return $user->role === 'admin';
    }
}