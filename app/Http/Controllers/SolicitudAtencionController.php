<?php

namespace App\Http\Controllers;

use App\Models\Solicitud;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SolicitudAtencionController extends Controller
{
    /**
     * Download the atención PDF the solicitante submitted for a solicitud.
     */
    public function __invoke(Solicitud $solicitud): StreamedResponse
    {
        Gate::authorize('view', $solicitud);

        $atencion = $solicitud->respuesta()->firstOrFail()->atencion()->firstOrFail();

        return Storage::disk($atencion->disco)->download($atencion->ruta, $atencion->nombre_original);
    }
}
