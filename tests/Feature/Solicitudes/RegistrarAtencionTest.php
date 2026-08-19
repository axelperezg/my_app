<?php

use App\Enums\RecomendacionEstatus;
use App\Enums\SolicitudEstatus;
use App\Livewire\Solicitudes\Show;
use App\Mail\AtencionRegistrada;
use App\Models\Recomendacion;
use App\Models\Respuesta;
use App\Models\Solicitud;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

test('a solicitante can register how they will attend every recomendación', function () {
    Storage::fake('s3');
    Mail::fake();

    $solicitante = User::factory()->solicitante()->create();
    $solicitud = Solicitud::factory()->create([
        'solicitante_id' => $solicitante->id,
        'estatus' => SolicitudEstatus::Respondida,
    ]);
    $respuesta = Respuesta::factory()->for($solicitud)->create();
    $recomendacion1 = Recomendacion::factory()->for($respuesta)->create(['numero' => 1]);
    $recomendacion2 = Recomendacion::factory()->for($respuesta)->create(['numero' => 2]);

    Livewire::actingAs($solicitante)
        ->test(Show::class, ['solicitud' => $solicitud])
        ->set("descripciones.{$recomendacion1->id}", 'Corregiremos el formato en 5 días hábiles.')
        ->set("descripciones.{$recomendacion2->id}", 'Anexaremos la evidencia solicitada.')
        ->set('pdfAtencion', UploadedFile::fake()->create('atencion.pdf', 100, 'application/pdf'))
        ->call('submit')
        ->assertHasNoErrors()
        ->assertRedirect(route('solicitudes.index'));

    $solicitud->refresh();

    expect($solicitud->estatus)->toBe(SolicitudEstatus::EnAtencion)
        ->and($respuesta->atencion)->not->toBeNull();

    expect($recomendacion1->fresh()->estatus)->toBe(RecomendacionEstatus::Pendiente)
        ->and($recomendacion1->fresh()->atencion_descripcion)->toBe('Corregiremos el formato en 5 días hábiles.')
        ->and($recomendacion2->fresh()->estatus)->toBe(RecomendacionEstatus::Pendiente);

    Storage::disk('s3')->assertExists($respuesta->atencion->ruta);

    Mail::assertQueued(AtencionRegistrada::class, fn (AtencionRegistrada $mail) => $mail->solicitud->is($solicitud));
});

test('atención requires a description for every pending recomendación but not the pdf', function () {
    $solicitante = User::factory()->solicitante()->create();
    $solicitud = Solicitud::factory()->create([
        'solicitante_id' => $solicitante->id,
        'estatus' => SolicitudEstatus::Respondida,
    ]);
    $respuesta = Respuesta::factory()->for($solicitud)->create();
    $recomendacion = Recomendacion::factory()->for($respuesta)->create();

    Livewire::actingAs($solicitante)
        ->test(Show::class, ['solicitud' => $solicitud])
        ->call('submit')
        ->assertHasErrors(["descripciones.{$recomendacion->id}" => 'required'])
        ->assertHasNoErrors('pdfAtencion');
});

test('a solicitante can register atención without a pdf', function () {
    Mail::fake();

    $solicitante = User::factory()->solicitante()->create();
    $solicitud = Solicitud::factory()->create([
        'solicitante_id' => $solicitante->id,
        'estatus' => SolicitudEstatus::Respondida,
    ]);
    $respuesta = Respuesta::factory()->for($solicitud)->create();
    $recomendacion = Recomendacion::factory()->for($respuesta)->create();

    Livewire::actingAs($solicitante)
        ->test(Show::class, ['solicitud' => $solicitud])
        ->set("descripciones.{$recomendacion->id}", 'Lo atenderemos la próxima semana.')
        ->call('submit')
        ->assertHasNoErrors()
        ->assertRedirect(route('solicitudes.index'));

    $solicitud->refresh();

    expect($solicitud->estatus)->toBe(SolicitudEstatus::EnAtencion)
        ->and($respuesta->atencion->ruta)->toBeNull();
});

test('an already atendida recomendación cannot be edited again', function () {
    Storage::fake('s3');
    Mail::fake();

    $solicitante = User::factory()->solicitante()->create();
    $solicitud = Solicitud::factory()->create([
        'solicitante_id' => $solicitante->id,
        'estatus' => SolicitudEstatus::EnAtencion,
    ]);
    $respuesta = Respuesta::factory()->for($solicitud)->create();
    $atendida = Recomendacion::factory()->for($respuesta)->atendida()->create(['numero' => 1]);
    $pendiente = Recomendacion::factory()->for($respuesta)->create(['numero' => 2]);

    $descripcionOriginal = $atendida->atencion_descripcion;

    Livewire::actingAs($solicitante)
        ->test(Show::class, ['solicitud' => $solicitud])
        ->set("descripciones.{$pendiente->id}", 'Ya lo atendemos.')
        ->set('pdfAtencion', UploadedFile::fake()->create('atencion.pdf', 100, 'application/pdf'))
        ->call('submit')
        ->assertHasNoErrors();

    expect($atendida->fresh())
        ->estatus->toBe(RecomendacionEstatus::Atendida)
        ->atencion_descripcion->toBe($descripcionOriginal);
});

test('a solicitante cannot register atención before there is a respuesta', function () {
    $solicitante = User::factory()->solicitante()->create();
    $solicitud = Solicitud::factory()->create(['solicitante_id' => $solicitante->id]);

    Livewire::actingAs($solicitante)
        ->test(Show::class, ['solicitud' => $solicitud])
        ->assertOk()
        ->call('submit')
        ->assertForbidden();
});

test('a different solicitante cannot view someone else\'s solicitud', function () {
    $otroSolicitante = User::factory()->solicitante()->create();
    $solicitud = Solicitud::factory()->create();

    Livewire::actingAs($otroSolicitante)
        ->test(Show::class, ['solicitud' => $solicitud])
        ->assertForbidden();
});
