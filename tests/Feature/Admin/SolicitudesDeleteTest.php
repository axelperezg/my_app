<?php

use App\Livewire\Admin\Solicitudes;
use App\Models\Recomendacion;
use App\Models\Respuesta;
use App\Models\Solicitud;
use App\Models\SolicitudArchivo;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

test('admins can delete a solicitud, its files and its related records', function () {
    Storage::fake('local');

    $admin = User::factory()->admin()->create();
    $solicitud = Solicitud::factory()->create(['folio' => '2026-001']);
    $archivo = SolicitudArchivo::factory()->for($solicitud)->create(['ruta' => 'solicitudes/2026-001/oficio_entrada/doc.pdf']);
    $respuesta = Respuesta::factory()->for($solicitud)->create(['ruta' => 'solicitudes/2026-001/respuesta/doc.pdf']);
    $recomendacion = Recomendacion::factory()->for($respuesta)->create();

    Storage::disk('local')->put($archivo->ruta, 'contenido');
    Storage::disk('local')->put($respuesta->ruta, 'contenido');

    Livewire::actingAs($admin)
        ->test(Solicitudes::class)
        ->call('eliminar', $solicitud);

    $this->assertModelMissing($solicitud);
    $this->assertModelMissing($archivo);
    $this->assertModelMissing($respuesta);
    $this->assertModelMissing($recomendacion);

    Storage::disk('local')->assertDirectoryEmpty('solicitudes/2026-001');
});

test('non-admins cannot reach the page that lets them delete a solicitud', function () {
    $responsable = User::factory()->responsable()->create();

    $this->actingAs($responsable)
        ->get(route('admin.solicitudes'))
        ->assertForbidden();
});
