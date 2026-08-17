<?php

namespace App\Enums;

enum SolicitudEstatus: string
{
    case Recibida = 'recibida';
    case Asignada = 'asignada';
    case EnRevision = 'en_revision';
    case Respondida = 'respondida';
    case EnAtencion = 'en_atencion';
    case Cerrada = 'cerrada';

    /**
     * Get the human-readable label for the estatus.
     */
    public function label(): string
    {
        return match ($this) {
            self::Recibida => __('Recibida'),
            self::Asignada => __('Asignada'),
            self::EnRevision => __('En revisión'),
            self::Respondida => __('Respondida'),
            self::EnAtencion => __('En atención'),
            self::Cerrada => __('Cerrada'),
        };
    }
}
