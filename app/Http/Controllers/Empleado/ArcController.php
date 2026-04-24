<?php

namespace App\Http\Controllers\Empleado;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminReporteController;
use Illuminate\Support\Facades\Log;
use App\Services\ArcService; // Importamos el servicio

class ArcController extends Controller
{
    public function indexArc()
    {
        $anoActual = date('Y');
        $anios = [$anoActual, $anoActual - 1];
        return view('empleado.reportes.arc_index', compact('anios'));
    }

public function generarArc($ano, ArcService $arcService)
{
    ini_set('memory_limit', '1024M');
    set_time_limit(300);

    // Limpieza de buffer para evitar carácteres extraños en el PDF
    while (ob_get_level()) ob_end_clean();

    $user = Auth::user();
    // Aseguramos que el código de personal tenga los ceros a la izquierda para SIGESP
    $v_codper = str_pad(trim($user->codper), 10, "0", STR_PAD_LEFT);

    try {
        /**
         * 1. Obtener data procesada a través del Service
         * El Service ahora se encarga internamente de buscar en 'arc_parametros',
         * limpiar los strings tipo "00000000500 | A" y consultar SIGESP.
         */
        $mesesData = $arcService->obtenerDataReporte($ano, $v_codper);

        $detalles = collect(range(1, 12))->map(function ($mes) use ($mesesData) {
            $dataMes = $mesesData->firstWhere('mes', $mes);

            // Si el service no devuelve datos para ese mes, inicializamos en vacío
            $detalleOriginal = $dataMes ? (array)$dataMes->detalle : [];

            return (object)[
                'mes'               => $mes,
                'asignacion'        => $dataMes ? $dataMes->remuneracion : 0,
                'ret_islr'          => 0, // Puedes mapear esto si tienes el concepto de ISLR en el mapa
                'otras_retenciones' => 0,
                'detalle_original'  => $detalleOriginal,
                'total_patronales'  => $dataMes ? $dataMes->total_patronales : 0
            ];
        });

        // 2. Datos de SIGESP (Datos maestros del empleado y la institución)
        $personal = DB::connection('sigesp')->table('sno_personal')->where('codper', $v_codper)->first();
        $empresa = DB::connection('sigesp')->table('sigesp_empresa')->first();

        // 3. Lógica de Logo Dinámico
        $logoDinamicoPath = DB::table('settings')->where('key', 'logo_path')->value('value');

        if ($logoDinamicoPath && file_exists(public_path('storage/' . $logoDinamicoPath))) {
            $logoInstitucion = public_path('storage/' . $logoDinamicoPath);
        } else {
            $logoInstitucion = public_path('images/logo-fona.png');
        }

        // 4. Cálculos de totales finales basados en la colección de detalles
        $total_asignacion = $detalles->sum('asignacion');
        $total_desglose_ley = $detalles->sum('total_patronales');

        $data = [
            'agente' => [
                'nombre'    => $empresa->nomrep ?? 'S/N',
                'cedula'    => number_format($empresa->cedrep ?? 0, 0, '', '.'),
                'ente'      => $empresa->nombre ?? 'S/N',
                'rif'       => $empresa->rifemp ?? 'S/N',
                'direccion' => $empresa->diremp ?? 'S/N',
                'telefono'  => $empresa->telemp ?? 'S/N',
                'ciudad'    => $empresa->ciuemp ?? 'CARACAS',
                'estado'    => $empresa->estemp ?? 'DISTRITO CAPITAL',
                'cargo'     => 'DIRECTOR EJECUTIVO',
            ],
            'personal' => (object)[
                'codper' => $personal->codper ?? $v_codper,
                'cedper' => $personal->cedper ?? '0',
                'nomper' => trim(($personal->nomper ?? '') . ' ' . ($personal->apeper ?? '')),
            ],
            'detalles' => $detalles,
            'total_desglose_ley' => round($total_desglose_ley, 2),
            'totales'  => [
                'total_asignacion' => round($total_asignacion, 2),
                'total_ret_islr'   => 0,
                'total_otras'      => 0,
                'total_general'    => round($total_asignacion, 2),
            ],
            'ano'   => $ano,
            'fecha' => date('d/m/Y'),
            'meses' => [
                1=>'ENERO', 2=>'FEBRERO', 3=>'MARZO', 4=>'ABRIL',
                5=>'MAYO', 6=>'JUNIO', 7=>'JULIO', 8=>'AGOSTO',
                9=>'SEPTIEMBRE', 10=>'OCTUBRE', 11=>'NOVIEMBRE', 12=>'DICIEMBRE'
            ],
            'logoRepublica' => public_path('images/logo_ministerio.png'),
            'logoFona'      => $logoInstitucion,
        ];

        // 5. Generación del PDF
        return \Barryvdh\DomPDF\Facade\Pdf::loadView('empleado.reportes.arc_pdf', $data)
            ->setPaper('letter')
            ->stream("ARC_{$v_codper}_{$ano}.pdf");

    } catch (\Exception $e) {
        // Log del error para depuración en Debian
        Log::error("Error en generarArc: " . $e->getMessage());
        return "Hubo un error al generar el reporte: " . $e->getMessage();
    }
}
/**
 * Función auxiliar para convertir imágenes a Base64
 * Asegúrate de que las imágenes estén en public/images/
 */
private function cargarLogo($nombreArchivo)
{
    $ruta = public_path('images/' . $nombreArchivo); // Ajusta la carpeta si es necesario

    if (!file_exists($ruta)) {
        return null;
    }

    $contenido = file_get_contents($ruta);
    $tipo = pathinfo($ruta, PATHINFO_EXTENSION);
    return 'data:image/' . $tipo . ';base64,' . base64_encode($contenido);
}
// Métodos auxiliares para limpiar el código principal

    public function index(Request $request)
    {
        $buscar = $request->input('buscar');

        $personal = DB::connection('sigesp')->table('sno_personal')
            ->select('cedper', 'nomper', 'apeper', 'codper')
            ->where('estper', '1') // También filtramos aquí para el admin
            ->when($buscar, function ($query, $buscar) {
                return $query->where(function($q) use ($buscar) {
                    $q->where('cedper', 'like', "%{$buscar}%")
                      ->orWhere('nomper', 'like', "%{$buscar}%")
                      ->orWhere('apeper', 'like', "%{$buscar}%");
                });
            })
            ->limit(10)
            ->get();

        $anios = [date('Y'), date('Y') - 1];

        return view('admin.reportes.index_arc', compact('personal', 'anios'));
    }

public function generarArcAdmin($cedper, $ano, ArcService $arcService)
{
    // Limpieza de buffer para evitar carácteres extraños en el PDF
    if (ob_get_level()) ob_end_clean();

    try {
        // 1. Búsqueda del Trabajador en SIGESP
        // Usamos la conexión a SIGESP para validar que el trabajador existe
        $personal = DB::connection('sigesp')->table('sno_personal')
            ->select('nomper', 'apeper', 'cedper', 'codper')
            ->where('cedper', $cedper)
            ->first();

        if (!$personal) {
            return redirect()->back()->with('error', "No se encontró el personal con cédula: $cedper");
        }

        // Formateamos el codper con ceros a la izquierda para la consulta en tablas históricas
        $v_codper = str_pad(trim($personal->codper), 10, "0", STR_PAD_LEFT);

        /**
         * 2. Obtener data procesada
         * El Service ya fue ajustado para limpiar los códigos de nómina "00000000500 | A"
         * y filtrar solo por lo configurado en arc_parametros.
         */
        $mesesData = $arcService->obtenerDataReporte($ano, $v_codper);

        // 3. Mapeo dinámico de los 12 meses
        $detalles = collect(range(1, 12))->map(function ($mes) use ($mesesData) {
            $dataMes = $mesesData->firstWhere('mes', $mes);

            // detalle_original contiene los objetos {monto, nombre} (Patronales/Ley)
            $detalleOriginal = $dataMes ? (array)$dataMes->detalle : [];

            return (object)[
                'mes'               => $mes,
                'asignacion'        => $dataMes ? $dataMes->remuneracion : 0,
                'ret_islr'          => 0,
                'otras_retenciones' => 0,
                'detalle_original'  => $detalleOriginal,
                'total_patronales'  => $dataMes ? $dataMes->total_patronales : 0 // Importante para el desglose
            ];
        });

        // 4. Datos de la Institución y Logo Dinámico
        $datosEmpresa = DB::connection('sigesp')->table('sigesp_empresa')->first();
        $logoDinamicoPath = DB::table('settings')->where('key', 'logo_path')->value('value');

        if ($logoDinamicoPath && file_exists(public_path('storage/' . $logoDinamicoPath))) {
            $logoInstitucion = public_path('storage/' . $logoDinamicoPath);
        } else {
            $logoInstitucion = public_path('images/logo-fona.png');
        }

        // 5. Cálculo dinámico del total anual para el desglose de ley
        // Sumamos el campo total_patronales que calculó el Service
        $total_desglose_ley = $detalles->sum('total_patronales');

        // 6. Preparación de data para la vista
        $data = [
            'agente' => [
                'nombre'    => $datosEmpresa->nomrep ?? 'S/N',
                'cedula'    => number_format($datosEmpresa->cedrep ?? 0, 0, '', '.'),
                'ente'      => $datosEmpresa->nombre ?? 'S/N',
                'rif'       => $datosEmpresa->rifemp ?? 'S/N',
                'direccion' => $datosEmpresa->diremp ?? $datosEmpresa->direccion ?? 'S/N',
                'telefono'  => $datosEmpresa->telemp ?? 'S/N',
                'ciudad'    => $datosEmpresa->ciuemp ?? 'S/N',
                'estado'    => $datosEmpresa->estemp ?? 'S/N',
                'cargo'     => 'DIRECTOR EJECUTIVO',
            ],
            'personal' => (object)[
                'codper' => $personal->codper,
                'cedper' => $personal->cedper,
                'nomper' => strtoupper(trim($personal->nomper . ' ' . $personal->apeper)),
            ],
            'detalles' => $detalles,
            'total_desglose_ley' => round($total_desglose_ley, 2),
            'totales'  => [
                'total_asignacion' => round($detalles->sum('asignacion'), 2),
                'total_ret_islr'   => 0,
                'total_otras'      => 0,
                'total_general'    => round($detalles->sum('asignacion'), 2),
            ],
            'ano'   => $ano,
            'fecha' => date('d/m/Y'),
            'meses' => [
                1 => 'ENERO', 2 => 'FEBRERO', 3 => 'MARZO', 4 => 'ABRIL', 5 => 'MAYO', 6 => 'JUNIO',
                7 => 'JULIO', 8 => 'AGOSTO', 9 => 'SEPTIEMBRE', 10 => 'OCTUBRE', 11 => 'NOVIEMBRE', 12 => 'DICIEMBRE'
            ],
            'logoRepublica' => public_path('images/logo_ministerio.png'),
            'logoFona'      => $logoInstitucion,
        ];

        // 7. Registro en Auditoría (Indispensable en Debian/Producción)
        DB::table('reporte_descargas')->insert([
            'cedula'          => $personal->cedper,
            'nombre_empleado' => strtoupper($personal->nomper . ' ' . $personal->apeper),
            'tipo_reporte'    => 'Planilla ARC (Admin)',
            'detalles'        => "Generado por Analista: " . Auth::user()->name . " | Año Fiscal: $ano",
            'created_at'      => now(),
        ]);

        // Generación del PDF con DomPDF
        return \Barryvdh\DomPDF\Facade\Pdf::loadView('empleado.reportes.arc_pdf', $data)
            ->setPaper('letter', 'portrait')
            ->stream("ARC_{$cedper}_{$ano}.pdf");

    } catch (\Exception $e) {
        Log::error("Error ARC Admin: " . $e->getMessage());
        return redirect()->back()
            ->with('error', "Error al generar el reporte: " . $e->getMessage());
    }
}
 }



