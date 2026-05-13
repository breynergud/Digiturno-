<?php

namespace App\Http\Controllers;

use App\Models\Asesor;
use App\Models\Coordinador;
use App\Models\Persona;
use App\Models\TurnoUnificado;
use App\Models\Atencion;
use App\Models\SesionAsesor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
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

    public function showRegister()
    {
        if (session('coor_id')) {
            return redirect()->route('coordinador.dashboard');
        }
        return view('coordinador.register');
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
            'coor_correo'    => 'required|email|unique:coordinador,coor_correo',
            'coor_password'  => 'required|string|min:6|confirmed',
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

            // 2. Crear Coordinador
            $coor = Coordinador::create([
                'coor_correo'      => $request->coor_correo,
                'coor_password'    => Hash::make($request->coor_password),
                'PERSONA_pers_doc' => $request->pers_doc,
                'coor_estado'      => 'disponible',
                'coor_vigencia'    => 1,
            ]);

            return redirect()->route('coordinador.login')->with('success', 'Cuenta de coordinador creada. Inicie sesión.');
        });
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

        // Establecer un window_id único para esta pestaña (aislamiento)
        $windowId = bin2hex(random_bytes(8));
        session(['coor_window_id' => $windowId]);
        
        return view('coordinador.dashboard', compact('coordinador', 'asesores'));
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
                'mesa'   => $a->ase_mesa,
                'estado' => $a->ase_estado,
                'turno'  => $a->ase_turno_actual_id,
            ];
        });

        return response()->json([
            'asesores'       => $asesores,
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    //  ACCIONES
    // ─────────────────────────────────────────────────────────────

    public function reasignarAsesor(Request $request)
    {
        $request->validate([
            'ase_id'   => 'required|exists:asesor,ase_id',
            'nuevo_tipo' => 'required|in:G,V',
        ]);

        $asesor = Asesor::findOrFail($request->ase_id);
        $asesor->update(['ase_tipo_asesor' => $request->nuevo_tipo]);

        // Rebalancear turnos luego de cambiar un rol para actualizar las colas
        $this->rebalancearTurnos();

        return response()->json(['success' => true]);
    }

    private function rebalancearTurnos()
    {
        $atendidos = \App\Models\Atencion::pluck('TURNO_tur_id')->toArray();
        $pendientes = \App\Models\TurnoUnificado::whereNotIn('tur_id', $atendidos)
            ->whereNotNull('ASESOR_ase_id')
            ->whereDate('tur_hora_fecha', today())
            ->get();

        foreach ($pendientes as $turno) {
            $tipoAtencion = strtolower($turno->tur_tipo); // victimas, general, prioritario

            if ($tipoAtencion !== 'empresario') {
                $queryAdvisors = \App\Models\Asesor::query();
                if ($tipoAtencion === 'victimas') {
                    $queryAdvisors->where('ase_tipo_asesor', 'V');
                } else {
                    $queryAdvisors->where('ase_tipo_asesor', 'G');
                }
                
                $advisors = $queryAdvisors->get();
                if ($advisors->isEmpty()) continue;

                $asesorAsignado = null;
                $minLoad = PHP_INT_MAX;

                foreach ($advisors as $adv) {
                    $load = \App\Models\TurnoUnificado::where('ASESOR_ase_id', $adv->ase_id)
                        ->whereNotIn('tur_id', $atendidos)
                        ->whereDate('tur_hora_fecha', today())
                        ->count();
                    
                    $carga = $load + ($adv->ase_estado === 'ocupado' ? 1 : 0);

                    if ($carga < $minLoad) {
                        $minLoad = $carga;
                        $asesorAsignado = $adv;
                    }
                }

                if ($asesorAsignado && $asesorAsignado->ase_id !== $turno->ASESOR_ase_id) {
                    $turno->update([
                        'ASESOR_ase_id' => $asesorAsignado->ase_id,
                        'tur_mesa'      => $asesorAsignado->ase_mesa,
                    ]);
                    \App\Models\Turno::where('codigo_turno', $turno->tur_numero)
                        ->whereDate('created_at', today())
                        ->update(['mesa' => $asesorAsignado->ase_mesa]);
                }
            }
        }
        
        \Illuminate\Support\Facades\Cache::forget('tv_pending_turns');
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
        $sesionesDetalle   = [];
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
                        'estado' => $atnc->atnc_estado,
                        'observacion' => $atnc->atnc_observacion,
                    ];
                });

            // Cargar jornadas de trabajo de la semana para este asesor con pausas
            $sesionesDetalle = \App\Models\SesionAsesor::with('pausas')
                ->where('ASESOR_ase_id', $asesor_id_filter)
                ->whereBetween('ses_inicio', [$startOfWeek, $endOfWeek])
                ->orderBy('ses_inicio', 'desc')
                ->get()
                ->map(function($ses) use ($formatTime, $asesor_id_filter) {
                    $fin    = $ses->ses_fin;
                    $inicio = $ses->ses_inicio;
                    
                    // Cálculo de duración bruta
                    $duracionSegundos = $fin 
                        ? Carbon::parse($inicio)->diffInSeconds(Carbon::parse($fin))
                        : Carbon::parse($inicio)->diffInSeconds(now());
                    
                    // Cálculo de pausas
                    $totalPausaSegundos = 0;
                    $tieneAutoPausa = false;
                    foreach ($ses->pausas as $pausa) {
                        $pFin = $pausa->pau_fin ?? now();
                        $totalPausaSegundos += Carbon::parse($pausa->pau_inicio)->diffInSeconds(Carbon::parse($pFin));
                        if ($pausa->pau_motivo === 'Inactividad Automática') {
                            $tieneAutoPausa = true;
                        }
                    }

                    $duracionEfectiva = $duracionSegundos - $totalPausaSegundos;

                    // Contar atenciones realizadas dentro de esta jornada
                    $finParaQuery = $fin ?? now();
                    $atenciones = Atencion::where('ASESOR_ase_id', $asesor_id_filter)
                        ->whereBetween('atnc_hora_inicio', [$inicio, $finParaQuery])
                        ->whereNotNull('atnc_hora_fin')
                        ->count();

                    return [
                        'inicio'      => Carbon::parse($inicio)->format('d/m/Y h:i A'),
                        'fin'         => $fin ? Carbon::parse($fin)->format('d/m/Y h:i A') : null,
                        'duracion'    => $formatTime($duracionSegundos),
                        'pausa'       => $formatTime($totalPausaSegundos),
                        'efectiva'    => $formatTime(max(0, $duracionEfectiva)),
                        'atenciones'  => $atenciones,
                        'activa'      => $fin === null,
                        'auto_pausa'  => $tieneAutoPausa,
                    ];
                });
        }

        return view('coordinador.reporte', compact('reporte', 'asesoresDropdown', 'asesor_id_filter', 'atencionesDetalle', 'sesionesDetalle'));
    }

    public function storeAsesor(Request $request)
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
            'ase_password'   => 'required|string|min:6',
            'ase_tipo_asesor'=> 'required|in:G,V',
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
                'ase_estado'        => 'inactivo',
                'ase_vigencia'      => 1,
            ]);

            return response()->json(['success' => true]);
        });
    }

    // ─────────────────────────────────────────────────────────────
    //  HELPERS
    // ─────────────────────────────────────────────────────────────

}
