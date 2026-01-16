<?php

namespace App\Http\Controllers\Empleado;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class IvssController extends Controller
{





public function index()
{
    // Obtenemos los años disponibles de la historia de periodos
    $anos = DB::connection('sigesp')
        ->table('sno_hperiodo')
        ->select(DB::raw('DISTINCT(EXTRACT(YEAR FROM fecdesper)) as ano'))
        ->orderBy('ano', 'desc')
        ->get();

    return view('empleado.reportes.ivss_index', compact('anos'));
}


public function generar14100($ano)
{
    $cedula_usuario = Auth::user()->cedula;

    // 1. Buscamos al trabajador asegurando el formato de 10 dígitos (relleno con ceros)
    $trabajador = DB::connection('sigesp')->table('sno_personal')
        ->whereRaw("LPAD(TRIM(codper), 10, '0') = LPAD(?, 10, '0')", [$cedula_usuario])
        ->first();

    // 2. Si el trabajador no existe en SIGESP, redirigimos con un error en lugar de colapsar
    if (!$trabajador) {
        return redirect()->back()->with('error', 'No se encontraron sus datos filiatorios en el sistema de nómina.');
    }

    // 3. Ya teniendo al trabajador, buscamos sus salarios (Usamos su codper real de SIGESP)
    $v_codper = $trabajador->codper;

    $detalles = DB::connection('sigesp')
        ->table('sno_hsalida as hs')
        ->join('sno_hperiodo as hp', function($join) {
            $join->on('hs.codnom', '=', 'hp.codnom')->on('hs.codperi', '=', 'hp.codperi');
        })
        ->join('sno_nomina as n', 'hs.codnom', '=', 'n.codnom')
        ->select(
            DB::raw('EXTRACT(MONTH FROM hp.fecdesper) as mes'),
            DB::raw("SUM(CASE WHEN hs.tipsal = 'A' AND (
                (n.espnom='0' AND n.tippernom='1') OR
                (n.espnom='0' AND n.tippernom='2') OR
                (n.espnom='1' AND n.tippernom='1')
            ) THEN hs.valsal ELSE 0 END) as monto")
        )
        ->where('hs.codper', $v_codper)
        ->whereYear('hp.fecdesper', $ano)
        ->groupBy('mes')
        ->get()
        ->keyBy('mes');

    // Datos de la empresa (asegúrate de que esta variable exista)
    $empresa = [
        'nombre' => 'MINISTERIO DEL PODER POPULAR...',
        'rif' => 'G-20000000-0',
        'patronal' => '000-000000-0',
        'direccion' => 'CARACAS, VENEZUELA',
        'telefono' => '0212-0000000',
        'email' => 'contacto@institucion.gob.ve'
    ];

    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('empleado.reportes.ivss_14100', compact('detalles', 'trabajador', 'empresa', 'ano'));
    return $pdf->stream("IVSS_14100_{$v_codper}.pdf");
}
}
