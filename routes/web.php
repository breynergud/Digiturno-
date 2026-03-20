<?php

use App\Http\Controllers\TurnoController;

Route::get('/', function () {
    return view('digiturno.index');
});

Route::post('/turnos', [TurnoController::class, 'store'])->name('turnos.store');
Route::get('/turnos-api/ultimo', [TurnoController::class, 'latestTurns'])->name('turnos.api.ultimo');
Route::get('/pantalla', [TurnoController::class, 'tv'])->name('turnos.tv');
