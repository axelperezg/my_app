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
     *
     * Files may live on more than one disco: solicitudes created before the
     * move to R2 have their files on "local", newer ones on "s3". Delete the
     * folder from every disco actually used instead of assuming one.
     */
    public function handle(Solicitud $solicitud): void
    {
        $discos = $solicitud->archivos->pluck('disco')
            ->push($solicitud->respuesta?->disco)
            ->push($solicitud->respuesta?->atencion?->disco)
            ->filter()
            ->unique();

        foreach ($discos as $disco) {
            Storage::disk($disco)->deleteDirectory("solicitudes/{$solicitud->folio}");
        }

        $solicitud->delete();
    }
}
