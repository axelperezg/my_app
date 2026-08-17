<?php

use App\Enums\UserRole;
use App\Livewire\Admin\Users;
use App\Models\Institucion;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

test('admins can create a solicitante account with an institución and password', function () {
    $admin = User::factory()->admin()->create();
    $institucion = Institucion::factory()->create();

    Livewire::actingAs($admin)
        ->test(Users::class)
        ->call('create')
        ->set('name', 'Juana Pérez')
        ->set('email', 'juana@example.com')
        ->set('role', UserRole::Solicitante->value)
        ->set('institucion_id', (string) $institucion->id)
        ->set('numero_celular', '5512345678')
        ->set('direccion', 'Av. Siempre Viva 123')
        ->set('telefono_oficina', '5587654321')
        ->set('password', 'contraseña-segura')
        ->set('password_confirmation', 'contraseña-segura')
        ->call('save')
        ->assertHasNoErrors();

    $creado = User::query()->where('email', 'juana@example.com')->firstOrFail();

    expect($creado->role)->toBe(UserRole::Solicitante)
        ->and($creado->institucion_id)->toBe($institucion->id)
        ->and($creado->numero_celular)->toBe('5512345678')
        ->and($creado->direccion)->toBe('Av. Siempre Viva 123')
        ->and($creado->telefono_oficina)->toBe('5587654321')
        ->and($creado->email_verified_at)->not->toBeNull()
        ->and(Hash::check('contraseña-segura', $creado->password))->toBeTrue();
});

test('numero_celular, dirección and teléfono de oficina are optional', function () {
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin)
        ->test(Users::class)
        ->call('create')
        ->set('name', 'Sin datos de contacto')
        ->set('email', 'sin-contacto@example.com')
        ->set('role', UserRole::Responsable->value)
        ->set('password', 'contraseña-segura')
        ->set('password_confirmation', 'contraseña-segura')
        ->call('save')
        ->assertHasNoErrors();

    $creado = User::query()->where('email', 'sin-contacto@example.com')->firstOrFail();

    expect($creado->numero_celular)->toBeNull()
        ->and($creado->direccion)->toBeNull()
        ->and($creado->telefono_oficina)->toBeNull();
});

test('admins can create a responsable account without an institución', function () {
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin)
        ->test(Users::class)
        ->call('create')
        ->set('name', 'Carlos Ruiz')
        ->set('email', 'carlos@example.com')
        ->set('role', UserRole::Responsable->value)
        ->set('password', 'contraseña-segura')
        ->set('password_confirmation', 'contraseña-segura')
        ->call('save')
        ->assertHasNoErrors();

    $creado = User::query()->where('email', 'carlos@example.com')->firstOrFail();

    expect($creado->role)->toBe(UserRole::Responsable)
        ->and($creado->institucion_id)->toBeNull();
});

test('an institución is required for a solicitante account', function () {
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin)
        ->test(Users::class)
        ->call('create')
        ->set('name', 'Sin institución')
        ->set('email', 'sin-institucion@example.com')
        ->set('role', UserRole::Solicitante->value)
        ->set('password', 'contraseña-segura')
        ->set('password_confirmation', 'contraseña-segura')
        ->call('save')
        ->assertHasErrors(['institucion_id' => 'required']);
});

test('email must be unique', function () {
    $existing = User::factory()->create();
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin)
        ->test(Users::class)
        ->call('create')
        ->set('name', 'Duplicado')
        ->set('email', $existing->email)
        ->set('role', UserRole::Responsable->value)
        ->set('password', 'contraseña-segura')
        ->set('password_confirmation', 'contraseña-segura')
        ->call('save')
        ->assertHasErrors(['email' => 'unique']);
});

test('admins cannot create another admin from the panel', function () {
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin)
        ->test(Users::class)
        ->call('create')
        ->set('name', 'Otro Admin')
        ->set('email', 'otroadmin@example.com')
        ->set('role', UserRole::Admin->value)
        ->set('password', 'contraseña-segura')
        ->set('password_confirmation', 'contraseña-segura')
        ->call('save')
        ->assertHasErrors(['role']);
});

test('admins can edit an existing account, including changing its password', function () {
    $admin = User::factory()->admin()->create();
    $institucionOriginal = Institucion::factory()->create();
    $institucionNueva = Institucion::factory()->create();
    $solicitante = User::factory()->solicitante()->create(['institucion_id' => $institucionOriginal->id]);
    $passwordOriginal = $solicitante->password;

    Livewire::actingAs($admin)
        ->test(Users::class)
        ->call('edit', $solicitante)
        ->set('name', 'Nombre actualizado')
        ->set('institucion_id', (string) $institucionNueva->id)
        ->set('numero_celular', '5500000000')
        ->set('direccion', 'Nueva dirección 45')
        ->set('telefono_oficina', '5511111111')
        ->set('password', 'otra-contraseña-segura')
        ->set('password_confirmation', 'otra-contraseña-segura')
        ->call('save')
        ->assertHasNoErrors();

    $solicitante->refresh();

    expect($solicitante->name)->toBe('Nombre actualizado')
        ->and($solicitante->institucion_id)->toBe($institucionNueva->id)
        ->and($solicitante->numero_celular)->toBe('5500000000')
        ->and($solicitante->direccion)->toBe('Nueva dirección 45')
        ->and($solicitante->telefono_oficina)->toBe('5511111111')
        ->and($solicitante->password)->not->toBe($passwordOriginal);
});

test('leaving the password blank when editing keeps the current password', function () {
    $admin = User::factory()->admin()->create();
    $solicitante = User::factory()->solicitante()->create();
    $passwordOriginal = $solicitante->password;

    Livewire::actingAs($admin)
        ->test(Users::class)
        ->call('edit', $solicitante)
        ->set('name', 'Solo cambia el nombre')
        ->call('save')
        ->assertHasNoErrors();

    expect($solicitante->fresh()->password)->toBe($passwordOriginal);
});

test('non-admins cannot reach the users page', function () {
    $solicitante = User::factory()->solicitante()->create();

    $this->actingAs($solicitante)
        ->get(route('admin.usuarios'))
        ->assertForbidden();
});
