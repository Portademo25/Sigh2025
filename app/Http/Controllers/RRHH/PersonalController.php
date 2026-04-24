<?php

namespace App\Http\Controllers\RRHH;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\Services\ArcService;

class PersonalController extends Controller
{
   public function index(Request $request)
{
    $search = $request->input('search');

    // Usamos GROUP BY para asegurar que cada trabajador aparezca una sola vez
    $query = DB::connection('sigesp')->table('sno_personal as p')
        ->join('sno_personalnomina as n', 'p.codper', '=', 'n.codper')
        ->select(
            'p.cedper',
            'p.nomper',
            'p.apeper',
            'p.fecingper',
            'p.estper'
        )
        ->where(function($q) {
            $q->whereRaw("trim(p.estper) = '1'")
              ->where('n.sueintper', '>', 0)
              ->where('p.fecegrper', '1900-01-01');
        })
        // El secreto está aquí: agrupamos por los campos que seleccionamos
        ->groupBy('p.cedper', 'p.nomper', 'p.apeper', 'p.fecingper', 'p.estper');

    if ($search) {
        $query->where(function($q) use ($search) {
            $q->where('p.cedper', 'LIKE', "%$search%")
              ->orWhere('p.nomper', 'LIKE', "%$search%")
              ->orWhere('p.apeper', 'LIKE', "%$search%");
        });
    }

    // Al usar groupBy, Laravel a veces tiene problemas contando para el paginate
    // Así que usamos este pequeño ajuste para obtener el total correcto
    $personal = $query->orderBy('p.apeper', 'asc')->paginate(15);
    $personal->appends(['search' => $search]);

    return view('rrhh.personal.index', compact('personal', 'search'));
}

public function generarARC($cedper, $ano, ArcService $arcService)
{
    if (ob_get_level()) ob_end_clean();

    try {
        // 1. Busqueda de Personal en SIGESP
        $personal = DB::connection('sigesp')->table('sno_personal')
            ->select('nomper', 'apeper', 'cedper', 'codper', 'estper')
            ->where(DB::raw("TRIM(cedper)"), trim($cedper))
            ->where('estper', '1') // Solo personal activo
            ->first();

        if (!$personal) return redirect()->back()->with('error', "Trabajador no encontrado.");

        $v_codper = str_pad(trim($personal->codper), 10, "0", STR_PAD_LEFT);

        /**
         * 2. Obtener data procesada
         * El Service ya maneja internamente la limpieza de las nóminas configuradas
         * en la tabla 'arc_parametros'.
         */
        $mesesData = $arcService->obtenerDataReporte($ano, $v_codper);

        // 3. Mapeo de Detalles Mensuales
        $detalles = collect(range(1, 12))->map(function ($mes) use ($mesesData) {
            $dataMes = $mesesData->firstWhere('mes', $mes);

            $detalleOriginal = $dataMes ? (array)$dataMes->detalle : [];

            return (object)[
                'mes'               => $mes,
                'asignacion'        => $dataMes ? $dataMes->remuneracion : 0,
                'ret_islr'          => 0,
                'otras_retenciones' => 0,
                'detalle_original'  => $detalleOriginal,
                'total_patronales'  => $dataMes ? $dataMes->total_patronales : 0 // Campo inyectado en el Service
            ];
        });

        // 4. LÓGICA DE LOGO DINÁMICO (Mantenemos tu blindaje de rutas)
        $logoDinamicoPath = DB::table('settings')->where('key', 'logo_path')->value('value');
        $cleanPath = ltrim(str_replace('storage/', '', $logoDinamicoPath), '/');

        $pathPublic  = public_path('storage/' . $cleanPath);
        $pathStorage = storage_path('app/public/' . $cleanPath);

        if (!empty($cleanPath) && file_exists($pathPublic)) {
            $logoInstitucion = $pathPublic;
        } elseif (!empty($cleanPath) && file_exists($pathStorage)) {
            $logoInstitucion = $pathStorage;
        } else {
            $logoInstitucion = public_path('images/logo-fona.png');
        }

        // 5. Datos Empresa y Totales
        $datosEmpresa = DB::connection('sigesp')->table('sigesp_empresa')->first();

        $totalAsignacionAnual = $detalles->sum('asignacion');
        $totalDesgloseLey     = $detalles->sum('total_patronales');

        $data = [
            'agente' => [
                'nombre'    => $datosEmpresa->nomrep ?? 'S/N',
                'cedula'    => number_format($datosEmpresa->cedrep ?? 0, 0, '', '.'),
                'ente'      => $datosEmpresa->nombre ?? 'S/N',
                'rif'       => $datosEmpresa->rifemp ?? 'S/N',
                'direccion' => $datosEmpresa->diremp ?? 'S/N',
                'telefono'  => $datosEmpresa->telemp ?? 'S/N',
                'ciudad'    => $datosEmpresa->ciuemp ?? 'S/N',
                'estado'    => $datosEmpresa->estemp ?? 'S/N',
                'cargo'     => $datosEmpresa->carrep ?? 'DIRECTOR EJECUTIVO'
            ],
            'personal'      => $personal,
            'ano'           => $ano,
            'detalles'      => $detalles,
            'total_desglose_ley' => round($totalDesgloseLey, 2),
            'totales'       => [
                'total_asignacion' => round($totalAsignacionAnual, 2),
                'total_ret_islr'   => 0,
                'total_otras'      => 0,
                'total_general'    => round($totalAsignacionAnual, 2),
            ],
            'meses'         => [1 => 'ENERO', 2 => 'FEBRERO', 3 => 'MARZO', 4 => 'ABRIL', 5 => 'MAYO', 6 => 'JUNIO', 7 => 'JULIO', 8 => 'AGOSTO', 9 => 'SEPTIEMBRE', 10 => 'OCTUBRE', 11 => 'NOVIEMBRE', 12 => 'DICIEMBRE'],
            'logoRepublica' => public_path('images/logo_ministerio.png'),
            'logoFona'      => $logoInstitucion,
            'fecha'         => date('d/m/Y'),
        ];

        // 6. Configuración y Stream del PDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('empleado.reportes.arc_pdf', $data)
            ->setPaper('letter', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => true,
                'chroot'               => [public_path(), storage_path('app/public')],
            ]);

        if (ob_get_length()) ob_end_clean();
        return $pdf->stream("ARC_{$cedper}_{$ano}.pdf");

    } catch (\Exception $e) {
        if (ob_get_length()) ob_end_clean();
        Log::error("Error PDF ARC (Controlador 4): " . $e->getMessage());
        return back()->with('error', "Error al procesar reporte: " . $e->getMessage());
    }
}
// En PersonalController.php

public function gestionarPagos($cedper)
{
    try {
        // Consulta limpia SOLO a la tabla personal (sin buscar el cargo por ahora)
        $personal = DB::connection('sigesp')->table('sno_personal')
            ->select('nomper', 'apeper', 'cedper', 'codper')
            ->where('cedper', $cedper)
            ->first();

        // Si no existe, avisamos
        if (!$personal) {
            dd("Error: El trabajador con cédula {$cedper} no existe en SIGESP.");
        }

        // Le asignamos un cargo por defecto en memoria para que la vista no explote
        $personal->descar = 'Personal del Ente';

        $meses = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];

        // Retornamos la vista de pagos
        return view('rrhh.personal.pagos', compact('personal', 'meses'));

    } catch (\Exception $e) {
        dd("Falla técnica al buscar al trabajador: " . $e->getMessage());
    }
}


public function listaPagos(Request $request)
{
    $buscar = $request->get('buscar');

    // 1. Iniciamos la consulta con JOIN
    $query = DB::connection('sigesp')->table('sno_personal as p')
        ->join('sno_personalnomina as n', 'p.codper', '=', 'n.codper')
        ->select(
            'p.nomper',
            'p.apeper',
            'p.cedper',
            'p.codper'
        )
        ->where(function($q) {
            $q->whereRaw("trim(p.estper) = '1'") // Ficha activa
              ->where('n.sueintper', '>', 0)      // Sueldo real
              ->where('p.fecegrper', '1900-01-01'); // Sin fecha de egreso
        });

    // 2. Filtro de búsqueda
    if ($buscar) {
        $query->where(function($q) use ($buscar) {
            $q->where('p.cedper', 'like', "%$buscar%")
              ->orWhere('p.nomper', 'like', "%$buscar%")
              ->orWhere('p.apeper', 'like', "%$buscar%");
        });
    }

    // 3. Arreglo del Paginate: Agrupamos por la clave primaria para que el conteo sea único
    // Esto elimina los duplicados y ajusta el total de páginas
    $personal = $query->groupBy('p.codper', 'p.cedper', 'p.nomper', 'p.apeper')
                      ->orderBy('p.apeper', 'asc')
                      ->paginate(15);

    // 4. Mantenemos el parámetro de búsqueda en los links
    $personal->appends(['buscar' => $buscar]);

    return view('rrhh.personal.lista_pagos', compact('personal', 'buscar'));
}

public function descargarRecibo(Request $request, ArcService $arcService)
{
    if (ob_get_level()) ob_end_clean();

    try {
        $cedper = $request->cedper;
        $mes = $request->mes;
        $ano = $request->ano;
        $periodo = $request->periodo;

        $v_codperi = str_pad(($mes * 2) - (2 - $periodo), 3, "0", STR_PAD_LEFT);

        // 1. VALIDACIÓN DE SEGURIDAD
        $config = DB::table('arc_parametros')->where('anio', $ano)->first();
        if (!$config || empty($config->nominas)) {
            return redirect()->back()->with('error', "Las nóminas para el año $ano no han sido habilitadas.");
        }

        $nominasHabilitadas = json_decode($config->nominas, true);

        // 2. BÚSQUEDA DEL TRABAJADOR
        $infoBasica = DB::connection('sigesp')->table('sno_personalnomina as pn')
            ->join('sno_personal as p', 'pn.codper', '=', 'p.codper')
            ->select('pn.codper', 'pn.codnom', 'p.nomper', 'p.apeper', 'p.cedper')
            ->where('p.cedper', $cedper)
            ->first();

        if (!$infoBasica) return back()->with('error', "Trabajador no encontrado.");

        $v_codnom = $infoBasica->codnom;
        $v_codper = str_pad($infoBasica->codper, 10, "0", STR_PAD_LEFT);

        // 3. DATOS DEL ENCABEZADO
        $resumen = DB::connection('sigesp')->table('sno_hresumen as hr')
            ->join('sno_nomina as n', 'hr.codnom', '=', 'n.codnom')
            ->join('sno_hperiodo as hp', function($join) {
                $join->on('hr.codnom', '=', 'hp.codnom')->on('hr.codperi', '=', 'hp.codperi');
            })
            ->join('sno_personalnomina as pn', function($join) {
                $join->on('hr.codnom', '=', 'pn.codnom')->on('hr.codper', '=', 'pn.codper');
            })
            ->join('sno_unidadadmin as u', function($join) {
                $join->on('pn.minorguniadm', '=', 'u.minorguniadm')
                     ->on('pn.uniuniadm', '=', 'u.uniuniadm')
                     ->on('pn.depuniadm', '=', 'u.depuniadm')
                     ->on('pn.prouniadm', '=', 'u.prouniadm');
            })
            ->select('hr.*', 'n.desnom', 'hp.fecdesper', 'hp.fechasper', 'pn.fecingper', 'pn.codcueban', 'u.desuniadm')
            ->where([['hr.codnom', $v_codnom], ['hr.codperi', $v_codperi], ['hr.codper', $v_codper]])
            ->first();

        if (!$resumen) return back()->with('error', "No hay datos para este periodo.");
        $resumen->ctabanper = $resumen->codcueban ?? 'N/A';

        // 4. CONSULTA DE CONCEPTOS (CONSOLIDACIÓN DE MONTOS)
        $movimientos = DB::connection('sigesp')->table('sno_hsalida as hs')
            ->leftJoin('sno_concepto as c', function($join) {
                $join->on('hs.codnom', '=', 'c.codnom')->on('hs.codconc', '=', 'c.codconc');
            })
            ->select(
                'hs.codconc as codcon',
                'c.nomcon',
                DB::raw('MAX(hs.tipsal) as tipsal'),
                DB::raw('SUM(hs.valsal) as valcalcur')
            )
            ->where([['hs.codnom', $v_codnom], ['hs.codperi', $v_codperi], ['hs.codper', $v_codper]])
            ->whereIn('hs.tipsal', ['A', 'D', 'P1', 'P2'])
            ->groupBy('hs.codconc', 'c.nomcon')
            ->get();

        // 5. CLASIFICACIÓN FINAL
        $asigConfiguradas = $arcService->getAsignacionesConfiguradas();

        $asignaciones = $movimientos->filter(function($m) use ($asigConfiguradas) {
            $cod = ltrim(trim($m->codcon), '0');
            return (trim($m->tipsal) == 'A' || in_array($cod, $asigConfiguradas)) && $m->valcalcur > 0;
        });

        $deducciones = $movimientos->filter(function($m) {
            return $m->valcalcur < 0;
        });

        // --- LÓGICA DE LOGO DINÁMICO ---
        $logoDinamicoPath = DB::table('settings')->where('key', 'logo_path')->value('value');

        if ($logoDinamicoPath && file_exists(public_path('storage/' . $logoDinamicoPath))) {
            $logoInstitucion = public_path('storage/' . $logoDinamicoPath);
        } else {
            // Usamos logo-fona.png como fallback estándar
            $logoInstitucion = public_path('images/logo-fona.png');
        }
        // -------------------------------

        // 6. GENERACIÓN DEL PDF
        $nombreArchivo = "Recibo_Pago_{$cedper}_Q{$periodo}_" . str_pad($mes, 2, "0", STR_PAD_LEFT) . "_{$ano}.pdf";

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('rrhh.personal.recibo_pdf', [
            'resumen' => $resumen,
            'user' => (object)['name' => $infoBasica->nomper, 'apellido' => $infoBasica->apeper, 'codper' => $infoBasica->cedper],
            'asignaciones' => $asignaciones,
            'deducciones' => $deducciones,
            'logoFona' => $logoInstitucion, // Aplicamos el logo dinámico
            'logoRepublica' => public_path('images/logo_ministerio.png'),
            'totalAsignaciones' => $asignaciones->sum('valcalcur'),
            'totalDeducciones' => $deducciones->sum('valcalcur')
        ])->setOption('dpi', 96);

        $pdfContent = $pdf->output();

        // 7. ENVÍO DE CORREO (El PDF adjunto ya llevará el logo correcto)
        $this->enviarCorreoRecibo($cedper, $infoBasica, $periodo, $mes, $ano, $pdfContent, $nombreArchivo);

        return response($pdfContent)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="'.$nombreArchivo.'"');

    } catch (\Exception $e) {
        Log::error("Falla en Recibo: " . $e->getMessage());
        return back()->with('error', "Error al generar el documento.");
    }
}
// Método auxiliar para el logo en Base64


// Método auxiliar para limpiar el controlador (Envío de correo)
private function enviarCorreoRecibo($cedper, $info, $q, $m, $a, $content, $filename)
{
    $usuario = \App\Models\User::where('cedula', (int)$cedper)->first();
    if ($usuario && $usuario->email) {
        try {
            $dataMail = [
                'nombre' => $info->nomper . ' ' . $info->apeper,
                'tipoDocumento' => "RECIBO DE PAGO - QUINCENA {$q} (" . str_pad($m, 2, "0", STR_PAD_LEFT) . "/{$a})"
            ];
            \Illuminate\Support\Facades\Mail::send('emails.documento_rrhh', $dataMail, function ($message) use ($usuario, $content, $filename) {
                $message->to($usuario->email)
                    ->subject('SIGH - Recibo de Pago Generado')
                    ->attachData($content, $filename, ['mime' => 'application/pdf']);
            });
        } catch (\Exception $e) {
            Log::error("Error enviando correo: " . $e->getMessage());
        }
    }
}
public function descargarConstancia(Request $request, ArcService $arcService)
{
    if (ob_get_level()) ob_end_clean();

    try {
        $cedper = $request->cedper;
        $anoActual = date('Y');

        $config = DB::table('arc_parametros')->where('anio', $anoActual)->first();
        $beneficioAlim = (float) DB::table('settings')->where('key', 'monto_cestaticket')->value('value') ?? 0.00;

        if (!$config) return redirect()->back()->with('error', "Configuración no encontrada.");

        $nominasRaw = json_decode($config->nominas, true) ?? [];
        $nominasHabilitadas = collect($nominasRaw)->map(fn($n) => trim(explode('|', $n)[0]))->toArray();

        $personal = DB::connection('sigesp')->table('sno_personalnomina as pn')
            ->join('sno_personal as p', 'pn.codper', '=', 'p.codper')
            ->join('sno_unidadadmin as u', function($join) {
                $join->on('pn.minorguniadm', '=', 'u.minorguniadm')->on('pn.uniuniadm', '=', 'u.uniuniadm')
                     ->on('pn.depuniadm', '=', 'u.depuniadm')->on('pn.prouniadm', '=', 'u.prouniadm');
            })
            ->join('sno_cargo as c', function($join) {
                $join->on('pn.codnom', '=', 'c.codnom')->on('pn.codcar', '=', 'c.codcar');
            })
            ->select(
                'p.nomper', 'p.apeper', 'p.cedper', 'p.fecingper as fecha_ingreso_real',
                'pn.codnom', 'pn.codper as codper_sigesp',
                'c.descar as cargo', 'u.desuniadm as unidad'
            )
            ->where('p.cedper', $cedper)
            ->where('pn.staper', '1')
            ->first();

        if (!$personal) return back()->with('error', "Trabajador no activo.");

        if (!in_array(trim($personal->codnom), $nominasHabilitadas)) {
            return redirect()->back()->with('error', "Nómina {$personal->codnom} no autorizada.");
        }

        $v_codper = str_pad(trim($personal->codper_sigesp), 10, "0", STR_PAD_LEFT);

        // El Service ahora aplica (Quincena * 2) = 468,38
        $sueldoIntegral = $arcService->obtenerSueldoActual($v_codper, $personal->codnom);

        if ($sueldoIntegral <= 0) return back()->with('error', "No se pudo calcular el sueldo mensual.");

        // FORMATEADOR DE NÚMEROS A LETRAS
        $formatter = new \NumberFormatter("es", \NumberFormatter::SPELLOUT);

        // Formateo de Sueldo
        $sueldoEntero = floor($sueldoIntegral);
        $sueldoCents = str_pad(round(($sueldoIntegral - $sueldoEntero) * 100), 2, "0", STR_PAD_LEFT);
        $sueldoTexto = strtoupper($formatter->format($sueldoEntero));

        // Formateo de Alimentación (Cestaticket)
        $alimEntero = floor($beneficioAlim);
        $alimCents = str_pad(round(($beneficioAlim - $alimEntero) * 100), 2, "0", STR_PAD_LEFT);
        $alimTexto = strtoupper($formatter->format($alimEntero));

        $data = [
            'ls_nombres'               => strtoupper(trim($personal->nomper)),
            'ls_apellidos'             => strtoupper(trim($personal->apeper)),
            'ls_cedula'                => number_format($personal->cedper, 0, '', '.'),
            'ls_cargo'                 => strtoupper($personal->cargo),
            'ls_unidad_administrativa' => strtoupper($personal->unidad),
            'ld_fecha_ingreso'         => date('d/m/Y', strtotime($personal->fecha_ingreso_real)),
            // Sueldo en Letras y Números
            'li_mensual_inte_sueldo'   => $sueldoTexto . " BOLÍVARES CON " . $sueldoCents . "/100 (Bs. " . number_format($sueldoIntegral, 2, ',', '.') . ")",
            // Alimentación en Letras y Números
            'ls_monto_alimentacion'    => $alimTexto . " BOLÍVARES CON " . $alimCents . "/100 (Bs. " . number_format($beneficioAlim, 2, ',', '.') . ")",
            'ls_dia'                   => date('d'),
            'ls_mes'                   => $this->getMesLetras(date('m')),
            'ls_ano'                   => $anoActual,
            'qrCode'                   => base64_encode(QrCode::format('svg')->size(90)->margin(0)->generate(route('constancia.verificar', Str::random(32)))),
            'logoFona'                 => $this->cargarLogo('logo_fona.png')
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('rrhh.personal.constancia_pdf', $data)
            ->setPaper('letter', 'portrait');

        return $pdf->download("Constancia_{$cedper}.pdf");

    } catch (\Exception $e) {
        return back()->with('error', "Error: " . $e->getMessage());
    }
}
private function getMesLetras($mes) {
    $meses = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
    return $meses[(int)$mes - 1];
}

private function numeroALetras($numero) {
    $formatter = new \NumberFormatter("es", \NumberFormatter::SPELLOUT);
    $entero = floor($numero);
    $decimales = round(($numero - $entero) * 100);

    $texto = strtoupper($formatter->format($entero));
    $centimos = str_pad($decimales, 2, '0', STR_PAD_LEFT);

    // Ajuste para que "uno" pase a "un" en moneda (Ej: UN BOLÍVAR)
    $texto = str_replace("UNO", "UN", $texto);

    return $texto . " BOLÍVARES CON " . $centimos . "/100";
}

public function listaConstancias(Request $request)
{
    $buscar = $request->get('buscar');

    // 1. Iniciamos con JOIN a nómina para validar actividad real
    $query = DB::connection('sigesp')->table('sno_personal as p')
        ->join('sno_personalnomina as n', 'p.codper', '=', 'n.codper')
        ->select(
            'p.cedper',
            'p.nomper',
            'p.apeper'
        )
        ->where(function($q) {
            $q->whereRaw("trim(p.estper) = '1'") // Ficha activa
              ->where('n.sueintper', '>', 0)      // Sueldo real
              ->where('p.fecegrper', '1900-01-01'); // Sin fecha de egreso
        });

    // 2. Filtro de búsqueda (si existe)
    if ($buscar) {
        $query->where(function($q) use ($buscar) {
            $q->where('p.cedper', 'like', "%{$buscar}%")
              ->orWhere('p.nomper', 'like', "%{$buscar}%")
              ->orWhere('p.apeper', 'like', "%{$buscar}%");
        });
    }

    // 3. Arreglo de Paginate y Duplicados
    // Agrupamos para que el contador de Laravel sea sobre personas únicas
    $personal = $query->groupBy('p.cedper', 'p.nomper', 'p.apeper')
                      ->orderBy('p.nomper', 'asc')
                      ->paginate(15);

    // 4. Persistencia de búsqueda en los links
    $personal->appends(['buscar' => $buscar]);

    return view('rrhh.personal.constancias', compact('personal', 'buscar'));
}


public function indexValidacion(Request $request)
{
    $buscar = $request->get('buscar');

    $constancias = DB::table('constancias_generadas')
        ->when($buscar, function ($query, $buscar) {
            return $query->where('cedula', 'like', "%{$buscar}%")
                         ->orWhere('nombre_completo', 'like', "%{$buscar}%")
                         ->orWhere('token', 'like', "%{$buscar}%");
        })
        ->orderBy('fecha_generacion', 'desc')
        ->paginate(20);

    return view('rrhh.personal.validar_constancias', compact('constancias'));
}

public function gestion_arc(Request $request)
{
    $anoSeleccionado = $request->input('ano', date('Y'));
    $years = range(date('Y'), date('Y') - 5);

    // 1. Obtener Nóminas de SIGESP (Se mantiene igual)
    $nominas = DB::connection('sigesp')
        ->table('sno_nomina')
        ->select('codnom', 'desnom')
        ->orderBy('codnom')
        ->get();

    // 2. OBTENER CONCEPTOS AMPLIADOS (Asignaciones, Deducciones y Patronales)
    // Cambiamos 'tipsal' por 'sigcon' (que es el estándar) y quitamos la restricción de solo 'A'
    $conceptosRaw = DB::connection('sigesp')
        ->table('sno_concepto')
        ->select(
            'codconc',
            'nomcon',
            DB::raw("TRIM(sigcon) as sigcon") // Traemos el tipo limpio para la vista
        )
        // Buscamos los 3 tipos principales de SIGESP
        ->whereIn(DB::raw("TRIM(sigcon)"), ['A', 'D', 'P'])
        ->orderBy(DB::raw("TRIM(sigcon)"))
        ->orderBy('codconc')
        ->get();

    // IMPORTANTE: Eliminamos duplicados de códigos (como el P.I.E.) antes de enviar a la vista
    $conceptos = $conceptosRaw->unique('codconc')->values();

    // 3. Obtener la parametrización guardada (Se mantiene igual)
    $config = DB::table('arc_parametros')->where('anio', $anoSeleccionado)->first();

    $nominasTildadas = $config ? json_decode($config->nominas, true) : [];
    $conceptosTildados = $config ? json_decode($config->conceptos, true) : [];

    return view('rrhh.personal.gestion_arc', compact(
        'anoSeleccionado', 'years', 'nominas', 'conceptos',
        'nominasTildadas', 'conceptosTildados'
    ));
}


public function vistaGestionArc(Request $request)
{
    $anoSeleccionado = $request->input('ano', date('Y'));
    $years = range(date('Y'), date('Y') - 5);

    try {
        // 1. Obtener nóminas únicas con periodos en el año
        $nominas = DB::connection('sigesp')->table('sno_nomina as n')
            ->select('n.codnom', 'n.desnom')
            ->whereIn('n.codnom', function($query) use ($anoSeleccionado) {
                $query->select('codnom')->from('sno_periodo')->whereYear('fecdesper', $anoSeleccionado)
                ->union(
                    $query->newQuery()->select('codnom')->from('sno_hperiodo')->whereYear('fecdesper', $anoSeleccionado)
                );
            })
            ->orderBy('n.codnom', 'ASC')
            ->get();

        $codigosNominas = $nominas->pluck('codnom')->toArray();

        // 2. Obtener conceptos con movimiento y aplicar ORDENAMIENTO LOGICO
        $conceptosRaw = DB::connection('sigesp')->table('sno_concepto as c')
            ->select(
                'c.codconc',
                DB::raw("MAX(c.nomcon) as nomcon"),
                DB::raw("TRIM(c.sigcon) as sigcon")
            )
            ->whereIn(DB::raw("TRIM(c.sigcon)"), ['A', 'D', 'P'])
            ->whereExists(function ($query) use ($codigosNominas, $anoSeleccionado) {
                $query->select(DB::raw(1))
                    ->from('sno_hsalida as hs')
                    ->join('sno_hperiodo as hp', function($join) {
                        $join->on('hs.codnom', '=', 'hp.codnom')
                             ->on('hs.codperi', '=', 'hp.codperi');
                    })
                    ->whereColumn('hs.codconc', 'c.codconc')
                    ->whereIn('hs.codnom', $codigosNominas)
                    ->whereYear('hp.fecdesper', $anoSeleccionado);
            })
            ->groupBy('c.codconc', 'c.sigcon')
            ->get();

        // Aplicamos la "Llave Única" y el orden: Asignaciones -> Deducciones -> Patronales
        $conceptos = $conceptosRaw->unique(function ($item) {
                return trim($item->codconc) . '|' . trim($item->sigcon);
            })
            ->sort(function($a, $b) {
                $prioridad = ['A' => 1, 'D' => 2, 'P' => 3];
                $pA = $prioridad[trim($a->sigcon)] ?? 9;
                $pB = $prioridad[trim($b->sigcon)] ?? 9;

                if ($pA === $pB) {
                    return strcmp(trim($a->codconc), trim($b->codconc));
                }
                return $pA <=> $pB;
            })
            ->values();

        // 3. Cargar configuración guardada con LIMPIEZA PROFUNDA
        $config = DB::table('arc_parametros')->where('anio', $anoSeleccionado)->first();

        // Aseguramos que los arrays existan y no tengan espacios extras
        $nominasTildadas = $config ? json_decode($config->nominas ?? '[]', true) : [];
        $conceptosTildados = $config ? json_decode($config->conceptos ?? '[]', true) : [];

        return view('rrhh.personal.gestion_arc', [
            'anoSeleccionado'   => $anoSeleccionado,
            'years'             => $years,
            'nominas'           => $nominas,
            'conceptos'         => $conceptos,
            'nominasTildadas'   => array_map('trim', (array)$nominasTildadas),
            'conceptosTildados' => array_map('trim', (array)$conceptosTildados),
        ]);

    } catch (\Exception $e) {
        // Mejor un log o un mensaje amigable que un dd en producción
        return back()->with('error', "Error en SIGESP: " . $e->getMessage());
    }
}

public function listar_nomina_arc(Request $request)
{
    $anio = $request->input('anio');
    if (!$anio) {
        return back()->with('error', 'El año fiscal no es válido.');
    }

    // 1. Recibimos los datos (vienen como "0000000500|P")
    $nominasSeleccionadas = array_values(array_unique(array_filter($request->input('nominas', []))));
    $conceptosRaw = $request->input('conceptos', []); // Aquí vienen las llaves completas

    $mapaContable = [
        'asignaciones' => [],
        'deducciones'  => [],
        'patronales'   => []
    ];

    // 2. Clasificación Inteligente usando la llave completa
    foreach ($conceptosRaw as $item) {
        if (str_contains($item, '|')) {
            list($codigo, $tipo) = explode('|', $item);

            // Clasificamos según el tipo (A, D, P)
            if ($tipo === 'A') $mapaContable['asignaciones'][] = $item; // Guardamos la llave completa
            if ($tipo === 'D') $mapaContable['deducciones'][] = $item;
            if ($tipo === 'P') $mapaContable['patronales'][] = $item;
        }
    }

    try {
        DB::table('arc_parametros')->updateOrInsert(
            ['anio' => (int)$anio],
            [
                'nominas'       => json_encode($nominasSeleccionadas),
                // IMPORTANTE: Guardamos las llaves completas (codigo|tipo)
                // para que al recargar la vista los ganchitos aparezcan bien.
                'conceptos'     => json_encode($conceptosRaw),
                'clasificacion' => json_encode([
                    'configurado_en' => now()->format('d/m/Y H:i'),
                    'total_nominas'  => count($nominasSeleccionadas),
                    'total_conceptos'=> count($conceptosRaw),
                    'mapa'           => $mapaContable
                ]),
                'updated_at'    => now()
            ]
        );

        return redirect()->route('rrhh.personal.lista_trabajadores_arc', ['anio' => $anio])
            ->with('success', "Configuración $anio guardada con éxito.");

    } catch (\Exception $e) {
        Log::error("Error guardando ARC $anio: " . $e->getMessage());
        return back()->with('error', 'Error interno: ' . $e->getMessage());
    }
}
public function generarPdfArc($cedula, $anio, ArcService $arcService)
{
    if (ob_get_level()) ob_end_clean();

    try {
        // 1. Datos del Trabajador en SIGESP
        // Usamos TRIM para evitar fallos por espacios en blanco en la cédula
        $personal = DB::connection('sigesp')->table('sno_personal')
            ->where(DB::raw("TRIM(cedper)"), trim($cedula))
            ->first();

        if (!$personal) return back()->with('error', "Trabajador no encontrado.");

        // Formateo de codper para compatibilidad con el histórico de SIGESP
        $v_codper = str_pad(trim($personal->codper), 10, "0", STR_PAD_LEFT);

        /**
         * 2. Uso del Service para obtener la data
         * El Service ya procesa 'arc_parametros' y limpia las nóminas "00000000500 | A"
         */
        $mesesData = $arcService->obtenerDataReporte($anio, $v_codper);

        // 3. Mapeo de Detalles Mensuales
        $detalles = collect(range(1, 12))->map(function ($mes) use ($mesesData) {
            $dataMes = $mesesData->firstWhere('mes', $mes);
            $detalleOriginal = $dataMes ? (array)$dataMes->detalle : [];

            return (object)[
                'mes'               => $mes,
                'asignacion'        => $dataMes ? $dataMes->remuneracion : 0,
                'ret_islr'          => 0,
                'otras_retenciones' => 0,
                'detalle_original'  => $detalleOriginal,
                'total_patronales'  => $dataMes ? $dataMes->total_patronales : 0 // Campo clave para el total final
            ];
        });

        // 4. Totales y Datos de Empresa
        $datosEmpresa = DB::connection('sigesp')->table('sigesp_empresa')->first();

        // --- LÓGICA DE LOGO DINÁMICO (Mantenemos tu blindaje) ---
        $logoDinamicoPath = DB::table('settings')->where('key', 'logo_path')->value('value');
        $cleanPath = ltrim(str_replace('storage/', '', $logoDinamicoPath), '/');

        $pathPublic  = public_path('storage/' . $cleanPath);
        $pathStorage = storage_path('app/public/' . $cleanPath);

        if (!empty($cleanPath) && file_exists($pathPublic)) {
            $logoInstitucion = $pathPublic;
        } elseif (!empty($cleanPath) && file_exists($pathStorage)) {
            $logoInstitucion = $pathStorage;
        } else {
            $logoInstitucion = public_path('images/logo-fona.png');
        }

        $pathRepublica = public_path('images/logo_ministerio.png');

        // 5. Cálculos de Totales Finales
        $totalAsignacionAnual = $detalles->sum('asignacion');
        $totalDesgloseLey     = $detalles->sum('total_patronales');

        // 6. Construcción del Data para la Vista
        $data = [
            'personal'           => $personal,
            'ano'                => $anio,
            'detalles'           => $detalles,
            'total_desglose_ley' => round($totalDesgloseLey, 2),
            'totales'  => [
                'total_asignacion' => round($totalAsignacionAnual, 2),
                'total_ret_islr'   => 0,
                'total_otras'      => 0,
                'total_general'    => round($totalAsignacionAnual, 2),
            ],
            'agente' => [
                'nombre'    => $datosEmpresa->nomrep ?? 'S/N',
                'cedula'    => number_format($datosEmpresa->cedrep ?? 0, 0, '', '.'),
                'ente'      => $datosEmpresa->nombre ?? 'S/N',
                'rif'       => $datosEmpresa->rifemp ?? 'S/N',
                'direccion' => !empty($datosEmpresa->diremp) ? $datosEmpresa->diremp : ($datosEmpresa->direccion ?? 'S/N'),
                'telefono'  => $datosEmpresa->telemp ?? 'S/N',
                'ciudad'    => $datosEmpresa->ciuemp ?? 'CARACAS',
                'estado'    => $datosEmpresa->estemp ?? 'DISTRITO CAPITAL',
                'cargo'     => $datosEmpresa->carrep ?? 'DIRECTOR EJECUTIVO',
            ],
            'fecha'         => date('d/m/Y'),
            'logoRepublica' => $pathRepublica,
            'logoFona'      => $logoInstitucion,
            'meses'         => [
                1=>'ENERO', 2=>'FEBRERO', 3=>'MARZO', 4=>'ABRIL',
                5=>'MAYO', 6=>'JUNIO', 7=>'JULIO', 8=>'AGOSTO',
                9=>'SEPTIEMBRE', 10=>'OCTUBRE', 11=>'NOVIEMBRE', 12=>'DICIEMBRE'
            ],
        ];

        // 7. Generación y Stream del PDF
        return \Barryvdh\DomPDF\Facade\Pdf::loadView('empleado.reportes.arc_pdf', $data)
            ->setPaper('letter', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => true,
                'chroot'               => [public_path(), storage_path('app/public')],
            ])
            ->stream("ARC_{$cedula}_{$anio}.pdf");

    } catch (\Exception $e) {
        if (ob_get_length()) ob_end_clean();
        Log::error("Error en ARC PDF Empleado: " . $e->getMessage());
        return "Error crítico al procesar el reporte: " . $e->getMessage();
    }
}
public function indexTrabajadoresArc(Request $request)
{
    $anio = $request->input('anio', date('Y'));
    $buscar = $request->input('buscar'); // Cambié 'search' por 'buscar' para coincidir con tu vista anterior

    // 1. Obtenemos las nóminas que guardaste en la configuración ARC
    $config = DB::table('arc_parametros')->where('anio', $anio)->first();

    if (!$config || empty($config->nominas)) {
        return redirect()->route('rrhh.personal.gestion_arc', ['ano' => $anio])
            ->with('info', 'Debe configurar las nóminas para el año ' . $anio . ' antes de ver el personal.');
    }

    $nominasPermitidas = json_decode($config->nominas, true);

    // 2. Construimos la query siguiendo tu lógica de "Personal Activo"
    $query = DB::connection('sigesp')->table('sno_personal as p')
        ->join('sno_personalnomina as n', 'p.codper', '=', 'n.codper')
        ->select(
            'p.cedper',
            'p.nomper',
            'p.apeper'
        )
        ->where(function($q) use ($nominasPermitidas) {
            $q->whereRaw("trim(p.estper) = '1'") // Estatus Activo
              ->where('n.sueintper', '>', 0)      // Con sueldo mayor a 0
              ->where('p.fecegrper', '1900-01-01') // Sin fecha de egreso real
              ->whereIn('n.codnom', $nominasPermitidas); // SOLO las nóminas que elegiste para ARC
        })
        // Agrupamos para evitar duplicados si el trabajador está en varias nóminas de las elegidas
        ->groupBy('p.cedper', 'p.nomper', 'p.apeper');

    // 3. Aplicamos el buscador si existe
    if ($buscar) {
        $query->where(function($q) use ($buscar) {
            $q->where('p.cedper', 'LIKE', "%$buscar%")
              ->orWhere('p.nomper', 'LIKE', "%$buscar%")
              ->orWhere('p.apeper', 'LIKE', "%$buscar%");
        });
    }

    // 4. Paginación (50 para que sea más ágil la descarga masiva)
    $trabajadores = $query->orderBy('p.apeper', 'asc')->paginate(50);

    // Mantener los parámetros en los links de paginación
    $trabajadores->appends(['anio' => $anio, 'buscar' => $buscar]);

    return view('rrhh.personal.lista_trabajadores_arc', compact('trabajadores', 'anio'));
}

private function getMontoConcepto($codper, $mes, $anio, $conceptos)
{
    // Buscamos tanto en histórico como en actual
    $tablas = [
        ['salida' => 'sno_hsalida', 'periodo' => 'sno_hperiodo'],
        ['salida' => 'sno_salida', 'periodo' => 'sno_periodo']
    ];

    foreach ($tablas as $t) {
        $monto = DB::connection('sigesp')->table($t['salida'] . " as s")
            ->join($t['periodo'] . " as p", function($join) {
                $join->on('s.codnom', '=', 'p.codnom')->on('s.codperi', '=', 'p.codperi');
            })
            ->where('s.codper', $codper)
            ->whereIn('s.codconc', $conceptos) // Aquí pasamos los códigos de SIGESP
            ->whereYear('p.fecdesper', $anio)
            ->whereRaw("EXTRACT(MONTH FROM p.fecdesper) = ?", [$mes])
            ->sum('s.valsal');

        if ($monto > 0) return $monto;
    }

    return 0;
}
/**
 * Convierte una imagen a Base64 para ser embebida en el PDF
 */
private function cargarLogo($filename)
{
    // Rutas posibles en tu servidor (ajustadas a tu estructura)
    $paths = [
        public_path('images/' . $filename),
        base_path('public/images/' . $filename),
        '/var/www/html/Laravel/Sigh2025/public/images/' . $filename,
    ];

    foreach ($paths as $path) {
        if (file_exists($path)) {
            $data = @file_get_contents($path);
            if ($data !== false) {
                $type = pathinfo($path, PATHINFO_EXTENSION);
                return 'data:image/' . $type . ';base64,' . base64_encode($data);
            }
        }
    }

    return null; // Si no encuentra el logo, retorna null para no romper el PDF
}


}





