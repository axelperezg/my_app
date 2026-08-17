<?php

use App\Http\Controllers\SolicitudArchivoController;
use App\Http\Controllers\SolicitudAtencionController;
use App\Http\Controllers\SolicitudRespuestaController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    Route::get('solicitudes/{solicitud}/archivos/{archivo}', SolicitudArchivoController::class)
        ->name('solicitudes.archivos.download');

    Route::get('solicitudes/{solicitud}/respuesta', SolicitudRespuestaController::class)
        ->name('solicitudes.respuesta.download');

    Route::get('solicitudes/{solicitud}/atencion', SolicitudAtencionController::class)
        ->name('solicitudes.atencion.download');
});

require __DIR__.'/settings.php';
require __DIR__.'/admin.php';
require __DIR__.'/solicitudes.php';
require __DIR__.'/responsable.php';
