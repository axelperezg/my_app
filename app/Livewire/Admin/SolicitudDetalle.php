<?php

namespace App\Livewire\Admin;

use App\Models\Solicitud;
use App\Models\SolicitudArchivo;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Solicitud')]
class SolicitudDetalle extends Component
{
    public Solicitud $solicitud;

    /**
     * Mount the component.
     */
    public function mount(Solicitud $solicitud): void
    {
        $this->solicitud = $solicitud->load([
            'solicitante',
            'institucion',
            'ejercicioFiscal',
            'responsable',
            'archivos',
            'respuesta.recomendaciones',
            'respuesta.atencion',
        ]);
    }

    /**
     * The 5 required requisitos, in a fixed order.
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
}
