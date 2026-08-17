<section class="w-full max-w-3xl">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <flux:heading size="xl" level="1">{{ __('Folio :folio', ['folio' => $solicitud->folio]) }}</flux:heading>
            <flux:subheading size="lg">{{ $solicitud->institucion->nombre }} — {{ $solicitud->ejercicioFiscal->anio }}</flux:subheading>
        </div>

        <flux:badge color="blue">{{ $solicitud->estatus->label() }}</flux:badge>
    </div>

    <flux:heading size="lg">{{ __('Documentos enviados') }}</flux:heading>
    <ul class="mt-2 space-y-1">
        @foreach ($solicitud->archivos as $archivo)
            <li>
                <flux:link :href="route('solicitudes.archivos.download', [$solicitud, $archivo])">
                    {{ $archivo->tipo->label() }} — {{ $archivo->nombre_original }}
                </flux:link>
            </li>
        @endforeach
    </ul>

    @if (! $solicitud->respuesta)
        <flux:callout class="mt-6" icon="clock" :heading="__('Aún sin respuesta')">
            {{ __('Tu solicitud está siendo revisada. Te avisaremos por correo cuando tengas una respuesta.') }}
        </flux:callout>
    @else
        <flux:separator variant="subtle" class="my-6" />

        <flux:heading size="lg">{{ __('Recomendaciones') }}</flux:heading>
        <flux:text class="mt-2">
            <flux:link :href="route('solicitudes.respuesta.download', $solicitud)">
                {{ __('Descargar documento firmado con las Recomendaciones') }}
            </flux:link>
        </flux:text>

        @if ($solicitud->respuesta->atencion)
            <flux:text class="mt-1">
                <flux:link :href="route('solicitudes.atencion.download', $solicitud)">
                    {{ __('Descargar tu documento de atención a las recomendaciones') }}
                </flux:link>
            </flux:text>
        @endif

        <form wire:submit="submit" class="mt-4 space-y-6">
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

                        @if ($recomendacion->estatus === \App\Enums\RecomendacionEstatus::Atendida)
                            <flux:text class="mt-2 italic">{{ $recomendacion->atencion_descripcion }}</flux:text>
                        @elseif (auth()->user()->can('registrarAtencion', $solicitud))
                            <flux:field class="mt-2">
                                <flux:label>{{ __('¿Cómo atenderás esta recomendación?') }}</flux:label>
                                <flux:textarea wire:model="descripciones.{{ $recomendacion->id }}" rows="2" />
                                <flux:error name="descripciones.{{ $recomendacion->id }}" />
                            </flux:field>
                        @elseif ($recomendacion->atencion_descripcion)
                            <flux:text class="mt-2 italic">{{ $recomendacion->atencion_descripcion }}</flux:text>
                        @endif
                    </li>
                @endforeach
            </ol>

            @can('registrarAtencion', $solicitud)
                <flux:field>
                    <flux:label>{{ __('Documento donde señalas cómo atenderás las recomendaciones (PDF)') }}</flux:label>
                    <input type="file" wire:model="pdfAtencion" accept="application/pdf" class="block w-full text-sm text-zinc-700 dark:text-zinc-300 file:mr-4 file:rounded-lg file:border-0 file:bg-zinc-100 file:px-4 file:py-2 file:text-sm file:font-medium hover:file:bg-zinc-200 dark:file:bg-zinc-700 dark:hover:file:bg-zinc-600" />
                    <flux:error name="pdfAtencion" />
                </flux:field>

                <div class="flex justify-end">
                    <flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="submit">
                        {{ __('Enviar') }}
                    </flux:button>
                </div>
            @endcan
        </form>
    @endif
</section>
