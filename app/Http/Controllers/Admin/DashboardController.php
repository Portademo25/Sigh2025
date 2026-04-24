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

    // 2. Inicializar variables
    $arcStats = array_fill(0, 12, 0);
    $reciboStats = array_fill(0, 12, 0);
    $constanciaStats = array_fill(0, 12, 0);

    // 3. Consulta para la gráfica de barras (Tendencia Mensual)
    // Normalizamos con TRIM e INITCAP para que "Constancia de trabajo" y "Constancia de Trabajo " sean lo mismo
    $reportesMensuales = DB::table('reporte_descargas')
        ->select(
            DB::raw('EXTRACT(MONTH FROM created_at) as mes'),
            DB::raw('INITCAP(TRIM(tipo_reporte)) as tipo_limpio'), 
            DB::raw('count(*) as total')
        )
        ->whereYear('created_at', date('Y'))
        ->groupBy(DB::raw('EXTRACT(MONTH FROM created_at)'), 'tipo_limpio')
        ->get();

    foreach ($reportesMensuales as $dato) {
        $mesIndex = (int)$dato->mes - 1;
        $tipo = $dato->tipo_limpio;

        // Usamos una lógica más flexible para agrupar variantes
        if (str_contains($tipo, 'Arc')) $arcStats[$mesIndex] += (int)$dato->total;
        if (str_contains($tipo, 'Recibo')) $reciboStats[$mesIndex] += (int)$dato->total;
        if (str_contains($tipo, 'Constancia')) $constanciaStats[$mesIndex] += (int)$dato->total;
    }

    // 4. Consulta para la gráfica de torta
    // Aquí agrupamos por el nombre normalizado para evitar las "dobles constancias"
    $distribucion = DB::table('reporte_descargas')
        ->select(
            DB::raw('INITCAP(TRIM(tipo_reporte)) as tipo_reporte'), 
            DB::raw('count(*) as total')
        )
        ->whereBetween('created_at', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])
        ->groupBy(DB::raw('INITCAP(TRIM(tipo_reporte))'))
        ->get();

    $labelsPie = $distribucion->pluck('tipo_reporte')->toArray();
    $datosPie = $distribucion->pluck('total')->toArray();

    // 5. Datos adicionales
    $usuariosActivos = DB::table('users')->count();
    $totalHoy = DB::table('reporte_descargas')->whereDate('created_at', now())->count();
    
    $ultimasDescargas = DB::table('reporte_descargas')
        ->orderBy('created_at', 'desc')
        ->limit(10)
        ->get();

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

public static function registrarDescarga($personal, $tipoReporte, $detalles = null)
{
    // Normalización: Si la palabra "Constancia" aparece, forzamos el nombre estándar
    $nombreEstandar = $tipoReporte;
    
    if (str_contains(strtolower($tipoReporte), 'constancia')) {
        $nombreEstandar = 'Constancia de Trabajo';
    } elseif (str_contains(strtolower($tipoReporte), 'recibo')) {
        $nombreEstandar = 'Recibo de Pago';
    }

    DB::table('reporte_descargas')->insert([
        'cedula' => $personal->cedper,
        'nombre_trabajador' => strtoupper($personal->nomper . ' ' . $personal->apeper),
        'tipo_reporte' => $nombreEstandar, // El nombre limpio para la gráfica
        'detalles' => $detalles, // Aquí puedes guardar "Generado por: Analista"
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}
}
