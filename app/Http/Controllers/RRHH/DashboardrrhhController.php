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
    // 1. Estadísticas: Usamos DISTINCT p.cedper para ignorar las duplicidades de nómina
    $stats = DB::connection('sigesp')->table('sno_personal as p')
        ->join('sno_personalnomina as n', 'p.codper', '=', 'n.codper')
        ->select(DB::raw("
            COUNT(DISTINCT CASE
                WHEN TRIM(p.estper) = '1'
                AND n.sueintper > 0
                AND p.fecegrper = '1900-01-01'
                AND n.codnom IN ('0001', '0002', '0003', '0004', '0005', '0006')
                THEN p.cedper END) as activos,

            COUNT(DISTINCT CASE
                WHEN TRIM(p.estper) = '3'
                THEN p.cedper END) as egresados,

            COUNT(DISTINCT p.cedper) as total_en_sistema
        "))
        ->first();

    // 2. Cestaticket (Local)
    $montoCestaticket = DB::table('settings')
        ->where('key', 'monto_cestaticket')
        ->value('value') ?? 0;

    // 3. Últimos Ingresos (Sin duplicados visuales)
    $ultimosIngresos = DB::connection('sigesp')->table('sno_personal as p')
        ->join('sno_personalnomina as n', 'p.codper', '=', 'n.codper')
        ->select('p.nomper', 'p.apeper', 'p.cedper', DB::raw("MAX(n.fecingper) as fecingper"))
        ->whereRaw("TRIM(p.estper) = '1'")
        ->where('n.sueintper', '>', 0)
        ->where('p.fecegrper', '1900-01-01')
        ->whereIn('n.codnom', ['0001', '0002', '0003', '0004', '0005', '0006'])
        ->groupBy('p.cedper', 'p.nomper', 'p.apeper')
        ->orderBy('fecingper', 'desc')
        ->limit(4)
        ->get();

    return view('rrhh.dashboard', compact('stats', 'montoCestaticket', 'ultimosIngresos'));
}
}
