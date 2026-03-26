<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InactividadAsesor
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (session()->has('asesor_id')) {
            $lastActivity = session('asesor_ultima_actividad');
            $currentTime = time();
            $timeout = 900; // 15 minutos en segundos

            if ($lastActivity && ($currentTime - $lastActivity > $timeout)) {
                // EXCEPCIÓN: Si el asesor está en espera o atendiendo un turno, no cerramos sesión
                $asesor = \App\Models\Asesor::find(session('asesor_id'));
                if ($asesor && ($asesor->ase_estado === 'en_espera' || $asesor->ase_estado === 'ocupado')) {
                    // Refrescamos actividad para que no salte el modal
                    session(['asesor_ultima_actividad' => $currentTime]);
                } else {
                    // Al expirar, redirigimos a una página especial de "Sesión Finalizada"
                    session()->forget(['asesor_id', 'asesor_tipo', 'asesor_ultima_actividad']);
                    return redirect()->route('asesor.finalizada');
                }
            }

            // Solo actualizamos la actividad si NO es una petición AJAX (como el polling)
            // EXCEPTO que venga con el parámetro 'heartbeat=1' Y el 'window_id' coincida con el que inició la sesión
            $isAjax     = $request->ajax() || $request->wantsJson() || $request->hasHeader('X-Requested-With');
            $heartbeat  = $request->has('heartbeat');
            $windowId   = $request->input('window_id');
            $storedId   = session('asesor_window_id');
            
            // Si es una carga de página normal (no AJAX), establecemos/re-confirmamos el dueño de la sesión
            if (!$isAjax) {
                session(['asesor_ultima_actividad' => $currentTime]);
                // Si no hay window_id en sesión, la próxima carga AJAX lo establecerá o el controller lo hará
            } 
            // Si es un HEARTBEAT, solo actualizamos si el window_id coincide
            else if ($heartbeat && $windowId && $windowId === $storedId) {
                session(['asesor_ultima_actividad' => $currentTime]);
            }
        }

        return $next($request);
    }
}
