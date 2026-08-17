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

test('a responsable can mark a recomendación as atendida (cumple)', function () {
    $responsable = User::factory()->responsable()->create();
    $solicitud = Solicitud::factory()->create([
        'responsable_id' => $responsable->id,
        'estatus' => SolicitudEstatus::EnAtencion,
    ]);
    $respuesta = Respuesta::factory()->for($solicitud)->create(['responsable_id' => $responsable->id]);
    $recomendacion = Recomendacion::factory()->for($respuesta)->propuesta()->create();

    Livewire::actingAs($responsable)
        ->test(Show::class, ['solicitud' => $solicitud])
        ->call('marcarAtendida', $recomendacion);

    expect($recomendacion->fresh()->estatus)->toBe(RecomendacionEstatus::Atendida);
});

test('a responsable can mark a recomendación as no atendida (no cumple)', function () {
    $responsable = User::factory()->responsable()->create();
    $solicitud = Solicitud::factory()->create([
        'responsable_id' => $responsable->id,
        'estatus' => SolicitudEstatus::EnAtencion,
    ]);
    $respuesta = Respuesta::factory()->for($solicitud)->create(['responsable_id' => $responsable->id]);
    $recomendacion = Recomendacion::factory()->for($respuesta)->propuesta()->create();

    Livewire::actingAs($responsable)
        ->test(Show::class, ['solicitud' => $solicitud])
        ->call('marcarNoAtendida', $recomendacion);

    expect($recomendacion->fresh()->estatus)->toBe(RecomendacionEstatus::NoAtendida);
});

test('the solicitud closes and the solicitante is notified once every recomendación is atendida', function () {
    Mail::fake();

    $responsable = User::factory()->responsable()->create();
    $solicitud = Solicitud::factory()->create([
        'responsable_id' => $responsable->id,
        'estatus' => SolicitudEstatus::EnAtencion,
    ]);
    $respuesta = Respuesta::factory()->for($solicitud)->create(['responsable_id' => $responsable->id]);
    $primera = Recomendacion::factory()->for($respuesta)->atendida()->create(['numero' => 1]);
    $segunda = Recomendacion::factory()->for($respuesta)->propuesta()->create(['numero' => 2]);

    Livewire::actingAs($responsable)
        ->test(Show::class, ['solicitud' => $solicitud])
        ->call('marcarAtendida', $segunda);

    expect($solicitud->fresh()->estatus)->toBe(SolicitudEstatus::Concluida);

    Mail::assertQueued(SolicitudCerrada::class, fn (SolicitudCerrada $mail) => $mail->solicitud->is($solicitud));
});

test('the solicitud does not close while a recomendación is no atendida', function () {
    Mail::fake();

    $responsable = User::factory()->responsable()->create();
    $solicitud = Solicitud::factory()->create([
        'responsable_id' => $responsable->id,
        'estatus' => SolicitudEstatus::EnAtencion,
    ]);
    $respuesta = Respuesta::factory()->for($solicitud)->create(['responsable_id' => $responsable->id]);
    $primera = Recomendacion::factory()->for($respuesta)->atendida()->create(['numero' => 1]);
    $segunda = Recomendacion::factory()->for($respuesta)->propuesta()->create(['numero' => 2]);

    Livewire::actingAs($responsable)
        ->test(Show::class, ['solicitud' => $solicitud])
        ->call('marcarNoAtendida', $segunda);

    expect($solicitud->fresh()->estatus)->toBe(SolicitudEstatus::EnAtencion);

    Mail::assertNotQueued(SolicitudCerrada::class);
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
