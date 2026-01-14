<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // IMPORTANTE: Para las consultas a SIGESP
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class EmpleadoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // Asegúrate de que tu middleware de rol permita el acceso
        $this->middleware('role:empleado');
    }

    /**
     * Muestra el panel principal con datos de nómina SIGESP
     */
    public function dashboard()
    {
        $user = Auth::user();

        // Buscamos datos del trabajador en SIGESP usando el codper guardado en el usuario
        // Usamos un LEFT JOIN por si algún dato histórico falta, no rompa la vista
        $datosNomina = DB::table('sno_personal as p')
            ->leftJoin('sno_hpersonal as hp', 'p.codper', '=', 'hp.codper')
            ->select(
                'p.nomper',
                'p.apeper',
                'hp.descar',    // Nombre del Cargo
                'hp.desuniadm', // Unidad Administrativa
                'p.fecingper'   // Fecha de Ingreso
            )
            ->where('p.codper', $user->codper)
            ->orderBy('hp.fecdoc', 'desc') // Traer el historial más reciente
            ->first();

        return view('empleado.dashboard', compact('user', 'datosNomina'));
    }

    /**
     * Vista de perfil del usuario
     */
    public function perfil()
    {
        $user = Auth::user();
        return view('empleado.perfil', compact('user'));
    }

    /**
     * Lógica para actualizar datos básicos y contraseña
     */
    public function actualizarPerfil(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|min:8|confirmed',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('new_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'La contraseña actual no es correcta']);
            }
            $user->password = Hash::make($request->new_password);
        }

        $user->save();

        return back()->with('success', 'Perfil actualizado exitosamente');
    }

    /**
     * Sección de tareas o actividades (Opcional)
     */
public function misRecibos()
{
    // Usamos el helper auth() para obtener el usuario actual
    $user = Auth::user();

    // Aseguramos que la cédula tenga los 10 dígitos (relleno con ceros)
    $codper = str_pad($user->codper, 10, "0", STR_PAD_LEFT);

    // Lista de códigos de nóminas ordinarias (según tu base de datos)
    $nominasNormales = ['0001', '0002', '0003', '0004', '0005', '0006'];

    $recibos = DB::connection('sigesp')
        ->table('sno_hperiodo as hp')
        ->join('sno_hresumen as hr', function($join) {
            $join->on('hp.codnom', '=', 'hr.codnom')
                 ->on('hp.codperi', '=', 'hr.codperi');
        })
        ->select(
            'hp.codnom',
            'hp.codperi',
            'hp.fecdesper',
            'hp.fechasper',
            'hr.monnetres'
        )
        ->where('hr.codper', '=', $codper)
        ->whereIn('hp.codnom', $nominasNormales) // Solo nóminas de sueldo
        ->where('hr.monnetres', '>', 0)          // Solo recibos con pago real
        ->orderBy('hp.fecdesper', 'desc')        // Los más recientes primero
        ->paginate(12);

    return view('empleado.reportes.recibos', compact('recibos'));
}
public function menuReportes()
{
    return view('empleado.reportes.menu');
}


public function descargarPDF($codnom, $codperi)
{
    $user = \Illuminate\Support\Facades\Auth::user();

    // Aseguramos el padding exacto de SIGESP
    $v_codnom  = str_pad($codnom, 4, "0", STR_PAD_LEFT);
    $v_codperi = str_pad($codperi, 3, "0", STR_PAD_LEFT);
    $v_codper  = str_pad($user->codper, 10, "0", STR_PAD_LEFT);



    try {
        // 1. Datos del encabezado (Resumen)
        $resumen = DB::connection('sigesp')
            ->table('sno_hresumen as hr')
            ->join('sno_nomina as n', 'hr.codnom', '=', 'n.codnom')
            ->join('sno_hperiodo as hp', function($join) {
                $join->on('hr.codnom', '=', 'hp.codnom')->on('hr.codperi', '=', 'hp.codperi');
            })
            ->leftJoin('sno_hpersonalnomina as hpn', function($join) {
                $join->on('hr.codnom', '=', 'hpn.codnom')
                     ->on('hr.codper', '=', 'hpn.codper')
                     ->on('hr.codperi', '=', 'hpn.codperi');
            })
            ->select('hr.*', 'n.desnom', 'hp.fecdesper', 'hp.fechasper', 'hpn.fecingper')
            ->where([['hr.codnom', $v_codnom], ['hr.codperi', $v_codperi], ['hr.codper', $v_codper]])
            ->first();

        // 2. Datos de Personal y Cuenta Bancaria (scubabanper es el estándar)
        $p = DB::connection('sigesp')->table('sno_personal')->where('codper', $v_codper)->first();
        $resumen->ctabanper = $p->scubabanper ?? $p->ctabanper ?? $p->scuubanper ?? '01020135790106615766';
        $resumen->fecingper = $p->fecingper ?? $resumen->fecingper;

        // 3. CONSULTA DE CONCEPTOS (Sin filtros restrictivos de tipo)
        // 3. Obtener Conceptos (Ajustado para asegurar la recuperación de datos)
// ... dentro de descargarPDF ...

// 3. Consulta de conceptos (sin cambios)
// 3. Obtener Conceptos (Sin cambios en la consulta SQL)
$conceptos = DB::connection('sigesp')
    ->table('sno_hsalida as hs')
    ->leftJoin('sno_concepto as c', function($join) {
        $join->on('hs.codnom', '=', 'c.codnom')->on('hs.codconc', '=', 'c.codconc');
    })
    ->select('hs.codconc as codcon', 'c.nomcon', 'hs.valsal as valcalcur', 'hs.tipsal')
    ->where([
        ['hs.codnom', '=', $v_codnom],
        ['hs.codperi', '=', $v_codperi],
        ['hs.codper', '=', $v_codper]
    ])
    ->get();

// 4. LÓGICA DE FILTRADO SEGÚN TU DIAGNÓSTICO
// Asignaciones: Solo las que tienen tipo "A" y valor mayor a 0
// 4. Lógica de Filtrado por Quincena
$esPar = (intval($v_codperi) % 2 == 0);
$quincenaFiltro = $esPar ? 'P2' : 'P1';

// 2. Filtramos las asignaciones (Tipo A)
$asignaciones = $conceptos->filter(function($c) {
    return trim($c->tipsal) == 'A' && $c->valcalcur > 0;
});

// 3. Filtramos las deducciones dinámicamente
$deducciones = $conceptos->filter(function($c) use ($quincenaFiltro) {
    $tipo = trim($c->tipsal);
    $monto = floatval($c->valcalcur);

    // Solo mostramos si coincide con la quincena actual (P1 o P2)
    // o si es una deducción general (D) y el monto es negativo
    return ($tipo == $quincenaFiltro || $tipo == 'D') && $monto < 0;
$totalDeducciones = $deducciones->sum('valcalcur');

// Luego, pásalo a la vista:
return $pdf->loadView('empleado.reportes.recibo_pdf', [
    'resumen' => $resumen,
    'user' => $user,
    'asignaciones' => $asignaciones,
    'deducciones' => $deducciones,
    'totalDeducciones' => $totalDeducciones // Nueva variable
]);
});

// Nota: Los tipos "R" (como Sueldo Integral) se ignoran automáticamente
// porque son informativos y no afectan el neto.
// ... resto del código ...
// Asegúrate de pasar estas variables a la vista


        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('empleado.reportes.recibo_pdf',
            compact('resumen', 'user', 'asignaciones', 'deducciones')
        );

        return $pdf->download("Recibo_{$v_codper}.pdf");

    } catch (\Exception $e) {
        return dd("Error: " . $e->getMessage());
    }
}
}
