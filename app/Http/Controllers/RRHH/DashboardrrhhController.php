<?php

namespace App\Http\Controllers\RRHH;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardrrhhController extends Controller
{
    public function index()
{
    // 1. Estadísticas básicas (Se mantiene igual)
    $stats = DB::connection('sigesp')->table('sno_personal')
        ->select(DB::raw("
            COUNT(CASE WHEN estper = '1' THEN 1 END) as activos,
            COUNT(CASE WHEN estper = '3' THEN 1 END) as jubilados,
            COUNT(*) as total
        "))
        ->first();

    // 2. Últimos ingresos (CON CORRECCIÓN DE DUPLICADOS)
    $ultimosIngresos = DB::connection('sigesp')->table('sno_personal as p')
        ->join('sno_personalnomina as pn', 'p.codper', '=', 'pn.codper')
        ->select('p.nomper', 'p.apeper', 'p.cedper', 'pn.fecingper')
        ->where('p.estper', '1')
        ->whereNotNull('pn.fecingper')
        // Agrupamos para evitar que salgan repetidos por múltiples nóminas
        ->groupBy('p.nomper', 'p.apeper', 'p.cedper', 'pn.fecingper')
        ->orderBy('pn.fecingper', 'desc')
        ->limit(4)
        ->get();

    return view('rrhh.dashboard', compact('ultimosIngresos', 'stats'));
}
}
