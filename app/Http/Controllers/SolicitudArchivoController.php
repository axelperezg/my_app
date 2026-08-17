<?php

namespace App\Http\Controllers;

use App\Models\Solicitud;
use App\Models\SolicitudArchivo;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SolicitudArchivoController extends Controller
{
    /**
     * Download a file attached to a solicitud.
     */
    public function __invoke(Solicitud $solicitud, SolicitudArchivo $archivo): StreamedResponse
    {
        Gate::authorize('view', $solicitud);

        abort_unless($archivo->solicitud_id === $solicitud->id, 404);

        return Storage::disk($archivo->disco)->download($archivo->ruta, $archivo->nombre_original);
    }
}
