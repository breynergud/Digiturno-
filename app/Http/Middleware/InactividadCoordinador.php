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
            $timeout = 900; // 15 minutos en segundos

            if ($lastActivity && ($currentTime - $lastActivity > $timeout)) {
                session()->forget(['coor_id', 'coor_ultima_actividad']);
                return redirect()->route('coordinador.login')
                    ->with('error', 'Su sesión ha expirado por inactividad (15 minutos).');
            }

            session(['coor_ultima_actividad' => $currentTime]);
        }

        return $next($request);
    }
}
