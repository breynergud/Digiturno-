<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;

/**
 * Configura la sesión del portal del ASESOR con una cookie separada.
 * Esto permite que asesor y coordinador coexistan en el mismo navegador.
 */
class StartAsesorSession
{
    public function handle(Request $request, Closure $next): Response
    {
        // Cambiar el nombre de la cookie de sesión al específico del asesor
        // antes de que Laravel inicie la sesión para esta solicitud.
        Config::set('session.cookie', 'digiturno_asesor_session');

        return $next($request);
    }
}
