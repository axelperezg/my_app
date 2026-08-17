<?php

use App\Enums\EstatusArchivoSolicitud;
use App\Enums\SolicitudEstatus;
use App\Livewire\Responsable\Solicitudes\Show;
use App\Models\Solicitud;
use App\Models\SolicitudArchivo;
use App\Models\User;
use Livewire\Livewire;

test('a documento starts out as vacío', function () {
    $responsable = User::factory()->responsable()->create();
    $solicitud = Solicitud::factory()->create([
        'responsable_id' => $responsable->id,
        'estatus' => SolicitudEstatus::Asignada,
    ]);
    $archivo = SolicitudArchivo::factory()->for($solicitud)->create();

    expect($archivo->estatus)->toBe(EstatusArchivoSolicitud::Vacio);

    Livewire::actingAs($responsable)
        ->test(Show::class, ['solicitud' => $solicitud])
        ->assertSet("archivoEstatus.{$archivo->id}", '');
});

test('a responsable can mark a documento as completo or incompleto while the solicitud is asignada', function () {
    $responsable = User::factory()->responsable()->create();
    $solicitud = Solicitud::factory()->create([
        'responsable_id' => $responsable->id,
        'estatus' => SolicitudEstatus::Asignada,
    ]);
    $archivo = SolicitudArchivo::factory()->for($solicitud)->create();

    Livewire::actingAs($responsable)
        ->test(Show::class, ['solicitud' => $solicitud])
        ->set("archivoEstatus.{$archivo->id}", 'completo');

    expect($archivo->fresh()->estatus)->toBe(EstatusArchivoSolicitud::Completo);
});

test('a responsable cannot change a documento estatus once the solicitud is no longer asignada', function () {
    $responsable = User::factory()->responsable()->create();
    $solicitud = Solicitud::factory()->create([
        'responsable_id' => $responsable->id,
        'estatus' => SolicitudEstatus::Respondida,
    ]);
    $archivo = SolicitudArchivo::factory()->for($solicitud)->create();

    Livewire::actingAs($responsable)
        ->test(Show::class, ['solicitud' => $solicitud])
        ->set("archivoEstatus.{$archivo->id}", 'completo')
        ->assertForbidden();

    expect($archivo->fresh()->estatus)->toBe(EstatusArchivoSolicitud::Vacio);
});
