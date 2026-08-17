<section class="w-full">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <flux:heading size="xl" level="1">{{ __('Instituciones') }}</flux:heading>
            <flux:subheading size="lg">{{ __('Catálogo de instituciones que pueden enviar solicitudes') }}</flux:subheading>
        </div>

        <flux:button variant="primary" icon="plus" wire:click="create">
            {{ __('Nueva institución') }}
        </flux:button>
    </div>

    <flux:table>
        <flux:table.columns>
            <flux:table.column>{{ __('Nombre') }}</flux:table.column>
            <flux:table.column>{{ __('Estado') }}</flux:table.column>
            <flux:table.column></flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($this->instituciones as $institucion)
                <flux:table.row :key="$institucion->id">
                    <flux:table.cell>{{ $institucion->nombre }}</flux:table.cell>
                    <flux:table.cell class="py-0">
                        <flux:badge size="sm" :color="$institucion->activo ? 'green' : 'zinc'">
                            {{ $institucion->activo ? __('Activa') : __('Inactiva') }}
                        </flux:badge>
                    </flux:table.cell>
                    <flux:table.cell class="flex justify-end gap-2 py-0">
                        <flux:button variant="ghost" size="sm" icon="pencil" wire:click="edit({{ $institucion->id }})">
                            {{ __('Editar') }}
                        </flux:button>
                        <flux:button variant="ghost" size="sm" icon="power" wire:click="toggleActivo({{ $institucion->id }})">
                            {{ $institucion->activo ? __('Desactivar') : __('Activar') }}
                        </flux:button>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="3">{{ __('Aún no hay instituciones registradas.') }}</flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    <flux:modal name="institucion-form" class="md:w-96">
        <form wire:submit="save" class="space-y-6">
            <flux:heading size="lg">
                {{ $institucionId ? __('Editar institución') : __('Nueva institución') }}
            </flux:heading>

            <flux:input wire:model="nombre" :label="__('Nombre')" required autofocus />

            <div class="flex justify-end gap-2">
                <flux:spacer />
                <flux:button type="submit" variant="primary">{{ __('Guardar') }}</flux:button>
            </div>
        </form>
    </flux:modal>
</section>
