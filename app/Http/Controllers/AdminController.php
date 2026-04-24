<?php
// app/Http/Controllers/AdminController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReporteDescargasExport;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Models\SecurityLog;
use Illuminate\Support\Facades\Artisan;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

public function dashboard(Request $request)
{
    $fechaInicio = $request->get('fecha_inicio', Carbon::now()->startOfMonth()->format('Y-m-d'));
    $fechaFin = $request->get('fecha_fin', Carbon::now()->format('Y-m-d'));

    $start = Carbon::parse($fechaInicio)->startOfDay();
    $end = Carbon::parse($fechaFin)->endOfDay();

    // 1. Contadores basados en la tabla de auditoría
    $totalPeriodo = DB::table('reporte_descargas')
                ->whereBetween('created_at', [$start, $end])
                ->count();

    // 2. Gráfico de Torta (ahora leerá ARC, Recibos y Constancias de la misma tabla)
    $statsPie = DB::table('reporte_descargas')
                ->select('tipo_reporte', DB::raw('count(*) as total'))
                ->whereBetween('created_at', [$start, $end])
                ->groupBy('tipo_reporte')
                ->get();

    $labelsPie = $statsPie->pluck('tipo_reporte')->toArray();
    $datosPie = $statsPie->pluck('total')->toArray();

    // 3. Actividad Reciente
    $ultimasDescargas = DB::table('reporte_descargas')
                ->whereBetween('created_at', [$start, $end])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

    // 4. Gráficas de Barras Mensuales
    $anio = $start->year;
    $arcStats = $this->getMonthlyStats('Planilla ARC', $anio);
    $reciboStats = $this->getMonthlyStats('Recibo de Pago', $anio);
    $constanciaStats = $this->getMonthlyStats('Constancia Trabajo', $anio);

    return view('admin.dashboard', compact(
        'fechaInicio', 'fechaFin', 'labelsPie', 'datosPie',
        'ultimasDescargas', 'arcStats', 'reciboStats', 'constanciaStats',
        'totalPeriodo'
    ));
}
/**
 * Función auxiliar para obtener estadísticas mensuales para Chart.js
 */
private function getMonthlyStats($tipo, $anio)
{
    $stats = DB::table('constancias_generadas')
        ->select(DB::raw('EXTRACT(MONTH FROM created_at) as mes'), DB::raw('count(*) as total'))
        ->where('tipo_reporte', $tipo)
        ->whereYear('created_at', $anio)
        ->groupBy('mes')
        ->pluck('total', 'mes')
        ->toArray();

    $data = [];
    for ($i = 1; $i <= 12; $i++) {
        $data[] = $stats[$i] ?? 0;
    }
    return $data;
}

  public function Graficas()
{
    echo "<h1>Si puedes leer esto, estoy en el archivo correcto</h1>";
    exit; //
    // 1. Intentamos obtener la distribución de los datos
    // Usamos DB::table o DB::connection('mysql')->table si tienes varias conexiones
    $distribucion = DB::table('reporte_descargas')
        ->select('tipo_reporte', DB::raw('count(*) as total'))
        ->groupBy('tipo_reporte')
        ->get();

    // 2. Extraemos los labels y los datos
    $labelsPie = $distribucion->pluck('tipo_reporte')->toArray();
    $datosPie = $distribucion->pluck('total')->toArray();

    // --- AQUÍ APLICAMOS EL DD ---
    dd([
        'Total_Registros_Encontrados' => $distribucion->count(),
        'Contenido_Distribucion' => $distribucion,
        'Labels_Procesados' => $labelsPie,
        'Datos_Procesados' => $datosPie,
        'Sql_Ejecutado' => DB::table('reporte_descargas')
                            ->select('tipo_reporte', DB::raw('count(*) as total'))
                            ->groupBy('tipo_reporte')
                            ->toSql()
    ]);
    // ----------------------------

    // El resto del código no se ejecutará mientras el dd esté arriba
    $totalHoy = DB::table('reporte_descargas')->whereDate('created_at', today())->count();
    $usuariosActivos = DB::table('reporte_descargas')->distinct('cedula')->count();
    $ultimasDescargas = DB::table('reporte_descargas')->orderBy('created_at', 'desc')->limit(5)->get();

    return view('admin.dashboard', compact('labelsPie', 'datosPie', 'totalHoy', 'usuariosActivos', 'ultimasDescargas'));
}
    // public function crearEmpleado(Request $request)
    // {
    //     $request->validate([
    //         'name' => 'required|string|max:255',
    //         'email' => 'required|email|unique:users',
    //         'password' => 'required|min:8|confirmed',
    //     ]);

    //     $empleado = User::create([
    //         'name' => $request->name,
    //         'email' => $request->email,
    //         'password' => bcrypt($request->password),
    //     ]);

    //     $empleado->assignRole('empleado');

    //     return redirect()->route('admin.empleados')->with('success', 'Empleado creado exitosamente');
    // }

    public function exportExcel(Request $request)
{
    // Capturamos las fechas que vienen del link del Dashboard
    $fechaInicio = $request->get('fecha_inicio');
    $fechaFin = $request->get('fecha_fin');

    // Validación básica por si acaso
    if (!$fechaInicio || !$fechaFin) {
        return back()->with('error', 'Debe seleccionar un rango de fechas para exportar.');
    }

    $nombreArchivo = "Reporte_Sigh2025_" . $fechaInicio . "_al_" . $fechaFin . ".xlsx";

    return Excel::download(new ReporteDescargasExport($fechaInicio, $fechaFin), $nombreArchivo);
}

public function downloadLogs()
{
    $fileName = 'log_seguridad_sigh2025_' . date('Y-m-d_H-i') . '.csv';
    $headers = [
        "Content-type"        => "text/csv",
        "Content-Disposition" => "attachment; filename=$fileName",
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        "Expires"             => "0"
    ];

    $columnas = ['ID', 'Evento', 'Usuario', 'IP', 'Gravedad', 'Fecha', 'Detalles'];

    $callback = function() use ($columnas) {
        $file = fopen('php://output', 'w');
        fputcsv($file, $columnas);

        // Usamos cursor() para no saturar la memoria RAM si hay muchos logs
        foreach (SecurityLog::orderBy('created_at', 'desc')->cursor() as $log) {
            fputcsv($file, [
                $log->id,
                $log->event,
                $log->user_identifier,
                $log->ip_address,
                $log->severity,
                $log->created_at,
                json_encode($log->details) // Convertimos el array de detalles a texto
            ]);
        }
        fclose($file);
    };

    // Registramos que alguien descargó la auditoría
    record_security_event('Descarga de Log Completo', 'Baja', ['formato' => 'CSV']);

    return response()->stream($callback, 200, $headers);
}

public function optimizeSystem()
{
    try {
        // En PostgreSQL, el comando equivalente a OPTIMIZE es VACUUM
        // ANALYZE actualiza las estadísticas para que las consultas sean más rápidas
        DB::statement('VACUUM ANALYZE');

        record_security_event('Optimización de Base de Datos', 'Baja', ['motor' => 'PostgreSQL VACUUM']);

        return back()->with('success', 'Tablas optimizadas y estadísticas actualizadas con éxito.');
    } catch (\Exception $e) {
        return back()->with('error', 'Error al optimizar: ' . $e->getMessage());
    }
}

public function clearCache()
{
    try {
        // Limpiamos caché de rutas, vistas, configuración y la general
        Artisan::call('cache:clear');
        Artisan::call('view:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');

        record_security_event('Limpieza de Caché del Sistema', 'Baja');

        return back()->with('success', 'La caché del sistema ha sido purgada correctamente.');
    } catch (\Exception $e) {
        return back()->with('error', 'Error al limpiar caché: ' . $e->getMessage());
    }
}
}
