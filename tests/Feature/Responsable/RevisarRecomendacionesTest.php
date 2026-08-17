<?php

use App\Enums\RecomendacionEstatus;
use App\Enums\SolicitudEstatus;
use App\Livewire\Responsable\Solicitudes\Show;
use App\Mail\SolicitudCerrada;
use App\Models\Recomendacion;
use App\Models\Respuesta;
use App\Models\Solicitud;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

test('a responsable can accept a recomendación', function () {
    $responsable = User::factory()->responsable()->create();
    $solicitud = Solicitud::factory()->create([
        'responsable_id' => $responsable->id,
        'estatus' => SolicitudEstatus::EnAtencion,
    ]);
    $respuesta = Respuesta::factory()->for($solicitud)->create(['responsable_id' => $responsable->id]);
    $recomendacion = Recomendacion::factory()->for($respuesta)->propuesta()->create();

    Livewire::actingAs($responsable)
        ->test(Show::class, ['solicitud' => $solicitud])
        ->call('aceptar', $recomendacion);

    expect($recomendacion->fresh()->estatus)->toBe(RecomendacionEstatus::Aceptada);
});

test('a responsable can request an adjustment with a comment', function () {
    $responsable = User::factory()->responsable()->create();
    $solicitud = Solicitud::factory()->create([
        'responsable_id' => $responsable->id,
        'estatus' => SolicitudEstatus::EnAtencion,
    ]);
    $respuesta = Respuesta::factory()->for($solicitud)->create(['responsable_id' => $responsable->id]);
    $recomendacion = Recomendacion::factory()->for($respuesta)->propuesta()->create();

    Livewire::actingAs($responsable)
        ->test(Show::class, ['solicitud' => $solicitud])
        ->set("comentarios.{$recomendacion->id}", 'Falta anexar evidencia fotográfica.')
        ->call('pedirAjuste', $recomendacion);

    expect($recomendacion->fresh())
        ->estatus->toBe(RecomendacionEstatus::AjusteSolicitado)
        ->comentario_responsable->toBe('Falta anexar evidencia fotográfica.');
});

test('requesting an adjustment without a comment fails', function () {
    $responsable = User::factory()->responsable()->create();
    $solicitud = Solicitud::factory()->create([
        'responsable_id' => $responsable->id,
        'estatus' => SolicitudEstatus::EnAtencion,
    ]);
    $respuesta = Respuesta::factory()->for($solicitud)->create(['responsable_id' => $responsable->id]);
    $recomendacion = Recomendacion::factory()->for($respuesta)->propuesta()->create();

    Livewire::actingAs($responsable)
        ->test(Show::class, ['solicitud' => $solicitud])
        ->call('pedirAjuste', $recomendacion)
        ->assertHasErrors(["comentarios.{$recomendacion->id}"]);

    expect($recomendacion->fresh()->estatus)->toBe(RecomendacionEstatus::Propuesta);
});

test('the solicitud closes and the solicitante is notified once every recomendación is accepted', function () {
    Mail::fake();

    $responsable = User::factory()->responsable()->create();
    $solicitud = Solicitud::factory()->create([
        'responsable_id' => $responsable->id,
        'estatus' => SolicitudEstatus::EnAtencion,
    ]);
    $respuesta = Respuesta::factory()->for($solicitud)->create(['responsable_id' => $responsable->id]);
    $primera = Recomendacion::factory()->for($respuesta)->aceptada()->create(['numero' => 1]);
    $segunda = Recomendacion::factory()->for($respuesta)->propuesta()->create(['numero' => 2]);

    Livewire::actingAs($responsable)
        ->test(Show::class, ['solicitud' => $solicitud])
        ->call('aceptar', $segunda);

    expect($solicitud->fresh()->estatus)->toBe(SolicitudEstatus::Cerrada);

    Mail::assertQueued(SolicitudCerrada::class, fn (SolicitudCerrada $mail) => $mail->solicitud->is($solicitud));
});

test('a responsable not assigned to the solicitud cannot review its recomendaciones', function () {
    $responsable = User::factory()->responsable()->create();
    $solicitud = Solicitud::factory()->create([
        'responsable_id' => User::factory()->responsable(),
        'estatus' => SolicitudEstatus::EnAtencion,
    ]);

    Livewire::actingAs($responsable)
        ->test(Show::class, ['solicitud' => $solicitud])
        ->assertForbidden();
});
