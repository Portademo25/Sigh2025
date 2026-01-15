<?php

namespace App\Http\Controllers\Empleado;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ArcController extends Controller
{
    public function indexArc()
    {
        $anioActual = date('Y');
        $anios = range($anioActual, $anioActual - 5);
        return view('empleado.reportes.arc_index', compact('anios'));
    }

  public function generarArc($ano)
{
    $user = Auth::user();
    $v_codper = str_pad($user->codper, 10, "0", STR_PAD_LEFT);
    $codigoIslr = str_pad("1", 10, "0", STR_PAD_LEFT);

    try {

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
            ->select('nomper', 'apeper', 'cedper')
            ->where('codper', $v_codper)->first();

        // 3. Consulta de Remuneraciones y Retenciones (Ajustada)
       $detalles = DB::connection('sigesp')
    ->table('sno_hsalida as hs')
    ->join('sno_hperiodo as hp', function($join) {
        $join->on('hs.codnom', '=', 'hp.codnom')
             ->on('hs.codperi', '=', 'hp.codperi');
    })
    ->select(
        DB::raw('EXTRACT(MONTH FROM hp.fecdesper) as mes'),

        // ASIGNACIONES (Ya te funciona)
        DB::raw("SUM(CASE
WHEN hs.tipsal = 'A'
AND (
-- Rango 1: Sueldos y Primas Base
(hs.codnom BETWEEN '0001' AND '0006')
OR
-- Rango 2: Nóminas de Vacaciones (Solo si aplica)
(hs.codnom BETWEEN '0015' AND '0020')
OR
-- Rango 3: Nóminas de Ajuste/Aumento para el último trimestre
-- (Aquí es donde suelen estar los montos para llegar a 1092 y 1749)
(hs.codnom IN ('0051', '0056'))
)
-- IMPORTANTE: Excluimos explícitamente los Aguinaldos para que no se inflen
-- Si Oct/Dic deben ser 1092,30, no deben sumar las nóminas 0060-0066

THEN hs.valsal
ELSE 0
END) as asignacion"),

        // RETENCIONES INDIVIDUALES (Usando LPAD para asegurar los 10 dígitos)
       DB::raw("SUM(CASE WHEN hs.tipsal = 'P1' AND LPAD(TRIM(hs.codconc), 10, '0') = '0000000002' THEN hs.valsal ELSE 0 END) as monto_inces"),
        DB::raw("SUM(CASE WHEN hs.tipsal = 'P1' AND LPAD(TRIM(hs.codconc), 10, '0') = '0000000500' THEN hs.valsal ELSE 0 END) as monto_sso"),
        DB::raw("SUM(CASE WHEN hs.tipsal = 'P1' AND LPAD(TRIM(hs.codconc), 10, '0') = '0000000501' THEN hs.valsal ELSE 0 END) as monto_pie"),
        DB::raw("SUM(CASE WHEN hs.tipsal = 'P1' AND LPAD(TRIM(hs.codconc), 10, '0') = '0000000502' THEN hs.valsal ELSE 0 END) as monto_faov"),
        DB::raw("SUM(CASE WHEN hs.tipsal = 'P1' AND LPAD(TRIM(hs.codconc), 10, '0') = '0000000503' THEN hs.valsal ELSE 0 END) as monto_pension"),

        // TOTAL OTRAS RETENCIONES (Suma de los 5 conceptos anteriores)

        // ISLR (Si el ISLR sigue siendo 'R', lo dejamos así, si es P1 cámbialo también)

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
}
