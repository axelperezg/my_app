<?php

namespace App\Http\Controllers;

use App\Models\Solicitud;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SolicitudRespuestaController extends Controller
{
    /**
     * Download the respuesta PDF issued for a solicitud.
     */
    public function __invoke(Solicitud $solicitud): StreamedResponse
    {
        Gate::authorize('view', $solicitud);

        $respuesta = $solicitud->respuesta()->firstOrFail();

        return Storage::disk($respuesta->disco)->download($respuesta->ruta, $respuesta->nombre_original);
    }
}
