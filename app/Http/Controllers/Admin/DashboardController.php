<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
   public function index(Request $request)
{
    // Capturamos las fechas del filtro o usamos el mes actual por defecto
    $fechaInicio = $request->input('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
    $fechaFin = $request->input('fecha_fin', now()->endOfMonth()->format('Y-m-d'));

    // 1. Datos para la gráfica filtrados por fecha
    $distribucion = DB::table('reporte_descargas')
        ->select('tipo_reporte', DB::raw('count(*) as total'))
        ->whereBetween('created_at', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])
        ->groupBy('tipo_reporte')
        ->get();

    $labelsPie = $distribucion->pluck('tipo_reporte')->toArray();
    $datosPie = $distribucion->pluck('total')->toArray();

    // 2. Estadísticas de las tarjetas (Widgets)
    $totalHoy = DB::table('reporte_descargas')->whereDate('created_at', now())->count();
    $totalPeriodo = DB::table('reporte_descargas')->whereBetween('created_at', [$fechaInicio, $fechaFin])->count();
    $usuariosActivos = DB::table('reporte_descargas')->distinct('cedula')->count();

    // 3. Actividad Reciente (Sigue siendo las últimas 5 globales)
    $ultimasDescargas = DB::table('reporte_descargas')->orderBy('created_at', 'desc')->limit(5)->get();

    return view('admin.dashboard', compact(
        'labelsPie', 'datosPie', 'totalHoy', 'totalPeriodo',
        'usuariosActivos', 'ultimasDescargas', 'fechaInicio', 'fechaFin'
    ));
}
}
