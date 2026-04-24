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
use Illuminate\Support\Facades\Log;
use App\Services\ArcService;

class ConstanciaController extends Controller
{



public function pdfConstancia(ArcService $arcService)
{
    if (ob_get_level()) ob_end_clean();

    $user = Auth::user();
    // Aseguramos que el código de personal tenga los 10 dígitos para SIGESP
    $v_codper = str_pad($user->codper, 10, "0", STR_PAD_LEFT);
    $anioActual = date('Y');

    try {
        $parametros = DB::table('arc_parametros')->where('anio', $anioActual)->first()
                      ?? DB::table('arc_parametros')->orderBy('id', 'desc')->first();

        if (!$parametros) return back()->with('error', "No hay parámetros configurados.");

        $nominasAutorizadas = json_decode($parametros->nominas, true) ?? [];
        // Limpiamos los IDs de nómina por si vienen con el formato "0001|NOMBRE"
        $nominasHabilitadas = collect($nominasAutorizadas)->map(fn($n) => trim(explode('|', $n)[0]))->toArray();

        // 1. OBTENER FICHA Y NÓMINA ACTUAL
        $personal = DB::connection('sigesp')
            ->table('sno_personal as p')
            ->join('sno_personalnomina as pn', 'p.codper', '=', 'pn.codper')
            ->join('sno_cargo as c', function($join) {
                $join->on('pn.codnom', '=', 'c.codnom')->on('pn.codcar', '=', 'c.codcar');
            })
            ->leftJoin('sno_unidadadmin as ua', function($join) {
                $join->on('pn.minorguniadm', '=', 'ua.minorguniadm')
                     ->on('pn.uniuniadm', '=', 'ua.uniuniadm')
                     ->on('pn.depuniadm', '=', 'ua.depuniadm')
                     ->on('pn.prouniadm', '=', 'ua.prouniadm');
            })
            ->select('p.nomper', 'p.apeper', 'p.cedper', 'pn.codnom', 'c.descar as cargo', 'ua.desuniadm as unidad', 'p.fecingper as fecha_ingreso_real')
            ->where('p.codper', $v_codper)
            ->whereIn('pn.codnom', $nominasHabilitadas)
            ->where('pn.staper', '1')
            ->first();

        if (!$personal) return back()->with('error', 'Nómina no autorizada para este trabajador o no está activo.');

        // 2. OBTENER SUELDO ACTUAL (Lógica de Quincena * 2 del Service)
        $sueldoMensual = $arcService->obtenerSueldoActual($v_codper, $personal->codnom);

        if ($sueldoMensual <= 0) {
            return back()->with('error', "No hay historial de pago reciente para calcular el sueldo.");
        }

        // 3. CESTATICKET (Monto de la tabla local)
        $beneficioAlim = (float) DB::table('settings')->where('key', 'monto_cestaticket')->value('value') ?? 0.00;

        // 4. FORMATEO A LETRAS
        $formatter = new \NumberFormatter("es", \NumberFormatter::SPELLOUT);

        // Sueldo
        $enteroSueldo = floor($sueldoMensual);
        $centavosSueldo = str_pad(round(($sueldoMensual - $enteroSueldo) * 100), 2, "0", STR_PAD_LEFT);
        $sueldoTexto = strtoupper($formatter->format($enteroSueldo));

        // Alimentación
        $enteroAlim = floor($beneficioAlim);
        $centavosAlim = str_pad(round(($beneficioAlim - $enteroAlim) * 100), 2, "0", STR_PAD_LEFT);
        $alimTexto = strtoupper($formatter->format($enteroAlim));

        $token = Str::random(32);

        $data = [
            'ls_nombres'               => strtoupper(trim($personal->nomper)),
            'ls_apellidos'             => strtoupper(trim($personal->apeper)),
            'ls_cedula'                => number_format($personal->cedper, 0, '', '.'),
            'ld_fecha_ingreso'         => date('d/m/Y', strtotime($personal->fecha_ingreso_real)),
            'ls_cargo'                 => strtoupper($personal->cargo),
            'ls_unidad_administrativa' => strtoupper($personal->unidad ?? 'OFICINA GENERAL'),
            // Montos en Letras y Números
            'li_mensual_inte_sueldo'   => $sueldoTexto . " BOLÍVARES CON " . $centavosSueldo . "/100 (Bs. " . number_format($sueldoMensual, 2, ',', '.') . ")",
            'ls_monto_alimentacion'    => $alimTexto . " BOLÍVARES CON " . $centavosAlim . "/100 (Bs. " . number_format($beneficioAlim, 2, ',', '.') . ")",
            'ls_dia'                   => date('d'),
            'ls_mes'                   => $this->getMesNombre(date('m')), // Asegúrate que este método exista en el controller del empleado
            'ls_ano'                   => $anioActual,
            'qrCode'                   => base64_encode(QrCode::format('svg')->size(100)->margin(0)->generate(route('constancia.verificar', $token))),
            // CARGA DEL LOGO (Sincronizado con la parte administrativa)
            'logoFona'                 => $this->cargarLogo('logo_fona.png')
        ];

        // 5. REGISTRO DE AUDITORÍA
        DB::table('constancias_generadas')->insert([
            'token'            => $token,
            'cedula'           => $personal->cedper,
            'nombre_completo'  => strtoupper($personal->nomper . ' ' . $personal->apeper),
            'sueldo_integral'  => $sueldoMensual,
            'monto_alimentacion' => $beneficioAlim,
            'cargo'            => strtoupper($personal->cargo),
            'unidad'           => strtoupper($personal->unidad ?? 'OFICINA GENERAL'),
            'fecha_generacion' => now(),
            'created_at'       => now(),
        ]);

        $pdf = Pdf::loadView('empleado.reportes.constancia_pdf', $data)
                  ->setPaper('letter', 'portrait');

        if (ob_get_length()) ob_end_clean();
        return $pdf->stream("Constancia_{$personal->cedper}.pdf");

    } catch (\Exception $e) {
        Log::error("Error en PDF Empleado: " . $e->getMessage());
        return back()->with('error', "Error al generar el PDF: " . $e->getMessage());
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
/**
 * Genera la ruta o el base64 del logo para el PDF
 */
private function cargarLogo($nombreArchivo)
{
    $path = public_path('images/' . $nombreArchivo);
    if (!file_exists($path)) {
        return ''; // O una imagen por defecto
    }
    $type = pathinfo($path, PATHINFO_EXTENSION);
    $data = file_get_contents($path);
    return 'data:image/' . $type . ';base64,' . base64_encode($data);
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
