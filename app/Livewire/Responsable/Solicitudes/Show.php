<?php

namespace App\Livewire\Responsable\Solicitudes;

use App\Actions\Solicitudes\SubmitRespuesta;
use App\Enums\EstatusArchivoSolicitud;
use App\Enums\RecomendacionEstatus;
use App\Enums\SolicitudEstatus;
use App\Mail\SolicitudCerrada;
use App\Models\Recomendacion;
use App\Models\Solicitud;
use App\Models\SolicitudArchivo;
use Flux\Flux;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Title('Solicitud')]
class Show extends Component
{
    use WithFileUploads;

    public Solicitud $solicitud;

    public mixed $pdfRespuesta = null;

    /** @var array<int, string> */
    public array $recomendaciones = [''];

    /** @var array<int, string> */
    public array $comentarios = [];

    /** @var array<int, string> Keyed by archivo id; '' means EstatusArchivoSolicitud::Vacio. */
    public array $archivoEstatus = [];

    /**
     * Mount the component.
     */
    public function mount(Solicitud $solicitud): void
    {
        Gate::authorize('view', $solicitud);

        $this->refreshSolicitud($solicitud);
    }

    /**
     * Reload the solicitud with the relations this page needs.
     */
    private function refreshSolicitud(Solicitud $solicitud): void
    {
        $this->solicitud = $solicitud->load([
            'solicitante',
            'institucion',
            'ejercicioFiscal',
            'archivos',
            'respuesta.recomendaciones',
            'respuesta.atencion',
        ]);

        $this->archivoEstatus = $this->solicitud->archivos
            ->mapWithKeys(fn ($archivo) => [
                $archivo->id => $archivo->estatus === EstatusArchivoSolicitud::Vacio ? '' : $archivo->estatus->value,
            ])
            ->all();
    }

    /**
     * Persist the estatus chosen for a single documento.
     */
    public function updatedArchivoEstatus(string $value, string $archivoId): void
    {
        Gate::authorize('responder', $this->solicitud);

        $archivo = $this->solicitud->archivos->firstWhere('id', (int) $archivoId);

        abort_unless($archivo !== null, 404);

        $archivo->estatus = filled($value) ? EstatusArchivoSolicitud::from($value) : EstatusArchivoSolicitud::Vacio;
        $archivo->save();
    }

    /**
     * The 5 required requisitos, in a fixed order (Oficio de Entrada, Formato de
     * Resultados PDF, Formato de Resultados Excel, Carpeta de Resultados,
     * Instrumento de Evaluación).
     *
     * @return Collection<int, SolicitudArchivo>
     */
    #[Computed]
    public function documentosRequeridos(): Collection
    {
        return $this->solicitud->archivos->reject(fn (SolicitudArchivo $archivo) => $archivo->tipo->multiple());
    }

    /**
     * The Video/Audio/Imágenes files the solicitante attached as muestra de materiales.
     *
     * @return Collection<int, SolicitudArchivo>
     */
    #[Computed]
    public function muestraMateriales(): Collection
    {
        return $this->solicitud->archivos->filter(fn (SolicitudArchivo $archivo) => $archivo->tipo->multiple());
    }

    /**
     * Add another empty recomendación row.
     */
    public function agregarRecomendacion(): void
    {
        $this->recomendaciones[] = '';
    }

    /**
     * Remove a recomendación row.
     */
    public function quitarRecomendacion(int $index): void
    {
        unset($this->recomendaciones[$index]);

        $this->recomendaciones = array_values($this->recomendaciones);

        if ($this->recomendaciones === []) {
            $this->recomendaciones = [''];
        }
    }

    /**
     * Submit the respuesta for this solicitud.
     */
    public function submit(SubmitRespuesta $submitRespuesta): void
    {
        Gate::authorize('responder', $this->solicitud);

        $validated = $this->validate([
            'pdfRespuesta' => ['required', 'file', 'mimes:pdf', 'max:20480'],
            'recomendaciones' => ['required', 'array', 'min:1'],
            'recomendaciones.*' => ['required', 'string', 'max:2000'],
        ]);

        $submitRespuesta->handle(
            solicitud: $this->solicitud,
            responsable: Auth::user(),
            pdf: $validated['pdfRespuesta'],
            recomendaciones: $validated['recomendaciones'],
        );

        Flux::toast(variant: 'success', text: __('Respuesta enviada al solicitante.'));

        $this->redirectRoute('responsable.solicitudes.index', navigate: true);
    }

    /**
     * Accept the solicitante's proposed atención for a recomendación.
     */
    public function aceptar(Recomendacion $recomendacion): void
    {
        Gate::authorize('revisarRecomendaciones', $this->solicitud);

        abort_unless($recomendacion->respuesta_id === $this->solicitud->respuesta->id, 404);

        $recomendacion->estatus = RecomendacionEstatus::Aceptada;
        $recomendacion->comentario_responsable = null;
        $recomendacion->save();

        $this->cerrarSiTodoAceptado();

        Flux::toast(variant: 'success', text: __('Recomendación aceptada.'));

        $this->refreshSolicitud($this->solicitud);
    }

    /**
     * Request an adjustment from the solicitante for a recomendación.
     */
    public function pedirAjuste(Recomendacion $recomendacion): void
    {
        Gate::authorize('revisarRecomendaciones', $this->solicitud);

        abort_unless($recomendacion->respuesta_id === $this->solicitud->respuesta->id, 404);

        $comentario = trim($this->comentarios[$recomendacion->id] ?? '');

        if ($comentario === '') {
            $this->addError('comentarios.'.$recomendacion->id, __('Escribe qué debe ajustar el solicitante.'));

            return;
        }

        $recomendacion->estatus = RecomendacionEstatus::AjusteSolicitado;
        $recomendacion->comentario_responsable = $comentario;
        $recomendacion->save();

        unset($this->comentarios[$recomendacion->id]);

        Flux::toast(text: __('Se solicitó un ajuste al solicitante.'));

        $this->refreshSolicitud($this->solicitud);
    }

    /**
     * Close the solicitud once every recomendación has been accepted.
     */
    private function cerrarSiTodoAceptado(): void
    {
        $solicitud = $this->solicitud;

        $todasAceptadas = $solicitud->respuesta->recomendaciones()
            ->get()
            ->every(fn (Recomendacion $recomendacion) => $recomendacion->estatus === RecomendacionEstatus::Aceptada);

        if (! $todasAceptadas) {
            return;
        }

        $solicitud->estatus = SolicitudEstatus::Cerrada;
        $solicitud->save();

        Mail::to($solicitud->correo_electronico)->queue(new SolicitudCerrada($solicitud));
    }
}
