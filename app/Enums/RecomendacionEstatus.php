<?php

namespace App\Enums;

enum RecomendacionEstatus: string
{
    case Pendiente = 'pendiente';
    case Atendida = 'atendida';
    case NoAtendida = 'no_atendida';

    /**
     * Get the human-readable label for the estatus.
     */
    public function label(): string
    {
        return match ($this) {
            self::Pendiente => __('Pendiente'),
            self::Atendida => __('Atendida'),
            self::NoAtendida => __('No atendida'),
        };
    }
}
