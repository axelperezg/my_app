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
