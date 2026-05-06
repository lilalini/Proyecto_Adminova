<?php

namespace App\Policies;

use App\Models\LoyaltySetting;
use App\Models\User;

class LoyaltySettingPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'owner']);
    }

    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function update(User $user, LoyaltySetting $loyaltySetting): bool
    {
        return $user->role === 'admin';
    }

    public function delete(User $user, LoyaltySetting $loyaltySetting): bool
    {
        return $user->role === 'admin';
    }
}