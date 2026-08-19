<?php

namespace App\Livewire\Solicitudes;

use App\Actions\Solicitudes\RegistrarAtencion;
use App\Enums\RecomendacionEstatus;
use App\Models\Recomendacion;
use App\Models\Solicitud;
use Flux\Flux;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Title('Solicitud')]
class Show extends Component
{
    use WithFileUploads;

    public Solicitud $solicitud;

    public mixed $pdfAtencion = null;

    /** @var array<int, string> */
    public array $descripciones = [];

    /**
     * Mount the component.
     */
    public function mount(Solicitud $solicitud): void
    {
        Gate::authorize('view', $solicitud);

        $this->refreshSolicitud($solicitud);

        foreach ($this->recomendacionesEditables() as $recomendacion) {
            $this->descripciones[$recomendacion->id] = (string) $recomendacion->atencion_descripcion;
        }
    }

    /**
     * Reload the solicitud with the relations this page needs.
     */
    private function refreshSolicitud(Solicitud $solicitud): void
    {
        $this->solicitud = $solicitud->load([
            'institucion',
            'ejercicioFiscal',
            'archivos',
            'respuesta.recomendaciones',
            'respuesta.atencion',
        ]);
    }

    /**
     * @return array<int, Recomendacion>
     */
    public function recomendacionesEditables(): array
    {
        if (! $this->solicitud->respuesta) {
            return [];
        }

        return $this->solicitud->respuesta->recomendaciones
            ->reject(fn (Recomendacion $recomendacion) => $recomendacion->estatus === RecomendacionEstatus::Atendida)
            ->all();
    }

    /**
     * Register how each pending recomendación will be attended.
     */
    public function submit(RegistrarAtencion $registrarAtencion): void
    {
        Gate::authorize('registrarAtencion', $this->solicitud);

        $editables = $this->recomendacionesEditables();

        $rules = [
            'pdfAtencion' => ['nullable', 'file', 'mimes:pdf', 'max:20480'],
        ];

        foreach ($editables as $recomendacion) {
            $rules["descripciones.{$recomendacion->id}"] = ['required', 'string', 'max:2000'];
        }

        $validated = $this->validate($rules);

        $registrarAtencion->handle(
            solicitud: $this->solicitud,
            pdf: $validated['pdfAtencion'],
            descripciones: $validated['descripciones'] ?? [],
        );

        Flux::toast(variant: 'success', text: __('Registraste cómo atenderás las recomendaciones.'));

        $this->redirectRoute('solicitudes.index', navigate: true);
    }
}
