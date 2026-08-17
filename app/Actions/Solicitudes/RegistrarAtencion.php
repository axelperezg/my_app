<?php

namespace App\Actions\Solicitudes;

use App\Enums\RecomendacionEstatus;
use App\Enums\SolicitudEstatus;
use App\Mail\AtencionRegistrada;
use App\Models\Atencion;
use App\Models\Solicitud;
use Carbon\CarbonImmutable;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use RuntimeException;

class RegistrarAtencion
{
    /**
     * Register how the solicitante will attend each not-yet-atendida recomendación,
     * attach the signed PDF, and notify the responsable.
     *
     * @param  array<int, string>  $descripciones  Keyed by recomendación id
     */
    public function handle(Solicitud $solicitud, UploadedFile $pdf, array $descripciones): Atencion
    {
        $respuesta = $solicitud->respuesta()->with(['recomendaciones', 'responsable'])->firstOrFail();

        $atencion = DB::transaction(function () use ($solicitud, $respuesta, $pdf, $descripciones) {
            $ruta = $pdf->store("solicitudes/{$solicitud->folio}/atencion", 'local');

            if ($ruta === false) {
                throw new RuntimeException('No se pudo guardar el documento de atención.');
            }

            $atencion = $respuesta->atencion()->firstOrNew();
            $atencion->disco = 'local';
            $atencion->ruta = $ruta;
            $atencion->nombre_original = $pdf->getClientOriginalName();
            $atencion->fecha_atencion = CarbonImmutable::now();
            $atencion->save();

            foreach ($respuesta->recomendaciones as $recomendacion) {
                if ($recomendacion->estatus === RecomendacionEstatus::Atendida) {
                    continue;
                }

                if (! array_key_exists($recomendacion->id, $descripciones)) {
                    continue;
                }

                $recomendacion->atencion_descripcion = $descripciones[$recomendacion->id];
                $recomendacion->estatus = RecomendacionEstatus::Pendiente;
                $recomendacion->save();
            }

            $solicitud->estatus = SolicitudEstatus::EnAtencion;
            $solicitud->save();

            return $atencion;
        });

        Mail::to($respuesta->responsable->email)->queue(new AtencionRegistrada($solicitud));

        return $atencion;
    }
}
