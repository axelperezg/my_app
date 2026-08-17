<section class="w-full">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <flux:heading size="xl" level="1">{{ __('Usuarios') }}</flux:heading>
            <flux:subheading size="lg">{{ __('Solicitantes y responsables del sistema') }}</flux:subheading>
        </div>

        <flux:button variant="primary" icon="user-plus" wire:click="create">
            {{ __('Nuevo usuario') }}
        </flux:button>
    </div>

    <flux:table>
        <flux:table.columns>
            <flux:table.column>{{ __('Nombre') }}</flux:table.column>
            <flux:table.column>{{ __('Correo electrónico') }}</flux:table.column>
            <flux:table.column>{{ __('Rol') }}</flux:table.column>
            <flux:table.column>{{ __('Institución') }}</flux:table.column>
            <flux:table.column></flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($this->users as $user)
                <flux:table.row :key="$user->id">
                    <flux:table.cell>{{ $user->name }}</flux:table.cell>
                    <flux:table.cell>{{ $user->email }}</flux:table.cell>
                    <flux:table.cell class="py-0">
                        <flux:badge size="sm" :color="$user->role->value === 'responsable' ? 'blue' : 'zinc'">
                            {{ $user->role->label() }}
                        </flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>{{ $user->institucion?->nombre }}</flux:table.cell>
                    <flux:table.cell class="py-0">
                        <flux:button variant="ghost" size="sm" icon="pencil" wire:click="edit({{ $user->id }})">
                            {{ __('Editar') }}
                        </flux:button>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="5">{{ __('Aún no has creado ningún usuario.') }}</flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    <flux:modal name="user-form" class="md:w-96">
        <form wire:submit="save" class="space-y-6">
            <flux:heading size="lg">
                {{ $userId ? __('Editar usuario') : __('Nuevo usuario') }}
            </flux:heading>

            <flux:input wire:model="name" :label="__('Nombre')" required autofocus />

            <flux:input wire:model="email" :label="__('Correo electrónico')" type="email" required />

            <flux:select wire:model.live="role" :label="__('Rol')" placeholder="{{ __('Selecciona un rol') }}">
                @foreach (\App\Enums\UserRole::cases() as $availableRole)
                    @unless ($availableRole === \App\Enums\UserRole::Admin)
                        <flux:select.option :value="$availableRole->value">{{ $availableRole->label() }}</flux:select.option>
                    @endunless
                @endforeach
            </flux:select>

            @if ($role === \App\Enums\UserRole::Solicitante->value)
                <flux:select wire:model="institucion_id" :label="__('Institución')" placeholder="{{ __('Selecciona una institución') }}">
                    @foreach ($this->instituciones as $institucion)
                        <flux:select.option :value="$institucion->id">{{ $institucion->nombre }}</flux:select.option>
                    @endforeach
                </flux:select>
            @endif

            <flux:input wire:model="numero_celular" :label="__('Número de celular')" type="tel" />

            <flux:input wire:model="telefono_oficina" :label="__('Teléfono de oficina')" type="tel" />

            <flux:input wire:model="direccion" :label="__('Dirección')" />

            <flux:separator variant="subtle" />

            <flux:input wire:model="password" :label="$userId ? __('Nueva contraseña (opcional)') : __('Contraseña')" type="password" viewable />

            <flux:input wire:model="password_confirmation" :label="__('Confirmar contraseña')" type="password" viewable />

            <div class="flex justify-end gap-2">
                <flux:spacer />
                <flux:button type="submit" variant="primary">{{ __('Guardar') }}</flux:button>
            </div>
        </form>
    </flux:modal>
</section>
