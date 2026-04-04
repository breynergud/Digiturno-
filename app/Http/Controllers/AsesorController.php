<?php

namespace App\Http\Controllers;

use App\Models\Asesor;
use App\Models\Persona;
use App\Models\Usuario;
use App\Models\TurnoUnificado;
use App\Models\Atencion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;

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
        $cola    = $this->getColaParaTipo($asesor->ase_tipo_asesor, $asesorId);
        $historial = $this->getHistorialHoy($asesor);

        // Establecer un window_id único para esta pestaña (aislamiento)
        $windowId = bin2hex(random_bytes(8));
        session(['asesor_window_id' => $windowId]);

        return view('asesor.dashboard', compact('asesor', 'cola', 'historial'));
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

        $asesor   = Asesor::findOrFail($asesorId);
        $tipo     = $asesor->ase_tipo_asesor;
        
        // 1. Obtener mi cola filtrada (Cacheado por 5s para reducir carga de polling múltiple)
        $cacheKey = "cola_asesor_{$asesorId}";
        $colaPersonal = Cache::remember($cacheKey, 5, function() use ($tipo, $asesorId) {
            return $this->getColaParaTipo($tipo, $asesorId);
        });
        
        // 2. Separar por tipo para la interfaz
        $colaGeneral     = array_values(array_filter($colaPersonal, fn($t) => $t['tipo'] === 'General'));
        $colaPrioritaria = array_values(array_filter($colaPersonal, fn($t) => $t['tipo'] === 'Prioritario'));
        $colaVictimas    = array_values(array_filter($colaPersonal, fn($t) => $t['tipo'] === 'Victimas'));

        $historial = $this->getHistorialHoy($asesor);

        return response()->json([
            'estado'            => $asesor->ase_estado,
            'turno_actual_id'   => $asesor->ase_turno_actual_id,
            'turno_actual_tipo' => $asesor->ase_turno_actual_tipo,
            'cola_count'        => count($colaGeneral) + count($colaPrioritaria) + count($colaVictimas),
            'cola_prioritaria'  => $colaPrioritaria,
            'cola_general'      => $colaGeneral,
            'cola_victimas'     => $colaVictimas,
            'historial'         => $historial,
        ]);
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
        $asesorId = session('asesor_id');
        if (! $asesorId) {
            return response()->json(['error' => 'no_session'], 401);
        }

        $request->validate([
            'pers_doc'       => 'required',
            'pers_tipodoc'   => 'required|string|max:45',
            'pers_nombres'   => 'required|string|max:100',
            'pers_apellidos' => 'required|string|max:100',
            'pers_telefono'  => 'nullable|string|max:20',
            'pers_fecha_nac' => 'nullable|date',
        ]);

        $persona = Persona::find($request->pers_doc);
        if (! $persona) {
            return response()->json(['error' => 'Persona no encontrada'], 404);
        }

        $persona->update([
            'pers_tipodoc'   => $request->pers_tipodoc,
            'pers_nombres'   => $request->pers_nombres,
            'pers_apellidos' => $request->pers_apellidos,
            'pers_telefono'  => $request->pers_telefono,
            'pers_fecha_nac' => $request->pers_fecha_nac,
        ]);

        return response()->json(['success' => true, 'persona' => $persona]);
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

        if ($asesor->ase_estado !== 'disponible') {
            return response()->json(['error' => 'El asesor no está disponible.'], 422);
        }

        return DB::transaction(function () use ($asesor, $request) {
            $tipoAsesor     = $asesor->ase_tipo_asesor;
            $tiposPermitidos = $this->obtenerTiposPermitidos($tipoAsesor);
            $turno = null;

            // Si se envió un ID específico (desde la lista de prioritarios o víctimas)
            if ($request->has('tur_id')) {
                $turno = TurnoUnificado::where('tur_id', $request->tur_id)
                    ->whereNotIn('tur_id', Atencion::pluck('TURNO_tur_id')) // No atendido
                    ->whereIn('tur_tipo', $tiposPermitidos)
                    ->where('tur_mesa', $asesor->ase_mesa) // Estricto a mi mesa
                    ->first();
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
                'tur_mesa'      => $asesor->ase_mesa,
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
                ->update(['mesa' => $asesor->ase_mesa]);

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

    public function finalizaratencion(Request $request)
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

        return response()->json(['success' => true, 'ase_estado' => 'disponible']);
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
        $asesor->update(['ase_estado' => $nuevoEstado]);

        return response()->json(['success' => true, 'ase_estado' => $nuevoEstado]);
    }

    // ─────────────────────────────────────────────────────────────
    //  HELPERS PRIVADOS
    // ─────────────────────────────────────────────────────────────

    private function mapearTipo(string $tipoLetra): string
    {
        return match($tipoLetra) {
            'V' => 'Victimas',
            'P' => 'Prioritario',
            default => 'General',
        };
    }

    /** Obtiene los turnos pendientes asignados a la mesa de este asesor */
    private function getColaParaTipo(string $tipo, int $asesorId): array
    {
        $asesor = Asesor::find($asesorId);
        if (!$asesor) return [];

        $tiposPermitidos = $this->obtenerTiposPermitidos($tipo);
        
        $atendidos = Atencion::pluck('TURNO_tur_id')->toArray();
        return TurnoUnificado::whereNotIn('tur_id', $atendidos)
            ->whereIn('tur_tipo', $tiposPermitidos)
            ->where('tur_mesa', $asesor->ase_mesa) // <-- Filtro por MESA
            ->whereDate('tur_hora_fecha', today())
            ->orderByRaw("FIELD(tur_tipo, 'Victimas', 'Prioritario', 'General')")
            ->orderBy('tur_id')
            ->get()
            ->map(fn($t) => [
                'id'     => $t->tur_id,
                'codigo' => $t->tur_numero,
                'hora'   => $t->tur_hora_fecha,
                'tipo'   => $t->tur_tipo,
            ])->toArray();
    }

    private function getSiguienteTurno(string $tipoAsesor, Asesor $asesor)
    {
        $tiposPermitidos = $this->obtenerTiposPermitidos($tipoAsesor);
        
        $atendidos = Atencion::pluck('TURNO_tur_id')->toArray();
        $hoy = today();

        // 1. Inanición (Prioridad Dinámica): Buscar General con más de 35 min de espera
        if (in_array('General', $tiposPermitidos)) {
            $turnoCritico = TurnoUnificado::whereNotIn('tur_id', $atendidos)
                ->where('tur_tipo', 'General')
                ->where('tur_mesa', $asesor->ase_mesa)
                ->whereDate('tur_hora_fecha', $hoy)
                ->where('tur_hora_fecha', '<=', now()->subMinutes(35))
                ->orderBy('tur_id', 'asc') // El más viejo
                ->first();
            
            if ($turnoCritico) return $turnoCritico;
        }

        // 2. Comportamiento normal: Buscar en MI mesa
        $turnoNormal = TurnoUnificado::whereNotIn('tur_id', $atendidos)
            ->whereIn('tur_tipo', $tiposPermitidos)
            ->where('tur_mesa', $asesor->ase_mesa) // <-- Filtro por MESA
            ->whereDate('tur_hora_fecha', $hoy)
            ->orderByRaw("FIELD(tur_tipo, 'Victimas', 'Prioritario', 'General')")
            ->orderBy('tur_id', 'asc')
            ->first();

        if ($turnoNormal) return $turnoNormal;

        // 3. Desbordamiento: Si mi mesa no tiene turnos, robar de otra mesa
        // que coincida con mis tipos permitidos.
        return TurnoUnificado::whereNotIn('tur_id', $atendidos)
            ->whereIn('tur_tipo', $tiposPermitidos)
            // Sin filtro de mesa
            ->whereDate('tur_hora_fecha', $hoy)
            ->orderByRaw("FIELD(tur_tipo, 'Victimas', 'Prioritario', 'General')")
            ->orderBy('tur_id', 'asc')
            ->first();
    }

    private function obtenerTiposPermitidos(string $tipoAsesor): array
    {
        return match($tipoAsesor) {
            'V' => ['Victimas'],
            'G' => ['Prioritario', 'General'],
            'GO'=> ['General'],
            default => ['General'],
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
}
