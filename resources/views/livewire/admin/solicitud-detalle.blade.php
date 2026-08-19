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
        <flux:text>{{ __('Responsable') }}: {{ $solicitud->responsable->name ?? __('Sin asignar') }}</flux:text>
    </flux:card>

    <flux:card class="mt-6 space-y-2">
        <flux:heading size="lg">{{ __('Documentos recibidos') }}</flux:heading>
        <ul class="mt-2 space-y-1">
            @forelse ($this->documentosRequeridos as $archivo)
                <li class="flex items-center justify-between gap-4">
                    <flux:link :href="route('solicitudes.archivos.download', [$solicitud, $archivo])">
                        {{ $archivo->tipo->label() }} — {{ $archivo->nombre_original }}
                    </flux:link>
                    <flux:badge size="sm" color="zinc">{{ $archivo->estatus->label() }}</flux:badge>
                </li>
            @empty
                <flux:text class="text-zinc-500 dark:text-zinc-400">{{ __('Sin documentos.') }}</flux:text>
            @endforelse
        </ul>
    </flux:card>

    <flux:card class="mt-6 space-y-2">
        <flux:heading size="lg">{{ __('Muestra de Materiales') }}</flux:heading>
        @if ($this->muestraMateriales->isEmpty())
            <flux:text class="text-zinc-500 dark:text-zinc-400">
                {{ __('El solicitante no adjuntó video, audio o imágenes.') }}
            </flux:text>
        @else
            <ul class="mt-2 space-y-1">
                @foreach ($this->muestraMateriales as $archivo)
                    <li class="flex items-center justify-between gap-4">
                        <flux:link :href="route('solicitudes.archivos.download', [$solicitud, $archivo])">
                            {{ $archivo->tipo->label() }} — {{ $archivo->nombre_original }}
                        </flux:link>
                        <flux:badge size="sm" color="zinc">{{ $archivo->estatus->label() }}</flux:badge>
                    </li>
                @endforeach
            </ul>
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
                    </li>
                @endforeach
            </ol>
        </flux:card>
    @endif
</section>
