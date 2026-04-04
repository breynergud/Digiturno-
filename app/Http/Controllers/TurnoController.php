<?php

namespace App\Http\Controllers;

use App\Models\Turno;
use App\Models\TurnoUnificado;
use App\Models\Persona;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class TurnoController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'tipo_atencion' => 'required|string',
            'pers_tipodoc' => 'required|string',
            'numero_documento' => 'required|string',
            'pers_nombres' => 'nullable|string',
            'pers_apellidos' => 'nullable|string',
            'telefono' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($request) {
            // 1. Registrar o actualizar la Persona
            $nombres = $request->input('pers_nombres', '');
            $apellidos = $request->input('pers_apellidos', '');
            
            $persona = Persona::updateOrCreate(
                ['pers_doc' => (int)$request->numero_documento],
                [
                    'pers_tipodoc' => $request->pers_tipodoc,
                    'pers_nombres' => $nombres,
                    'pers_apellidos' => $apellidos,
                    'pers_telefono' => (int)$request->telefono,
                ]
            );

            // 2. Registrar o buscar el Usuario
            $usuario = Usuario::firstOrCreate(
                ['PERSONA_pers_doc' => $persona->pers_doc],
                ['user_tipo' => 'cliente']
            );

            $prefijo = match ($request->tipo_atencion) {
                'victimas' => 'V',
                'especial' => 'S',
                'empresario' => 'E',
                default => 'G',
            };

            $tur_tipo = match ($request->tipo_atencion) {
                'victimas' => 'Victimas',
                'empresario' => 'Empresario',
                'especial' => 'Prioritario',
                default => 'General',
            };

            // Buscamos el último número generado HOY para que su turno vuelva a 0 sin borrar los datos anteriores de la base 
            $ultimoTurnoEspecifico = TurnoUnificado::where('tur_numero', 'like', $prefijo . '-%')
                ->whereDate('tur_hora_fecha', today())
                ->orderBy('tur_id', 'desc')
                ->first();

            // También buscamos en la tabla espejo vieja para evitar colisiones
            $ultimoTurnoViejo = Turno::where('codigo_turno', 'like', $prefijo . '-%')
                ->whereDate('created_at', today())
                ->orderBy('id', 'desc')
                ->first();

            $numeroEspecifico = 0;
            if ($ultimoTurnoEspecifico) {
                $codigoActual = $ultimoTurnoEspecifico->tur_numero;
                $partes = explode('-', $codigoActual);
                $numeroEspecifico = (int) end($partes);
            }

            $numeroViejo = 0;
            if ($ultimoTurnoViejo) {
                $codigoActual = $ultimoTurnoViejo->codigo_turno;
                $partes = explode('-', $codigoActual);
                $numeroViejo = (int) end($partes);
            }

            $numero = max($numeroEspecifico, $numeroViejo) + 1;
            $codigo = $prefijo . '-' . str_pad($numero, 3, '0', STR_PAD_LEFT);

            // Invalidar el caché de la TV al crear un nuevo turno
            Cache::forget('tv_pending_turns');

            // 3. Crear el turno unificado
            $turnoUnificado = TurnoUnificado::create([
                'tur_hora_fecha' => now(),
                'tur_numero' => $codigo,
                'tur_tipo' => $tur_tipo,
                'tur_mesa' => 0, 
                'USUARIO_user_id' => $usuario->user_id
            ]);

            // 4. Buscar Asesor para Pre-asignación Balanceada
            $asesorAsignado = null;
            if ($request->tipo_atencion !== 'empresario') {
                $queryAdvisors = \App\Models\Asesor::query();
                if ($request->tipo_atencion === 'victimas') {
                    $queryAdvisors->whereIn('ase_tipo_asesor', ['V', 'G']);
                } else {
                    $queryAdvisors->where('ase_tipo_asesor', 'G');
                }
                
                $advisors = $queryAdvisors->get();

                // Algoritmo: Menor carga de trabajo (pendientes + activos)
                $bestAdvisor = null;
                $minLoad = 999999;

                foreach ($advisors as $adv) {
                    $load = TurnoUnificado::where('ASESOR_ase_id', $adv->ase_id)
                        ->where(function($q) {
                            $q->whereDoesntHave('atencion') // Pendiente
                              ->orWhereHas('atencion', function($sq) { // O en proceso
                                  $sq->whereNull('atnc_hora_fin');
                              });
                        })
                        ->whereDate('tur_hora_fecha', today())
                        ->count();

                    if ($load < $minLoad) {
                        $minLoad = $load;
                        $bestAdvisor = $adv;
                    }
                }
                $asesorAsignado = $bestAdvisor;
            }

            $mesaAsignada = $asesorAsignado ? $asesorAsignado->ase_mesa : 0;

            // 5. Vincular asesor al turno unificado
            if ($asesorAsignado) {
                $turnoUnificado->update([
                    'ASESOR_ase_id' => $asesorAsignado->ase_id,
                    'tur_mesa'      => $asesorAsignado->ase_mesa,
                ]);
            }

            // 6. Mantenemos espejo en la tabla vieja para la TV
            $turno = Turno::create([
                'tipo_atencion' => $request->tipo_atencion,
                'tipo_documento' => $request->pers_tipodoc,
                'numero_documento' => $request->numero_documento,
                'telefono' => $request->telefono,
                'codigo_turno' => $codigo,
                'mesa' => $mesaAsignada,
            ]);

            return response()->json([
                'success' => true,
                'codigo' => $codigo,
                'tipo' => $request->tipo_atencion,
                'mesa' => $mesaAsignada,
                'asignado' => $asesorAsignado ? true : false
            ]);
        });
    }

    public function latestTurns()
    {
        // Obtener el último turno de cada tipo para mostrar en la TV
        $tipos = ['victimas', 'especial', 'general', 'empresario'];
        $turnos = [];

        foreach ($tipos as $tipo) {
            $turnos[] = Turno::where('tipo_atencion', $tipo)
                ->whereDate('created_at', today())
                ->orderBy('id', 'desc')
                ->first();
        }

        return response()->json($turnos);
    }

    /**
     * Devuelve los turnos que están asignados a una mesa y siguen vigentes.
     */
    public function pendingTurns()
    {
        return Cache::remember('tv_pending_turns', 60, function() {
            $hoy = today();

            // 1. Turnos que están esperando (asignados pero no aceptados aún)
            $waitingTurns = TurnoUnificado::whereNotNull('ASESOR_ase_id')
                ->whereDoesntHave('atencion')
                ->whereDate('tur_hora_fecha', $hoy)
                ->orderBy('tur_id', 'asc')
                ->get();

            // 2. Turnos que acaban de ser aceptados (Llamado en curso)
            $callingTurns = TurnoUnificado::with('atencion')
                ->whereHas('atencion', function($q) {
                    $q->whereNull('atnc_hora_fin')
                      ->where('atnc_hora_inicio', '>=', now()->subSeconds(20));
                })
                ->whereDate('tur_hora_fecha', $hoy)
                ->orderBy('tur_id', 'desc')
                ->get();

            // Optimización N+1: Cargar turnos espejo de una vez
            $codigos = $waitingTurns->pluck('tur_numero')->merge($callingTurns->pluck('tur_numero'))->toArray();
            $espejos = Turno::whereIn('codigo_turno', $codigos)->whereDate('created_at', $hoy)->get()->keyBy('codigo_turno');

            $result = [
                'waiting' => [],
                'calling' => []
            ];

            foreach ($waitingTurns as $tu) {
                if (isset($espejos[$tu->tur_numero])) {
                    $turnoEspejo = $espejos[$tu->tur_numero];
                    $turnoEspejo->is_active = false;
                    $result['waiting'][] = $turnoEspejo;
                }
            }

            foreach ($callingTurns as $tu) {
                if (isset($espejos[$tu->tur_numero])) {
                    $turnoEspejo = $espejos[$tu->tur_numero];
                    $turnoEspejo->is_active = true;
                    $turnoEspejo->atencion_id = $tu->atencion->atnc_id;
                    $turnoEspejo->llamado_at = $tu->atencion->atnc_hora_inicio;
                    $result['calling'][] = $turnoEspejo;
                }
            }

            return $result;
        });
    }

    public function tv()
    {
        return view('digiturno.tv');
    }

    public function index()
    {
        return view('digiturno.index');
    }
}
