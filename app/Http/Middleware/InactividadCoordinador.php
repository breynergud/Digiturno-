<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InactividadCoordinador
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (session()->has('coor_id')) {
            $lastActivity = session('coor_ultima_actividad');
            $currentTime = time();
            $timeout = 600; // 10 minutos en segundos

            if ($lastActivity && ($currentTime - $lastActivity > $timeout)) {
                session()->forget(['coor_id', 'coor_ultima_actividad']);
                return redirect()->route('coordinador.finalizada');
            }

            // Solo actualizamos la actividad si NO es una petición AJAX (como el polling)
            // EXCEPTO que venga con el parámetro 'heartbeat=1' Y el 'window_id' coincida con el que inició la sesión
            $isAjax     = $request->ajax() || $request->wantsJson() || $request->hasHeader('X-Requested-With');
            $heartbeat  = $request->has('heartbeat');
            $windowId   = $request->input('window_id');
            $storedId   = session('coor_window_id');
            
            // Si es una carga de página normal (no AJAX), establecemos/re-confirmamos el dueño de la sesión
            if (!$isAjax) {
                session(['coor_ultima_actividad' => $currentTime]);
            } 
            // Si es un HEARTBEAT, solo actualizamos si el window_id coincide
            else if ($heartbeat && $windowId && $windowId === $storedId) {
                session(['coor_ultima_actividad' => $currentTime]);
            }
        } else {
            // NO hay sesión iniciada, denegar acceso
            if ($request->ajax() || $request->wantsJson() || $request->hasHeader('X-Requested-With')) {
                return response()->json(['error' => 'unauthenticated'], 401);
            }
            return redirect()->route('coordinador.login');
        }

        return $next($request);
    }
}
