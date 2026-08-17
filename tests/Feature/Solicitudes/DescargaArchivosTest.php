<?php

use App\Models\Solicitud;
use App\Models\SolicitudArchivo;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

test('the owning solicitante can download an attached file', function () {
    Storage::fake('s3');

    $solicitante = User::factory()->solicitante()->create();
    $solicitud = Solicitud::factory()->create(['solicitante_id' => $solicitante->id]);
    $archivo = SolicitudArchivo::factory()->for($solicitud)->create();

    Storage::disk('s3')->put($archivo->ruta, 'contenido');

    $this->actingAs($solicitante)
        ->get(route('solicitudes.archivos.download', [$solicitud, $archivo]))
        ->assertSuccessful();
});

test('a different solicitante cannot download someone else\'s file', function () {
    Storage::fake('s3');

    $otroSolicitante = User::factory()->solicitante()->create();
    $solicitud = Solicitud::factory()->create();
    $archivo = SolicitudArchivo::factory()->for($solicitud)->create();

    Storage::disk('s3')->put($archivo->ruta, 'contenido');

    $this->actingAs($otroSolicitante)
        ->get(route('solicitudes.archivos.download', [$solicitud, $archivo]))
        ->assertForbidden();
});

test('the assigned responsable can download an attached file', function () {
    Storage::fake('s3');

    $responsable = User::factory()->responsable()->create();
    $solicitud = Solicitud::factory()->create(['responsable_id' => $responsable->id]);
    $archivo = SolicitudArchivo::factory()->for($solicitud)->create();

    Storage::disk('s3')->put($archivo->ruta, 'contenido');

    $this->actingAs($responsable)
        ->get(route('solicitudes.archivos.download', [$solicitud, $archivo]))
        ->assertSuccessful();
});

test('a file that does not belong to the solicitud returns 404', function () {
    $solicitante = User::factory()->solicitante()->create();
    $solicitud = Solicitud::factory()->create(['solicitante_id' => $solicitante->id]);
    $archivo = SolicitudArchivo::factory()->create();

    $this->actingAs($solicitante)
        ->get(route('solicitudes.archivos.download', [$solicitud, $archivo]))
        ->assertNotFound();
});
