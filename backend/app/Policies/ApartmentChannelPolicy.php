<?php

namespace App\Policies;

use App\Models\ApartmentChannel;
use App\Models\User;

class ApartmentChannelPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'owner']);
    }

    public function view(User $user, ApartmentChannel $apartmentChannel): bool
    {
        if ($user->role === 'admin') return true;
        return $user->id === $apartmentChannel->accommodation()->value('owner_id');
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'owner']);
    }

    public function update(User $user, ApartmentChannel $apartmentChannel): bool
    {
        if ($user->role === 'admin') return true;
        return $user->id === $apartmentChannel->accommodation()->value('owner_id');
    }

    public function delete(User $user, ApartmentChannel $apartmentChannel): bool
    {
        if ($user->role === 'admin') return true;
        return $user->id === $apartmentChannel->accommodation()->value('owner_id');
    }

    public function restore(User $user, ApartmentChannel $apartmentChannel): bool
    {
        return false;
    }

    public function forceDelete(User $user, ApartmentChannel $apartmentChannel): bool
    {
        return false;
    }
}