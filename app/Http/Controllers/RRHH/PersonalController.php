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

class PersonalController extends Controller
{
    public function index(Request $request)
{
    $search = $request->input('search');

    // Iniciamos la consulta al SIGESP
    $query = DB::connection('sigesp')->table('sno_personal')
        ->select('cedper', 'nomper', 'apeper', 'fecingper', 'estper')
        ->orderBy('apeper', 'asc');

    // Si el analista escribe algo, filtramos
    if ($search) {
        $query->where(function($q) use ($search) {
            $q->where('cedper', 'LIKE', "%$search%")
              ->orWhere('nomper', 'LIKE', "%$search%")
              ->orWhere('apeper', 'LIKE', "%$search%");
        });
    }

    // Paginamos de 15 en 15 para mantener la fluidez
    $personal = $query->paginate(15);

    return view('rrhh.personal.index', compact('personal', 'search'));
}



public function generarARC($cedper, $ano)
{
    // 1. LIMPIEZA INICIAL: Detenemos cualquier salida previa
    if (ob_get_contents()) ob_end_clean();

    try {
        // 1. Datos de la Empresa (Igual al anterior)
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

        // 2. BUSQUEDA DEL TRABAJADOR (Cambio clave: buscamos por la cédula recibida)
        // Normalizamos el codper si es necesario
        $personal = DB::connection('sigesp')->table('sno_personal')
            ->select('nomper', 'apeper', 'cedper', 'codper')
            ->where('cedper', $cedper) // Buscamos por la cédula que viene de la URL
            ->where('estper', '1')     // Solo activos
            ->first();

        if (!$personal) {
            return redirect()->back()->with('error', "No se encontró ficha ACTIVA para la cédula: $cedper");
        }

        $v_codper = str_pad(trim($personal->codper), 10, "0", STR_PAD_LEFT);

        // 3. Consulta de Remuneraciones (Usamos el $v_codper encontrado)
        $nominasARC = ['0001', '0002', '0003', '0004', '0005', '0006', '0009', '0010', '0011', '0012', '0013', '0014', '0051', '0052', '0053', '0054', '0055', '0056'];

        $detalles = DB::connection('sigesp')
            ->table('sno_hsalida as hs')
            ->join('sno_hperiodo as hp', function($join) {
                $join->on('hs.codnom', '=', 'hp.codnom')
                     ->on('hs.codperi', '=', 'hp.codperi');
            })
            ->select(
                DB::raw('EXTRACT(MONTH FROM hp.fecdesper) as mes'),
                DB::raw("SUM(CASE
                    WHEN hs.tipsal IN ('A', 'A ')
                    AND hs.valsal < 5000
                    AND hs.codconc IN ('0000000001', '0000000002', '0000000006')
                    THEN ABS(hs.valsal) ELSE 0 END) as asignacion"),
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

        // 4. Token y QR (Se mantiene la validación)
        $token = Str::random(32);
        $qrCode = base64_encode(QrCode::format('svg')->size(80)->margin(0)->generate(route('arc.verificar', $token)));

        // 5. Logos y Preparación de Data (Igual al anterior)
        $pathRepublica = public_path('images/logo_ministerio.png');
        $pathEnte = public_path('images/logo_fona.png');
        $logoRepublica = file_exists($pathRepublica) ? base64_encode(file_get_contents($pathRepublica)) : "";
        $logoEnte = file_exists($pathEnte) ? base64_encode(file_get_contents($pathEnte)) : "";

        $meses = [1 => 'ENERO', 2 => 'FEBRERO', 3 => 'MARZO', 4 => 'ABRIL', 5 => 'MAYO', 6 => 'JUNIO', 7 => 'JULIO', 8 => 'AGOSTO', 9 => 'SEPTIEMBRE', 10 => 'OCTUBRE', 11 => 'NOVIEMBRE', 12 => 'DICIEMBRE'];

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

        // Usamos la misma vista que ya tienes para que el formato sea idéntico
        return Pdf::loadView('empleado.reportes.arc_pdf', $data)
            ->setPaper('letter', 'portrait')
            ->stream("ARC_{$cedper}_{$ano}.pdf");

    } catch (\Exception $e) {
        return back()->with('error', "Error al generar ARC: " . $e->getMessage());
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

    $personal = DB::connection('sigesp')->table('sno_personal')
        ->select('nomper', 'apeper', 'cedper', 'codper')
        ->where(function($query) use ($buscar) {
            if ($buscar) {
                $query->where('cedper', 'like', "%$buscar%")
                      ->orWhere('nomper', 'like', "%$buscar%")
                      ->orWhere('apeper', 'like', "%$buscar%");
            }
        })
        ->paginate(15);

    return view('rrhh.personal.lista_pagos', compact('personal'));
}


public function descargarRecibo(Request $request)
{
    if (ob_get_level()) ob_end_clean();

    try {
        $cedper = $request->cedper;
        $mes = $request->mes;
        $ano = $request->ano;
        $periodo = $request->periodo;

        $v_codperi = str_pad(($mes * 2) - (2 - $periodo), 3, "0", STR_PAD_LEFT);

        // 1. Buscamos al trabajador (SIGESP)
        $infoBasica = DB::connection('sigesp')->table('sno_personalnomina as pn')
            ->join('sno_personal as p', 'pn.codper', '=', 'p.codper')
            ->select('pn.codper', 'pn.codnom', 'p.nomper', 'p.apeper', 'p.cedper')
            ->where('p.cedper', $cedper)
            ->first();

        if (!$infoBasica) return back()->with('error', "Trabajador no encontrado.");

        $v_codnom = $infoBasica->codnom;
        $v_codper = str_pad($infoBasica->codper, 10, "0", STR_PAD_LEFT);

        // 2. Objeto para la vista
        $user = (object)[
            'name' => $infoBasica->nomper,
            'apellido' => $infoBasica->apeper,
            'codper' => $infoBasica->cedper
        ];

        // 3. Consulta de Resumen
        $resumen = DB::connection('sigesp')->table('sno_hresumen as hr')
            ->join('sno_nomina as n', 'hr.codnom', '=', 'n.codnom')
            ->join('sno_hperiodo as hp', function($join) {
                $join->on('hr.codnom', '=', 'hp.codnom')->on('hr.codperi', '=', 'hp.codperi');
            })
            ->join('sno_personalnomina as pn', function($join) {
                $join->on('hr.codnom', '=', 'pn.codnom')->on('hr.codper', '=', 'pn.codper');
            })
            ->select('hr.*', 'n.desnom', 'hp.fecdesper', 'hp.fechasper', 'pn.fecingper', 'pn.codcueban')
            ->where([['hr.codnom', $v_codnom], ['hr.codperi', $v_codperi], ['hr.codper', $v_codper]])
            ->first();

        if (!$resumen) return back()->with('error', "No hay datos en el histórico para este periodo.");

        $resumen->ctabanper = $resumen->codcueban ?? 'N/A';

        // 4. Consulta de Conceptos
        $conceptos = DB::connection('sigesp')->table('sno_hsalida as hs')
            ->leftJoin('sno_concepto as c', function($join) {
                $join->on('hs.codnom', '=', 'c.codnom')->on('hs.codconc', '=', 'c.codconc');
            })
            ->select('hs.codconc as codcon', 'c.nomcon', 'hs.valsal as valcalcur', 'hs.tipsal')
            ->where([['hs.codnom', $v_codnom], ['hs.codperi', $v_codperi], ['hs.codper', $v_codper]])
            ->get();

        // 5. Filtrado Quincenal
        $quincenaFiltro = ($periodo == 1) ? 'P1' : 'P2';
        $asignaciones = $conceptos->filter(fn($c) => trim($c->tipsal) == 'A' && $c->valcalcur > 0);
        $deducciones = $conceptos->filter(fn($c) => (trim($c->tipsal) == $quincenaFiltro || trim($c->tipsal) == 'D') && $c->valcalcur < 0);

        // 6. Generación del PDF con nombre específico
        // Definimos el nombre ANTES para usarlo en el envío y en el stream
        $nombreArchivo = "Recibo_Pago_{$cedper}_Q{$periodo}_" . str_pad($mes, 2, "0", STR_PAD_LEFT) . "_{$ano}.pdf";

       $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('rrhh.personal.recibo_pdf', [
            'resumen' => $resumen,
            'user' => $user,
            'asignaciones' => $asignaciones,
            'deducciones' => $deducciones
        ])->setOption('dpi', 96);

        // ESTA LÍNEA ES MAGIA PARA LA PESTAÑA:
        $pdf->getDomPDF()->add_info('Title', $nombreArchivo);

        $pdfContent = $pdf->output();
        

        // 7. Envío de Correo (SIGH)
        $cedulaLimpia = (int)$cedper;
        $usuario = \App\Models\User::where('cedula', $cedulaLimpia)->first();

        if ($usuario && $usuario->email) {
            try {
                $dataMail = [
                    'nombre' => $infoBasica->nomper . ' ' . $infoBasica->apeper,
                    'tipoDocumento' => "RECIBO DE PAGO - QUINCENA {$periodo} (" . str_pad($mes, 2, "0", STR_PAD_LEFT) . "/{$ano})"
                ];

                \Illuminate\Support\Facades\Mail::send('emails.documento_rrhh', $dataMail, function ($message) use ($usuario, $pdfContent, $nombreArchivo) {
                    $message->to($usuario->email)
                        ->subject('SIGH - Recibo de Pago Generado')
                        ->attachData($pdfContent, $nombreArchivo, ['mime' => 'application/pdf']);
                });

                Log::info("Correo enviado: " . $usuario->email);
            } catch (\Exception $mailError) {
                Log::error("Error SMTP Recibo: " . $mailError->getMessage());
            }
        } else {
            Log::warning("No se envió correo: Usuario {$cedper} no existe o no tiene email.");
        }

        // 8. RETORNO CLAVE: Pasamos $nombreArchivo al stream
        return response($pdfContent)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="'.$nombreArchivo.'"');

    } catch (\Exception $e) {
        return back()->with('error', "Error: " . $e->getMessage());
    }
}


public function descargarConstancia($cedper)
{
    if (ob_get_level()) ob_end_clean();
    $v_codper = str_pad($cedper, 10, "0", STR_PAD_LEFT);

    try {
        // 1. Datos del Personal (SIGESP)
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
            ->select('p.nomper','p.apeper','p.cedper','pn.fecingper','c.descar as cargo','ua.desuniadm as unidad')
            ->where('p.cedper', $cedper)
            ->where('p.estper', '1')
            ->first();

        if (!$personal) { return dd("Trabajador no encontrado."); }

        // 2. Beneficio de Alimentación y Sueldo (Lógica SIGESP)
        $montoSetting = DB::table('settings')->where('key', 'monto_cestaticket')->value('value');
        $beneficioAlim = (!empty($montoSetting)) ? (float)$montoSetting : 13000.00;

        $ultimoAno = DB::connection('sigesp')->table('sno_hsalida')->where('codper', $v_codper)->max('anocur');
        $ultimoPeriodo = DB::connection('sigesp')->table('sno_hsalida')->where('codper', $v_codper)->where('anocur', $ultimoAno)->max('codperi');

        $sueldoMensual = DB::connection('sigesp')->table('sno_hsalida')
            ->where([['codper', $v_codper],['anocur', $ultimoAno],['codperi', $ultimoPeriodo],['valsal', '>', 0]])
            ->whereIn('tipsal', ['A', 'A '])
            ->whereIn('codconc', ['0000000001', '0000000002', '0000000006'])
            ->sum('valsal') * 2;

        // 3. Token y Registro para Validación
        $token = Str::random(32);
        DB::table('constancias_generadas')->insert([
            'token' => $token,
            'cedula' => $personal->cedper,
            'nombre_completo' => strtoupper($personal->nomper . ' ' . $personal->apeper),
            'sueldo_integral' => $sueldoMensual,
            'monto_alimentacion' => $beneficioAlim,
            'cargo' => strtoupper($personal->cargo),
            'unidad' => strtoupper($personal->unidad ?? 'OFICINA DE TECNOLOGIA'),
            'fecha_generacion' => now(),
            'created_at' => now(),
        ]);

        // 4. Preparación de Data para PDF
        \Carbon\Carbon::setLocale('es');
        $data = [
            'ls_nombres' => strtoupper($personal->nomper),
            'ls_apellidos' => strtoupper($personal->apeper),
            'ls_cedula' => number_format($personal->cedper, 0, '', '.'),
            'ld_fecha_ingreso' => \Carbon\Carbon::parse($personal->fecingper)->format('d/m/Y'),
            'ls_cargo' => strtoupper($personal->cargo),
            'ls_unidad_administrativa' => strtoupper($personal->unidad ?? 'OFICINA DE TECNOLOGIA DE LA INFORMACION Y LA COMUNICACION'),
            'li_mensual_inte_sueldo' => $this->numeroALetras($sueldoMensual) . " (Bs. " . number_format($sueldoMensual, 2, ',', '.') . ")",
            'ls_monto_alimentacion' => $this->numeroALetras($beneficioAlim) . " (Bs. " . number_format($beneficioAlim, 2, ',', '.') . ")",
            'ls_dia' => date('d'),
            'ls_mes' => ucfirst(\Carbon\Carbon::now()->translatedFormat('F')),
            'ls_ano' => date('Y'),
            'qrCode' => base64_encode(QrCode::format('svg')->size(100)->margin(0)->generate(route('constancia.verificar', $token)))
        ];

        // 5. Generación del PDF
        $pdf = Pdf::loadView('rrhh.personal.constancia_pdf', $data)->setOption('dpi', 96);
        $pdfContent = $pdf->output();
        $nombreArchivo = "Constancia_Trabajo_{$personal->cedper}.pdf";

        // 6. ENVÍO DE CORREO USANDO LA VISTA ESPECÍFICA
        $usuario = \App\Models\User::where('cedula', $personal->cedper)->first();

        if ($usuario && $usuario->email) {
            try {
                $dataMail = [
                    'nombre' => $personal->nomper . ' ' . $personal->apeper,
                    'tipoDocumento' => 'CONSTANCIA DE TRABAJO'
                ];

                // CAMBIO AQUÍ: Ahora usa 'emails.documento_trabajador'
                \Illuminate\Support\Facades\Mail::send('emails.documento_trabajador', $dataMail, function ($message) use ($usuario, $pdfContent, $nombreArchivo) {
                    $message->to($usuario->email)
                        ->subject('SIGH - Constancia de Trabajo Generada')
                        ->attachData($pdfContent, $nombreArchivo, ['mime' => 'application/pdf']);
                });
            } catch (\Exception $mailError) {
                Log::error("Fallo envío correo Constancia: " . $mailError->getMessage());
            }
        }

        return $pdf->stream($nombreArchivo);

    } catch (\Exception $e) {
        return dd("Error: " . $e->getMessage());
    }
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

    $personal = DB::connection('sigesp')->table('sno_personal as p')
        // El estatus está en la tabla maestra 'p', no en 'pn'
        ->select('p.cedper', 'p.nomper', 'p.apeper')
        ->where('p.estper', '1') // <-- Corregido: p.estper en lugar de pn.estper
        ->when($buscar, function ($query, $buscar) {
            return $query->where(function($q) use ($buscar) {
                $q->where('p.cedper', 'like', "%{$buscar}%")
                  ->orWhere('p.nomper', 'like', "%{$buscar}%")
                  ->orWhere('p.apeper', 'like', "%{$buscar}%");
            });
        })
        ->orderBy('p.nomper', 'asc')
        ->paginate(15);

    return view('rrhh.personal.constancias', compact('personal'));
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


}



