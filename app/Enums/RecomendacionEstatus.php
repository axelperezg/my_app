<?php

namespace App\Enums;

enum RecomendacionEstatus: string
{
    case Pendiente = 'pendiente';
    case Propuesta = 'propuesta';
    case Aceptada = 'aceptada';
    case AjusteSolicitado = 'ajuste_solicitado';

    /**
     * Get the human-readable label for the estatus.
     */
    public function label(): string
    {
        return match ($this) {
            self::Pendiente => __('Pendiente'),
            self::Propuesta => __('En revisión'),
            self::Aceptada => __('Aceptada'),
            self::AjusteSolicitado => __('Ajuste solicitado'),
        };
    }
}
