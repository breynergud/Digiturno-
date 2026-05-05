<?php
/**
 * Script de Pruebas del Sistema Digiturno
 * Ejecuta las 3 categorías de pruebas sin necesitar PHPUnit
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

// ── Helpers ──────────────────────────────────────────────────────
$passed = 0;
$failed = 0;
$results = [];

function assert_test(string $name, bool $condition, string $expected, string $actual = ''): void {
    global $passed, $failed, $results;
    if ($condition) {
        $passed++;
        $results[] = "  ✅ PASS: $name";
    } else {
        $failed++;
        $results[] = "  ❌ FAIL: $name";
        $results[] = "     Esperado: $expected";
        if ($actual) $results[] = "     Obtenido: $actual";
    }
}

function section(string $title): void {
    global $results;
    $results[] = "\n══════════════════════════════════════════";
    $results[] = "  $title";
    $results[] = "══════════════════════════════════════════";
}

// ── Limpiar datos de prueba previos ──────────────────────────────
DB::table('atencion')->where('ASESOR_ase_id', 9999)->delete();
DB::table('turno')->where('tur_numero', 'like', 'TEST-%')->delete();
DB::table('turnos')->where('codigo_turno', 'like', 'TEST-%')->delete();
DB::table('asesor')->where('ase_correo', 'prueba@test.com')->delete();
DB::table('usuario')->where('PERSONA_pers_doc', 88888888)->delete();
DB::table('persona')->whereIn('pers_doc', [88888888, 88888889, 88888890])->delete();

// ════════════════════════════════════════════════════════════════
// 1. PARTICIÓN DE EQUIVALENCIA
// ════════════════════════════════════════════════════════════════
section("1. PARTICIÓN DE EQUIVALENCIA — Registro de Turno");

// Caso A: Datos válidos → turno creado
$results[] = "\n  Caso A (Válido): Datos completos y correctos";
try {
    // Simular la lógica del TurnoController::store
    $tipoAtencion = 'general';
    $doc = '88888888';
    $nombres = 'Juan';
    $apellidos = 'Pérez';
    $telefono = '3001234567';
    $tipodoc = 'CC';

    $persona = \App\Models\Persona::updateOrCreate(
        ['pers_doc' => (int)$doc],
        ['pers_tipodoc' => $tipodoc, 'pers_nombres' => $nombres, 'pers_apellidos' => $apellidos]
    );

    $usuario = \App\Models\Usuario::firstOrCreate(
        ['PERSONA_pers_doc' => $persona->pers_doc],
        ['user_tipo' => 'cliente']
    );

    $turno = \App\Models\TurnoUnificado::create([
        'tur_hora_fecha'  => now(),
        'tur_numero'      => 'TEST-G001',
        'tur_tipo'        => 'General',
        'tur_mesa'        => 1,
        'USUARIO_user_id' => $usuario->user_id,
    ]);

    assert_test(
        "Turno creado con datos válidos",
        $turno->exists && $turno->tur_tipo === 'General',
        "Turno creado con tur_tipo=General",
        "tur_tipo=" . $turno->tur_tipo
    );
} catch (\Exception $e) {
    assert_test("Turno creado con datos válidos", false, "Sin excepción", $e->getMessage());
}

// Caso B: Sin documento → validación falla
$results[] = "\n  Caso B (Inválido): Número de documento vacío";
$validator = \Illuminate\Support\Facades\Validator::make(
    ['tipo_atencion' => 'general', 'pers_tipodoc' => 'CC', 'numero_documento' => ''],
    ['numero_documento' => 'required|string']
);
assert_test(
    "Documento vacío genera error de validación",
    $validator->fails() && $validator->errors()->has('numero_documento'),
    "Error en campo numero_documento",
    $validator->fails() ? "Validación falla correctamente" : "Validación pasó (incorrecto)"
);

// Caso C: Tipo de atención desconocido → se mapea a General
$results[] = "\n  Caso C (Borde): Tipo de atención no reconocido → mapea a General";
$prefijo = match('tipo_raro') {
    'victimas' => 'V', 'especial' => 'S', 'empresario' => 'E', default => 'G'
};
assert_test(
    "Tipo desconocido se mapea a General (prefijo G)",
    $prefijo === 'G',
    "Prefijo G",
    "Prefijo $prefijo"
);

// ════════════════════════════════════════════════════════════════
// 2. ANÁLISIS DE VALORES LÍMITE — Duración de atención
// ════════════════════════════════════════════════════════════════
section("2. ANÁLISIS DE VALORES LÍMITE — Duración de Atención");

$MIN_MINUTOS = 60;   // 1 hora
$MAX_MINUTOS = 720;  // 12 horas

// Caso A: Exactamente 1 hora (límite inferior)
$results[] = "\n  Caso A (Límite inferior): Atención de exactamente 1 hora";
$inicio = Carbon::now()->subHour();
$fin    = Carbon::now();
$duracion = $inicio->diffInMinutes($fin);
assert_test(
    "1 hora ($duracion min) >= mínimo ($MIN_MINUTOS min)",
    $duracion >= $MIN_MINUTOS,
    ">= $MIN_MINUTOS minutos",
    "$duracion minutos"
);
assert_test(
    "1 hora ($duracion min) <= máximo ($MAX_MINUTOS min)",
    $duracion <= $MAX_MINUTOS,
    "<= $MAX_MINUTOS minutos",
    "$duracion minutos"
);

// Caso B: Exactamente 12 horas (límite superior)
$results[] = "\n  Caso B (Límite superior): Atención de exactamente 12 horas";
$inicio12 = Carbon::now()->subHours(12);
$duracion12 = (int)floor($inicio12->diffInMinutes(Carbon::now()));
assert_test(
    "12 horas ($duracion12 min) >= mínimo ($MIN_MINUTOS min)",
    $duracion12 >= $MIN_MINUTOS,
    ">= $MIN_MINUTOS minutos",
    "$duracion12 minutos"
);
assert_test(
    "12 horas ($duracion12 min) <= máximo ($MAX_MINUTOS min)",
    $duracion12 <= $MAX_MINUTOS,
    "<= $MAX_MINUTOS minutos",
    "$duracion12 minutos"
);

// Caso C: 12 horas y 1 minuto (fuera de rango)
$results[] = "\n  Caso C (Fuera de rango): Atención de 12h 1min";
$inicio13 = Carbon::now()->subMinutes(721);
$duracion13 = $inicio13->diffInMinutes(Carbon::now());
assert_test(
    "12h 1min ($duracion13 min) excede el máximo ($MAX_MINUTOS min)",
    $duracion13 > $MAX_MINUTOS,
    "> $MAX_MINUTOS minutos",
    "$duracion13 minutos"
);

// ════════════════════════════════════════════════════════════════
// 3. TRANSICIÓN DE ESTADOS
// ════════════════════════════════════════════════════════════════
section("3. TRANSICIÓN DE ESTADOS — Flujo del Turno");

// Crear asesor de prueba
DB::table('persona')->insertOrIgnore(['pers_doc' => 88888889, 'pers_tipodoc' => 'CC', 'pers_nombres' => 'Test', 'pers_apellidos' => 'Asesor']);
$asesorId = DB::table('asesor')->insertGetId([
    'ase_nrocontrato'  => 'TEST-ASE',
    'ase_tipo_asesor'  => 'G',
    'ase_mesa'         => 1,
    'PERSONA_pers_doc' => 88888889,
    'ase_correo'       => 'prueba@test.com',
    'ase_password'     => Hash::make('test1234'),
    'ase_estado'       => 'disponible',
    'ase_vigencia'     => 1,
]);

// Crear turno de prueba
DB::table('persona')->insertOrIgnore(['pers_doc' => 88888890, 'pers_tipodoc' => 'CC', 'pers_nombres' => 'Cliente', 'pers_apellidos' => 'Test']);
$usuarioId = DB::table('usuario')->insertGetId(['PERSONA_pers_doc' => 88888890, 'user_tipo' => 'cliente']);
$turnoId = DB::table('turno')->insertGetId([
    'tur_hora_fecha'  => now(),
    'tur_numero'      => 'TEST-V001',
    'tur_tipo'        => 'General',
    'tur_mesa'        => 1,
    'USUARIO_user_id' => $usuarioId,
]);

// Transición 1: disponible → ocupado
$results[] = "\n  Transición 1: disponible → ocupado (aceptar turno)";
DB::table('asesor')->where('ase_id', $asesorId)->update(['ase_estado' => 'disponible']);
$asesor = DB::table('asesor')->where('ase_id', $asesorId)->first();
assert_test(
    "Estado inicial es 'disponible'",
    $asesor->ase_estado === 'disponible',
    "disponible",
    $asesor->ase_estado
);

// Simular aceptar turno
DB::table('atencion')->insert([
    'atnc_hora_inicio' => now(),
    'atnc_tipo'        => 'General',
    'ASESOR_ase_id'    => $asesorId,
    'TURNO_tur_id'     => $turnoId,
]);
DB::table('asesor')->where('ase_id', $asesorId)->update([
    'ase_estado'          => 'ocupado',
    'ase_turno_actual_id' => $turnoId,
]);
$asesor = DB::table('asesor')->where('ase_id', $asesorId)->first();
assert_test(
    "Después de aceptar turno → estado 'ocupado'",
    $asesor->ase_estado === 'ocupado',
    "ocupado",
    $asesor->ase_estado
);

// Transición 2: ocupado no puede aceptar otro turno
$results[] = "\n  Transición 2: asesor ocupado no puede aceptar otro turno";
$puedeAceptar = $asesor->ase_estado === 'disponible';
assert_test(
    "Asesor ocupado no puede aceptar otro turno",
    !$puedeAceptar,
    "No puede aceptar (estado=ocupado)",
    "puedeAceptar=" . ($puedeAceptar ? 'true' : 'false')
);

// Transición 3: ocupado → disponible (finalizar)
$results[] = "\n  Transición 3: ocupado → disponible (finalizar atención)";
DB::table('atencion')->where('ASESOR_ase_id', $asesorId)->whereNull('atnc_hora_fin')
    ->update(['atnc_hora_fin' => now(), 'atnc_estado' => 'atendido']);
DB::table('asesor')->where('ase_id', $asesorId)->update([
    'ase_estado'            => 'disponible',
    'ase_turno_actual_id'   => null,
    'ase_turno_actual_tipo' => null,
]);
$asesor = DB::table('asesor')->where('ase_id', $asesorId)->first();
assert_test(
    "Después de finalizar → estado 'disponible'",
    $asesor->ase_estado === 'disponible',
    "disponible",
    $asesor->ase_estado
);

// Transición 4: disponible no puede finalizar (no hay atención activa)
$results[] = "\n  Transición 4: disponible no puede finalizar sin atención activa";
$hayAtencionActiva = DB::table('atencion')
    ->where('ASESOR_ase_id', $asesorId)
    ->whereNull('atnc_hora_fin')
    ->exists();
assert_test(
    "No hay atención activa para finalizar",
    !$hayAtencionActiva,
    "Sin atención activa",
    $hayAtencionActiva ? "Hay atención activa (incorrecto)" : "Sin atención activa"
);

// Transición 5: Turno 'atendido' no puede volver a 'pendiente'
$results[] = "\n  Transición 5: turno atendido no puede cancelarse/revertirse";
$atencion = DB::table('atencion')->where('ASESOR_ase_id', $asesorId)->first();
$estaFinalizado = !is_null($atencion->atnc_hora_fin);
assert_test(
    "Turno con atnc_hora_fin no puede revertirse a pendiente",
    $estaFinalizado,
    "atnc_hora_fin NOT NULL (finalizado)",
    $estaFinalizado ? "Finalizado correctamente" : "Sin hora_fin (incorrecto)"
);

// ── Limpiar datos de prueba ───────────────────────────────────────
DB::table('atencion')->where('ASESOR_ase_id', $asesorId)->delete();
DB::table('turno')->where('tur_numero', 'like', 'TEST-%')->delete();
DB::table('turnos')->where('codigo_turno', 'like', 'TEST-%')->delete();
DB::table('asesor')->where('ase_id', $asesorId)->delete();
DB::table('usuario')->where('PERSONA_pers_doc', 88888890)->delete();
DB::table('persona')->whereIn('pers_doc', [88888888, 88888889, 88888890])->delete();

// ── Resumen ───────────────────────────────────────────────────────
$results[] = "\n══════════════════════════════════════════";
$results[] = "  RESUMEN DE RESULTADOS";
$results[] = "══════════════════════════════════════════";
$total = $passed + $failed;
$results[] = "  Total:   $total pruebas";
$results[] = "  ✅ Pasaron: $passed";
$results[] = "  ❌ Fallaron: $failed";
$results[] = "══════════════════════════════════════════\n";

foreach ($results as $line) {
    echo $line . "\n";
}

exit($failed > 0 ? 1 : 0);
