<?php

namespace App\Enums;

enum EstatusArchivoSolicitud: string
{
    case Vacio = 'vacio';
    case Incompleto = 'incompleto';
    case Completo = 'completo';

    /**
     * Get the human-readable label for the estatus.
     */
    public function label(): string
    {
        return match ($this) {
            self::Vacio => __('Vacío'),
            self::Incompleto => __('Incompleto'),
            self::Completo => __('Completo'),
        };
    }
}
