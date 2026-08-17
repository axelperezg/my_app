<?php

namespace App\Enums;

enum UserRole: string
{
    case Solicitante = 'solicitante';
    case Responsable = 'responsable';
    case Admin = 'admin';

    /**
     * Get the human-readable label for the role.
     */
    public function label(): string
    {
        return match ($this) {
            self::Solicitante => __('Solicitante'),
            self::Responsable => __('Responsable'),
            self::Admin => __('Administrador'),
        };
    }
}
