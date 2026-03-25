<?php

namespace App\Http\Controllers;

use App\Models\Asesor;
use App\Models\Coordinador;
use App\Models\Persona;
use App\Models\TurnoUnificado;
use App\Models\Atencion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        $coor = Coordinador::where('coor_correo', $request->coor_correo)
                         ->where('coor_password', $request->coor_password)
                         ->first();

        if (! $coor) {
            return back()->withErrors(['coor_correo' => 'Credenciales incorrectas.'])->withInput();
        }

        session([
            'coor_id' => $coor->coor_id,
        ]);

        return redirect()->route('coordinador.dashboard');
    }

    public function logout()
    {
        session()->forget(['coor_id']);
        return redirect()->route('coordinador.login');
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
            'nuevo_tipo' => 'required|in:V,P,G',
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

    public function reporteSemanal()
    {
        $startOfWeek = now()->startOfWeek();
        $endOfWeek   = now()->endOfWeek();

        $reporte = Asesor::with(['persona'])
            ->get()
            ->map(function($asesor) use ($startOfWeek, $endOfWeek) {
                $atenciones = Atencion::where('ASESOR_ase_id', $asesor->ase_id)
                    ->whereBetween('atnc_hora_inicio', [$startOfWeek, $endOfWeek])
                    ->whereNotNull('atnc_hora_fin')
                    ->get()
                    ->groupBy(function($date) {
                        return Carbon::parse($date->atnc_hora_inicio)->format('l'); // Nombre del día
                    });

                $dias = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                $counts = [];
                foreach ($dias as $dia) {
                    $counts[$dia] = isset($atenciones[$dia]) ? count($atenciones[$dia]) : 0;
                }

                return [
                    'asesor' => $asesor->persona->pers_nombres . ' ' . $asesor->persona->pers_apellidos,
                    'tipo'   => $asesor->ase_tipo_asesor,
                    'atenciones' => $counts,
                    'total' => array_sum($counts)
                ];
            });

        return view('coordinador.reporte', compact('reporte'));
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
