<?php

namespace App\Http\Controllers;

use App\Models\Asesor;
use App\Models\Persona;
use App\Models\Usuario;
use App\Models\TurnoUnificado;
use App\Models\Atencion;
use App\Models\SesionAsesor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;

use Illuminate\Validation\Rule;

class AsesorController extends Controller
{
    // ─────────────────────────────────────────────────────────────
    //  AUTH
    // ─────────────────────────────────────────────────────────────

    public function showLogin()
    {
        if (session('asesor_id')) {
            return redirect()->route('asesor.dashboard');
        }
        return view('asesor.login');
    }

    public function showRegister()
    {
        if (session('asesor_id')) {
            return redirect()->route('asesor.dashboard');
        }
        return view('asesor.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'pers_doc'       => [
                'required',
                'numeric',
                'unique:persona,pers_doc',
                function ($attribute, $value, $fail) use ($request) {
                    $tipo = $request->input('pers_tipodoc');
                    $len = strlen((string)$value);
                    if ($tipo === 'CC' && ($len < 6 || $len > 10)) {
                        $fail('La Cédula de Ciudadanía debe tener entre 6 y 10 dígitos.');
                    }
                    if ($tipo === 'PPT' && ($len < 7 || $len > 8)) {
                        $fail('El Permiso de Protección Temporal (PPT) debe tener 7 u 8 dígitos.');
                    }
                    if ($tipo === 'NIT' && ($len < 10 || $len > 11)) {
                        $fail('El NIT debe tener 10 u 11 dígitos (incluyendo el dígito de verificación, sin guiones).');
                    }
                },
            ],
            'pers_tipodoc'   => 'required|string',
            'pers_nombres'   => 'required|string',
            'pers_apellidos' => 'required|string',
            'ase_correo'     => 'required|email|unique:asesor,ase_correo',
            'ase_password'   => 'required|string|min:6|confirmed',
            'ase_tipo_asesor'=> 'required|in:G,V,E',
            'ase_mesa'       => [
                'required',
                'integer',
                'min:1',
                'max:20',
                Rule::unique('asesor', 'ase_mesa')->where(fn ($q) => $q->where('ase_vigencia', 1))
            ],
        ], [
            'ase_mesa.unique' => 'La mesa seleccionada ya está ocupada por otro asesor activo.'
        ]);

        return DB::transaction(function () use ($request) {
            // 1. Crear Persona
            Persona::create([
                'pers_doc'       => $request->pers_doc,
                'pers_tipodoc'   => $request->pers_tipodoc,
                'pers_nombres'   => $request->pers_nombres,
                'pers_apellidos' => $request->pers_apellidos,
                'pers_fecha_nac' => now(),
            ]);

            // 2. Crear Asesor
            $asesor = Asesor::create([
                'ase_nrocontrato'   => 'APE-' . now()->timestamp,
                'ase_tipo_asesor'   => $request->ase_tipo_asesor,
                'ase_mesa'          => $request->ase_mesa,
                'PERSONA_pers_doc' => $request->pers_doc,
                'ase_correo'        => $request->ase_correo,
                'ase_password'      => Hash::make($request->ase_password),
                'ase_estado'        => 'inactivo',
                'ase_vigencia'      => 1,
            ]);

            return redirect()->route('asesor.login')->with('success', 'Cuenta creada exitosamente. Inicie sesión para continuar.');
        });
    }

    public function login(Request $request)
    {
        $request->validate([
            'ase_correo'   => 'required|email',
            'ase_password' => 'required|string',
        ]);

        $asesor = Asesor::where('ase_correo', $request->ase_correo)->first();
                        
        if (! $asesor || ! Hash::check($request->ase_password, $asesor->ase_password)) {
            return back()->withErrors(['ase_correo' => 'Credenciales incorrectas.'])->withInput();
        }

        session([
            'asesor_id'   => $asesor->ase_id,
            'asesor_tipo' => $asesor->ase_tipo_asesor,
            'asesor_ultima_actividad' => time(),
        ]);

        return redirect()->route('asesor.dashboard');
    }

    public function logout(Request $request)
    {
        $asesorId = session('asesor_id');
        
        if ($asesorId) {
            $asesor = Asesor::find($asesorId);
            if ($asesor && $request->has('inactivity')) {
                // Si es por inactividad, NO cerramos el turno, solo lo ponemos en PAUSA
                DB::transaction(function() use ($asesor) {
                    $asesor->update(['ase_estado' => 'en_espera']);
                    
                    $sesion = SesionAsesor::where('ASESOR_ase_id', $asesor->ase_id)
                        ->whereNull('ses_fin')
                        ->latest('ses_id')
                        ->first();
                        
                    if ($sesion) {
                        // Crear pausa por inactividad si no hay una activa
                        $pausaActiva = \App\Models\PausaAsesor::where('SESION_ses_id', $sesion->ses_id)
                            ->whereNull('pau_fin')
                            ->first();
                            
                        if (!$pausaActiva) {
                            \App\Models\PausaAsesor::create([
                                'pau_inicio'    => now(),
                                'SESION_ses_id' => $sesion->ses_id,
                                'pau_motivo'    => 'Inactividad Automática'
                            ]);
                        }
                    }
                });
            } else if ($asesorId) {
                // Si es un logout manual, el comportamiento por defecto es cerrar el turno si existía
                // Pero el frontend ahora advertirá antes de llegar aquí.
                $this->cerrarTurnoTrabajo($asesorId);
            }
        }

        $request->session()->forget(['asesor_id', 'asesor_tipo', 'asesor_ultima_actividad']);
        return redirect()->route('asesor.login');
    }

    public function sesionFinalizada()
    {
        return view('asesor.finalizada');
    }

    // ─────────────────────────────────────────────────────────────
    //  DASHBOARD
    // ─────────────────────────────────────────────────────────────

    public function dashboard()
    {
        $asesorId = session('asesor_id');
        if (! $asesorId) {
            return redirect()->route('asesor.login');
        }

        $asesor  = Asesor::with('persona')->findOrFail($asesorId);
        
        $turnoActual = null;
        $personaActual = null;
        if ($asesor->ase_estado === 'ocupado' && $asesor->ase_turno_actual_id) {
            $turnoActual = TurnoUnificado::find($asesor->ase_turno_actual_id);
            if ($turnoActual) {
                // Reutilizamos el helper privado para consistencia
                $personaActual = $this->getPersonaDelTurno($turnoActual);
            }
        }

        $cola    = $this->getColaParaTipo($asesor->ase_tipo_asesor, $asesorId);
        $historial = $this->getHistorialHoy($asesor);

        // Establecer un window_id único para esta pestaña (aislamiento)
        $windowId = bin2hex(random_bytes(8));
        session(['asesor_window_id' => $windowId]);

        return view('asesor.dashboard', compact('asesor', 'cola', 'historial', 'turnoActual', 'personaActual'));
    }

    // ─────────────────────────────────────────────────────────────
    //  API: datos en tiempo real (polling)
    // ─────────────────────────────────────────────────────────────

    public function apiEstado()
    {
        $asesorId = session('asesor_id');
        if (! $asesorId) {
            return response()->json(['error' => 'no_session'], 401);
        }

        $asesor = Asesor::findOrFail($asesorId);
        return $this->getFullStatusResponse($asesor);
    }

    // ─────────────────────────────────────────────────────────────
    //  API: Datos de Persona del turno activo
    // ─────────────────────────────────────────────────────────────

    public function personaDetalles(Request $request)
    {
        $asesorId = session('asesor_id');
        if (! $asesorId) {
            return response()->json(['error' => 'no_session'], 401);
        }

        $usuarioId = $request->query('usuario_id');
        if (! $usuarioId) {
            return response()->json(['error' => 'usuario_id requerido'], 422);
        }

        $usuario = Usuario::with('persona')->find($usuarioId);
        if (! $usuario || ! $usuario->persona) {
            return response()->json(['error' => 'Persona no encontrada'], 404);
        }

        $p = $usuario->persona;
        return response()->json([
            'pers_doc'        => $p->pers_doc,
            'pers_tipodoc'    => $p->pers_tipodoc,
            'pers_nombres'    => $p->pers_nombres,
            'pers_apellidos'  => $p->pers_apellidos,
            'pers_telefono'   => $p->pers_telefono,
            'pers_fecha_nac'  => $p->pers_fecha_nac,
        ]);
    }

    public function updatePersona(Request $request)
    {
        \Log::info('updatePersona called', ['asesor_id' => session('asesor_id'), 'data' => $request->all()]);
        $asesorId = session('asesor_id');
        if (! $asesorId) {
            return response()->json(['error' => 'no_session'], 401);
        }

        try {
            $request->validate([
                'pers_doc'       => [
                    'required',
                    'numeric',
                    function ($attribute, $value, $fail) use ($request) {
                        $tipo = $request->input('pers_tipodoc');
                        $len = strlen((string)$value);
                        if ($tipo === 'CC' && ($len < 6 || $len > 10)) {
                            $fail('La Cédula de Ciudadanía debe tener entre 6 y 10 dígitos.');
                        }
                        if ($tipo === 'PPT' && ($len < 7 || $len > 8)) {
                            $fail('El Permiso de Protección Temporal (PPT) debe tener 7 u 8 dígitos.');
                        }
                        if ($tipo === 'NIT' && ($len < 10 || $len > 11)) {
                            $fail('El NIT debe tener 10 u 11 dígitos (incluyendo el dígito de verificación, sin guiones).');
                        }
                    },
                ],
                'pers_tipodoc'   => 'required|string|max:45',
                'pers_nombres'   => 'required|string|max:100',
                'pers_apellidos' => 'required|string|max:100',
                'pers_telefono'  => 'nullable|string|max:20',
                'pers_fecha_nac' => 'nullable',
            ]);

            $persona = Persona::find($request->pers_doc);
            if (! $persona) {
                return response()->json(['error' => 'Persona no encontrada'], 404);
            }

            // Limpiar teléfono (solo dígitos para bigint)
            $telefono = $request->pers_telefono
                ? preg_replace('/\D/', '', $request->pers_telefono)
                : null;

            // Normalizar fecha (acepta yyyy-mm-dd o vacío)
            $fecha = null;
            if ($request->pers_fecha_nac) {
                try {
                    $fecha = \Carbon\Carbon::parse($request->pers_fecha_nac)->format('Y-m-d H:i:s');
                } catch (\Exception $e) {
                    $fecha = null;
                }
            }

            $persona->update([
                'pers_tipodoc'   => $request->pers_tipodoc,
                'pers_nombres'   => $request->pers_nombres,
                'pers_apellidos' => $request->pers_apellidos,
                'pers_telefono'  => $telefono ?: null,
                'pers_fecha_nac' => $fecha,
            ]);

            return response()->json(['success' => true, 'persona' => $persona->fresh()]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            \Log::error('updatePersona error: ' . $e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────
    //  ACCIONES
    // ─────────────────────────────────────────────────────────────

    // ─────────────────────────────────────────────────────────────
    //  TURNO DE TRABAJO
    // ─────────────────────────────────────────────────────────────

    public function iniciarTurno(Request $request)
    {
        $asesorId = session('asesor_id');
        if (! $asesorId) {
            return response()->json(['error' => 'no_session'], 401);
        }

        $asesor = Asesor::findOrFail($asesorId);

        if ($asesor->ase_estado !== 'inactivo') {
            return response()->json(['error' => 'El turno ya está activo.'], 422);
        }

        // Cerrar cualquier sesión huérfana (por si acaso)
        SesionAsesor::where('ASESOR_ase_id', $asesorId)
            ->whereNull('ses_fin')
            ->update(['ses_fin' => now()]);

        // Crear nuevo registro de jornada
        $sesion = SesionAsesor::create([
            'ses_inicio'      => now(),
            'ses_fin'         => null,
            'ASESOR_ase_id'   => $asesorId,
        ]);

        $asesor->update(['ase_estado' => 'disponible']);

        return $this->getFullStatusResponse($asesor);
    }

    public function finalizarTurno(Request $request)
    {
        $asesorId = session('asesor_id');
        if (! $asesorId) {
            return response()->json(['error' => 'no_session'], 401);
        }

        $asesor = Asesor::findOrFail($asesorId);

        if ($asesor->ase_estado === 'inactivo') {
            return response()->json(['error' => 'El turno ya está finalizado.'], 422);
        }

        // Si está atendiendo, finalizar la atención primero
        if ($asesor->ase_estado === 'ocupado' && $asesor->ase_turno_actual_id) {
            $this->cerrarAtencion($asesor->ase_turno_actual_id, $asesorId, 'atendido');
            Cache::forget('tv_pending_turns');
            Cache::forget("cola_asesor_{$asesorId}");
        }

        $this->cerrarTurnoTrabajo($asesorId);

        return $this->getFullStatusResponse($asesor);
    }

    // ─────────────────────────────────────────────────────────────
    //  ACCIONES
    // ─────────────────────────────────────────────────────────────

    public function aceptarTurno(Request $request)
    {
        $asesorId = session('asesor_id');
        if (! $asesorId) {
            return response()->json(['error' => 'no_session'], 401);
        }

        $asesor = Asesor::findOrFail($asesorId);

        if ($asesor->ase_estado === 'inactivo') {
            return response()->json(['error' => 'Debe iniciar su turno de trabajo primero.'], 422);
        }

        if ($asesor->ase_estado !== 'disponible') {
            return response()->json(['error' => 'El asesor no está disponible.'], 422);
        }

        return DB::transaction(function () use ($asesor, $request) {
            $tipoAsesor     = $asesor->ase_tipo_asesor;
            $tiposPermitidos = $this->obtenerTiposPermitidos($tipoAsesor);
            $turno = null;

            // Si se envió un ID específico (desde la lista de prioritarios, víctimas o empresario)
            if ($request->has('tur_id')) {
                $mesa = $asesor->ase_mesa ?? 0;
                $turno = TurnoUnificado::where('tur_id', $request->tur_id)
                    ->whereNotIn('tur_id', Atencion::pluck('TURNO_tur_id'))
                    ->whereIn('tur_tipo', $tiposPermitidos)
                    ->first(); // El asesor eligió este turno explícitamente
            }

            // Fallback por prioridad automática (Siguiente en mi mesa o Desbordamiento)
            if (! $turno) {
                $turno = $this->getSiguienteTurno($tipoAsesor, $asesor);
            }

            if (! $turno) {
                return response()->json(['error' => 'No hay turnos disponibles.'], 404);
            }

            // Registrar atención
            $this->crearAtencion($turno, $asesor->ase_id);

            // Invalidar cachés
            Cache::forget('tv_pending_turns');
            Cache::forget("cola_asesor_{$asesor->ase_id}");

            // Re-vincular el turno unificado al asesor actual (por si se desbordó de otra mesa)
            $turno->update([
                'tur_mesa'      => $asesor->ase_mesa ?? 0,
                'ASESOR_ase_id' => $asesor->ase_id,
            ]);

            // Actualizar estado del asesor
            $asesor->update([
                'ase_estado'           => 'ocupado',
                'ase_turno_actual_id'  => $turno->tur_id,
                'ase_turno_actual_tipo'=> $tipoAsesor,
            ]);

            // Actualizar espejo en tabla vieja para la TV
            \App\Models\Turno::where('codigo_turno', $turno->tur_numero)
                ->whereDate('created_at', today())
                ->update(['mesa' => $asesor->ase_mesa ?? 0]);

            // Obtener info del cliente
            $persona = $this->getPersonaDelTurno($turno);
            $usuarioId = $turno->USUARIO_user_id;

            return response()->json([
                'success'      => true,
                'codigo_turno' => $turno->tur_numero,
                'usuario_id'   => $usuarioId,
                'persona'      => $persona,
                'ase_estado'   => 'ocupado',
            ]);
        });
    }

    public function finalizarAtencion(Request $request)
    {
        $asesorId = session('asesor_id');
        if (! $asesorId) {
            return response()->json(['error' => 'no_session'], 401);
        }

        $asesor = Asesor::findOrFail($asesorId);

        if ($asesor->ase_estado !== 'ocupado') {
            return response()->json(['error' => 'No hay atención activa.'], 422);
        }

        $turnoId = $asesor->ase_turno_actual_id;
        $estadoAtencion = $request->input('estado', 'atendido'); // Puede llegar 'ausente'

        // Actualizar hora_fin del registro de atención
        $this->cerrarAtencion($turnoId, $asesor->ase_id, $estadoAtencion);

        // Invalidar cachés
        Cache::forget('tv_pending_turns');
        Cache::forget("cola_asesor_{$asesorId}");

        $asesor->update([
            'ase_estado'            => 'disponible',
            'ase_turno_actual_id'   => null,
            'ase_turno_actual_tipo' => null,
        ]);

        return $this->getFullStatusResponse($asesor);
    }

    public function toggleEspera()
    {
        $asesorId = session('asesor_id');
        if (! $asesorId) {
            return response()->json(['error' => 'no_session'], 401);
        }

        $asesor = Asesor::findOrFail($asesorId);

        if ($asesor->ase_estado === 'ocupado') {
            return response()->json(['error' => 'Finalice la atención actual antes de cambiar estado.'], 422);
        }

        $nuevoEstado = $asesor->ase_estado === 'disponible' ? 'en_espera' : 'disponible';
        
        DB::transaction(function() use ($asesor, $nuevoEstado) {
            $asesor->update(['ase_estado' => $nuevoEstado]);

            // Obtener la sesión activa
            $sesion = SesionAsesor::where('ASESOR_ase_id', $asesor->ase_id)
                ->whereNull('ses_fin')
                ->latest('ses_id')
                ->first();

            if ($sesion) {
                if ($nuevoEstado === 'en_espera') {
                    // Iniciar pausa
                    \App\Models\PausaAsesor::create([
                        'pau_inicio'    => now(),
                        'SESION_ses_id' => $sesion->ses_id,
                        'pau_motivo'    => 'Descanso/Pausa'
                    ]);
                } else {
                    // Finalizar última pausa pendiente
                    $pausa = \App\Models\PausaAsesor::where('SESION_ses_id', $sesion->ses_id)
                        ->whereNull('pau_fin')
                        ->latest('pau_id')
                        ->first();
                    
                    if ($pausa) {
                        $pausa->update(['pau_fin' => now()]);
                    }
                }
            }
        });

        return $this->getFullStatusResponse($asesor);
    }

    // ─────────────────────────────────────────────────────────────
    //  HELPERS PRIVADOS
    // ─────────────────────────────────────────────────────────────

    private function getFullStatusResponse($asesor)
    {
        $asesorId = $asesor->ase_id;
        $tipo     = $asesor->ase_tipo_asesor;
        
        $colaPersonal = $this->getColaParaTipo($tipo, $asesorId);
        
        // Separar por tipo para la interfaz
        $colaGeneral     = array_values(array_filter($colaPersonal, fn($t) => $t['tipo'] === 'General'));
        $colaPrioritaria = array_values(array_filter($colaPersonal, fn($t) => in_array($t['tipo'], ['Prioritario', 'Especial'])));
        $colaVictimas    = array_values(array_filter($colaPersonal, fn($t) => $t['tipo'] === 'Victimas'));
        $colaEmpresario  = array_values(array_filter($colaPersonal, fn($t) => $t['tipo'] === 'Empresario'));

        // Hay turnos del tipo principal pendientes?
        $tipoPrincipal   = $this->tipoPrincipal($tipo);
        $hayPrincipales  = !empty(array_filter($colaPersonal, fn($t) => $t['tipo'] === $tipoPrincipal && $t['habilitado']));

        $historial = $this->getHistorialHoy($asesor);

        // Obtener sesión de trabajo activa con pausas
        $sesionActiva = SesionAsesor::with('pausas')
            ->where('ASESOR_ase_id', $asesorId)
            ->whereNull('ses_fin')
            ->orderBy('ses_id', 'desc')
            ->first();

        $totalPausaSegundos = 0;
        if ($sesionActiva) {
            foreach ($sesionActiva->pausas as $pausa) {
                $fin = $pausa->pau_fin ?? now();
                $totalPausaSegundos += $pausa->pau_inicio->diffInSeconds($fin);
            }
        }

        // Obtener detalles del turno actual si está ocupado
        $turnoActual = null;
        $personaActual = null;
        if ($asesor->ase_estado === 'ocupado' && $asesor->ase_turno_actual_id) {
            $turnoActual = TurnoUnificado::find($asesor->ase_turno_actual_id);
            if ($turnoActual) {
                $personaActual = $this->getPersonaDelTurno($turnoActual);
            }
        }

        return response()->json([
            'success'           => true,
            'estado'            => $asesor->ase_estado,
            'turno_actual_id'   => $asesor->ase_turno_actual_id,
            'turno_actual_codigo'=> $turnoActual ? $turnoActual->tur_numero : null,
            'usuario_id'        => $turnoActual ? $turnoActual->USUARIO_user_id : null,
            'persona'           => $personaActual,
            'turno_actual_tipo' => $asesor->ase_turno_actual_tipo,
            'tipo_asesor'       => $tipo,
            'hay_principales'   => $hayPrincipales,
            'cola_count'        => count($colaGeneral) + count($colaPrioritaria) + count($colaVictimas) + count($colaEmpresario),
            'cola_prioritaria'  => $colaPrioritaria,
            'cola_general'      => $colaGeneral,
            'cola_victimas'     => $colaVictimas,
            'cola_empresario'   => $colaEmpresario,
            'historial'         => $historial,
            'ses_inicio'        => $sesionActiva ? $sesionActiva->ses_inicio->toIso8601String() : null,
            'total_pausa_ms'    => $totalPausaSegundos * 1000,
            'en_pausa'          => $asesor->ase_estado === 'en_espera',
        ]);
    }

    private function mapearTipo(string $tipoLetra): string
    {
        return match($tipoLetra) {
            'V' => 'Victimas',
            'P' => 'Prioritario',
            'E' => 'Empresario',
            default => 'General',
        };
    }

    /** Obtiene los turnos pendientes que este asesor puede atender */
    private function getColaParaTipo(string $tipo, int $asesorId): array
    {
        $asesor = Asesor::find($asesorId);
        if (!$asesor) return [];

        $tiposPermitidos = $this->obtenerTiposPermitidos($tipo);
        $atendidos = Atencion::pluck('TURNO_tur_id')->toArray();
        $hoy = today();
        $tipoPrincipal = $this->tipoPrincipal($tipo);

        // Obtener todos los turnos permitidos en un solo paso
        // habilitados todos para permitir selección manual libre según la nueva jerarquía
        return TurnoUnificado::whereNotIn('tur_id', $atendidos)
            ->whereIn('tur_tipo', $tiposPermitidos)
            ->whereDate('tur_hora_fecha', $hoy)
            ->orderByRaw("FIELD(tur_tipo, 'Empresario', 'Victimas', 'Prioritario', 'General')")
            ->orderBy('tur_id', 'asc')
            ->get()
            ->map(fn($t) => [
                'id'        => $t->tur_id,
                'codigo'    => $t->tur_numero,
                'hora'      => $t->tur_hora_fecha,
                'tipo'      => $t->tur_tipo,
                'principal' => ($t->tur_tipo === $tipoPrincipal),
                'habilitado'=> true, // Siempre habilitado para permitir desbordamiento y cumplimiento de prioridad
            ])->toArray();
    }

    private function getSiguienteTurno(string $tipoAsesor, Asesor $asesor)
    {
        $atendidos = Atencion::pluck('TURNO_tur_id')->toArray();
        $hoy = today();
        $tiposPermitidos = $this->obtenerTiposPermitidos($tipoAsesor);

        // Jerarquía de Turnos (Global): Empresario > Victimas > Prioritario > General
        // El sistema asigna el tipo más importante disponible entre los permitidos para el asesor,
        // respetando estrictamente el orden de llegada (FIFO) dentro de cada categoría.
        return TurnoUnificado::whereNotIn('tur_id', $atendidos)
            ->whereIn('tur_tipo', $tiposPermitidos)
            ->whereDate('tur_hora_fecha', $hoy)
            ->orderByRaw("FIELD(tur_tipo, 'Empresario', 'Victimas', 'Prioritario', 'General')")
            ->orderBy('tur_id', 'asc')
            ->first();
    }

    private function obtenerTiposPermitidos(string $tipoAsesor): array
    {
        return match($tipoAsesor) {
            'V'  => ['Victimas', 'Prioritario', 'General', 'Empresario'],
            'G'  => ['Prioritario', 'General', 'Empresario'],
            'E'  => ['Empresario', 'Prioritario', 'General'],
            default => ['General'],
        };
    }

    /** Tipo principal del asesor (el que debe atender primero) */
    private function tipoPrincipal(string $tipoAsesor): string
    {
        return match($tipoAsesor) {
            'V'  => 'Victimas',
            'P'  => 'Prioritario',
            'E'  => 'Empresario',
            default => 'General',
        };
    }

    private function crearAtencion($turno, int $asesorId): void
    {
        $atnc_tipo = match($turno->tur_tipo) {
            'Victimas' => 'Victimas',
            'Prioritario' => 'Prioritaria',
            default => 'General',
        };

        Atencion::create([
            'atnc_hora_inicio' => now(),
            'atnc_hora_fin'    => null,
            'atnc_tipo'        => $atnc_tipo,
            'ASESOR_ase_id'    => $asesorId,
            'TURNO_tur_id'     => $turno->tur_id,
        ]);
    }

    private function cerrarAtencion(int $turnoId, int $asesorId, string $estado = 'atendido'): void
    {
        Atencion::where('TURNO_tur_id', $turnoId)
            ->where('ASESOR_ase_id', $asesorId)
            ->whereNull('atnc_hora_fin')
            ->update([
                'atnc_hora_fin' => now(),
                'atnc_estado'   => $estado
            ]);
    }

    private function getPersonaDelTurno($turno): ?array
    {
        try {
            $usuarioId = $turno->USUARIO_user_id;
            $persona = \App\Models\Usuario::with('persona')->find($usuarioId)?->persona;
            if (! $persona) return null;
            return [
                'nombres'   => trim($persona->pers_nombres . ' ' . $persona->pers_apellidos),
                'documento' => $persona->pers_doc,
                'telefono'  => $persona->pers_telefono,
            ];
        } catch (\Exception $e) {
            return null;
        }
    }

    private function getHistorialHoy(Asesor $asesor): array
    {
        $id = $asesor->ase_id;

        return Atencion::with('turno')
            ->where('ASESOR_ase_id', $id)
            ->whereDate('atnc_hora_inicio', today())
            ->orderBy('atnc_id', 'desc')
            ->get()
            ->map(function($a) {
                return [
                    'codigo'     => $a->turno->tur_numero ?? '-',
                    'hora_inicio'=> $a->atnc_hora_inicio,
                    'hora_fin'   => $a->atnc_hora_fin,
                ];
            })->toArray();
    }

    public function cerrarTurnoTrabajo(int $asesorId): void
    {
        // Cerrar sesión de trabajo activa
        SesionAsesor::where('ASESOR_ase_id', $asesorId)
            ->whereNull('ses_fin')
            ->update(['ses_fin' => now()]);

        // Poner asesor en inactivo y limpiar turno actual
        Asesor::where('ase_id', $asesorId)->update([
            'ase_estado'            => 'inactivo',
            'ase_turno_actual_id'   => null,
            'ase_turno_actual_tipo' => null,
        ]);
    }
}
