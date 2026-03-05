<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DocumentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
         return true; // Filtramos en controller
    }

    /**
     * Determine whether the user can view the model.
     */
   public function view(User $user, Document $document): bool
    {
        // Verificar si el usuario es el propietario del documento
        if ($document->documentable_type === 'App\\Models\\User') {
            return $user->id === $document->documentable_id;
        }
        
        if ($document->documentable_type === 'App\\Models\\Owner') {
            return $user->role === 'admin' || $user->id === $document->documentable_id;
        }
        
        if ($document->documentable_type === 'App\\Models\\Guest') {
            return $user->id === $document->documentable_id;
        }
        
        if ($document->documentable_type === 'App\\Models\\Accommodation') {
            return $user->role === 'admin' || 
                   ($user->role === 'owner' && $user->id === $document->documentable->owner_id);
        }
        
        return $user->role === 'admin';
    }

    public function create(User $user): bool
    {
        return true; // Cualquier usuario autenticado puede subir documentos
    }

    public function update(User $user, Document $document): bool
    {
        // Solo admin puede actualizar metadatos/verificación
        return $user->role === 'admin';
    }

    public function delete(User $user, Document $document): bool
    {
        // El propietario o admin pueden eliminar
        if ($user->role === 'admin') return true;
        
        return $user->id === $document->documentable_id;
    }

    public function verify(User $user, Document $document): bool
    {
        // Solo admin puede verificar documentos
        return $user->role === 'admin';
    }
    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Document $document): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Document $document): bool
    {
        return false;
    }
}
