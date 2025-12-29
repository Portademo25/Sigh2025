<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Models\Setting;

class CheckMaintenanceMode
{
    public function handle($request, Closure $next)
    {
        // 1. Buscamos el valor en la tabla de configuraciones
        $isOffline = Setting::where('key', 'site_offline')->value('value');

        if ($isOffline == '1') {
            // 2. Si el sitio está offline, permitimos pasar solo si:
            // - Es un Administrador logueado
            // - Es la ruta de login o logout (para que el admin pueda entrar)
            if (Auth::check() && Auth::user()->hasRole('administrador')) {
                return $next($request);
            }

            if ($request->is('login') || $request->is('logout')) {
                return $next($request);
            }

            // 3. A todos los demás, les mostramos la vista de mantenimiento
            return response()->view('errors.maintenance', [], 503);
        }

        return $next($request);
    }
}