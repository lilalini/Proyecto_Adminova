<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\User;

class ReviewPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Review $review): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->role === 'guest';
    }

    public function update(User $user, Review $review): bool
    {
        if ($user->role === 'admin') return true;
        return $user->role === 'guest' &&
               $user->guest?->id === $review->guest_id &&
               !$review->host_response;
    }

    public function delete(User $user, Review $review): bool
    {
        return $user->role === 'admin';
    }

    public function respond(User $user, Review $review): bool
    {
        if ($user->role === 'admin') return true;
        return $user->role === 'owner' &&
               $user->email === optional($review->accommodation?->owner)->email;
    }
}