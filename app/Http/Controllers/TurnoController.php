<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Turno;

class TurnoController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'tipo_atencion' => 'required|string',
            'tipo_documento' => 'required|string',
            'numero_documento' => 'required|string',
            'telefono' => 'required|string',
        ]);

        $prefijo = match ($request->tipo_atencion) {
            'Víctimas' => 'V',
            'Especial' => 'S',
            'Empresario' => 'E',
            default => 'G',
        };

        // Obtener el último turno de ese tipo para incrementar el número
        $ultimoTurno = Turno::where('tipo_atencion', $request->tipo_atencion)
            ->whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->first();

        $numero = $ultimoTurno ? ((int) substr($ultimoTurno->codigo_turno, strpos($ultimoTurno->codigo_turno, '-') + 1)) + 1 : 1;
        $codigo = $prefijo . '-' . str_pad($numero, 3, '0', STR_PAD_LEFT);

        $turno = Turno::create([
            'tipo_atencion' => $request->tipo_atencion,
            'tipo_documento' => $request->tipo_documento,
            'numero_documento' => $request->numero_documento,
            'telefono' => $request->telefono,
            'codigo_turno' => $codigo,
            'mesa' => rand(1, 5), // Asignamos una mesa aleatoria del 1 al 5
        ]);

        return response()->json([
            'success' => true,
            'codigo' => $turno->codigo_turno,
            'tipo' => $turno->tipo_atencion,
            'mesa' => $turno->mesa
        ]);
    }

    public function latestTurns()
    {
        // Obtener el último turno de cada tipo para mostrar en la TV
        $tipos = ['Víctimas', 'Especial', 'General', 'Empresario'];
        $turnos = [];

        foreach ($tipos as $tipo) {
            $turnos[] = Turno::where('tipo_atencion', $tipo)
                ->whereDate('created_at', today())
                ->orderBy('id', 'desc')
                ->first();
        }

        return response()->json($turnos);
    }

    public function tv()
    {
        return view('digiturno.tv');
    }
}
