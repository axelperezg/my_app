<section class="w-full max-w-2xl">
    <flux:heading size="xl" level="1">{{ __('Logos') }}</flux:heading>
    <flux:subheading size="lg">{{ __('Logo de la aplicación y los logos del membrete del acuse en PDF') }}</flux:subheading>

    <form wire:submit="guardar" class="my-6 space-y-8">
        <x-admin.logo-field
            name="logoApp"
            :label="__('Logo de la aplicación')"
            :description="__('Se muestra en la barra lateral y en las pantallas de acceso.')"
            :url="$this->configuracion->logoAppUrl()"
            quitar="quitarLogoApp"
        />

        <x-admin.logo-field
            name="logoPdfIzquierdo"
            :label="__('Logo izquierdo del PDF')"
            :description="__('Aparece del lado izquierdo del membrete en el acuse de recepción.')"
            :url="$this->configuracion->logoPdfIzquierdoBase64()"
            quitar="quitarLogoPdfIzquierdo"
        />

        <x-admin.logo-field
            name="logoPdfDerecho"
            :label="__('Logo derecho del PDF')"
            :description="__('Aparece del lado derecho del membrete en el acuse de recepción.')"
            :url="$this->configuracion->logoPdfDerechoBase64()"
            quitar="quitarLogoPdfDerecho"
        />

        <div class="flex justify-end">
            <flux:button type="submit" variant="primary">{{ __('Guardar') }}</flux:button>
        </div>
    </form>
</section>
