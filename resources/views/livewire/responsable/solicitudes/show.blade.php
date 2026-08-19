<section class="w-full max-w-3xl">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <flux:heading size="xl" level="1">{{ __('Folio :folio', ['folio' => $solicitud->folio]) }}</flux:heading>
            <flux:subheading size="lg">{{ $solicitud->institucion->nombre }} — {{ $solicitud->ejercicioFiscal->anio }}</flux:subheading>
        </div>

        <flux:badge color="blue">{{ $solicitud->estatus->label() }}</flux:badge>
    </div>

    <flux:card class="space-y-2">
        <flux:heading size="lg">{{ __('Datos del solicitante') }}</flux:heading>
        <flux:text>{{ $solicitud->solicitante->name }}</flux:text>
        <flux:text>{{ $solicitud->correo_electronico }}</flux:text>
        @if ($solicitud->solicitante->numero_celular)
            <flux:text>{{ $solicitud->solicitante->numero_celular }}</flux:text>
        @endif
        <flux:text>{{ $solicitud->fecha_recepcion->translatedFormat('d M Y, H:i') }}</flux:text>
    </flux:card>

    <flux:card class="mt-6 space-y-2">
        <flux:heading size="lg">{{ __('Documentos recibidos') }}</flux:heading>
        <x-solicitudes.archivos-table :solicitud="$solicitud" :archivos="$this->documentosRequeridos" />
    </flux:card>

    <flux:card class="mt-6 space-y-2">
        <flux:heading size="lg">{{ __('Muestra de Materiales') }}</flux:heading>
        @if ($this->muestraMateriales->isEmpty())
            <flux:text class="text-zinc-500 dark:text-zinc-400">
                {{ __('El solicitante no adjuntó video, audio o imágenes.') }}
            </flux:text>
        @else
            <x-solicitudes.archivos-table :solicitud="$solicitud" :archivos="$this->muestraMateriales" />
        @endif
    </flux:card>

    @if ($solicitud->respuesta)
        <flux:separator variant="subtle" class="my-6" />

        <flux:card class="space-y-2">
            <flux:heading size="lg">{{ __('Respuesta emitida') }}</flux:heading>

            @if ($solicitud->respuesta->ruta)
                <flux:text>
                    <flux:link :href="route('solicitudes.respuesta.download', $solicitud)">
                        {{ __('Descargar documento firmado con las Recomendaciones') }}
                    </flux:link>
                </flux:text>
            @endif

            @if ($solicitud->respuesta->atencion?->ruta)
                <flux:text>
                    <flux:link :href="route('solicitudes.atencion.download', $solicitud)">
                        {{ __('Descargar documento del solicitante con la atención a las recomendaciones') }}
                    </flux:link>
                </flux:text>
            @endif
        </flux:card>

        <flux:card class="mt-4">
            <ol class="list-decimal space-y-4 ps-5">
                @foreach ($solicitud->respuesta->recomendaciones as $recomendacion)
                    <li wire:key="recomendacion-{{ $recomendacion->id }}">
                        <div class="flex items-start justify-between gap-4">
                            <span>{{ $recomendacion->descripcion }}</span>
                            <flux:badge size="sm" :color="match ($recomendacion->estatus) {
                                \App\Enums\RecomendacionEstatus::Atendida => 'green',
                                \App\Enums\RecomendacionEstatus::NoAtendida => 'red',
                                default => 'zinc',
                            }">{{ $recomendacion->estatus->label() }}</flux:badge>
                        </div>

                        @if ($recomendacion->atencion_descripcion)
                            <flux:text class="mt-2 italic">{{ $recomendacion->atencion_descripcion }}</flux:text>
                        @endif

                        @if ($recomendacion->estatus === \App\Enums\RecomendacionEstatus::Pendiente && $recomendacion->atencion_descripcion && auth()->user()->can('revisarRecomendaciones', $solicitud))
                            <div class="mt-2 flex flex-wrap items-center gap-2">
                                <flux:button size="sm" variant="primary" wire:click="marcarAtendida({{ $recomendacion->id }})">
                                    {{ __('Cumple') }}
                                </flux:button>

                                <flux:button size="sm" variant="danger" wire:click="marcarNoAtendida({{ $recomendacion->id }})">
                                    {{ __('No cumple') }}
                                </flux:button>
                            </div>
                        @endif
                    </li>
                @endforeach
            </ol>
        </flux:card>
    @elseif (auth()->user()->can('responder', $solicitud))
        <flux:separator variant="subtle" class="my-6" />

        <flux:heading size="lg">{{ __('Emitir respuesta') }}</flux:heading>

        <form wire:submit="submit" class="mt-4 space-y-6">
            <flux:card class="space-y-6">
                <flux:field>
                    <flux:label>{{ __('Documento firmado con las Recomendaciones (PDF, opcional)') }}</flux:label>
                    <input type="file" wire:model="pdfRespuesta" accept="application/pdf" class="block w-full text-sm text-zinc-700 dark:text-zinc-300 file:mr-4 file:rounded-lg file:border-0 file:bg-zinc-100 file:px-4 file:py-2 file:text-sm file:font-medium hover:file:bg-zinc-200 dark:file:bg-zinc-700 dark:hover:file:bg-zinc-600" />
                    <flux:error name="pdfRespuesta" />
                </flux:field>

                <div>
                    <flux:label>{{ __('Recomendaciones') }}</flux:label>

                    <div class="mt-2 space-y-3">
                        @foreach ($recomendaciones as $index => $recomendacion)
                            <div class="flex gap-2" wire:key="recomendacion-{{ $index }}">
                                <flux:textarea wire:model="recomendaciones.{{ $index }}" rows="2" class="flex-1" />

                                @if (count($recomendaciones) > 1)
                                    <flux:button type="button" variant="ghost" icon="trash" wire:click="quitarRecomendacion({{ $index }})" />
                                @endif
                            </div>
                            <flux:error name="recomendaciones.{{ $index }}" />
                        @endforeach
                    </div>

                    <flux:button type="button" variant="ghost" size="sm" icon="plus" class="mt-2" wire:click="agregarRecomendacion">
                        {{ __('Agregar recomendación') }}
                    </flux:button>

                    <flux:error name="recomendaciones" />
                </div>

                <div class="flex justify-end">
                    <flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="submit">
                        {{ __('Enviar respuesta') }}
                    </flux:button>
                </div>
            </flux:card>
        </form>
    @endif
</section>
