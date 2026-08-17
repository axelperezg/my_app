<?php

namespace App\Policies;

use App\Enums\SolicitudEstatus;
use App\Enums\UserRole;
use App\Models\Solicitud;
use App\Models\User;

class SolicitudPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->role === UserRole::Solicitante;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Solicitud $solicitud): bool
    {
        return $solicitud->solicitante_id === $user->id
            || $solicitud->responsable_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role === UserRole::Solicitante;
    }

    /**
     * Determine whether the user can issue a respuesta for the solicitud.
     */
    public function responder(User $user, Solicitud $solicitud): bool
    {
        return $solicitud->responsable_id === $user->id
            && $solicitud->estatus === SolicitudEstatus::Asignada;
    }

    /**
     * Determine whether the user can calificar (Incompleto/Completo) the documentos
     * recibidos. Unlike responder(), this stays open for the whole life of the
     * solicitud — not just while it's Asignada — until it's Cerrada.
     */
    public function calificarArchivos(User $user, Solicitud $solicitud): bool
    {
        return $solicitud->responsable_id === $user->id
            && $solicitud->estatus !== SolicitudEstatus::Cerrada;
    }

    /**
     * Determine whether the user can register how they'll attend the recomendaciones.
     */
    public function registrarAtencion(User $user, Solicitud $solicitud): bool
    {
        return $solicitud->solicitante_id === $user->id
            && in_array($solicitud->estatus, [SolicitudEstatus::Respondida, SolicitudEstatus::EnAtencion], strict: true);
    }

    /**
     * Determine whether the user can review the solicitante's proposed atención.
     */
    public function revisarRecomendaciones(User $user, Solicitud $solicitud): bool
    {
        return $solicitud->responsable_id === $user->id
            && $solicitud->estatus === SolicitudEstatus::EnAtencion;
    }
}
