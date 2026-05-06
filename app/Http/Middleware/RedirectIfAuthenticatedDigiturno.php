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
        $isAsesorRoute = str_starts_with($request->path(), 'asesor');
        $isCoorRoute   = str_starts_with($request->path(), 'coordinador');

        // Si ya está logueado en su respectivo portal, lo redirigimos al dashboard
        if (session()->has('asesor_id') && $isAsesorRoute) {
            return redirect()->route('asesor.dashboard');
        }
        if (session()->has('coor_id') && $isCoorRoute) {
            return redirect()->route('coordinador.dashboard');
        }

        // Si tiene sesión de coordinador pero intenta entrar al login de asesor, cerramos la de coordinador
        if (session()->has('coor_id') && $isAsesorRoute) {
            session()->forget(['coor_id', 'coor_ultima_actividad']);
        }

        // Si tiene sesión de asesor pero intenta entrar al login de coordinador, cerramos la de asesor
        if (session()->has('asesor_id') && $isCoorRoute) {
            session()->forget(['asesor_id', 'asesor_tipo', 'asesor_ultima_actividad']);
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
