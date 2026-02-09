<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    DB::table('politicas_acceso')->updateOrInsert(
        ['id' => 1], // Siempre editamos el registro único
        [
            'intentos_maximos' => $request->intentos_maximos,
            'duracion_bloqueo' => $request->duracion_bloqueo,
            'expiracion_sesion' => $request->expiracion_sesion,
            'updated_at' => now()
        ]
    );

    return back()->with('success', 'Políticas actualizadas correctamente.');
}
}
