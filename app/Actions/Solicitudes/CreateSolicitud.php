<?php

namespace App\Actions\Solicitudes;

use App\Enums\TipoArchivoSolicitud;
use App\Mail\SolicitudRecibida;
use App\Models\EjercicioFiscal;
use App\Models\Solicitud;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class CreateSolicitud
{
    public function __construct(private CountPdfPages $countPdfPages) {}

    /**
     * Create a solicitud, store its files, assign a folio, and email the acuse.
     *
     * @param  array{institucion_id: int, ejercicio_fiscal_id: int, correo_electronico: string}  $datos
     * @param  array<string, UploadedFile|array<int, UploadedFile>>  $archivos  Keyed by TipoArchivoSolicitud value
     */
    public function handle(User $solicitante, array $datos, array $archivos): Solicitud
    {
        $solicitud = DB::transaction(function () use ($solicitante, $datos, $archivos) {
            $solicitud = new Solicitud($datos);
            $solicitud->solicitante_id = $solicitante->id;
            $solicitud->fecha_recepcion = CarbonImmutable::now();
            $solicitud->folio = $this->generateFolio($datos['ejercicio_fiscal_id']);
            $solicitud->save();

            $this->storeArchivos($solicitud, $archivos);

            return $solicitud;
        });

        Mail::to($solicitud->correo_electronico)->queue(new SolicitudRecibida($solicitud));

        return $solicitud;
    }

    /**
     * Generate the next sequential folio for the given ejercicio fiscal.
     *
     * Locks the ejercicio fiscal row for the duration of the transaction so
     * concurrent submissions cannot compute the same consecutive number.
     */
    private function generateFolio(int $ejercicioFiscalId): string
    {
        $ejercicioFiscal = EjercicioFiscal::query()
            ->whereKey($ejercicioFiscalId)
            ->lockForUpdate()
            ->firstOrFail();

        $consecutivo = Solicitud::query()
            ->where('ejercicio_fiscal_id', $ejercicioFiscal->id)
            ->count() + 1;

        return sprintf('%d-%06d', $ejercicioFiscal->anio, $consecutivo);
    }

    /**
     * @param  array<string, UploadedFile|array<int, UploadedFile>>  $archivos
     */
    private function storeArchivos(Solicitud $solicitud, array $archivos): void
    {
        foreach ($archivos as $tipoValue => $files) {
            $tipo = TipoArchivoSolicitud::from($tipoValue);

            foreach (Arr::wrap($files) as $file) {
                $ruta = $file->store("solicitudes/{$solicitud->folio}/{$tipo->value}", 'local');

                if ($ruta === false) {
                    throw new RuntimeException('No se pudo guardar uno de los documentos de la solicitud.');
                }

                $contenido = $tipo->cuentaParaReportePaginas() ? Storage::disk('local')->get($ruta) : null;
                $paginas = $contenido !== null ? $this->countPdfPages->handle($contenido) : null;

                $solicitud->archivos()->create([
                    'tipo' => $tipo,
                    'disco' => 'local',
                    'ruta' => $ruta,
                    'nombre_original' => $file->getClientOriginalName(),
                    'mime' => $file->getMimeType(),
                    'tamano' => $file->getSize(),
                    'paginas' => $paginas,
                ]);
            }
        }
    }
}
