<?php

namespace App\Policies;

use App\Models\Tender;
use App\Models\User;

class TenderPolicy
{
    /**
     * Determinar si el usuario puede ver todas las licitaciones
     */
    public function viewAny(User $user): bool
    {
        return true; // Todos los usuarios autenticados pueden ver licitaciones
    }

    /**
     * Determinar si el usuario puede ver una licitación específica
     */
    public function view(User $user, Tender $tender): bool
    {
        return true; // Todos los usuarios autenticados pueden ver una licitación
    }

    /**
     * Determinar si el usuario puede crear licitaciones
     * Solo usuarios con rol 'admin'
     */
    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determinar si el usuario puede actualizar una licitación
     * Solo usuarios con rol 'admin'
     */
    public function update(User $user, Tender $tender): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determinar si el usuario puede eliminar una licitación
     * Solo usuarios con rol 'admin'
     */
    public function delete(User $user, Tender $tender): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determinar si el usuario puede adjuntar productos a una licitación
     * Solo usuarios con rol 'admin'
     */
    public function attachProduct(User $user, Tender $tender): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determinar si el usuario puede desadjuntar productos de una licitación
     * Solo usuarios con rol 'admin'
     */
    public function detachProduct(User $user, Tender $tender): bool
    {
        return $user->role === 'admin';
    }
}
