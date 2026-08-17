<section class="w-full">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <flux:heading size="xl" level="1">{{ __('Ejercicios fiscales') }}</flux:heading>
            <flux:subheading size="lg">{{ __('Catálogo de ejercicios fiscales válidos para las solicitudes') }}</flux:subheading>
        </div>

        <flux:button variant="primary" icon="plus" wire:click="create">
            {{ __('Nuevo ejercicio fiscal') }}
        </flux:button>
    </div>

    <flux:table>
        <flux:table.columns>
            <flux:table.column>{{ __('Año') }}</flux:table.column>
            <flux:table.column>{{ __('Estado') }}</flux:table.column>
            <flux:table.column></flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($this->ejerciciosFiscales as $ejercicioFiscal)
                <flux:table.row :key="$ejercicioFiscal->id">
                    <flux:table.cell>{{ $ejercicioFiscal->anio }}</flux:table.cell>
                    <flux:table.cell class="py-0">
                        <flux:badge size="sm" :color="$ejercicioFiscal->activo ? 'green' : 'zinc'">
                            {{ $ejercicioFiscal->activo ? __('Activo') : __('Inactivo') }}
                        </flux:badge>
                    </flux:table.cell>
                    <flux:table.cell class="flex justify-end gap-2 py-0">
                        <flux:button variant="ghost" size="sm" icon="pencil" wire:click="edit({{ $ejercicioFiscal->id }})">
                            {{ __('Editar') }}
                        </flux:button>
                        <flux:button variant="ghost" size="sm" icon="power" wire:click="toggleActivo({{ $ejercicioFiscal->id }})">
                            {{ $ejercicioFiscal->activo ? __('Desactivar') : __('Activar') }}
                        </flux:button>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="3">{{ __('Aún no hay ejercicios fiscales registrados.') }}</flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    <flux:modal name="ejercicio-fiscal-form" class="md:w-96">
        <form wire:submit="save" class="space-y-6">
            <flux:heading size="lg">
                {{ $ejercicioFiscalId ? __('Editar ejercicio fiscal') : __('Nuevo ejercicio fiscal') }}
            </flux:heading>

            <flux:input wire:model="anio" :label="__('Año')" type="number" required autofocus />

            <div class="flex justify-end gap-2">
                <flux:spacer />
                <flux:button type="submit" variant="primary">{{ __('Guardar') }}</flux:button>
            </div>
        </form>
    </flux:modal>
</section>
