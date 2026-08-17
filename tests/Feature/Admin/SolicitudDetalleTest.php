<?php

use App\Livewire\Admin\SolicitudDetalle;
use App\Models\Solicitud;
use App\Models\SolicitudArchivo;
use App\Models\User;
use Livewire\Livewire;

test('admins can view a solicitud\'s details', function () {
    $admin = User::factory()->admin()->create();
    $solicitud = Solicitud::factory()->create();
    $archivo = SolicitudArchivo::factory()->for($solicitud)->create();

    Livewire::actingAs($admin)
        ->test(SolicitudDetalle::class, ['solicitud' => $solicitud])
        ->assertOk()
        ->assertSee($solicitud->folio)
        ->assertSee($solicitud->solicitante->name)
        ->assertSee($archivo->nombre_original);
});

test('non-admins cannot view a solicitud through the admin panel', function () {
    $responsable = User::factory()->responsable()->create();
    $solicitud = Solicitud::factory()->create();

    $this->actingAs($responsable)
        ->get(route('admin.solicitudes.show', $solicitud))
        ->assertForbidden();
});
