<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    public function viewAny(User $user): bool
    {
        // Admin ve todos, owner ve de sus propiedades, guest ve los suyos
        return in_array($user->role, ['admin', 'owner', 'guest']);
    }

    public function view(User $user, Payment $payment): bool
    {
        if ($user->role === 'admin') return true;
        
        if ($user->role === 'owner') {
            return $user->id === $payment->booking?->accommodation?->owner_id;
        }
        
        return $user->id === $payment->guest_id;
    }

    public function create(User $user): bool
    {
        // Solo admin puede crear pagos manualmente
        return $user->role === 'admin';
    }

    public function update(User $user, Payment $payment): bool
    {
        // Solo admin puede actualizar pagos
        return $user->role === 'admin';
    }

    public function delete(User $user, Payment $payment): bool
    {
        // Solo admin puede eliminar pagos
        return $user->role === 'admin';
    }
}