<section class="w-full">
    <flux:heading size="xl" level="1">{{ __('Solicitudes') }}</flux:heading>
    <flux:subheading size="lg">{{ __('Asigna un responsable a cada solicitud recibida') }}</flux:subheading>

    <flux:table class="mt-6">
        <flux:table.columns>
            <flux:table.column>{{ __('Folio') }}</flux:table.column>
            <flux:table.column>{{ __('Solicitante') }}</flux:table.column>
            <flux:table.column>{{ __('Institución') }}</flux:table.column>
            <flux:table.column>{{ __('Estatus') }}</flux:table.column>
            <flux:table.column>{{ __('Responsable') }}</flux:table.column>
            <flux:table.column></flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($this->solicitudes as $solicitud)
                <flux:table.row :key="$solicitud->id">
                    <flux:table.cell class="py-0">
                        <flux:link :href="route('admin.solicitudes.show', $solicitud)" wire:navigate>
                            <flux:badge size="sm" color="emerald">{{ $solicitud->folio }}</flux:badge>
                        </flux:link>
                    </flux:table.cell>
                    <flux:table.cell>{{ $solicitud->solicitante->name }}</flux:table.cell>
                    <flux:table.cell>{{ $solicitud->institucion->nombre }}</flux:table.cell>
                    <flux:table.cell class="py-0">
                        <flux:badge size="sm" color="blue">{{ $solicitud->estatus->label() }}</flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>
                        <select
                            wire:change="asignar({{ $solicitud->id }}, $event.target.value)"
                            class="rounded-lg border-zinc-300 bg-white text-sm dark:border-zinc-600 dark:bg-zinc-800"
                        >
                            <option value="">{{ __('Sin asignar') }}</option>
                            @foreach ($this->responsables as $responsable)
                                <option value="{{ $responsable->id }}" @selected($solicitud->responsable_id === $responsable->id)>
                                    {{ $responsable->name }}
                                </option>
                            @endforeach
                        </select>
                    </flux:table.cell>
                    <flux:table.cell class="py-0">
                        <div class="flex justify-end gap-2">
                            <flux:modal.trigger name="eliminar-solicitud-{{ $solicitud->id }}">
                                <flux:button variant="danger" size="sm" icon="trash">
                                    {{ __('Eliminar') }}
                                </flux:button>
                            </flux:modal.trigger>

                            <flux:modal name="eliminar-solicitud-{{ $solicitud->id }}" class="max-w-lg">
                                <div class="space-y-6">
                                    <div>
                                        <flux:heading size="lg">
                                            {{ __('¿Eliminar la solicitud :folio?', ['folio' => $solicitud->folio]) }}
                                        </flux:heading>

                                        <flux:subheading>
                                            {{ __('Se eliminarán permanentemente sus documentos, respuesta y recomendaciones. Esta acción no se puede deshacer.') }}
                                        </flux:subheading>
                                    </div>

                                    <div class="flex justify-end gap-2">
                                        <flux:modal.close>
                                            <flux:button variant="filled">{{ __('Cancelar') }}</flux:button>
                                        </flux:modal.close>

                                        <flux:button variant="danger" wire:click="eliminar({{ $solicitud->id }})">
                                            {{ __('Eliminar') }}
                                        </flux:button>
                                    </div>
                                </div>
                            </flux:modal>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="6">{{ __('Aún no se ha recibido ninguna solicitud.') }}</flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
</section>
