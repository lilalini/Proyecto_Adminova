<?php

namespace App\Policies;

use App\Models\CleaningTask;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CleaningTaskPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Solo admin y staff pueden ver tareas
        return in_array($user->role, ['admin', 'staff']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, CleaningTask $cleaningTask): bool
    {
       // Admin ve todo, staff solo sus tareas asignadas
        if ($user->role === 'admin') return true;
        
        return $user->id === $cleaningTask->assigned_to_user_id;
    }

    /**
     * Determine whether the user can create models.
     */
     public function create(User $user): bool
    {
        // Solo admin puede crear tareas
        return $user->role === 'admin';
    }

    public function update(User $user, CleaningTask $cleaningTask): bool
    {
        // Admin puede actualizar cualquier tarea
        if ($user->role === 'admin') return true;
        
        // Staff puede actualizar el estado de sus tareas asignadas
        return $user->id === $cleaningTask->assigned_to_user_id;
    }

    public function delete(User $user, CleaningTask $cleaningTask): bool
    {
        return $user->role === 'admin';
    }

    public function verify(User $user, CleaningTask $cleaningTask): bool
    {
        // Solo admin puede verificar tareas completadas
        return $user->role === 'admin';
    }

    
    public function assign(User $user, CleaningTask $cleaningTask): bool
    {
        // Solo admin puede reasignar tareas
        return $user->role === 'admin';
    }
    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, CleaningTask $cleaningTask): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, CleaningTask $cleaningTask): bool
    {
        return false;
    }
}
