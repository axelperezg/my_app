<?php

use App\Enums\EstatusArchivoSolicitud;
use App\Enums\SolicitudEstatus;
use App\Enums\TipoArchivoSolicitud;
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

test('los documentos requeridos se muestran en un orden fijo y no se reacomodan al calificar uno', function () {
    $responsable = User::factory()->responsable()->create();
    $solicitud = Solicitud::factory()->create([
        'responsable_id' => $responsable->id,
        'estatus' => SolicitudEstatus::Asignada,
    ]);

    // Se crean deliberadamente en un orden distinto al esperado.
    $instrumento = SolicitudArchivo::factory()->for($solicitud)->create(['tipo' => TipoArchivoSolicitud::InstrumentoEvaluacion]);
    $oficio = SolicitudArchivo::factory()->for($solicitud)->create(['tipo' => TipoArchivoSolicitud::OficioEntrada]);
    $excel = SolicitudArchivo::factory()->for($solicitud)->create(['tipo' => TipoArchivoSolicitud::FormatoResultadosExcel]);
    $carpeta = SolicitudArchivo::factory()->for($solicitud)->create(['tipo' => TipoArchivoSolicitud::CarpetaResultados]);
    $pdf = SolicitudArchivo::factory()->for($solicitud)->create(['tipo' => TipoArchivoSolicitud::FormatoResultadosPdf]);
    $imagen = SolicitudArchivo::factory()->for($solicitud)->create(['tipo' => TipoArchivoSolicitud::Imagenes]);

    $ordenEsperado = [$oficio->id, $pdf->id, $excel->id, $carpeta->id, $instrumento->id];

    $component = Livewire::actingAs($responsable)
        ->test(Show::class, ['solicitud' => $solicitud]);

    expect($component->instance()->documentosRequeridos->pluck('id')->all())->toBe($ordenEsperado)
        ->and($component->instance()->muestraMateriales->pluck('id')->all())->toBe([$imagen->id]);

    $component->set("archivoEstatus.{$oficio->id}", 'completo');

    expect($solicitud->fresh()->archivos->pluck('id')->all())
        ->toBe([...$ordenEsperado, $imagen->id]);
});
