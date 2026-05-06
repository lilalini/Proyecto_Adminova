<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'owner', 'guest']);
    }

    public function view(User $user, Payment $payment): bool
    {
        if ($user->role === 'admin') return true;

        if ($user->role === 'owner') {
            return $user->email === $payment->booking?->accommodation?->owner?->email;
        }

        return $user->guest?->id === $payment->guest_id;
    }

    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function update(User $user, Payment $payment): bool
    {
        return $user->role === 'admin';
    }

    public function delete(User $user, Payment $payment): bool
    {
        return $user->role === 'admin';
    }
}