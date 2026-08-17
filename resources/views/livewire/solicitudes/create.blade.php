<section class="w-full max-w-2xl">
    <flux:heading size="xl" level="1">{{ __('Nueva solicitud') }}</flux:heading>
    <flux:subheading size="lg">{{ __('Completa los datos y adjunta los documentos requeridos') }}</flux:subheading>

    @unless ($this->institucionValida)
        <flux:callout class="mt-6" icon="exclamation-triangle" variant="danger" :heading="__('Tu cuenta no tiene una institución activa asignada')">
            {{ __('Contacta al administrador del sistema para que la asigne antes de enviar una solicitud.') }}
        </flux:callout>
    @else
        <flux:text class="mt-2">
            {{ __('Institución') }}: <span class="font-medium">{{ auth()->user()->institucion->nombre }}</span>
        </flux:text>

        <form wire:submit="submit" class="my-6 space-y-6">
            <flux:select wire:model="ejercicio_fiscal_id" :label="__('Ejercicio fiscal')" placeholder="{{ __('Selecciona un ejercicio') }}" class="sm:max-w-xs">
                @foreach ($this->ejerciciosFiscales as $ejercicioFiscal)
                    <flux:select.option :value="$ejercicioFiscal->id">{{ $ejercicioFiscal->anio }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:input wire:model="correo_electronico" :label="__('Correo electrónico')" type="email" class="sm:max-w-xs" required />

            <flux:separator variant="subtle" />

            <flux:heading size="lg">{{ __('Requisitos') }}</flux:heading>

            <div class="grid gap-6">
                <x-solicitudes.file-field name="oficioEntrada" :label="__('Oficio de Entrada (PDF)')" accept="application/pdf" />
                <x-solicitudes.file-field name="formatoResultadosPdf" :label="__('Formato de Resultados (PDF)')" accept="application/pdf" />
                <x-solicitudes.file-field name="formatoResultadosExcel" :label="__('Formato de Resultados (Excel)')" accept=".xlsx,.xls" />
                <x-solicitudes.file-field name="carpetaResultados" :label="__('Carpeta de Resultados (PDF)')" accept="application/pdf" />
                <x-solicitudes.file-field name="instrumentoEvaluacion" :label="__('Instrumento de Evaluación (PDF)')" accept="application/pdf" />
                <x-solicitudes.file-field name="videos" :label="__('Video (opcional)')" accept="video/*" multiple />
                <x-solicitudes.file-field name="audios" :label="__('Audio (opcional)')" accept="audio/*" multiple />
                <x-solicitudes.file-field name="imagenes" :label="__('Imágenes (opcional)')" accept="image/*" multiple />
            </div>

            <div class="flex justify-end gap-2">
                <flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="submit">
                    {{ __('Enviar solicitud') }}
                </flux:button>
            </div>
        </form>
    @endunless
</section>
