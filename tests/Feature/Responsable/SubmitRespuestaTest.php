<?php

use App\Enums\SolicitudEstatus;
use App\Livewire\Responsable\Solicitudes\Show;
use App\Mail\RespuestaEmitida;
use App\Models\Solicitud;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

test('a responsable can submit a respuesta with recomendaciones for their assigned solicitud', function () {
    Storage::fake('s3');
    Mail::fake();

    $responsable = User::factory()->responsable()->create();
    $solicitud = Solicitud::factory()->create([
        'responsable_id' => $responsable->id,
        'estatus' => SolicitudEstatus::Asignada,
    ]);

    Livewire::actingAs($responsable)
        ->test(Show::class, ['solicitud' => $solicitud])
        ->set('pdfRespuesta', UploadedFile::fake()->create('respuesta.pdf', 100, 'application/pdf'))
        ->set('recomendaciones', ['Corregir el formato X', 'Anexar evidencia Y'])
        ->call('submit')
        ->assertHasNoErrors()
        ->assertRedirect(route('responsable.solicitudes.index'));

    $solicitud->refresh();

    expect($solicitud->estatus)->toBe(SolicitudEstatus::Respondida)
        ->and($solicitud->respuesta)->not->toBeNull()
        ->and($solicitud->respuesta->recomendaciones)->toHaveCount(2)
        ->and($solicitud->respuesta->recomendaciones->pluck('numero')->all())->toBe([1, 2]);

    Storage::disk('s3')->assertExists($solicitud->respuesta->ruta);

    Mail::assertQueued(RespuestaEmitida::class, fn (RespuestaEmitida $mail) => $mail->solicitud->is($solicitud));
});

test('a respuesta requires at least one recomendación but not the pdf', function () {
    $responsable = User::factory()->responsable()->create();
    $solicitud = Solicitud::factory()->create([
        'responsable_id' => $responsable->id,
        'estatus' => SolicitudEstatus::Asignada,
    ]);

    Livewire::actingAs($responsable)
        ->test(Show::class, ['solicitud' => $solicitud])
        ->set('recomendaciones', [''])
        ->call('submit')
        ->assertHasErrors(['recomendaciones.0' => 'required'])
        ->assertHasNoErrors('pdfRespuesta');
});

test('a responsable can submit a respuesta without a pdf', function () {
    Mail::fake();

    $responsable = User::factory()->responsable()->create();
    $solicitud = Solicitud::factory()->create([
        'responsable_id' => $responsable->id,
        'estatus' => SolicitudEstatus::Asignada,
    ]);

    Livewire::actingAs($responsable)
        ->test(Show::class, ['solicitud' => $solicitud])
        ->set('recomendaciones', ['Corregir el formato X'])
        ->call('submit')
        ->assertHasNoErrors()
        ->assertRedirect(route('responsable.solicitudes.index'));

    $solicitud->refresh();

    expect($solicitud->estatus)->toBe(SolicitudEstatus::Respondida)
        ->and($solicitud->respuesta->ruta)->toBeNull();
});

test('a responsable cannot even open a solicitud assigned to someone else', function () {
    $responsable = User::factory()->responsable()->create();
    $solicitud = Solicitud::factory()->create([
        'responsable_id' => User::factory()->responsable(),
        'estatus' => SolicitudEstatus::Asignada,
    ]);

    Livewire::actingAs($responsable)
        ->test(Show::class, ['solicitud' => $solicitud])
        ->assertForbidden();
});

test('a solicitud cannot be answered twice', function () {
    $responsable = User::factory()->responsable()->create();
    $solicitud = Solicitud::factory()->create([
        'responsable_id' => $responsable->id,
        'estatus' => SolicitudEstatus::Respondida,
    ]);

    Livewire::actingAs($responsable)
        ->test(Show::class, ['solicitud' => $solicitud])
        ->set('pdfRespuesta', UploadedFile::fake()->create('respuesta.pdf', 100, 'application/pdf'))
        ->set('recomendaciones', ['Algo'])
        ->call('submit')
        ->assertForbidden();
});

test('a responsable cannot open a solicitud that is not theirs', function () {
    $responsable = User::factory()->responsable()->create();
    $solicitud = Solicitud::factory()->create();

    Livewire::actingAs($responsable)
        ->test(Show::class, ['solicitud' => $solicitud])
        ->assertForbidden();
});
