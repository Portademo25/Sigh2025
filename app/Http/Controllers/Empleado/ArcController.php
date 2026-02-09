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
    if (ob_get_contents()) ob_end_clean(); // Limpia cualquier residuo de salida

    $user = Auth::user();
    $v_codper = str_pad(trim($user->codper), 10, "0", STR_PAD_LEFT);

    try {
        // 1. Datos de la Empresa
        $datosEmpresa = DB::connection('sigesp')->table('sigesp_empresa')->first();

        $agente = [
            'nombre'    => $datosEmpresa->nomrep ?? 'S/N',
            'cedula'    => number_format($datosEmpresa->cedrep ?? 0, 0, '', '.'),
            'ente'      => $datosEmpresa->nombre ?? 'S/N',
            'rif'       => $datosEmpresa->rifemp ?? 'S/N',
            'direccion' => $datosEmpresa->direccion ?? 'S/N',
            'telefono'  => $datosEmpresa->telemp ?? 'S/N',
            'ciudad'    => $datosEmpresa->ciuemp ?? 'S/N',
            'estado'    => $datosEmpresa->estemp ?? 'S/N',
            'cargo'     => $datosEmpresa->carrep ?? 'DIRECTOR EJECUTIVO'
        ];

        // 2. Datos del Trabajador (Garantizando Estatus 1 / Activo)
        $personal = DB::connection('sigesp')->table('sno_personal')
            ->select('nomper', 'apeper', 'cedper', 'codper')
            ->where('codper', $v_codper)
            ->where('estper', '1')
            ->first();

        if (!$personal) {
            return redirect()->back()->with('error', "No se encontró ficha ACTIVA para el trabajador.");
        }

        // 3. Consulta de Remuneraciones
        // 3. Consulta de Remuneraciones AJUSTADA (Solo P1 y Filtro de Seguridad)
$nominasARC = ['0001', '0002', '0003', '0004', '0005', '0006', '0009', '0010', '0011', '0012', '0013', '0014', '0051', '0052', '0053', '0054', '0055', '0056'];

$detalles = DB::connection('sigesp')
    ->table('sno_hsalida as hs')
    ->join('sno_hperiodo as hp', function($join) {
        $join->on('hs.codnom', '=', 'hp.codnom')
             ->on('hs.codperi', '=', 'hp.codperi');
    })
    ->select(
        DB::raw('EXTRACT(MONTH FROM hp.fecdesper) as mes'),
        
        // ASIGNACIONES: Suma conceptos 001, 002 y 006 (Sueldo y Primas)
        // Ignora cualquier registro individual mayor a 5000 para limpiar el error de Enero
        DB::raw("SUM(CASE 
            WHEN hs.tipsal IN ('A', 'A ') 
            AND hs.valsal < 5000 
            AND hs.codconc IN ('0000000001', '0000000002', '0000000006') 
            THEN ABS(hs.valsal) ELSE 0 END) as asignacion"),
        
        // RETENCIONES: Solo tipo P1 como solicitaste
        DB::raw("SUM(CASE WHEN hs.tipsal = 'P1' AND hs.codconc = '0000000001' THEN ABS(hs.valsal) ELSE 0 END) as ret_islr"),
        DB::raw("SUM(CASE WHEN hs.tipsal = 'P1' AND hs.codconc = '0000000502' THEN ABS(hs.valsal) ELSE 0 END) as monto_faov"),
        DB::raw("SUM(CASE WHEN hs.tipsal = 'P1' AND hs.codconc = '0000000500' THEN ABS(hs.valsal) ELSE 0 END) as monto_sso"),
        DB::raw("SUM(CASE WHEN hs.tipsal = 'P1' AND hs.codconc = '0000000501' THEN ABS(hs.valsal) ELSE 0 END) as monto_pie"),
        DB::raw("SUM(CASE WHEN hs.tipsal = 'P1' AND hs.codconc = '0000000002' THEN ABS(hs.valsal) ELSE 0 END) as monto_inces"),
        DB::raw("SUM(CASE WHEN hs.tipsal = 'P1' AND hs.codconc = '0000000503' THEN ABS(hs.valsal) ELSE 0 END) as monto_pension")
    )
    ->where('hs.codper', $v_codper)
    ->whereIn('hs.codnom', $nominasARC)
    ->whereYear('hp.fecdesper', $ano)
    ->groupBy('mes')
    ->orderBy('mes')
    ->get();
        // 4. Token y QR
        $token = Str::random(32);
        $qrCode = base64_encode(QrCode::format('svg')->size(80)->margin(0)->generate(route('arc.verificar', $token)));

        // 5. Logos (Restaurados para evitar el error de variable indefinida)
        $pathRepublica = public_path('images/logo_ministerio.png');
        $pathEnte = public_path('images/logo_fona.png');

        $logoRepublica = file_exists($pathRepublica) ? base64_encode(file_get_contents($pathRepublica)) : "";
        $logoEnte = file_exists($pathEnte) ? base64_encode(file_get_contents($pathEnte)) : "";

        $meses = [
            1 => 'ENERO', 2 => 'FEBRERO', 3 => 'MARZO', 4 => 'ABRIL',
            5 => 'MAYO', 6 => 'JUNIO', 7 => 'JULIO', 8 => 'AGOSTO',
            9 => 'SEPTIEMBRE', 10 => 'OCTUBRE', 11 => 'NOVIEMBRE', 12 => 'DICIEMBRE'
        ];

        $data = [
            'agente'        => $agente,
            'personal'      => $personal,
            'ano'           => $ano,
            'detalles'      => $detalles,
            'meses'         => $meses,
            'qrCode'        => $qrCode,
            'logoRepublica' => $logoRepublica,
            'logoEnte'      => $logoEnte,
            'fecha'         => date('d/m/Y')
        ];

        return Pdf::loadView('empleado.reportes.arc_pdf', $data)
            ->setPaper('letter', 'portrait')
            ->stream("ARC_{$ano}.pdf");

    } catch (\Exception $e) {
        return dd("Error técnico: " . $e->getMessage() . " en Línea: " . $e->getLine());
    }
}

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
}


