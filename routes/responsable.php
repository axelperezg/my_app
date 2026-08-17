<?php

use App\Livewire\Responsable\Solicitudes\Index;
use App\Livewire\Responsable\Solicitudes\Show;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'role:responsable'])->prefix('responsable')->name('responsable.')->group(function () {
    Route::livewire('solicitudes', Index::class)->name('solicitudes.index');

    Route::livewire('solicitudes/{solicitud}', Show::class)->name('solicitudes.show');
});
