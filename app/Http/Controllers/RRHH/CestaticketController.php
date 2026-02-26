<?php

namespace App\Http\Controllers\RRHH;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CestaticketController extends Controller
{
   public function index()
{
    // Buscamos todas las configuraciones locales
    $config = DB::table('settings')->pluck('value', 'key')->toArray();

    // Si no existe la llave, definimos el valor por defecto
    $montoActual = $config['monto_cestaticket'] ?? '0.00';

    // Opcional: Podrías traer la fecha de la última actualización si tienes esa llave
    $ultimaActualizacion = $config['last_cestaticket_update'] ?? 'No registrada';

    return view('rrhh.cestaticket.index', compact('montoActual', 'ultimaActualizacion'));
}


public function update(Request $request)
{
    $request->validate([
        'monto_cestaticket' => 'required|numeric|min:0',
    ]);

    // Guardamos en la tabla settings de la DB Local
    DB::table('settings')->updateOrInsert(
        ['key' => 'monto_cestaticket'],
        ['value' => $request->monto_cestaticket]
    );

    // Guardamos también la fecha del cambio para el registro
    DB::table('settings')->updateOrInsert(
        ['key' => 'last_cestaticket_update'],
        ['value' => now()->format('d/m/Y h:i A')]
    );

    return redirect()->route('rrhh.cestaticket.index')
                     ->with('success', 'Monto actualizado localmente con éxito.');
}
}

