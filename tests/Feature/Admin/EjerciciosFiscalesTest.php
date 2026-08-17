<?php

use App\Livewire\Admin\EjerciciosFiscales;
use App\Models\EjercicioFiscal;
use App\Models\User;
use Livewire\Livewire;

test('admins can create an ejercicio fiscal', function () {
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin)
        ->test(EjerciciosFiscales::class)
        ->call('create')
        ->set('anio', '2027')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('ejercicios_fiscales', [
        'anio' => 2027,
        'activo' => true,
    ]);
});

test('ejercicio fiscal year must be unique', function () {
    EjercicioFiscal::factory()->create(['anio' => 2027]);
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin)
        ->test(EjerciciosFiscales::class)
        ->call('create')
        ->set('anio', '2027')
        ->call('save')
        ->assertHasErrors(['anio' => 'unique']);
});

test('admins can toggle whether an ejercicio fiscal is active', function () {
    $ejercicioFiscal = EjercicioFiscal::factory()->create(['activo' => true]);
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin)
        ->test(EjerciciosFiscales::class)
        ->call('toggleActivo', $ejercicioFiscal);

    expect($ejercicioFiscal->fresh()->activo)->toBeFalse();
});

test('non-admins cannot reach the ejercicios fiscales page', function () {
    $responsable = User::factory()->responsable()->create();

    $this->actingAs($responsable)
        ->get(route('admin.ejercicios-fiscales'))
        ->assertForbidden();
});
