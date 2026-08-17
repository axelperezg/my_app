<section class="w-full">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <flux:heading size="xl" level="1">{{ __('Páginas por solicitud') }}</flux:heading>
            <flux:subheading size="lg">{{ __('Suma de páginas de los 4 requisitos en PDF: Oficio de Entrada, Formato de Resultados, Carpeta de Resultados e Instrumento de Evaluación') }}</flux:subheading>
        </div>

        <flux:button variant="primary" icon="arrow-down-tray" wire:click="exportar">
            {{ __('Exportar CSV') }}
        </flux:button>
    </div>

    <flux:text class="mb-4">
        {{ __('Total de páginas') }}: <span class="font-semibold tabular-nums">{{ $this->filas->sum('paginas') }}</span>
    </flux:text>

    <flux:table>
        <flux:table.columns>
            <flux:table.column>{{ __('Folio') }}</flux:table.column>
            <flux:table.column>{{ __('Solicitante') }}</flux:table.column>
            <flux:table.column>{{ __('Institución') }}</flux:table.column>
            <flux:table.column>{{ __('Ejercicio fiscal') }}</flux:table.column>
            <flux:table.column>{{ __('Páginas') }}</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($this->filas as $fila)
                <flux:table.row :key="$fila['folio']">
                    <flux:table.cell variant="strong">{{ $fila['folio'] }}</flux:table.cell>
                    <flux:table.cell>{{ $fila['solicitante'] }}</flux:table.cell>
                    <flux:table.cell>{{ $fila['institucion'] }}</flux:table.cell>
                    <flux:table.cell>{{ $fila['ejercicio_fiscal'] }}</flux:table.cell>
                    <flux:table.cell class="tabular-nums">{{ $fila['paginas'] }}</flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="5">{{ __('Aún no hay solicitudes recibidas.') }}</flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
</section>
