<?php

use App\Enums\TipoArchivoSolicitud;
use App\Livewire\Admin\Reportes\SolicitudesPorMes;
use App\Models\Solicitud;
use App\Models\SolicitudArchivo;
use App\Models\User;
use Livewire\Livewire;

test('it groups solicitudes by the month they were received', function () {
    $admin = User::factory()->admin()->create();

    Solicitud::factory()->count(2)->create(['fecha_recepcion' => '2026-06-15 10:00:00']);
    Solicitud::factory()->create(['fecha_recepcion' => '2026-07-01 09:00:00']);

    $filas = Livewire::actingAs($admin)
        ->test(SolicitudesPorMes::class)
        ->instance()
        ->porMes();

    expect($filas->firstWhere('mes', '2026-06')['cantidad'])->toBe(2)
        ->and($filas->firstWhere('mes', '2026-07')['cantidad'])->toBe(1);
});

test('it sums the pdf pages received in each month', function () {
    $admin = User::factory()->admin()->create();

    $solicitudJunio1 = Solicitud::factory()->create(['fecha_recepcion' => '2026-06-10 10:00:00']);
    SolicitudArchivo::factory()->for($solicitudJunio1)->create([
        'tipo' => TipoArchivoSolicitud::OficioEntrada,
        'paginas' => 3,
    ]);

    $solicitudJunio2 = Solicitud::factory()->create(['fecha_recepcion' => '2026-06-20 10:00:00']);
    SolicitudArchivo::factory()->for($solicitudJunio2)->create([
        'tipo' => TipoArchivoSolicitud::CarpetaResultados,
        'paginas' => 5,
    ]);
    SolicitudArchivo::factory()->for($solicitudJunio2)->create([
        'tipo' => TipoArchivoSolicitud::FormatoResultadosExcel,
        'paginas' => null,
    ]);

    $filas = Livewire::actingAs($admin)
        ->test(SolicitudesPorMes::class)
        ->instance()
        ->porMes();

    expect($filas->firstWhere('mes', '2026-06')['paginas'])->toBe(8);
});

test('the report can be exported as csv', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $solicitud = Solicitud::factory()->create(['fecha_recepcion' => '2026-06-15 10:00:00']);
    SolicitudArchivo::factory()->for($solicitud)->create([
        'tipo' => TipoArchivoSolicitud::OficioEntrada,
        'paginas' => 4,
    ]);

    $response = (new SolicitudesPorMes)->exportar();

    ob_start();
    $response->sendContent();
    $csv = ob_get_clean();

    expect($csv)->toContain('Mes,Solicitudes,Páginas')
        ->and($csv)->toContain('2026-06,1,4');
});

test('non-admins cannot reach the report', function () {
    $responsable = User::factory()->responsable()->create();

    $this->actingAs($responsable)
        ->get(route('admin.reportes.solicitudes-por-mes'))
        ->assertForbidden();
});
