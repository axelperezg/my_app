@php $maxCantidad = max(1, $this->porMes->max('cantidad') ?? 1); @endphp

<section class="w-full">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <flux:heading size="xl" level="1">{{ __('Solicitudes por mes') }}</flux:heading>
            <flux:subheading size="lg">{{ __('Número de solicitudes recibidas y páginas de PDF acumuladas, agrupadas por mes de recepción') }}</flux:subheading>
        </div>

        <flux:button variant="primary" icon="arrow-down-tray" wire:click="exportar">
            {{ __('Exportar CSV') }}
        </flux:button>
    </div>

    <flux:table>
        <flux:table.columns>
            <flux:table.column>{{ __('Mes') }}</flux:table.column>
            <flux:table.column>{{ __('Solicitudes') }}</flux:table.column>
            <flux:table.column>{{ __('Páginas') }}</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($this->porMes as $fila)
                <flux:table.row :key="$fila['mes']">
                    <flux:table.cell class="whitespace-nowrap">
                        {{ \Carbon\CarbonImmutable::createFromFormat('Y-m', $fila['mes'])->translatedFormat('F Y') }}
                    </flux:table.cell>
                    <flux:table.cell>
                        <div class="flex items-center gap-3">
                            <span class="w-8 text-right tabular-nums">{{ $fila['cantidad'] }}</span>
                            <div class="h-2 flex-1 max-w-xs rounded-full bg-zinc-100 dark:bg-zinc-700">
                                <div
                                    class="h-2 rounded-full bg-blue-500"
                                    style="width: {{ (int) round(($fila['cantidad'] / $maxCantidad) * 100) }}%"
                                ></div>
                            </div>
                        </div>
                    </flux:table.cell>
                    <flux:table.cell class="tabular-nums">{{ $fila['paginas'] }}</flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="3">{{ __('Aún no hay solicitudes recibidas.') }}</flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
</section>
