<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TurnoController;
use App\Http\Controllers\AsesorController;
use App\Http\Controllers\CoordinadorController;

// ── Rutas del Usuario / Digiturno ──────────────────────────────────────────
Route::get('/', [TurnoController::class, 'index'])->name('turnos.index');
Route::post('/turnos', [TurnoController::class, 'store'])->name('turnos.store');
Route::get('/turnos-api/ultimo', [TurnoController::class, 'latestTurns'])->name('turnos.api.ultimo');
Route::get('/turnos-api/pendientes', [TurnoController::class, 'pendingTurns'])->name('turnos.api.pendientes');
Route::get('/pantalla', [TurnoController::class, 'tv'])->name('turnos.tv');

// ── Rutas del Asesor ──────────────────────────────────────────────────────
Route::get('/asesor/login',      [AsesorController::class, 'showLogin'])->name('asesor.login');
Route::post('/asesor/login',     [AsesorController::class, 'login'])->name('asesor.login.post');
Route::get('/asesor/finalizada', [AsesorController::class, 'sesionFinalizada'])->name('asesor.finalizada');

Route::prefix('asesor')->middleware(['asesor.inactividad'])->group(function () {
    Route::post('/logout',    [AsesorController::class, 'logout'])->name('asesor.logout');
    Route::get('/dashboard',  [AsesorController::class, 'dashboard'])->name('asesor.dashboard');
    Route::get('/api/estado', [AsesorController::class, 'apiEstado'])->name('asesor.api.estado');
    Route::post('/aceptar-turno',      [AsesorController::class, 'aceptarTurno'])->name('asesor.aceptar');
    Route::post('/finalizar-atencion', [AsesorController::class, 'finalizarAtencion'])->name('asesor.finalizar');
    Route::post('/toggle-espera',      [AsesorController::class, 'toggleEspera'])->name('asesor.espera');
    Route::get('/persona',             [AsesorController::class, 'personaDetalles'])->name('asesor.persona.get');
    Route::post('/persona/update',      [AsesorController::class, 'updatePersona'])->name('asesor.persona.update');
});

// ── Rutas del Coordinador (Super Admin) ───────────────────────────────────
Route::get('/coordinador/finalizada', [CoordinadorController::class, 'sesionFinalizada'])->name('coordinador.finalizada');
Route::prefix('coordinador')->middleware(['coordinador.inactividad'])->group(function () {
    Route::get('/login',             [CoordinadorController::class, 'showLogin'])->name('coordinador.login')->withoutMiddleware(['coordinador.inactividad']);
    Route::post('/login',            [CoordinadorController::class, 'login'])->name('coordinador.login.post')->withoutMiddleware(['coordinador.inactividad']);
    Route::post('/logout',           [CoordinadorController::class, 'logout'])->name('coordinador.logout');
    Route::get('/dashboard',         [CoordinadorController::class, 'dashboard'])->name('coordinador.dashboard');
    Route::get('/api/estado',        [CoordinadorController::class, 'apiEstado'])->name('coordinador.api.estado');
    Route::post('/reasignar',        [CoordinadorController::class, 'reasignarAsesor'])->name('coordinador.reasignar');
    Route::post('/aceptar-turno',    [CoordinadorController::class, 'aceptarTurno'])->name('coordinador.aceptar');
    Route::get('/reporte',           [CoordinadorController::class, 'reporteSemanal'])->name('coordinador.reporte');
    Route::post('/asesor/store',     [CoordinadorController::class, 'storeAsesor'])->name('coordinador.asesor.store');
});
