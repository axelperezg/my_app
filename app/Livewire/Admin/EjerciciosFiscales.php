<?php

namespace App\Livewire\Admin;

use App\Models\EjercicioFiscal;
use Flux\Flux;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Ejercicios fiscales')]
class EjerciciosFiscales extends Component
{
    public ?int $ejercicioFiscalId = null;

    public string $anio = '';

    /**
     * Open the form to create a new ejercicio fiscal.
     */
    public function create(): void
    {
        $this->reset(['ejercicioFiscalId', 'anio']);

        Flux::modal('ejercicio-fiscal-form')->show();
    }

    /**
     * Open the form to edit an existing ejercicio fiscal.
     */
    public function edit(EjercicioFiscal $ejercicioFiscal): void
    {
        $this->ejercicioFiscalId = $ejercicioFiscal->id;
        $this->anio = (string) $ejercicioFiscal->anio;

        Flux::modal('ejercicio-fiscal-form')->show();
    }

    /**
     * Save the ejercicio fiscal being created or edited.
     */
    public function save(): void
    {
        $validated = $this->validate([
            'anio' => [
                'required',
                'integer',
                'digits:4',
                Rule::unique(EjercicioFiscal::class, 'anio')->ignore($this->ejercicioFiscalId),
            ],
        ]);

        EjercicioFiscal::updateOrCreate(['id' => $this->ejercicioFiscalId], $validated);

        $this->reset(['ejercicioFiscalId', 'anio']);

        Flux::modal('ejercicio-fiscal-form')->close();

        Flux::toast(variant: 'success', text: __('Ejercicio fiscal guardado.'));

        unset($this->ejerciciosFiscales);
    }

    /**
     * Toggle whether the ejercicio fiscal is active.
     */
    public function toggleActivo(EjercicioFiscal $ejercicioFiscal): void
    {
        $ejercicioFiscal->update(['activo' => ! $ejercicioFiscal->activo]);

        unset($this->ejerciciosFiscales);
    }

    /**
     * @return Collection<int, EjercicioFiscal>
     */
    #[Computed]
    public function ejerciciosFiscales(): Collection
    {
        return EjercicioFiscal::query()->orderByDesc('anio')->get();
    }
}
