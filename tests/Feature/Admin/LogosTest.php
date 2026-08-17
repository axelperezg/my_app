<?php

use App\Livewire\Admin\Logos;
use App\Models\Configuracion;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

test('admins can upload the three logos', function () {
    Storage::fake('public');
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin)
        ->test(Logos::class)
        ->set('logoApp', UploadedFile::fake()->image('logo-app.png'))
        ->set('logoPdfIzquierdo', UploadedFile::fake()->image('logo-izq.png'))
        ->set('logoPdfDerecho', UploadedFile::fake()->image('logo-der.png'))
        ->call('guardar')
        ->assertHasNoErrors();

    $configuracion = Configuracion::actual();

    expect($configuracion->logo_app_path)->not->toBeNull()
        ->and($configuracion->logo_pdf_izquierdo_path)->not->toBeNull()
        ->and($configuracion->logo_pdf_derecho_path)->not->toBeNull();

    Storage::disk('public')->assertExists($configuracion->logo_app_path);
});

test('uploading a new logo replaces and deletes the previous file', function () {
    Storage::fake('public');
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin)
        ->test(Logos::class)
        ->set('logoApp', UploadedFile::fake()->image('primero.png'))
        ->call('guardar');

    $rutaOriginal = Configuracion::actual()->logo_app_path;
    Storage::disk('public')->assertExists($rutaOriginal);

    Livewire::actingAs($admin)
        ->test(Logos::class)
        ->set('logoApp', UploadedFile::fake()->image('segundo.png'))
        ->call('guardar');

    $rutaNueva = Configuracion::actual()->logo_app_path;

    expect($rutaNueva)->not->toBe($rutaOriginal);
    Storage::disk('public')->assertMissing($rutaOriginal);
    Storage::disk('public')->assertExists($rutaNueva);
});

test('admins can remove a logo', function () {
    Storage::fake('public');
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin)
        ->test(Logos::class)
        ->set('logoApp', UploadedFile::fake()->image('logo-app.png'))
        ->call('guardar');

    $ruta = Configuracion::actual()->logo_app_path;

    Livewire::actingAs($admin)
        ->test(Logos::class)
        ->call('quitarLogoApp');

    expect(Configuracion::actual()->logo_app_path)->toBeNull();
    Storage::disk('public')->assertMissing($ruta);
});

test('only image files are accepted', function () {
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin)
        ->test(Logos::class)
        ->set('logoApp', UploadedFile::fake()->create('logo.pdf', 100, 'application/pdf'))
        ->call('guardar')
        ->assertHasErrors(['logoApp' => 'image']);
});

test('non-admins cannot reach the logos page', function () {
    $responsable = User::factory()->responsable()->create();

    $this->actingAs($responsable)
        ->get(route('admin.logos'))
        ->assertForbidden();
});

test('logoPdfIzquierdoBase64 returns a data uri when a logo is configured', function () {
    Storage::fake('public');

    $configuracion = Configuracion::actual();
    $configuracion->logo_pdf_izquierdo_path = UploadedFile::fake()->image('logo.png')->store('logos', 'public');
    $configuracion->save();

    expect($configuracion->logoPdfIzquierdoBase64())->toStartWith('data:image/');
});

test('logoPdfIzquierdoBase64 returns null when no logo is configured', function () {
    expect(Configuracion::actual()->logoPdfIzquierdoBase64())->toBeNull();
});
