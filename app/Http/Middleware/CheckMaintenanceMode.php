<?php


namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;

class CheckMaintenanceMode
{
  public function handle(Request $request, Closure $next)
{
    $isOffline = DB::table('settings')->where('key', 'site_offline')->value('value');

    if ($isOffline == '1') {
        // EXCEPCIONES: Rutas que deben funcionar SIEMPRE
        if (
            $request->is('login') ||
            $request->is('logout') ||
            $request->is('auth/*') || // <--- EXCLUYE TODO LO DE ONBOARDING
            $request->routeIs('admin.error.mantenance')
        ) {
            return $next($request);
        }

        // Si es admin, dejar pasar a su panel
        if ($request->user() && $request->user()->hasRole('admin')) {
            return $next($request);
        }

        // Bloqueo para todos los demás
        return response()->view('errors.maintenance', [], 503);
    }

    return $next($request);
}
}
