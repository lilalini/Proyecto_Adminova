<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\User;

class ReviewPolicy
{
    public function viewAny(User $user): bool
    {
        // Todos pueden ver reviews (público)
        return true;
    }

    public function view(User $user, Review $review): bool
    {
        // Todos pueden ver reviews (público)
        return true;
    }

    public function create(User $user): bool
    {
        // Solo guests pueden crear reviews
        return $user->role === 'guest';
    }

    public function update(User $user, Review $review): bool
    {
        // Admin puede actualizar cualquier review
        if ($user->role === 'admin') return true;
        
        // Guest solo puede actualizar su propia review si no tiene respuesta
        return $user->role === 'guest' && 
               $user->id === $review->guest_id && 
               !$review->host_response;
    }

    public function delete(User $user, Review $review): bool
    {
        // Solo admin puede eliminar reviews
        return $user->role === 'admin';
    }

    public function respond(User $user, Review $review): bool
    {
        // Owner puede responder a reviews de sus propiedades
        if ($user->role === 'owner') {
            return $user->id === $review->accommodation->owner_id;
        }
        
        // Admin también puede responder
        return $user->role === 'admin';
    }
}