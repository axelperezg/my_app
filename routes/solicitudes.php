<?php

use App\Livewire\Solicitudes\Create;
use App\Livewire\Solicitudes\Index;
use App\Livewire\Solicitudes\Show;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'role:solicitante'])->prefix('solicitudes')->name('solicitudes.')->group(function () {
    Route::livewire('/', Index::class)->name('index');

    Route::livewire('nueva', Create::class)->name('create');

    Route::livewire('{solicitud}', Show::class)->name('show');
});
