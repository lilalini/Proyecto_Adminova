<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;

class DocumentPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Document $document): bool
    {
        if ($user->role === 'admin') return true;

        if ($document->documentable_type === 'App\Models\Guest') {
            return $user->guest?->id === $document->documentable_id;
        }

        if ($document->documentable_type === 'App\Models\Owner') {
            return $user->role === 'owner' && $user->email === optional($document->documentable)->email;
        }

        if ($document->documentable_type === 'App\Models\Accommodation') {
            return $user->role === 'owner' &&
                   $user->email === optional($document->documentable?->owner)->email;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Document $document): bool
    {
        return $user->role === 'admin';
    }

    public function delete(User $user, Document $document): bool
    {
        if ($user->role === 'admin') return true;

        if ($document->documentable_type === 'App\Models\Guest') {
            return $user->guest?->id === $document->documentable_id;
        }

        if ($document->documentable_type === 'App\Models\Owner') {
            return $user->role === 'owner' && $user->email === optional($document->documentable)->email;
        }

        return false;
    }

    public function verify(User $user, Document $document): bool
    {
        return $user->role === 'admin';
    }

    public function restore(User $user, Document $document): bool
    {
        return false;
    }

    public function forceDelete(User $user, Document $document): bool
    {
        return false;
    }
}