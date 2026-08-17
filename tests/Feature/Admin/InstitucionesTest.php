<?php

use App\Livewire\Admin\Instituciones;
use App\Models\Institucion;
use App\Models\User;
use Livewire\Livewire;

test('admins can create an institucion', function () {
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin)
        ->test(Instituciones::class)
        ->call('create')
        ->set('nombre', 'Secretaría de Cultura')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('instituciones', [
        'nombre' => 'Secretaría de Cultura',
        'activo' => true,
    ]);
});

test('institucion name must be unique', function () {
    Institucion::factory()->create(['nombre' => 'Secretaría de Cultura']);
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin)
        ->test(Instituciones::class)
        ->call('create')
        ->set('nombre', 'Secretaría de Cultura')
        ->call('save')
        ->assertHasErrors(['nombre' => 'unique']);
});

test('admins can edit an institucion', function () {
    $institucion = Institucion::factory()->create(['nombre' => 'Nombre original']);
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin)
        ->test(Instituciones::class)
        ->call('edit', $institucion)
        ->set('nombre', 'Nombre actualizado')
        ->call('save')
        ->assertHasNoErrors();

    expect($institucion->fresh()->nombre)->toBe('Nombre actualizado');
});

test('admins can toggle whether an institucion is active', function () {
    $institucion = Institucion::factory()->create(['activo' => true]);
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin)
        ->test(Instituciones::class)
        ->call('toggleActivo', $institucion);

    expect($institucion->fresh()->activo)->toBeFalse();
});

test('non-admins cannot reach the instituciones page', function () {
    $solicitante = User::factory()->solicitante()->create();

    $this->actingAs($solicitante)
        ->get(route('admin.instituciones'))
        ->assertForbidden();
});
