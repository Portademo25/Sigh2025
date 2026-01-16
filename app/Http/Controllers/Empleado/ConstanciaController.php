<?php

namespace App\Http\Controllers\Empleado;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Str; // Importante para el token
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Http\Controllers\Admin\AdminReporteController;

class ConstanciaController extends Controller
{
    public function pdfConstancia()
    {
        $user = Auth::user();
        // Ajustar código de persona a 10 dígitos (estándar SIGESP)
        $v_codper = str_pad($user->codper, 10, "0", STR_PAD_LEFT);

        try {
            // 1. Datos del Personal (Desde SIGESP)
            $personal = DB::connection('sigesp')
    ->table('sno_personal as p')
    ->join('sno_personalnomina as pn', 'p.codper', '=', 'pn.codper')
    ->join('sno_nomina as n', 'pn.codnom', '=', 'n.codnom')
    // Agregamos el join con la tabla de cargos real
    ->join('sno_cargo as c', function($join) {
        $join->on('pn.codnom', '=', 'c.codnom')
             ->on('pn.codcar', '=', 'c.codcar');
    })
    ->leftJoin('sno_unidadadmin as ua', function($join) {
        $join->on('pn.minorguniadm', '=', 'ua.minorguniadm')
             ->on('pn.uniuniadm', '=', 'ua.uniuniadm')
             ->on('pn.depuniadm', '=', 'ua.depuniadm')
             ->on('pn.prouniadm', '=', 'ua.prouniadm');
    })
    ->select(
        'p.nomper',
        'p.apeper',
        'p.cedper',
        'pn.fecingper',
        'c.descar as cargo', // <--- CAMBIAMOS n.desnom POR c.descar
        'ua.desuniadm as unidad'
    )
    ->where('p.codper', $v_codper)
    // Importante: SIGESP a veces tiene registros históricos, tomamos el activo
    ->where('pn.staper', '1')
    ->first();

            if (!$personal) {
                return dd("Error: Trabajador no encontrado en SIGESP.");
            }

            // 2. Beneficio de Alimentación (Base de datos Local)
            $beneficioAlim = DB::table('balimentacion')->value('monto') ?? 7732.00;

            // 3. Cálculo de Sueldo Mensual (SIGESP)
            $ultimoPeriodo = DB::connection('sigesp')->table('sno_hsalida')->where('codper', $v_codper)->max('codperi');
            $conceptos = DB::connection('sigesp')->table('sno_hsalida')
                ->where([['codper', $v_codper], ['codperi', $ultimoPeriodo], ['valsal', '>', 0]])
                ->whereIn('tipsal', ['A', 'A '])
                ->get();

            // Multiplicamos por 2 asumiendo que el histórico es quincenal (ajustar según su nómina)
            $sueldoMensual = $conceptos->sum('valsal') * 2;

            // 4. Generación de Token Único y Guardado en DB Local
            $token = Str::random(32); // Generamos un token largo y seguro
            DB::table('constancias_generadas')->insert([
    'token'              => $token,
    'cedula'             => $personal->cedper,
    'nombre_completo'    => strtoupper($personal->nomper . ' ' . $personal->apeper),
    'sueldo_integral'    => $sueldoMensual,
    'monto_alimentacion' => $beneficioAlim,
    'cargo'              => strtoupper($personal->cargo),
    'unidad'             => strtoupper($personal->unidad ?? 'OFICINA GENERAL'),
    'fecha_generacion'   => now(),
    'created_at'         => now(),
]);

            // 5. Conversión de Montos a Letras
            $formatter = new \NumberFormatter("es", \NumberFormatter::SPELLOUT);

            $sueldoTexto = strtoupper($formatter->format(floor($sueldoMensual)));
            $sueldoCents = str_pad(round(($sueldoMensual - floor($sueldoMensual)) * 100), 2, "0", STR_PAD_LEFT);

            $alimTexto = strtoupper($formatter->format(floor($beneficioAlim)));
            $alimCents = str_pad(round(($beneficioAlim - floor($beneficioAlim)) * 100), 2, "0", STR_PAD_LEFT);

            // 6. Preparación de Datos para la Vista
            $data = [
                'ls_nombres'                => strtoupper($personal->nomper),
                'ls_apellidos'              => strtoupper($personal->apeper),
                'ls_cedula'                 => number_format($personal->cedper, 0, '', '.'),
                'ld_fecha_ingreso'          => Carbon::parse($personal->fecingper)->format('d/m/Y'),
                'ls_cargo'                  => strtoupper($personal->cargo),
                'ls_unidad_administrativa'  => strtoupper($personal->unidad ?? 'OFICINA GENERAL'),
                'li_mensual_inte_sueldo'    => $sueldoTexto . " BOLÍVARES CON " . $sueldoCents . "/100 (Bs. " . number_format($sueldoMensual, 2, ',', '.') . ")",
                'ls_monto_alimentacion'     => $alimTexto . " BOLÍVARES CON " . $alimCents . "/100 (Bs. " . number_format($beneficioAlim, 2, ',', '.') . ")",
                'ls_dia'                    => date('d'),
                'ls_mes'                    => $this->getMesNombre(date('m')),
                'ls_ano'                    => date('Y'),
            ];

            // 7. Generación del QR basado en el TOKEN (No en la cédula)
            $urlVerificacion = route('constancia.verificar', ['token' => $token]);

            $data['qrCode'] = base64_encode(QrCode::format('svg')
                ->size(100)
                ->margin(0)
                ->generate($urlVerificacion));

            // 8. Generación del PDF
           // Busca la línea donde registras y asegúrate de que sea así:

    AdminReporteController::registrarDescarga(
    $personal,
    'Constancia de Trabajo',
    "Token: " . substr($token, 0, 8)
);
            $nombreArchivo = "Constancia de Trabajador " . $personal->nomper . " " . $personal->apeper . ".pdf";

            return Pdf::loadView('empleado.reportes.constancia_pdf', $data)
                      ->setOption('dpi', 96)
                      ->stream($nombreArchivo);

        } catch (\Exception $e) {
            return dd("Error al generar constancia: " . $e->getMessage());
        }
    }

    private function getMesNombre($mesNum) {
        $meses = ['01'=>'Enero','02'=>'Febrero','03'=>'Marzo','04'=>'Abril','05'=>'Mayo','06'=>'Junio','07'=>'Julio','08'=>'Agosto','09'=>'Septiembre','10'=>'Octubre','11'=>'Noviembre','12'=>'Diciembre'];
        return $meses[$mesNum];
    }

    /**
     * Método público para validar el TOKEN de la constancia
     */
   public function verificarPublico($token)
{
    // 1. Intentamos buscar el registro por el token único
    $registro = DB::table('constancias_generadas')
        ->where('token', $token)
        ->first();

    // 2. Si el TOKEN NO EXISTE, retornamos la VISTA FALLIDA
    if (!$registro) {
        return view('publico.verificacion_fallida');
    }

    // 3. Si el TOKEN EXISTE, verificamos el estatus actual en SIGESP
    $personalSigesp = DB::connection('sigesp')
        ->table('sno_personal as p')
        ->join('sno_personalnomina as pn', 'p.codper', '=', 'pn.codper')
        ->select('pn.staper')
        ->where('p.cedper', $registro->cedula)
        ->first();

    $activo = ($personalSigesp && $personalSigesp->staper == '1');




    // 4. Retornamos la VISTA EXITOSA con los datos del registro
    return view('publico.verificacion_exitosa', [
        'registro' => $registro,
        'activo'   => $activo
    ]);
}



public function reporteAdmin()
{
    // Consultamos la tabla local
    $reporte = DB::table('constancias_generadas')
        ->orderBy('fecha_generacion', 'desc')
        ->paginate(15); // Paginación de 15 en 15

    return view('admin.reportes.index_constancias', compact('reporte'));
}
}
