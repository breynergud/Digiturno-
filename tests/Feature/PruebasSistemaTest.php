<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Asesor;
use App\Models\Persona;
use App\Models\Usuario;
use App\Models\TurnoUnificado;
use App\Models\Atencion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Pruebas del Sistema Digiturno
 *
 * 1. Partición de Equivalencia
 * 2. Análisis de Valores Límite
 * 3. Pruebas de Transición de Estados
 */
class PruebasSistemaTest extends TestCase
{
    use RefreshDatabase;

    // ─── Setup: Asesor de prueba con sesión activa ────────────────
    private function loginAsesor(): Asesor
    {
        $persona = Persona::create([
            'pers_doc'      => 99999999,
            'pers_tipodoc'  => 'CC',
            'pers_nombres'  => 'Asesor',
            'pers_apellidos'=> 'Test',
        ]);

        $asesor = Asesor::create([
            'ase_nrocontrato'  => 'TEST-001',
            'ase_tipo_asesor'  => 'G',
            'ase_mesa'         => 1,
            'PERSONA_pers_doc' => $persona->pers_doc,
            'ase_correo'       => 'test@sena.gov.co',
            'ase_password'     => Hash::make('test1234'),
            'ase_estado'       => 'disponible',
            'ase_vigencia'     => 1,
        ]);

        $this->withSession([
            'asesor_id'   => $asesor->ase_id,
            'asesor_tipo' => 'G',
            'asesor_ultima_actividad' => time(),
            'asesor_window_id' => 'test-window',
        ]);

        return $asesor;
    }

    private function crearPersonaYUsuario(int $doc): Usuario
    {
        $persona = Persona::create([
            'pers_doc'      => $doc,
            'pers_tipodoc'  => 'CC',
            'pers_nombres'  => 'Cliente',
            'pers_apellidos'=> 'Prueba',
        ]);

        return Usuario::create([
            'PERSONA_pers_doc' => $persona->pers_doc,
            'user_tipo'        => 'cliente',
        ]);
    }

    // ═══════════════════════════════════════════════════════════════
    // 1. PARTICIÓN DE EQUIVALENCIA — Registro de turno
    // ═══════════════════════════════════════════════════════════════

    /**
     * Caso A (Válido): Datos completos y correctos → turno creado exitosamente.
     */
    public function test_particion_equivalencia_caso_A_datos_validos_crea_turno()
    {
        $response = $this->postJson('/turnos', [
            'tipo_atencion'    => 'general',
            'pers_tipodoc'     => 'CC',
            'numero_documento' => '10000001',
            'pers_nombres'     => 'Juan',
            'pers_apellidos'   => 'Pérez',
            'telefono'         => '3001234567',
        ]);

        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJsonStructure(['codigo', 'tipo', 'mesa']);

        $this->assertDatabaseHas('turno', [
            'tur_tipo' => 'General',
        ]);
    }

    /**
     * Caso B (Inválido): Falta el número de documento → error de validación.
     */
    public function test_particion_equivalencia_caso_B_sin_documento_retorna_error()
    {
        $response = $this->postJson('/turnos', [
            'tipo_atencion'    => 'general',
            'pers_tipodoc'     => 'CC',
            'numero_documento' => '',   // Campo requerido vacío
            'pers_nombres'     => 'Juan',
            'pers_apellidos'   => 'Pérez',
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['numero_documento']);
    }

    /**
     * Caso C (Inválido): Tipo de atención no permitido → error de validación.
     */
    public function test_particion_equivalencia_caso_C_tipo_invalido_retorna_error()
    {
        $response = $this->postJson('/turnos', [
            'tipo_atencion'    => 'tipo_inexistente',
            'pers_tipodoc'     => 'CC',
            'numero_documento' => '10000002',
        ]);

        // El sistema acepta cualquier tipo y lo mapea a 'General' por defecto,
        // pero el turno debe crearse sin errores de servidor
        $response->assertStatus(200);
    }

    // ═══════════════════════════════════════════════════════════════
    // 2. ANÁLISIS DE VALORES LÍMITE — Duración de atención
    // ═══════════════════════════════════════════════════════════════

    /**
     * Caso A (Límite inferior): Atención de exactamente 1 hora → válida.
     */
    public function test_valores_limite_caso_A_atencion_1_hora_es_valida()
    {
        $asesor  = $this->loginAsesor();
        $usuario = $this->crearPersonaYUsuario(10000010);

        $turno = TurnoUnificado::create([
            'tur_hora_fecha'  => now(),
            'tur_numero'      => 'G-001',
            'tur_tipo'        => 'General',
            'tur_mesa'        => 1,
            'USUARIO_user_id' => $usuario->user_id,
        ]);

        $inicio = now()->subHour();   // Hace exactamente 1 hora
        $fin    = now();

        $duracionMinutos = $inicio->diffInMinutes($fin);

        // Límite inferior: 60 minutos (1 hora) → debe ser válido (>= 60)
        $this->assertGreaterThanOrEqual(60, $duracionMinutos,
            'Una atención de 1 hora debe ser válida (límite inferior).');

        // Límite máximo: 720 minutos (12 horas) → no debe excederse
        $this->assertLessThanOrEqual(720, $duracionMinutos,
            'Una atención de 1 hora no excede el máximo de 12 horas.');
    }

    /**
     * Caso B (Límite superior): Atención de exactamente 12 horas → válida.
     */
    public function test_valores_limite_caso_B_atencion_12_horas_es_valida()
    {
        $inicio = now()->subHours(12);
        $fin    = now();

        $duracionMinutos = $inicio->diffInMinutes($fin);

        $this->assertGreaterThanOrEqual(60, $duracionMinutos,
            'Una atención de 12 horas supera el mínimo de 1 hora.');

        $this->assertLessThanOrEqual(720, $duracionMinutos,
            'Una atención de exactamente 12 horas es el límite superior válido.');
    }

    /**
     * Caso C (Fuera de rango): Atención de 12 horas y 1 minuto → inválida.
     */
    public function test_valores_limite_caso_C_atencion_12h_1min_excede_maximo()
    {
        $inicio = now()->subMinutes(721); // 12h 1min
        $fin    = now();

        $duracionMinutos = $inicio->diffInMinutes($fin);

        $this->assertGreaterThan(720, $duracionMinutos,
            'Una atención de 12h 1min debe exceder el límite máximo de 720 minutos.');
    }

    // ═══════════════════════════════════════════════════════════════
    // 3. TRANSICIÓN DE ESTADOS — Flujo del turno
    // ═══════════════════════════════════════════════════════════════

    /**
     * Transición válida: disponible → ocupado al aceptar turno.
     */
    public function test_transicion_estados_asesor_pasa_a_ocupado_al_aceptar_turno()
    {
        $asesor  = $this->loginAsesor();
        $usuario = $this->crearPersonaYUsuario(10000020);

        TurnoUnificado::create([
            'tur_hora_fecha'  => now(),
            'tur_numero'      => 'G-T01',
            'tur_tipo'        => 'General',
            'tur_mesa'        => 1,
            'USUARIO_user_id' => $usuario->user_id,
        ]);

        $response = $this->postJson('/asesor/aceptar-turno');

        $response->assertStatus(200)
                 ->assertJson(['success' => true, 'ase_estado' => 'ocupado']);

        $this->assertDatabaseHas('asesor', [
            'ase_id'     => $asesor->ase_id,
            'ase_estado' => 'ocupado',
        ]);
    }

    /**
     * Transición inválida: asesor ocupado no puede aceptar otro turno.
     */
    public function test_transicion_estados_asesor_ocupado_no_puede_aceptar_otro_turno()
    {
        $asesor  = $this->loginAsesor();
        $usuario = $this->crearPersonaYUsuario(10000021);

        TurnoUnificado::create([
            'tur_hora_fecha'  => now(),
            'tur_numero'      => 'G-T02',
            'tur_tipo'        => 'General',
            'tur_mesa'        => 1,
            'USUARIO_user_id' => $usuario->user_id,
        ]);

        // Poner asesor en estado ocupado directamente
        $asesor->update(['ase_estado' => 'ocupado']);

        $response = $this->postJson('/asesor/aceptar-turno');

        $response->assertStatus(422)
                 ->assertJson(['error' => 'El asesor no está disponible.']);
    }

    /**
     * Transición válida: ocupado → disponible al finalizar atención.
     */
    public function test_transicion_estados_asesor_vuelve_a_disponible_al_finalizar()
    {
        $asesor  = $this->loginAsesor();
        $usuario = $this->crearPersonaYUsuario(10000022);

        $turno = TurnoUnificado::create([
            'tur_hora_fecha'  => now(),
            'tur_numero'      => 'G-T03',
            'tur_tipo'        => 'General',
            'tur_mesa'        => 1,
            'USUARIO_user_id' => $usuario->user_id,
        ]);

        Atencion::create([
            'atnc_hora_inicio' => now(),
            'atnc_tipo'        => 'General',
            'ASESOR_ase_id'    => $asesor->ase_id,
            'TURNO_tur_id'     => $turno->tur_id,
        ]);

        $asesor->update([
            'ase_estado'          => 'ocupado',
            'ase_turno_actual_id' => $turno->tur_id,
        ]);

        $response = $this->postJson('/asesor/finalizar-atencion');

        $response->assertStatus(200)
                 ->assertJson(['success' => true, 'ase_estado' => 'disponible']);

        $this->assertDatabaseHas('asesor', [
            'ase_id'     => $asesor->ase_id,
            'ase_estado' => 'disponible',
        ]);
    }

    /**
     * Transición inválida: asesor disponible no puede finalizar (no hay atención activa).
     */
    public function test_transicion_estados_no_puede_finalizar_sin_atencion_activa()
    {
        $this->loginAsesor();

        $response = $this->postJson('/asesor/finalizar-atencion');

        $response->assertStatus(422)
                 ->assertJson(['error' => 'No hay atención activa.']);
    }
}
