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
        ]);

        return redirect()->route('asesor.dashboard');
    }

    public function logout()
    {
        session()->forget(['asesor_id', 'asesor_tipo']);
        return redirect()->route('asesor.login');
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
        $cola    = $this->getColaParaTipo($asesor->ase_tipo_asesor);
        $historial = $this->getHistorialHoy($asesor);

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
        $cola     = $this->getColaParaTipo($asesor->ase_tipo_asesor);
        $historial = $this->getHistorialHoy($asesor);

        return response()->json([
            'estado'            => $asesor->ase_estado,
            'turno_actual_id'   => $asesor->ase_turno_actual_id,
            'turno_actual_tipo' => $asesor->ase_turno_actual_tipo,
            'cola_count'        => count($cola),
            'cola'              => $cola,
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

    public function aceptarTurno()
    {
        $asesorId = session('asesor_id');
        if (! $asesorId) {
            return response()->json(['error' => 'no_session'], 401);
        }

        $asesor = Asesor::findOrFail($asesorId);

        if ($asesor->ase_estado !== 'disponible') {
            return response()->json(['error' => 'El asesor no está disponible.'], 422);
        }

        return DB::transaction(function () use ($asesor) {
            $tipo    = $asesor->ase_tipo_asesor;
            $turno   = $this->getSiguienteTurno($tipo);

            if (! $turno) {
                return response()->json(['error' => 'No hay turnos en cola.'], 404);
            }

            // Registrar atención
            $this->crearAtencion($tipo, $turno, $asesor->ase_id);

            // Actualizar estado del asesor
            $asesor->update([
                'ase_estado'           => 'ocupado',
                'ase_turno_actual_id'  => $turno->tur_id,
                'ase_turno_actual_tipo'=> $tipo,
            ]);

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

    public function finalizarAtencion()
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

        // Actualizar hora_fin del registro de atención
        $this->cerrarAtencion($turnoId, $asesor->ase_id);

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

    /** Obtiene los turnos pendientes (sin atención registrada) según el tipo de asesor */
    private function getColaParaTipo(string $tipo): array
    {
        $tipoStr = $this->mapearTipo($tipo);
        
        $atendidos = Atencion::pluck('TURNO_tur_id')->toArray();
        return TurnoUnificado::whereNotIn('tur_id', $atendidos)
            ->where('tur_tipo', $tipoStr)
            ->whereDate('tur_hora_fecha', today())
            ->orderBy('tur_id')
            ->get()
            ->map(fn($t) => [
                'id'     => $t->tur_id,
                'codigo' => $t->tur_numero,
                'hora'   => $t->tur_hora_fecha,
                'tipo'   => $tipo,
            ])->toArray();
    }

    private function getSiguienteTurno(string $tipo)
    {
        $tipoStr = $this->mapearTipo($tipo);
        
        $atendidos = Atencion::pluck('TURNO_tur_id')->toArray();
        return TurnoUnificado::whereNotIn('tur_id', $atendidos)
            ->where('tur_tipo', $tipoStr)
            ->whereDate('tur_hora_fecha', today())
            ->orderBy('tur_id')
            ->first();
    }

    private function crearAtencion(string $tipo, $turno, int $asesorId): void
    {
        $atnc_tipo = match($tipo) {
            'V' => 'Victimas',
            'P' => 'Prioritaria', // "Prioritaria" con 'A' como dictaba tu ENUM
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

    private function cerrarAtencion(int $turnoId, int $asesorId): void
    {
        Atencion::where('TURNO_tur_id', $turnoId)
            ->where('ASESOR_ase_id', $asesorId)
            ->whereNull('atnc_hora_fin')
            ->update(['atnc_hora_fin' => now()]);
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
