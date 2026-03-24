<?php

use App\Http\Controllers\TurnoController;
use App\Http\Controllers\AsesorController;

// ── Rutas del Usuario / Digiturno ──────────────────────────────────────────
Route::get('/', [TurnoController::class, 'index'])->name('turnos.index');
Route::post('/turnos', [TurnoController::class, 'store'])->name('turnos.store');
Route::get('/turnos-api/ultimo', [TurnoController::class, 'latestTurns'])->name('turnos.api.ultimo');
Route::get('/turnos-api/pendientes', [TurnoController::class, 'pendingTurns'])->name('turnos.api.pendientes');
Route::get('/pantalla', [TurnoController::class, 'tv'])->name('turnos.tv');

// ── Rutas del Asesor ──────────────────────────────────────────────────────
Route::get('/asesor/login',      [AsesorController::class, 'showLogin'])->name('asesor.login');
Route::post('/asesor/login',     [AsesorController::class, 'login'])->name('asesor.login.post');
Route::post('/asesor/logout',    [AsesorController::class, 'logout'])->name('asesor.logout');
Route::get('/asesor/dashboard',  [AsesorController::class, 'dashboard'])->name('asesor.dashboard');
Route::get('/asesor/api/estado', [AsesorController::class, 'apiEstado'])->name('asesor.api.estado');
Route::post('/asesor/aceptar-turno',      [AsesorController::class, 'aceptarTurno'])->name('asesor.aceptar');
Route::post('/asesor/finalizar-atencion', [AsesorController::class, 'finalizarAtencion'])->name('asesor.finalizar');
Route::post('/asesor/toggle-espera',      [AsesorController::class, 'toggleEspera'])->name('asesor.espera');
Route::get('/asesor/persona',             [AsesorController::class, 'personaDetalles'])->name('asesor.persona.get');
Route::post('/asesor/persona/update',      [AsesorController::class, 'updatePersona'])->name('asesor.persona.update');
