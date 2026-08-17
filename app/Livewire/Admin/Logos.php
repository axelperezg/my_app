<?php

namespace App\Livewire\Admin;

use App\Models\Configuracion;
use Flux\Flux;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Title('Logos')]
class Logos extends Component
{
    use WithFileUploads;

    public mixed $logoApp = null;

    public mixed $logoPdfIzquierdo = null;

    public mixed $logoPdfDerecho = null;

    /**
     * @return array<int, string>
     */
    private function reglasLogo(): array
    {
        return ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'];
    }

    /**
     * Save whichever logos were uploaded, replacing the previous file (if any).
     */
    public function guardar(): void
    {
        $validated = $this->validate([
            'logoApp' => $this->reglasLogo(),
            'logoPdfIzquierdo' => $this->reglasLogo(),
            'logoPdfDerecho' => $this->reglasLogo(),
        ]);

        $configuracion = Configuracion::actual();

        if ($validated['logoApp']) {
            $this->borrarSiExiste($configuracion->logo_app_path);
            $configuracion->logo_app_path = $validated['logoApp']->store('logos', 'public');
        }

        if ($validated['logoPdfIzquierdo']) {
            $this->borrarSiExiste($configuracion->logo_pdf_izquierdo_path);
            $configuracion->logo_pdf_izquierdo_path = $validated['logoPdfIzquierdo']->store('logos', 'public');
        }

        if ($validated['logoPdfDerecho']) {
            $this->borrarSiExiste($configuracion->logo_pdf_derecho_path);
            $configuracion->logo_pdf_derecho_path = $validated['logoPdfDerecho']->store('logos', 'public');
        }

        $configuracion->save();

        $this->reset(['logoApp', 'logoPdfIzquierdo', 'logoPdfDerecho']);

        Flux::toast(variant: 'success', text: __('Logos actualizados.'));

        unset($this->configuracion);
    }

    /**
     * Remove the application logo.
     */
    public function quitarLogoApp(): void
    {
        $configuracion = Configuracion::actual();
        $this->borrarSiExiste($configuracion->logo_app_path);
        $configuracion->logo_app_path = null;
        $configuracion->save();

        unset($this->configuracion);
    }

    /**
     * Remove the left PDF logo.
     */
    public function quitarLogoPdfIzquierdo(): void
    {
        $configuracion = Configuracion::actual();
        $this->borrarSiExiste($configuracion->logo_pdf_izquierdo_path);
        $configuracion->logo_pdf_izquierdo_path = null;
        $configuracion->save();

        unset($this->configuracion);
    }

    /**
     * Remove the right PDF logo.
     */
    public function quitarLogoPdfDerecho(): void
    {
        $configuracion = Configuracion::actual();
        $this->borrarSiExiste($configuracion->logo_pdf_derecho_path);
        $configuracion->logo_pdf_derecho_path = null;
        $configuracion->save();

        unset($this->configuracion);
    }

    private function borrarSiExiste(?string $ruta): void
    {
        if ($ruta !== null) {
            Storage::disk('public')->delete($ruta);
        }
    }

    #[Computed]
    public function configuracion(): Configuracion
    {
        return Configuracion::actual();
    }
}
