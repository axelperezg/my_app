<?php

namespace App\Enums;

enum EstatusArchivoSolicitud: string
{
    case SinEvaluar = 'sin_evaluar';
    case Incompleto = 'incompleto';
    case Completo = 'completo';

    /**
     * Get the human-readable label for the estatus.
     */
    public function label(): string
    {
        return match ($this) {
            self::SinEvaluar => __('Sin Evaluar'),
            self::Incompleto => __('Incompleto'),
            self::Completo => __('Completo'),
        };
    }
}
