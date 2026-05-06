<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    public function viewAny(User $user): bool
    {
        return false;
    }

    public function view(User $user, Booking $booking): bool
    {
        if ($user->role === 'admin') return true;

        if ($user->role === 'owner') {
            return $user->id === $booking->accommodation()->value('owner_id');
        }

        if ($user->role === 'guest') {
            return $booking->guest_email === $user->email
                || $booking->guest_id === $user->guest?->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, Booking $booking): bool
    {
        return match ($user->role) {
            'admin' => true,
            'owner' => $user->id === $booking->accommodation()->value('owner_id'),
            default => false,
        };
    }

    public function delete(User $user, Booking $booking): bool
    {
        return $user->role === 'admin';
    }

    public function restore(User $user, Booking $booking): bool
    {
        return false;
    }

    public function forceDelete(User $user, Booking $booking): bool
    {
        return false;
    }

    public function confirmPayment(User $user, Booking $booking): bool
    {
        return $user->role === 'admin' || $user->id === $booking->guest->user_id;
    }
}