<?php

namespace App\Livewire\Solicitudes;

use App\Models\Solicitud;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Mis solicitudes')]
class Index extends Component
{
    /**
     * Mount the component.
     */
    public function mount(): void
    {
        Gate::authorize('viewAny', Solicitud::class);
    }

    /**
     * @return Collection<int, Solicitud>
     */
    #[Computed]
    public function solicitudes(): Collection
    {
        return Solicitud::query()
            ->with(['institucion', 'ejercicioFiscal'])
            ->whereBelongsTo(Auth::user(), 'solicitante')
            ->latest()
            ->get();
    }
}
