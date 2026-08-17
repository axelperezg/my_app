<?php

namespace App\Actions\Solicitudes;

use App\Enums\SolicitudEstatus;
use App\Mail\RespuestaEmitida;
use App\Models\Respuesta;
use App\Models\Solicitud;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use RuntimeException;

class SubmitRespuesta
{
    /**
     * Issue the respuesta for a solicitud with its recomendaciones, and notify the solicitante.
     *
     * @param  array<int, string>  $recomendaciones  Ordered list of recomendación descriptions
     */
    public function handle(Solicitud $solicitud, User $responsable, UploadedFile $pdf, array $recomendaciones): Respuesta
    {
        $respuesta = DB::transaction(function () use ($solicitud, $responsable, $pdf, $recomendaciones) {
            $ruta = $pdf->store("solicitudes/{$solicitud->folio}/respuesta", 'local');

            if ($ruta === false) {
                throw new RuntimeException('No se pudo guardar el documento de respuesta.');
            }

            $respuesta = $solicitud->respuesta()->make([
                'disco' => 'local',
                'ruta' => $ruta,
                'nombre_original' => $pdf->getClientOriginalName(),
                'fecha_respuesta' => CarbonImmutable::now(),
            ]);
            $respuesta->responsable_id = $responsable->id;
            $respuesta->save();

            foreach (array_values($recomendaciones) as $index => $descripcion) {
                $respuesta->recomendaciones()->create([
                    'numero' => $index + 1,
                    'descripcion' => $descripcion,
                ]);
            }

            $solicitud->estatus = SolicitudEstatus::Respondida;
            $solicitud->save();

            return $respuesta;
        });

        Mail::to($solicitud->correo_electronico)->queue(new RespuestaEmitida($solicitud));

        return $respuesta;
    }
}
