<section class="w-full">
    <flux:heading size="xl" level="1">{{ __('Solicitudes asignadas') }}</flux:heading>
    <flux:subheading size="lg">{{ __('Solicitudes que debes atender') }}</flux:subheading>

    <flux:table class="mt-6">
        <flux:table.columns>
            <flux:table.column>{{ __('Folio') }}</flux:table.column>
            <flux:table.column>{{ __('Institución') }}</flux:table.column>
            <flux:table.column>{{ __('Ejercicio fiscal') }}</flux:table.column>
            <flux:table.column>{{ __('Estatus') }}</flux:table.column>
            <flux:table.column></flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($this->solicitudes as $solicitud)
                <flux:table.row :key="$solicitud->id">
                    <flux:table.cell variant="strong">{{ $solicitud->folio }}</flux:table.cell>
                    <flux:table.cell>{{ $solicitud->institucion->nombre }}</flux:table.cell>
                    <flux:table.cell>{{ $solicitud->ejercicioFiscal->anio }}</flux:table.cell>
                    <flux:table.cell class="py-0">
                        <flux:badge size="sm" color="blue">{{ $solicitud->estatus->label() }}</flux:badge>
                    </flux:table.cell>
                    <flux:table.cell class="py-0">
                        <flux:button variant="ghost" size="sm" :href="route('responsable.solicitudes.show', $solicitud)" wire:navigate>
                            {{ __('Ver') }}
                        </flux:button>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="5">{{ __('No tienes solicitudes asignadas por el momento.') }}</flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
</section>
