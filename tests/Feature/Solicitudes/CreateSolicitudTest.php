<?php

use App\Livewire\Solicitudes\Create;
use App\Mail\SolicitudRecibida;
use App\Models\EjercicioFiscal;
use App\Models\Solicitud;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

test('solicitantes can submit a solicitud with all required files, using their account institución', function () {
    Storage::fake('local');
    Mail::fake();

    $solicitante = User::factory()->solicitante()->create();
    $ejercicioFiscal = EjercicioFiscal::factory()->create();

    Livewire::actingAs($solicitante)
        ->test(Create::class)
        ->set('ejercicio_fiscal_id', (string) $ejercicioFiscal->id)
        ->set('correo_electronico', 'solicitante@example.com')
        ->set('oficioEntrada', UploadedFile::fake()->create('oficio.pdf', 100, 'application/pdf'))
        ->set('formatoResultadosPdf', UploadedFile::fake()->create('resultados.pdf', 100, 'application/pdf'))
        ->set('formatoResultadosExcel', UploadedFile::fake()->create('resultados.xlsx', 100))
        ->set('carpetaResultados', UploadedFile::fake()->create('carpeta.pdf', 100, 'application/pdf'))
        ->set('instrumentoEvaluacion', UploadedFile::fake()->create('instrumento.pdf', 100, 'application/pdf'))
        ->set('imagenes', [UploadedFile::fake()->image('foto.jpg')])
        ->call('submit')
        ->assertHasNoErrors()
        ->assertRedirect(route('solicitudes.index'));

    $solicitud = Solicitud::query()->where('solicitante_id', $solicitante->id)->sole();

    expect($solicitud->archivos)->toHaveCount(6)
        ->and($solicitud->institucion_id)->toBe($solicitante->institucion_id);

    Mail::assertQueued(SolicitudRecibida::class);
});

test('the ejercicio fiscal defaults to the current year when an active one exists', function () {
    $solicitante = User::factory()->solicitante()->create();
    $ejercicioActual = EjercicioFiscal::factory()->create(['anio' => now()->year]);
    EjercicioFiscal::factory()->create(['anio' => now()->year - 1]);

    Livewire::actingAs($solicitante)
        ->test(Create::class)
        ->assertSet('ejercicio_fiscal_id', (string) $ejercicioActual->id);
});

test('the ejercicio fiscal is left blank when the current year has no active ejercicio', function () {
    $solicitante = User::factory()->solicitante()->create();
    EjercicioFiscal::factory()->create(['anio' => now()->year - 1]);

    Livewire::actingAs($solicitante)
        ->test(Create::class)
        ->assertSet('ejercicio_fiscal_id', '');
});

test('solicitantes can attach more than one video, audio and imagen using the repeater', function () {
    Storage::fake('local');
    Mail::fake();

    $solicitante = User::factory()->solicitante()->create();
    $ejercicioFiscal = EjercicioFiscal::factory()->create();

    Livewire::actingAs($solicitante)
        ->test(Create::class)
        ->set('ejercicio_fiscal_id', (string) $ejercicioFiscal->id)
        ->set('correo_electronico', 'solicitante@example.com')
        ->set('oficioEntrada', UploadedFile::fake()->create('oficio.pdf', 100, 'application/pdf'))
        ->set('formatoResultadosPdf', UploadedFile::fake()->create('resultados.pdf', 100, 'application/pdf'))
        ->set('formatoResultadosExcel', UploadedFile::fake()->create('resultados.xlsx', 100))
        ->set('carpetaResultados', UploadedFile::fake()->create('carpeta.pdf', 100, 'application/pdf'))
        ->set('instrumentoEvaluacion', UploadedFile::fake()->create('instrumento.pdf', 100, 'application/pdf'))
        ->call('agregarVideo')
        ->call('agregarAudio')
        ->call('agregarImagen')
        ->set('videos.0', UploadedFile::fake()->create('video1.mp4', 500))
        ->set('videos.1', UploadedFile::fake()->create('video2.mp4', 500))
        ->set('audios.0', UploadedFile::fake()->create('audio1.mp3', 200))
        ->set('audios.1', UploadedFile::fake()->create('audio2.mp3', 200))
        ->set('imagenes.0', UploadedFile::fake()->image('foto1.jpg'))
        ->set('imagenes.1', UploadedFile::fake()->image('foto2.jpg'))
        ->call('submit')
        ->assertHasNoErrors()
        ->assertRedirect(route('solicitudes.index'));

    $solicitud = Solicitud::query()->where('solicitante_id', $solicitante->id)->sole();

    expect($solicitud->archivos)->toHaveCount(11);
});

test('removing a repeater slot leaves at least one empty slot', function () {
    $solicitante = User::factory()->solicitante()->create();

    Livewire::actingAs($solicitante)
        ->test(Create::class)
        ->call('agregarVideo')
        ->assertCount('videos', 2)
        ->call('quitarVideo', 1)
        ->assertCount('videos', 1)
        ->call('quitarVideo', 0)
        ->assertCount('videos', 1);
});

test('a solicitud requires every document', function () {
    $solicitante = User::factory()->solicitante()->create();

    Livewire::actingAs($solicitante)
        ->test(Create::class)
        ->call('submit')
        ->assertHasErrors([
            'ejercicio_fiscal_id' => 'required',
            'oficioEntrada' => 'required',
            'formatoResultadosPdf' => 'required',
            'formatoResultadosExcel' => 'required',
            'carpetaResultados' => 'required',
            'instrumentoEvaluacion' => 'required',
        ]);
});

test('videos, audios and imágenes are optional', function () {
    Storage::fake('local');
    Mail::fake();

    $solicitante = User::factory()->solicitante()->create();
    $ejercicioFiscal = EjercicioFiscal::factory()->create();

    Livewire::actingAs($solicitante)
        ->test(Create::class)
        ->set('ejercicio_fiscal_id', (string) $ejercicioFiscal->id)
        ->set('correo_electronico', 'solicitante@example.com')
        ->set('oficioEntrada', UploadedFile::fake()->create('oficio.pdf', 100, 'application/pdf'))
        ->set('formatoResultadosPdf', UploadedFile::fake()->create('resultados.pdf', 100, 'application/pdf'))
        ->set('formatoResultadosExcel', UploadedFile::fake()->create('resultados.xlsx', 100))
        ->set('carpetaResultados', UploadedFile::fake()->create('carpeta.pdf', 100, 'application/pdf'))
        ->set('instrumentoEvaluacion', UploadedFile::fake()->create('instrumento.pdf', 100, 'application/pdf'))
        ->call('submit')
        ->assertHasNoErrors()
        ->assertRedirect(route('solicitudes.index'));

    $solicitud = Solicitud::query()->where('solicitante_id', $solicitante->id)->sole();

    expect($solicitud->archivos)->toHaveCount(5);
});

test('a solicitante without an active institución cannot submit a solicitud', function () {
    $solicitante = User::factory()->solicitante()->create(['institucion_id' => null]);

    Livewire::actingAs($solicitante)
        ->test(Create::class)
        ->assertOk()
        ->call('submit')
        ->assertForbidden();
});

test('only pdfs are accepted for the oficio de entrada', function () {
    $solicitante = User::factory()->solicitante()->create();

    Livewire::actingAs($solicitante)
        ->test(Create::class)
        ->set('oficioEntrada', UploadedFile::fake()->create('oficio.docx', 100))
        ->call('submit')
        ->assertHasErrors(['oficioEntrada' => 'mimes']);
});

test('responsables cannot access the solicitud form', function () {
    $responsable = User::factory()->responsable()->create();

    $this->actingAs($responsable)
        ->get(route('solicitudes.create'))
        ->assertForbidden();
});

test('admins cannot access the solicitud form', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('solicitudes.create'))
        ->assertForbidden();
});
