<?php

namespace App\Livewire\Responsable\Solicitudes;

use App\Models\Solicitud;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Solicitudes asignadas')]
class Index extends Component
{
    /**
     * @return Collection<int, Solicitud>
     */
    #[Computed]
    public function solicitudes(): Collection
    {
        return Solicitud::query()
            ->with(['institucion', 'ejercicioFiscal'])
            ->whereBelongsTo(Auth::user(), 'responsable')
            ->latest()
            ->get();
    }
}
