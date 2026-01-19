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


class ArcController extends Controller
{
    public function indexArc()
    {
        $anoActual = date('Y');
    $anios = [$anoActual, $anoActual - 1];

    return view('empleado.reportes.arc_index', compact('anios'));
    }

  public function generarArc($ano)
{
    $user = Auth::user();
    $v_codper = str_pad($user->codper, 10, "0", STR_PAD_LEFT);
    $codigoIslr = str_pad("1", 10, "0", STR_PAD_LEFT);


    try {

// 1. Consultamos todas las columnas de la tabla sno_nomina

        // 1. Datos del Agente de Retención
        // 1. Datos del Agente de Retención
$datosEmpresa = DB::connection('sigesp')->table('sigesp_empresa')->first();

// Intentamos obtener el cargo del representante, si no existe ponemos 'DIRECTOR EJECUTIVO' por defecto
$cargoRepresentante = $datosEmpresa->carrep ?? 'DIRECTOR EJECUTIVO';

$agente = [
    'nombre'    => $datosEmpresa->nomrep, // El nombre del Director que firma
    'cedula'    => number_format($datosEmpresa->cedrep, 0, '', '.'),
    'ente'      => $datosEmpresa->nombre,
    'rif'       => $datosEmpresa->rifemp,
    'direccion' => $datosEmpresa->direccion,
    'telefono'  => $datosEmpresa->telemp,
    'ciudad'    => $datosEmpresa->ciuemp,
    'estado'    => $datosEmpresa->estemp,
    'cargo'     => $cargoRepresentante // Nueva variable para el cargo
];
        // 2. Datos del Trabajador
      $personal = DB::connection('sigesp')->table('sno_personal')
    ->select('nomper', 'apeper', 'cedper', 'codper')
    ->where('codper', $v_codper)
    ->first(); // Esto devuelve un OBJETO o NULL

// VALIDACIÓN ANTIFALLO: Si SIGESP no encuentra la ficha, evitamos que el PDF explote
if (!$personal) {
    // Si eres empleado, te regresa con error. Si eres admin, te avisa.
    return redirect()->back()->with('error', "No se encontró la ficha del trabajador con código: $v_codper en SIGESP.");
}

        // 3. Consulta de Remuneraciones y Retenciones (Ajustada)
      $nominasExcluidas = [
    '0009', '0010', '0011', '0013', '0014', // Alimentación
    '0021', '0022', '0023', '0024', '0025', '0026', // Becas
    '0063', '0064', '0065', '0066', '0067', '0068'  // Juguetes
];
AdminReporteController::registrarDescarga($v_codper, 'Planilla ARC', "Año Fiscal: {$ano}");
// Definimos exactamente las nóminas que SI aplican para el AR-C
$nominasARC = ['0001', '0002', '0003', '0004', '0005', '0006', '0015', '0016', '0017', '0018', '0019', '0020', '0051', '0052', '0053', '0054', '0055', '0056'];

$detalles = DB::connection('sigesp')
    ->table('sno_hsalida as hs')
    ->join('sno_hperiodo as hp', function($join) {
        $join->on('hs.codnom', '=', 'hp.codnom')
             ->on('hs.codperi', '=', 'hp.codperi');
    })
    ->select(
        DB::raw('EXTRACT(MONTH FROM hp.fecdesper) as mes'),
        // Usamos ABS() para que los montos negativos de SIGESP salgan positivos en el reporte
        DB::raw("SUM(CASE WHEN hs.tipsal = 'A' AND hs.codnom IN ('" . implode("','", $nominasARC) . "') THEN ABS(hs.valsal) ELSE 0 END) as asignacion"),
        DB::raw("SUM(CASE WHEN hs.tipsal = 'P1' AND LPAD(TRIM(hs.codconc), 10, '0') = '0000000001' THEN ABS(hs.valsal) ELSE 0 END) as ret_islr"),
        DB::raw("SUM(CASE WHEN hs.tipsal = 'P1' AND LPAD(TRIM(hs.codconc), 10, '0') = '0000000502' THEN ABS(hs.valsal) ELSE 0 END) as monto_faov"),
        DB::raw("SUM(CASE WHEN hs.tipsal = 'P1' AND LPAD(TRIM(hs.codconc), 10, '0') = '0000000500' THEN ABS(hs.valsal) ELSE 0 END) as monto_sso"),
        DB::raw("SUM(CASE WHEN hs.tipsal = 'P1' AND LPAD(TRIM(hs.codconc), 10, '0') = '0000000501' THEN ABS(hs.valsal) ELSE 0 END) as monto_pie"),
        DB::raw("SUM(CASE WHEN hs.tipsal = 'P1' AND LPAD(TRIM(hs.codconc), 10, '0') = '0000000002' THEN ABS(hs.valsal) ELSE 0 END) as monto_inces"),
        DB::raw("SUM(CASE WHEN hs.tipsal = 'P1' AND LPAD(TRIM(hs.codconc), 10, '0') = '0000000503' THEN ABS(hs.valsal) ELSE 0 END) as monto_pension"),
    )
    ->where('hs.codper', $v_codper)
    ->whereYear('hp.fecdesper', $ano)
    ->groupBy('mes')
    ->orderBy('mes')
    ->get();
        // 4. Generación de Token y QR
        $token = Str::random(32);
        $qrCode = base64_encode(QrCode::format('svg')->size(80)->margin(0)->generate(route('arc.verificar', $token)));
$logoRepublica = base64_encode(file_get_contents(public_path('images/logo_ministerio.png')));
$logoEnte = base64_encode(file_get_contents(public_path('images/logo_fona.png')));

$meses = [
    1 => 'ENERO', 2 => 'FEBRERO', 3 => 'MARZO', 4 => 'ABRIL',
    5 => 'MAYO', 6 => 'JUNIO', 7 => 'JULIO', 8 => 'AGOSTO',
    9 => 'SEPTIEMBRE', 10 => 'OCTUBRE', 11 => 'NOVIEMBRE', 12 => 'DICIEMBRE'
];

$data = [
    'agente'   => $agente,
    'personal' => $personal,
    'ano'      => $ano,
    'detalles' => $detalles,
    'meses'    => $meses, // Enviamos la variable a la vista
    'qrCode'   => $qrCode,
    'logoRepublica' => $logoRepublica, // Si ya los agregaste
    'logoEnte'      => $logoEnte,
    'fecha'    => date('d/m/Y')
];



return Pdf::loadView('empleado.reportes.arc_pdf', $data)
    ->setPaper('letter', 'portrait')
    ->stream("ARC_{$ano}.pdf");

    } catch (\Exception $e) {
        return dd("Error: " . $e->getMessage());
    }
}
public function index(Request $request)
    {
        $buscar = $request->input('buscar');

        // Listado de personal para que el admin seleccione
        $personal = DB::connection('sigesp')->table('sno_personal')
            ->select('cedper', 'nomper', 'apeper', 'codper')
            ->when($buscar, function ($query, $buscar) {
                return $query->where('cedper', 'like', "%{$buscar}%")
                             ->orWhere('nomper', 'like', "%{$buscar}%")
                             ->orWhere('apeper', 'like', "%{$buscar}%");
            })
            ->limit(10)
            ->get();

        $anios = [date('Y'), date('Y') - 1];

        return view('admin.reportes.index_arc', compact('personal', 'anios'));
    }

}
