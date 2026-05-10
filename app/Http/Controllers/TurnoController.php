<?php

namespace App\Http\Controllers;

use App\Models\Turno;
use App\Models\TurnoUnificado;
use App\Models\Persona;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\StoreTurnoRequest;

class TurnoController extends Controller
{
    public function store(StoreTurnoRequest $request)
    {

        return DB::transaction(function () use ($request) {
            // 1. Registrar o recuperar la Persona (Solo con documento y tipo)
            $persona = Persona::firstOrCreate(
                ['pers_doc' => (int)$request->numero_documento],
                [
                    'pers_tipodoc' => $request->pers_tipodoc,
                    'pers_nombres' => $request->input('pers_nombres', ''),
                    'pers_apellidos' => $request->input('pers_apellidos', ''),
                    'pers_telefono' => $request->telefono ? (int)$request->telefono : null,
                ]
            );

            // Si la persona ya existe, podríamos actualizar solo el tipo de doc o teléfono si se proveen
            if (!$persona->wasRecentlyCreated) {
                $persona->update([
                    'pers_tipodoc' => $request->pers_tipodoc,
                    'pers_telefono' => $request->telefono ? (int)$request->telefono : $persona->pers_telefono,
                ]);
            }

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

            // 4. No asignar asesor automáticamente (Asignación manual al aceptar)
            // El turno queda pendiente para que cualquier asesor disponible lo tome.
            $mesaAsignada = 0;

            // 5. Mantenemos espejo en la tabla vieja para la TV
            $turno = Turno::create([
                'tipo_atencion'    => $request->tipo_atencion,
                'tipo_documento'   => $request->pers_tipodoc,
                'numero_documento' => $request->numero_documento,
                'telefono'         => $request->telefono ?: null,
                'codigo_turno'     => $codigo,
                'mesa'             => $mesaAsignada,
            ]);

            return response()->json([
                'success'  => true,
                'codigo'   => $codigo,
                'tipo'     => $request->tipo_atencion,
                'mesa'     => $mesaAsignada,
                'asignado' => false
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
        return Cache::remember('tv_pending_turns', 5, function() {
            $hoy = today();

            // 1. Turnos que están esperando (pendientes de ser tomados o asignados)
            $waitingTurns = TurnoUnificado::whereDoesntHave('atencion')
                ->whereDate('tur_hora_fecha', $hoy)
                ->orderBy('tur_id', 'asc')
                ->get();

            // 2. Turnos que están siendo atendidos (en proceso)
            // Usamos 'with' para evitar error N+1 y ordenamos por ID de forma descendente (los más recientes primero)
            $attendingTurns = TurnoUnificado::with(['atencion', 'asesor'])
            ->whereHas('atencion', function($q) {
                $q->whereNull('atnc_hora_fin');
            })
            ->whereDate('tur_hora_fecha', $hoy)
            ->orderBy('tur_id', 'desc')
            ->get();

            // 3. Identificamos cuáles de estos son "Llamados Recientes" (últimos 30 seg)
            $callingTurns = $attendingTurns->filter(function($tu) {
                return $tu->atencion && $tu->atencion->atnc_hora_inicio >= now()->subSeconds(30);
            });

            // Optimización N+1: Cargar turnos espejo de una vez
            $codigos = $waitingTurns->pluck('tur_numero')
                ->merge($attendingTurns->pluck('tur_numero'))
                ->toArray();
            $espejos = Turno::whereIn('codigo_turno', $codigos)->get()->keyBy('codigo_turno');

            $result = [
                'waiting' => [],
                'calling' => []
            ];

            foreach ($waitingTurns as $tu) {
                if (isset($espejos[$tu->tur_numero])) {
                    $turnoEspejo = $espejos[$tu->tur_numero];
                    $turnoEspejo->is_active = false;
                    $turnoEspejo->mesa = 0; // Aseguramos que diga ESPERA
                    $result['waiting'][] = $turnoEspejo;
                }
            }

            foreach ($attendingTurns as $tu) {
                // Solo incluimos en la TV los que son "Llamados Recientes" (últimos 30 seg)
                // Después de ese tiempo, desaparecen de la pantalla para liberar espacio.
                $isRecent = $tu->atencion && $tu->atencion->atnc_hora_inicio >= now()->subSeconds(30);
                
                if ($isRecent && isset($espejos[$tu->tur_numero])) {
                    $turnoEspejo = $espejos[$tu->tur_numero];
                    $turnoEspejo->is_active = true;
                    $turnoEspejo->mesa = $tu->asesor->ase_mesa ?? 0;
                    $turnoEspejo->atencion_id = $tu->atencion->atnc_id;
                    $turnoEspejo->llamado_at = $tu->atencion->atnc_hora_inicio;
                    $turnoEspejo->is_recent = true;

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
