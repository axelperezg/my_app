@props(['solicitud', 'archivos'])

<flux:table class="mt-2">
    <flux:table.columns>
        <flux:table.column>{{ __('Documento') }}</flux:table.column>
        <flux:table.column>{{ __('Estatus') }}</flux:table.column>
    </flux:table.columns>

    <flux:table.rows>
        @foreach ($archivos as $archivo)
            <flux:table.row :key="$archivo->id">
                <flux:table.cell>
                    <flux:link :href="route('solicitudes.archivos.download', [$solicitud, $archivo])">
                        {{ $archivo->tipo->label() }} — {{ $archivo->nombre_original }}
                    </flux:link>
                </flux:table.cell>
                <flux:table.cell class="py-0">
                    <flux:select
                        wire:model.live="archivoEstatus.{{ $archivo->id }}"
                        :placeholder="__('Vacío')"
                        size="sm"
                        class="max-w-[10rem]"
                        :disabled="! auth()->user()->can('calificarArchivos', $solicitud)"
                    >
                        <flux:select.option value="incompleto">{{ __('Incompleto') }}</flux:select.option>
                        <flux:select.option value="completo">{{ __('Completo') }}</flux:select.option>
                    </flux:select>
                </flux:table.cell>
            </flux:table.row>
        @endforeach
    </flux:table.rows>
</flux:table>
