<?php

namespace App\Policies;

use App\Models\AvailabilityCalendar;
use App\Models\User;

class AvailabilityCalendarPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, AvailabilityCalendar $availabilityCalendar): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'owner']);
    }

    public function update(User $user, AvailabilityCalendar $availabilityCalendar): bool
    {
        if ($user->role === 'admin') return true;
        return $user->role === 'owner' && 
               $user->id === $availabilityCalendar->accommodation()->value('owner_id');
    }

    public function delete(User $user, AvailabilityCalendar $availabilityCalendar): bool
    {
        if ($user->role === 'admin') return true;
        return $user->role === 'owner' && 
               $user->id === $availabilityCalendar->accommodation()->value('owner_id');
    }

    public function restore(User $user, AvailabilityCalendar $availabilityCalendar): bool
    {
        return false;
    }

    public function forceDelete(User $user, AvailabilityCalendar $availabilityCalendar): bool
    {
        return false;
    }
}