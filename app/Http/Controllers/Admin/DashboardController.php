<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class DashboardController extends Controller
{
public function index(Request $request)
{
    // 1. Filtros de fecha
    $fechaInicio = $request->input('fecha_inicio', now()->startOfYear()->format('Y-m-d'));
    $fechaFin = $request->input('fecha_fin', now()->format('Y-m-d'));

    // 2. Inicializar variables para evitar el error "Undefined variable"
    $arcStats = array_fill(0, 12, 0);
    $reciboStats = array_fill(0, 12, 0);
    $constanciaStats = array_fill(0, 12, 0);
    $labelsPie = [];
    $datosPie = [];

    // 3. Consulta para la gráfica de barras (Tendencia Mensual)
    $reportesMensuales = DB::table('reporte_descargas')
        ->select(
            DB::raw('EXTRACT(MONTH FROM created_at) as mes'),
            'tipo_reporte',
            DB::raw('count(*) as total')
        )
        ->whereYear('created_at', date('Y'))
        ->groupBy(DB::raw('EXTRACT(MONTH FROM created_at)'), 'tipo_reporte')
        ->get();

    foreach ($reportesMensuales as $dato) {
        $mesIndex = (int)$dato->mes - 1;
        if ($dato->tipo_reporte == 'Planilla ARC') $arcStats[$mesIndex] = (int)$dato->total;
        if ($dato->tipo_reporte == 'Recibo de Pago') $reciboStats[$mesIndex] = (int)$dato->total;
        if ($dato->tipo_reporte == 'Constancia de Trabajo') $constanciaStats[$mesIndex] = (int)$dato->total;
    }

    // 4. Consulta para la gráfica de torta (Distribución por Tipo)
    $distribucion = DB::table('reporte_descargas')
        ->select('tipo_reporte', DB::raw('count(*) as total'))
        ->whereBetween('created_at', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])
        ->groupBy('tipo_reporte')
        ->get();

    if ($distribucion->count() > 0) {
        $labelsPie = $distribucion->pluck('tipo_reporte')->toArray();
        $datosPie = $distribucion->pluck('total')->toArray();
    }

    // 5. Otros datos necesarios para la vista
    $usuariosActivos = DB::table('users')->count();
    $totalHoy = DB::table('reporte_descargas')->whereDate('created_at', now())->count();
    $ultimasDescargas = DB::table('reporte_descargas')->orderBy('created_at', 'desc')->limit(5)->get();

    // Ahora compact() siempre encontrará las variables, aunque estén vacías
    return view('admin.dashboard', compact(
        'arcStats', 'reciboStats', 'constanciaStats',
        'labelsPie', 'datosPie', 'fechaInicio', 'fechaFin',
        'usuariosActivos', 'totalHoy', 'ultimasDescargas'
    ));
}
public function getEstadisticasArc()
{
    $anioActual = date('Y');

    // Consultamos las descargas registradas como 'Planilla ARC'
    $stats = DB::table('reporte_descargas') // Ajusta al nombre real de tu tabla
        ->select(
            DB::raw('EXTRACT(MONTH FROM created_at) as mes'),
            DB::raw('count(*) as total')
        )
        ->where('nombre_reporte', 'Planilla ARC')
        ->whereYear('created_at', $anioActual)
        ->groupBy('mes')
        ->orderBy('mes')
        ->get();

    // Preparamos los datos para Chart.js (rellenando meses vacíos con 0)
    $data = array_fill(1, 12, 0);
    foreach ($stats as $stat) {
        $data[(int)$stat->mes] = $stat->total;
    }

    return view('admin.dashboard', [
        'arcStats' => array_values($data)
    ]);
}


// En DashboardController.php

public function exportarExcel(Request $request) {
    // Si no vienen fechas en la URL, usamos el mes actual por defecto
    $inicio = $request->input('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
    $fin = $request->input('fecha_fin', now()->format('Y-m-d'));

    try {
        return Excel::download(new \App\Exports\ReporteDescargasExport($inicio, $fin), 'reporte_descargas.xlsx');
    } catch (\Exception $e) {
        return back()->with('error', 'Error al exportar: ' . $e->getMessage());
    }
}
}
