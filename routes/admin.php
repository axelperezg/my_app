<?php

use App\Livewire\Admin\EjerciciosFiscales;
use App\Livewire\Admin\Instituciones;
use App\Livewire\Admin\Logos;
use App\Livewire\Admin\Reportes\PaginasPorSolicitud;
use App\Livewire\Admin\Reportes\SolicitudesPorMes;
use App\Livewire\Admin\Solicitudes;
use App\Livewire\Admin\Users;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::livewire('usuarios', Users::class)->name('usuarios');

    Route::livewire('logos', Logos::class)->name('logos');

    Route::livewire('instituciones', Instituciones::class)->name('instituciones');

    Route::livewire('ejercicios-fiscales', EjerciciosFiscales::class)->name('ejercicios-fiscales');

    Route::livewire('solicitudes', Solicitudes::class)->name('solicitudes');

    Route::livewire('reportes/solicitudes-por-mes', SolicitudesPorMes::class)->name('reportes.solicitudes-por-mes');

    Route::livewire('reportes/paginas-por-solicitud', PaginasPorSolicitud::class)->name('reportes.paginas-por-solicitud');
});
