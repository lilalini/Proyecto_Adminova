<?php

namespace App\Policies;

use App\Models\AvailabilityCalendar;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AvailabilityCalendarPolicy
{
    /**
     * Determine whether the user can view any models.
     */
public function viewAny(User $user): bool
    {
        // Todos pueden ver disponibilidad (público)
        return true;
    }

    public function view(User $user, AvailabilityCalendar $availabilityCalendar): bool
    {
        // Todos pueden ver disponibilidad
        return true;
    }

    public function create(User $user): bool
    {
        // Solo admin y owner pueden crear/modificar disponibilidad
        return in_array($user->role, ['admin', 'owner']);
    }

     public function update(User $user, AvailabilityCalendar $availabilityCalendar): bool
    {
        return $user->role === 'admin' || 
               ($user->role === 'owner' && $user->id === $availabilityCalendar->accommodation->owner_id);
    }

    public function delete(User $user, AvailabilityCalendar $availabilityCalendar): bool
    {
        return $user->role === 'admin' || 
               ($user->role === 'owner' && $user->id === $availabilityCalendar->accommodation->owner_id);
    }
    public function restore(User $user, AvailabilityCalendar $availability): bool
    {
        return false;
    }

    public function forceDelete(User $user, AvailabilityCalendar $availability): bool
    {
        return false;
    }
}
