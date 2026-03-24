<?php

namespace App\Http\Controllers;

use App\Models\Turno;
use App\Models\TurnoUnificado;
use App\Models\Persona;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            'telefono' => 'required|string',
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
                'especial', 'empresario' => 'Prioritario',
                default => 'General',
            };

            // Para obtener el último número, buscamos en la tabla unificada
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

            // 3. Crear el turno unificado
            TurnoUnificado::create([
                'tur_hora_fecha' => now(),
                'tur_numero' => $codigo,
                'tur_tipo' => $tur_tipo,
                'USUARIO_user_id' => $usuario->user_id
            ]);

            // Mantenemos espejo en la tabla vieja para la TV (compatibilidad)
            $turno = Turno::create([
                'tipo_atencion' => $request->tipo_atencion,
                'tipo_documento' => $request->pers_tipodoc,
                'numero_documento' => $request->numero_documento,
                'telefono' => $request->telefono,
                'codigo_turno' => $codigo,
                'mesa' => rand(1, 5),
            ]);

            return response()->json([
                'success' => true,
                'codigo' => $codigo,
                'tipo' => $request->tipo_atencion,
                'mesa' => $turno->mesa
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
     * Devuelve SOLO los turnos pendientes (no atendidos) para cada tipo.
     * La pantalla TV filtra los que ya fueron aceptados por un asesor.
     */
    public function pendingTurns()
    {
        $codigosAtendidos = TurnoUnificado::whereHas('atencion')
            ->whereDate('tur_hora_fecha', today())
            ->pluck('tur_numero')
            ->toArray();

        // Últimos turnos por tipo que NO han sido atendidos
        $tipos = ['victimas', 'especial', 'general', 'empresario'];
        $result = [];

        foreach ($tipos as $tipo) {
            $turno = Turno::where('tipo_atencion', $tipo)
                ->whereDate('created_at', today())
                ->whereNotIn('codigo_turno', $codigosAtendidos)
                ->orderBy('id', 'desc')
                ->first();
            if ($turno) {
                $result[] = $turno;
            }
        }

        return response()->json($result);
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
