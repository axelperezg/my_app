<?php

use App\Livewire\Solicitudes\Index;
use App\Models\Solicitud;
use App\Models\User;
use Livewire\Livewire;

test('solicitantes only see their own solicitudes', function () {
    $solicitante = User::factory()->solicitante()->create();
    $propia = Solicitud::factory()->create(['solicitante_id' => $solicitante->id]);
    $ajena = Solicitud::factory()->create();

    Livewire::actingAs($solicitante)
        ->test(Index::class)
        ->assertSee($propia->folio)
        ->assertDontSee($ajena->folio);
});

test('guests are redirected to login', function () {
    $this->get(route('solicitudes.index'))->assertRedirect(route('login'));
});

test('responsables cannot access the solicitudes list', function () {
    $responsable = User::factory()->responsable()->create();

    $this->actingAs($responsable)
        ->get(route('solicitudes.index'))
        ->assertForbidden();
});
