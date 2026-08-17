<?php

use App\Livewire\Responsable\Solicitudes\Index;
use App\Models\Solicitud;
use App\Models\User;
use Livewire\Livewire;

test('responsables only see solicitudes assigned to them', function () {
    $responsable = User::factory()->responsable()->create();
    $propia = Solicitud::factory()->create(['responsable_id' => $responsable->id]);
    $ajena = Solicitud::factory()->create();

    Livewire::actingAs($responsable)
        ->test(Index::class)
        ->assertSee($propia->folio)
        ->assertDontSee($ajena->folio);
});

test('solicitantes cannot access the responsable bandeja', function () {
    $solicitante = User::factory()->solicitante()->create();

    $this->actingAs($solicitante)
        ->get(route('responsable.solicitudes.index'))
        ->assertForbidden();
});
