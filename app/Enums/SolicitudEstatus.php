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
            self::Recibida => __('Enviado para Evaluación'),
            self::Asignada => __('Asignada'),
            self::EnRevision => __('En revisión'),
            self::Respondida => __('Recomendaciones DGNC'),
            self::EnAtencion => __('Con atención a Recomendaciones'),
            self::Cerrada => __('Cerrada'),
        };
    }
}
