<?php

namespace App\Http\Controllers;

use App\Models\Asesor;
use App\Models\Coordinador;
use App\Models\Persona;
use App\Models\TurnoUnificado;
use App\Models\Atencion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class CoordinadorController extends Controller
{
    // ─────────────────────────────────────────────────────────────
    //  AUTH
    // ─────────────────────────────────────────────────────────────

    public function showLogin()
    {
        if (session('coor_id')) {
            return redirect()->route('coordinador.dashboard');
        }
        return view('coordinador.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'coor_correo'   => 'required|email',
            'coor_password' => 'required|string',
        ]);

        $coor = Coordinador::where('coor_correo', $request->coor_correo)->first();
                         
        if (! $coor || ! Hash::check($request->coor_password, $coor->coor_password)) {
            return back()->withErrors(['coor_correo' => 'Credenciales incorrectas.'])->withInput();
        }

        session([
            'coor_id' => $coor->coor_id,
            'coor_ultima_actividad' => time(),
        ]);

        return redirect()->route('coordinador.dashboard');
    }

    public function logout()
    {
        session()->forget(['coor_id', 'coor_ultima_actividad']);
        return redirect()->route('coordinador.login');
    }

    public function sesionFinalizada()
    {
        return view('coordinador.finalizada');
    }

    // ─────────────────────────────────────────────────────────────
    //  DASHBOARD
    // ─────────────────────────────────────────────────────────────

    public function dashboard()
    {
        if (! session('coor_id')) {
            return redirect()->route('coordinador.login');
        }

        $coordinador = Coordinador::with('persona')->findOrFail(session('coor_id'));
        $asesores    = Asesor::with('persona')->get();
        $colaEmpresario = $this->getColaEmpresario();

        // Establecer un window_id único para esta pestaña (aislamiento)
        $windowId = bin2hex(random_bytes(8));
        session(['coor_window_id' => $windowId]);
        
        return view('coordinador.dashboard', compact('coordinador', 'asesores', 'colaEmpresario'));
    }

    // ─────────────────────────────────────────────────────────────
    //  API: tiempo real
    // ─────────────────────────────────────────────────────────────

    public function apiEstado()
    {
        if (! session('coor_id')) {
            return response()->json(['error' => 'no_session'], 401);
        }

        $asesores = Asesor::with('persona')->get()->map(function($a) {
            return [
                'ase_id' => $a->ase_id,
                'nombre' => $a->persona->pers_nombres . ' ' . $a->persona->pers_apellidos,
                'tipo'   => $a->ase_tipo_asesor,
                'estado' => $a->ase_estado,
                'turno'  => $a->ase_turno_actual_id,
            ];
        });

        return response()->json([
            'asesores'       => $asesores,
            'colaEmpresario' => $this->getColaEmpresario(),
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    //  ACCIONES
    // ─────────────────────────────────────────────────────────────

    public function reasignarAsesor(Request $request)
    {
        $request->validate([
            'ase_id'   => 'required|exists:asesor,ase_id',
            'nuevo_tipo' => 'required|in:V,G',
        ]);

        $asesor = Asesor::findOrFail($request->ase_id);
        $asesor->update(['ase_tipo_asesor' => $request->nuevo_tipo]);

        return response()->json(['success' => true]);
    }

    public function aceptarTurno()
    {
        $coorId = session('coor_id');
        if (! $coorId) return response()->json(['error' => 'no_session'], 401);

        $coordinador = Coordinador::findOrFail($coorId);

        if ($coordinador->coor_estado !== 'disponible') {
            return response()->json(['error' => 'El coordinador ya está ocupado.'], 422);
        }

        return DB::transaction(function () use ($coordinador) {
            $turno = $this->getSiguienteTurnoEmpresario();

            if (! $turno) {
                return response()->json(['error' => 'No hay turnos empresariales.'], 404);
            }

            // Registrar atención (tipo Especial/Empresario)
            Atencion::create([
                'atnc_hora_inicio' => now(),
                'atnc_tipo'        => 'General', 
                'TURNO_tur_id'     => $turno->tur_id,
                'COORDINADOR_coor_id' => $coordinador->coor_id,
            ]);

            $coordinador->update([
                'coor_estado' => 'ocupado'
            ]);

            return response()->json([
                'success'      => true,
                'codigo_turno' => $turno->tur_numero,
                'ase_estado'   => 'ocupado',
            ]);
        });
    }

    // ─────────────────────────────────────────────────────────────
    //  REPORTE SEMANAL
    // ─────────────────────────────────────────────────────────────

    public function reporteSemanal(Request $request)
    {
        $startOfWeek = now()->startOfWeek();
        $endOfWeek   = now()->endOfWeek();

        $asesor_id_filter = $request->input('asesor_id');
        $search = $request->input('search');
        
        $asesoresDropdown = Asesor::with('persona')->get();

        $formatTime = function($seconds) {
            if ($seconds <= 0) return '0s';
            $seconds = round($seconds);
            $hours = floor($seconds / 3600);
            $minutes = floor(($seconds % 3600) / 60);
            $sec = $seconds % 60;
            
            $res = [];
            if ($hours > 0) $res[] = $hours . 'h';
            if ($minutes > 0) $res[] = $minutes . 'm';
            if ($sec > 0 || count($res) == 0) $res[] = $sec . 's';
            
            return implode(' ', $res);
        };

        $query = Asesor::with(['persona']);
        if ($asesor_id_filter) {
            $query->where('ase_id', $asesor_id_filter);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('persona', function($subQ) use ($search) {
                    $subQ->where('pers_nombres', 'LIKE', "%{$search}%")
                         ->orWhere('pers_apellidos', 'LIKE', "%{$search}%");
                })->orWhere('ase_mesa', 'LIKE', "%{$search}%");
            });
        }

        $reporte = $query
            ->get()
            ->map(function($asesor) use ($startOfWeek, $endOfWeek, $formatTime) {
                $atencionesQuery = Atencion::with('turno')
                    ->where('ASESOR_ase_id', $asesor->ase_id)
                    ->whereBetween('atnc_hora_inicio', [$startOfWeek, $endOfWeek])
                    ->whereNotNull('atnc_hora_fin')
                    ->get();
                    
                $atenciones = $atencionesQuery->groupBy(function($date) {
                        return Carbon::parse($date->atnc_hora_inicio)->format('l'); // Nombre del día
                    });

                $dias = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                $counts = [];
                foreach ($dias as $dia) {
                    $counts[$dia] = isset($atenciones[$dia]) ? count($atenciones[$dia]) : 0;
                }

                // Cálculos de tiempos
                $total_espera_segundos = 0;
                $total_atencion_segundos = 0;
                $cantidad = count($atencionesQuery);

                foreach ($atencionesQuery as $atnc) {
                    if ($atnc->turno && $atnc->turno->tur_hora_fecha && $atnc->atnc_hora_inicio) {
                        $espera = Carbon::parse($atnc->turno->tur_hora_fecha)->diffInSeconds(Carbon::parse($atnc->atnc_hora_inicio));
                        $total_espera_segundos += max(0, $espera);
                    }
                    
                    if ($atnc->atnc_hora_inicio && $atnc->atnc_hora_fin) {
                        $atencion_tiempo = Carbon::parse($atnc->atnc_hora_inicio)->diffInSeconds(Carbon::parse($atnc->atnc_hora_fin));
                        $total_atencion_segundos += max(0, $atencion_tiempo);
                    }
                }

                $promedio_espera_segundos = $cantidad > 0 ? $total_espera_segundos / $cantidad : 0;
                $promedio_atencion_segundos = $cantidad > 0 ? $total_atencion_segundos / $cantidad : 0;

                return [
                    'asesor'            => $asesor->persona->pers_nombres . ' ' . $asesor->persona->pers_apellidos,
                    'mesa'              => $asesor->ase_mesa,
                    'tipo'              => $asesor->ase_tipo_asesor,
                    'atenciones'        => $counts,
                    'total'             => array_sum($counts),
                    'promedio_espera'   => $formatTime($promedio_espera_segundos),
                    'promedio_atencion' => $formatTime($promedio_atencion_segundos),
                    'total_espera'      => $formatTime($total_espera_segundos),
                    'total_atencion'    => $formatTime($total_atencion_segundos),
                ];
            });

        $atencionesDetalle = [];
        if ($asesor_id_filter) {
            $atencionesDetalle = Atencion::with('turno')
                ->where('ASESOR_ase_id', $asesor_id_filter)
                ->whereBetween('atnc_hora_inicio', [$startOfWeek, $endOfWeek])
                ->whereNotNull('atnc_hora_fin')
                ->orderBy('atnc_hora_inicio', 'desc')
                ->get()
                ->map(function($atnc) use ($formatTime) {
                    $espera = 0;
                    $atencion_tiempo = 0;
                    if ($atnc->turno && $atnc->turno->tur_hora_fecha && $atnc->atnc_hora_inicio) {
                        $espera = max(0, Carbon::parse($atnc->turno->tur_hora_fecha)->diffInSeconds(Carbon::parse($atnc->atnc_hora_inicio)));
                    }
                    if ($atnc->atnc_hora_inicio && $atnc->atnc_hora_fin) {
                        $atencion_tiempo = max(0, Carbon::parse($atnc->atnc_hora_inicio)->diffInSeconds(Carbon::parse($atnc->atnc_hora_fin)));
                    }
                    return [
                        'turno' => $atnc->turno ? $atnc->turno->tur_numero : '-',
                        'tipo' => $atnc->atnc_tipo,
                        'inicio' => Carbon::parse($atnc->atnc_hora_inicio)->format('d/m/Y h:i A'),
                        'espera' => $formatTime($espera),
                        'atencion' => $formatTime($atencion_tiempo),
                        'estado' => $atnc->atnc_estado
                    ];
                });
        }

        return view('coordinador.reporte', compact('reporte', 'asesoresDropdown', 'asesor_id_filter', 'atencionesDetalle'));
    }

    public function storeAsesor(Request $request)
    {
        $request->validate([
            'pers_doc'       => 'required|string|unique:persona,pers_doc',
            'pers_tipodoc'   => 'required|string',
            'pers_nombres'   => 'required|string',
            'pers_apellidos' => 'required|string',
            'ase_correo'     => 'required|email|unique:asesor,ase_correo',
            'ase_password'   => 'required|string|min:6',
            'ase_tipo_asesor'=> 'required|in:G,V',
            'ase_mesa'       => 'required|integer|min:1|max:20',
        ]);

        return DB::transaction(function () use ($request) {
            // 1. Crear Persona
            Persona::create([
                'pers_doc'       => $request->pers_doc,
                'pers_tipodoc'   => $request->pers_tipodoc,
                'pers_nombres'   => $request->pers_nombres,
                'pers_apellidos' => $request->pers_apellidos,
                'pers_fecha_nac' => now(), // Placeholder o agregar al form
            ]);

            // 2. Crear Asesor
            Asesor::create([
                'ase_nrocontrato'   => 'APE-' . now()->timestamp,
                'ase_tipo_asesor'   => $request->ase_tipo_asesor,
                'ase_mesa'          => $request->ase_mesa,
                'PERSONA_pers_doc' => $request->pers_doc,
                'ase_correo'        => $request->ase_correo,
                'ase_password'      => Hash::make($request->ase_password),
                'ase_estado'        => 'disponible',
                'ase_vigencia'      => 1,
            ]);

            return response()->json(['success' => true]);
        });
    }

    // ─────────────────────────────────────────────────────────────
    //  HELPERS
    // ─────────────────────────────────────────────────────────────

    private function getColaEmpresario(): array
    {
        $atendidos = Atencion::pluck('TURNO_tur_id')->toArray();
        return TurnoUnificado::whereNotIn('tur_id', $atendidos)
            ->where('tur_tipo', 'Empresario')
            ->whereDate('tur_hora_fecha', today())
            ->orderBy('tur_id')
            ->get()
            ->toArray();
    }

    private function getSiguienteTurnoEmpresario()
    {
        $atendidos = Atencion::pluck('TURNO_tur_id')->toArray();
        return TurnoUnificado::whereNotIn('tur_id', $atendidos)
            ->where('tur_tipo', 'Empresario')
            ->whereDate('tur_hora_fecha', today())
            ->orderBy('tur_id')
            ->first();
    }
}
