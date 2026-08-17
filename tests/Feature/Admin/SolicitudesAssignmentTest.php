<?php

use App\Enums\SolicitudEstatus;
use App\Livewire\Admin\Solicitudes;
use App\Models\Solicitud;
use App\Models\User;
use Livewire\Livewire;

test('admins can assign a responsable to a solicitud', function () {
    $admin = User::factory()->admin()->create();
    $responsable = User::factory()->responsable()->create();
    $solicitud = Solicitud::factory()->create();

    Livewire::actingAs($admin)
        ->test(Solicitudes::class)
        ->call('asignar', $solicitud, (string) $responsable->id);

    expect($solicitud->fresh())
        ->responsable_id->toBe($responsable->id)
        ->estatus->toBe(SolicitudEstatus::Asignada);
});

test('reassigning does not downgrade an already answered solicitud', function () {
    $admin = User::factory()->admin()->create();
    $otroResponsable = User::factory()->responsable()->create();
    $solicitud = Solicitud::factory()->create(['estatus' => SolicitudEstatus::Respondida]);

    Livewire::actingAs($admin)
        ->test(Solicitudes::class)
        ->call('asignar', $solicitud, (string) $otroResponsable->id);

    expect($solicitud->fresh())
        ->responsable_id->toBe($otroResponsable->id)
        ->estatus->toBe(SolicitudEstatus::Respondida);
});

test('admins can unassign a solicitud that has not been answered yet', function () {
    $admin = User::factory()->admin()->create();
    $responsable = User::factory()->responsable()->create();
    $solicitud = Solicitud::factory()->create([
        'responsable_id' => $responsable->id,
        'estatus' => SolicitudEstatus::Asignada,
    ]);

    Livewire::actingAs($admin)
        ->test(Solicitudes::class)
        ->call('asignar', $solicitud, '');

    expect($solicitud->fresh())
        ->responsable_id->toBeNull()
        ->estatus->toBe(SolicitudEstatus::Recibida);
});

test('non-admins cannot reach the solicitudes assignment page', function () {
    $responsable = User::factory()->responsable()->create();

    $this->actingAs($responsable)
        ->get(route('admin.solicitudes'))
        ->assertForbidden();
});
