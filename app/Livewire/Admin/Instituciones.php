<?php

namespace App\Livewire\Admin;

use App\Models\Institucion;
use Flux\Flux;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Instituciones')]
class Instituciones extends Component
{
    public ?int $institucionId = null;

    public string $nombre = '';

    /**
     * Open the form to create a new institución.
     */
    public function create(): void
    {
        $this->reset(['institucionId', 'nombre']);

        Flux::modal('institucion-form')->show();
    }

    /**
     * Open the form to edit an existing institución.
     */
    public function edit(Institucion $institucion): void
    {
        $this->institucionId = $institucion->id;
        $this->nombre = $institucion->nombre;

        Flux::modal('institucion-form')->show();
    }

    /**
     * Save the institución being created or edited.
     */
    public function save(): void
    {
        $validated = $this->validate([
            'nombre' => [
                'required',
                'string',
                'max:255',
                Rule::unique(Institucion::class, 'nombre')->ignore($this->institucionId),
            ],
        ]);

        Institucion::updateOrCreate(['id' => $this->institucionId], $validated);

        $this->reset(['institucionId', 'nombre']);

        Flux::modal('institucion-form')->close();

        Flux::toast(variant: 'success', text: __('Institución guardada.'));

        unset($this->instituciones);
    }

    /**
     * Toggle whether the institución is active.
     */
    public function toggleActivo(Institucion $institucion): void
    {
        $institucion->update(['activo' => ! $institucion->activo]);

        unset($this->instituciones);
    }

    /**
     * @return Collection<int, Institucion>
     */
    #[Computed]
    public function instituciones(): Collection
    {
        return Institucion::query()->orderBy('nombre')->get();
    }
}
