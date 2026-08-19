<section class="w-full">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <flux:heading size="xl" level="1">{{ __('Mis solicitudes') }}</flux:heading>
            <flux:subheading size="lg">{{ __('Solicitudes que has enviado y su estatus') }}</flux:subheading>
        </div>

        <flux:button variant="primary" icon="plus" :href="route('solicitudes.create')" wire:navigate>
            {{ __('Nueva solicitud') }}
        </flux:button>
    </div>

    <flux:table>
        <flux:table.columns>
            <flux:table.column>{{ __('Folio') }}</flux:table.column>
            <flux:table.column>{{ __('Institución') }}</flux:table.column>
            <flux:table.column>{{ __('Ejercicio fiscal') }}</flux:table.column>
            <flux:table.column>{{ __('Estatus') }}</flux:table.column>
            <flux:table.column>{{ __('Fecha de recepción') }}</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($this->solicitudes as $solicitud)
                <flux:table.row :key="$solicitud->id">
                    <flux:table.cell class="py-0">
                        <flux:link :href="route('solicitudes.show', $solicitud)" wire:navigate>
                            <flux:badge size="sm" color="emerald">{{ $solicitud->folio }}</flux:badge>
                        </flux:link>
                    </flux:table.cell>
                    <flux:table.cell>{{ $solicitud->institucion->nombre }}</flux:table.cell>
                    <flux:table.cell>{{ $solicitud->ejercicioFiscal->anio }}</flux:table.cell>
                    <flux:table.cell class="py-0">
                        <flux:badge size="sm" color="blue">{{ $solicitud->estatus->label() }}</flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>{{ $solicitud->fecha_recepcion->translatedFormat('d M Y, H:i') }}</flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="5">
                        {{ __('Aún no has enviado ninguna solicitud.') }}
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
</section>
