<?php

use App\Actions\Solicitudes\CreateSolicitud;
use App\Enums\TipoArchivoSolicitud;
use App\Mail\SolicitudRecibida;
use App\Models\EjercicioFiscal;
use App\Models\Institucion;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Spatie\LaravelPdf\Facades\Pdf;

function solicitudArchivos(): array
{
    return [
        TipoArchivoSolicitud::OficioEntrada->value => UploadedFile::fake()->create('oficio.pdf', 100, 'application/pdf'),
        TipoArchivoSolicitud::FormatoResultadosPdf->value => UploadedFile::fake()->create('resultados.pdf', 100, 'application/pdf'),
        TipoArchivoSolicitud::FormatoResultadosExcel->value => UploadedFile::fake()->create('resultados.xlsx', 100),
        TipoArchivoSolicitud::CarpetaResultados->value => UploadedFile::fake()->create('carpeta.pdf', 100, 'application/pdf'),
        TipoArchivoSolicitud::InstrumentoEvaluacion->value => UploadedFile::fake()->create('instrumento.pdf', 100, 'application/pdf'),
        TipoArchivoSolicitud::Video->value => [
            UploadedFile::fake()->create('video.mp4', 500),
        ],
        TipoArchivoSolicitud::Imagenes->value => [
            UploadedFile::fake()->image('foto.jpg'),
        ],
    ];
}

test('it creates a solicitud with a sequential folio, stores its files, and emails the acuse', function () {
    Storage::fake('local');
    Mail::fake();

    $solicitante = User::factory()->solicitante()->create();
    $institucion = Institucion::factory()->create();
    $ejercicioFiscal = EjercicioFiscal::factory()->create(['anio' => 2026]);

    $solicitud = app(CreateSolicitud::class)->handle(
        solicitante: $solicitante,
        datos: [
            'institucion_id' => $institucion->id,
            'ejercicio_fiscal_id' => $ejercicioFiscal->id,
            'correo_electronico' => 'solicitante@example.com',
        ],
        archivos: solicitudArchivos(),
    );

    expect($solicitud->folio)->toBe('2026-001')
        ->and($solicitud->solicitante_id)->toBe($solicitante->id)
        ->and($solicitud->archivos)->toHaveCount(7);

    $this->assertModelExists($solicitud);

    Storage::disk('local')->assertExists($solicitud->archivos->first()->ruta);

    Mail::assertQueued(SolicitudRecibida::class, fn (SolicitudRecibida $mail) => $mail->solicitud->is($solicitud)
        && $mail->hasTo('solicitante@example.com'));
});

test('folios are sequential per ejercicio fiscal and reset for a different one', function () {
    Storage::fake('local');
    Mail::fake();

    $solicitante = User::factory()->solicitante()->create();
    $institucion = Institucion::factory()->create();
    $ejercicioFiscal2026 = EjercicioFiscal::factory()->create(['anio' => 2026]);
    $ejercicioFiscal2027 = EjercicioFiscal::factory()->create(['anio' => 2027]);

    $crear = fn (EjercicioFiscal $ejercicioFiscal) => app(CreateSolicitud::class)->handle(
        solicitante: $solicitante,
        datos: [
            'institucion_id' => $institucion->id,
            'ejercicio_fiscal_id' => $ejercicioFiscal->id,
            'correo_electronico' => 'solicitante@example.com',
        ],
        archivos: solicitudArchivos(),
    );

    $primera = $crear($ejercicioFiscal2026);
    $segunda = $crear($ejercicioFiscal2026);
    $tercera = $crear($ejercicioFiscal2027);

    expect($primera->folio)->toBe('2026-001')
        ->and($segunda->folio)->toBe('2026-002')
        ->and($tercera->folio)->toBe('2027-001');
});

test('it counts the pages of each pdf requisito and leaves non-pdf archivos without a page count', function () {
    Storage::fake('local');
    Mail::fake();

    $solicitante = User::factory()->solicitante()->create();
    $institucion = Institucion::factory()->create();
    $ejercicioFiscal = EjercicioFiscal::factory()->create();

    $oficioDeDosPaginas = base64_decode(
        Pdf::html('<div>Página 1</div><div style="page-break-before: always;">Página 2</div>')->base64()
    );

    $archivos = solicitudArchivos();
    $archivos[TipoArchivoSolicitud::OficioEntrada->value] = UploadedFile::fake()
        ->createWithContent('oficio.pdf', $oficioDeDosPaginas);

    $solicitud = app(CreateSolicitud::class)->handle(
        solicitante: $solicitante,
        datos: [
            'institucion_id' => $institucion->id,
            'ejercicio_fiscal_id' => $ejercicioFiscal->id,
            'correo_electronico' => 'solicitante@example.com',
        ],
        archivos: $archivos,
    );

    $porTipo = $solicitud->archivos->keyBy(fn ($archivo) => $archivo->tipo->value);

    expect($porTipo[TipoArchivoSolicitud::OficioEntrada->value]->paginas)->toBe(2)
        ->and($porTipo[TipoArchivoSolicitud::FormatoResultadosExcel->value]->paginas)->toBeNull()
        ->and($porTipo[TipoArchivoSolicitud::Imagenes->value]->paginas)->toBeNull();
});
