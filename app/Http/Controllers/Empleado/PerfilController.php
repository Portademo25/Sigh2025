<?php

namespace App\Http\Controllers\Empleado;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class PerfilController extends Controller
{
    public function index()
    {
        $cedula = Auth::user()->cedula;

        // Buscamos los datos en la tabla sno_personal de SIGESP
        $empleado = DB::connection('sigesp')->table('sno_personal')
            ->whereRaw("LPAD(TRIM(codper), 10, '0') = LPAD(?, 10, '0')", [$cedula])
            ->first();

        if (!$empleado) {
            return redirect()->back()->with('error', 'No se encontraron datos del perfil.');
        }

        // Calculamos antigüedad
        $fechaIngreso = Carbon::parse($empleado->fecingper);
        $antiguedad = $fechaIngreso->diff(Carbon::now())->format('%y años, %m meses y %d días');

        return view('empleado.perfil', compact('empleado', 'antiguedad'));
    }
}
