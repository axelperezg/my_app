<?php

namespace App\Actions\Solicitudes;

use App\Models\Solicitud;
use Illuminate\Support\Facades\Storage;

class DeleteSolicitud
{
    /**
     * Delete a solicitud and everything that belongs to it: its stored files
     * (archivos, respuesta, atención — all kept under "solicitudes/{folio}")
     * and its DB records (archivos, respuesta, recomendaciones and atención
     * cascade-delete via their foreign keys).
     */
    public function handle(Solicitud $solicitud): void
    {
        Storage::disk('local')->deleteDirectory("solicitudes/{$solicitud->folio}");

        $solicitud->delete();
    }
}
