<?php

use App\Enums\TipoArchivoSolicitud;
use App\Livewire\Admin\Reportes\PaginasPorSolicitud;
use App\Models\Solicitud;
use App\Models\SolicitudArchivo;
use App\Models\User;
use Livewire\Livewire;

test('it sums the pages of only the pdf requisitos for each solicitud', function () {
    $admin = User::factory()->admin()->create();
    $solicitud = Solicitud::factory()->create();

    SolicitudArchivo::factory()->for($solicitud)->create([
        'tipo' => TipoArchivoSolicitud::OficioEntrada,
        'paginas' => 3,
    ]);
    SolicitudArchivo::factory()->for($solicitud)->create([
        'tipo' => TipoArchivoSolicitud::CarpetaResultados,
        'paginas' => 5,
    ]);
    SolicitudArchivo::factory()->for($solicitud)->create([
        'tipo' => TipoArchivoSolicitud::FormatoResultadosExcel,
        'paginas' => null,
    ]);
    SolicitudArchivo::factory()->for($solicitud)->create([
        'tipo' => TipoArchivoSolicitud::Imagenes,
        'paginas' => null,
    ]);

    $filas = Livewire::actingAs($admin)
        ->test(PaginasPorSolicitud::class)
        ->instance()
        ->filas();

    expect($filas->firstWhere('folio', $solicitud->folio)['paginas'])->toBe(8);
});

test('the report can be exported as csv', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $solicitud = Solicitud::factory()->create();
    SolicitudArchivo::factory()->for($solicitud)->create([
        'tipo' => TipoArchivoSolicitud::OficioEntrada,
        'paginas' => 4,
    ]);

    $response = (new PaginasPorSolicitud)->exportar();

    ob_start();
    $response->sendContent();
    $csv = ob_get_clean();

    expect($csv)->toContain($solicitud->folio)
        ->and($csv)->toContain(',4');
});

test('non-admins cannot reach the report', function () {
    $responsable = User::factory()->responsable()->create();

    $this->actingAs($responsable)
        ->get(route('admin.reportes.paginas-por-solicitud'))
        ->assertForbidden();
});
