<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticatedDigiturno
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Si hay sesión de asesor, redirigir al dashboard de asesor
        if (session()->has('asesor_id')) {
            return redirect()->route('asesor.dashboard');
        }

        // Si hay sesión de coordinador, redirigir al dashboard de coordinador
        if (session()->has('coor_id')) {
            return redirect()->route('coordinador.dashboard');
        }

        $response = $next($request);

        // Prevenir que el navegador guarde en caché la página de login/registro
        // Esto evita que al presionar "Atrás" se vea el formulario si ya se inició sesión
        if ($response instanceof Response) {
            $response->headers->set('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', 'Sun, 02 Jan 1990 00:00:00 GMT');
        }

        return $response;
    }
}
