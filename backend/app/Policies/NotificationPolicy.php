<?php

namespace App\Policies;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class NotificationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Todos pueden ver sus notificaciones (filtramos en controller)
    
    }

    /**
     * Determine whether the user can view the model.
     */
     public function view(User $user, Notification $notification): bool
    {
        return $user->id === $notification->notifiable_id && 
               get_class($user) === $notification->notifiable_type;
    }

    public function create(User $user): bool
    {
        return false; // Las notificaciones se crean automáticamente
    }

    public function update(User $user, Notification $notification): bool
    {
        return false; // No se actualizan, solo se marcan como leídas
    }

    public function delete(User $user, Notification $notification): bool
    {
        return $user->id === $notification->notifiable_id && 
               get_class($user) === $notification->notifiable_type;
    }

    public function markAsRead(User $user, Notification $notification): bool
    {
        return $user->id === $notification->notifiable_id && 
               get_class($user) === $notification->notifiable_type;
    }
    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Notification $notification): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Notification $notification): bool
    {
        return false;
    }
}
