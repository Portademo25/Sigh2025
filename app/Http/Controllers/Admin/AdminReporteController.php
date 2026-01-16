<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminReporteController extends Controller
{
    /**
     * Muestra la lista de descargas para el Administrador
     */
   public function historialDescargas(Request $request)
{
    // Datos para la tabla
    $descargas = DB::table('reporte_descargas')
        ->orderBy('created_at', 'desc')
        ->paginate(15);

    // Datos para el gráfico (últimos 7 días)
    $stats = DB::table('reporte_descargas')
        ->select(DB::raw('DATE(created_at) as fecha'), DB::raw('count(*) as total'))
        ->where('created_at', '>=', now()->subDays(7))
        ->groupBy('fecha')
        ->orderBy('fecha', 'ASC')
        ->get();

    // Preparamos los labels y datos para JS
    $labels = $stats->pluck('fecha')->map(fn($f) => \Carbon\Carbon::parse($f)->format('d/m'))->toArray();
    $valores = $stats->pluck('total')->toArray();

    return view('admin.reportes.descargas_index', compact('descargas', 'labels', 'valores'));
}

    /**
     * Esta función NO se llama por ruta, sino que se invoca internamente
     * desde tus otros controladores (Recibos, ARC, IVSS)
     */
   public static function registrarDescarga($empleado, $tipo, $detalles)
{
    try {
        // Usamos \DB::table para asegurar que apunte a la conexión por defecto (tu DB local)
        DB::table('reporte_descargas')->insert([
            'cedula'          => $empleado->cedper, // Según tu dd, el campo es 'cedper'
            'nombre_empleado' => $empleado->nomper . ' ' . $empleado->apeper,
            'tipo_reporte'    => $tipo,
            'detalles'        => $detalles,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

    } catch (\Exception $e) {
        // Si hay un error al insertar, este dd te dirá EXACTAMENTE por qué
        dd([
            'error' => $e->getMessage(),
            'tabla' => 'reporte_descargas',
            'datos_que_intente_guardar' => [
                'cedula' => $empleado->cedper,
                'nombre' => $empleado->nomper . ' ' . $empleado->apeper
            ]
        ]);
    }
}
}
