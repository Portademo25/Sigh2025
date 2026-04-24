<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\SecurityLog;

class PoliticaController extends Controller
{
    public function edit()
{
    $politica = DB::table('politicas_acceso')->first();
    return view('admin.security.policies', compact('politica'));
}

public function update(Request $request)
{
    $request->validate([
        'intentos_maximos' => 'required|integer',
        'duracion_bloqueo' => 'required|integer',
        'expiracion_sesion' => 'required|integer',
    ]);

    // 1. Guardar o actualizar las políticas
    DB::table('politicas_acceso')->updateOrInsert(
        ['id' => 1], 
        [
            'intentos_maximos'  => $request->intentos_maximos,
            'duracion_bloqueo'  => $request->duracion_bloqueo,
            'expiracion_sesion' => $request->expiracion_sesion,
            'updated_at'        => now()
        ]
    );

    // 2. REGISTRO USANDO EL HELPER GLOBAL (Más limpio)
    record_security_event(
        'Actualización de Políticas de Acceso', 
        'Media', 
        [
            'config_final' => [
                'intentos' => $request->intentos_maximos,
                'bloqueo'  => $request->duracion_bloqueo . ' min',
                'sesion'   => $request->expiracion_sesion . ' min'
            ]
        ]
    );

    return back()->with('success', 'Políticas actualizadas y rastro de seguridad generado.');
}

protected function registrarEventoSeguridad($request, $evento, $gravedad, $detalles)
{
    SecurityLog::create([
        'event'           => $evento,
        'user_identifier' => Auth::user()->cedper ?? Auth::user()->name ?? 'Admin',
        'ip_address'      => $request->ip(),
        'user_agent'      => $request->userAgent(),
        'severity'        => $gravedad,
        'details'         => $detalles,
    ]);
}

}
