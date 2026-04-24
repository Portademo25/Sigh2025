<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
   public function handle($request, Closure $next)
{
    // Primero verificamos que esté logueado
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    // Verificamos si es Administrador (rol_id == 1)
    if (Auth::user()->rol_id != 1) {
        // Si no es admin, lo mandamos a la parte de empleados
        return redirect()->route('home')->with('error', 'No tienes permisos de administrador.');
    }

    return $next($request);
}
}
