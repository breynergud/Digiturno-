<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;

/**
 * Configura la sesión del portal del COORDINADOR con una cookie separada.
 * Esto permite que asesor y coordinador coexistan en el mismo navegador.
 */
class StartCoordinadorSession
{
    public function handle(Request $request, Closure $next): Response
    {
        // Cambiar el nombre de la cookie de sesión al específico del coordinador
        // antes de que Laravel inicie la sesión para esta solicitud.
        Config::set('session.cookie', 'digiturno_coordinador_session');

        return $next($request);
    }
}
