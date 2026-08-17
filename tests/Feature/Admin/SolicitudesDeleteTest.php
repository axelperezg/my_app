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
    Storage::fake('s3');

    $admin = User::factory()->admin()->create();
    $solicitud = Solicitud::factory()->create(['folio' => '2026-001']);
    $archivo = SolicitudArchivo::factory()->for($solicitud)->create(['ruta' => 'solicitudes/2026-001/oficio_entrada/doc.pdf']);
    $respuesta = Respuesta::factory()->for($solicitud)->create(['ruta' => 'solicitudes/2026-001/respuesta/doc.pdf']);
    $recomendacion = Recomendacion::factory()->for($respuesta)->create();

    Storage::disk('s3')->put($archivo->ruta, 'contenido');
    Storage::disk('s3')->put($respuesta->ruta, 'contenido');

    Livewire::actingAs($admin)
        ->test(Solicitudes::class)
        ->call('eliminar', $solicitud);

    $this->assertModelMissing($solicitud);
    $this->assertModelMissing($archivo);
    $this->assertModelMissing($respuesta);
    $this->assertModelMissing($recomendacion);

    Storage::disk('s3')->assertDirectoryEmpty('solicitudes/2026-001');
});

test('admins can delete a solicitud whose files span both the old local disco and s3', function () {
    Storage::fake('local');
    Storage::fake('s3');

    $admin = User::factory()->admin()->create();
    $solicitud = Solicitud::factory()->create(['folio' => '2026-002']);
    $archivoViejo = SolicitudArchivo::factory()->for($solicitud)->create([
        'disco' => 'local',
        'ruta' => 'solicitudes/2026-002/oficio_entrada/doc.pdf',
    ]);
    $archivoNuevo = SolicitudArchivo::factory()->for($solicitud)->create([
        'disco' => 's3',
        'ruta' => 'solicitudes/2026-002/formato_resultados_pdf/doc.pdf',
    ]);

    Storage::disk('local')->put($archivoViejo->ruta, 'contenido');
    Storage::disk('s3')->put($archivoNuevo->ruta, 'contenido');

    Livewire::actingAs($admin)
        ->test(Solicitudes::class)
        ->call('eliminar', $solicitud);

    $this->assertModelMissing($solicitud);

    Storage::disk('local')->assertDirectoryEmpty('solicitudes/2026-002');
    Storage::disk('s3')->assertDirectoryEmpty('solicitudes/2026-002');
});

test('non-admins cannot reach the page that lets them delete a solicitud', function () {
    $responsable = User::factory()->responsable()->create();

    $this->actingAs($responsable)
        ->get(route('admin.solicitudes'))
        ->assertForbidden();
});
