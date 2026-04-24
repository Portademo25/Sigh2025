<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // IMPORTANTE: Para las consultas a SIGESP
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use App\Services\ArcService; // Asegúrate de tener este Service creado para la lógica de clasificación de conceptos

class EmpleadoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // Asegúrate de que tu middleware de rol permita el acceso
        $this->middleware('role:empleado');
    }

    /**
     * Muestra el panel principal con datos de nómina SIGESP
     */
    public function dashboard()
    {
        $user = Auth::user();

        // Buscamos datos del trabajador en SIGESP usando el codper guardado en el usuario
        // Usamos un LEFT JOIN por si algún dato histórico falta, no rompa la vista
        $datosNomina = DB::table('sno_personal as p')
            ->leftJoin('sno_hpersonal as hp', 'p.codper', '=', 'hp.codper')
            ->select(
                'p.nomper',
                'p.apeper',
                'hp.descar',    // Nombre del Cargo
                'hp.desuniadm', // Unidad Administrativa
                'p.fecingper'   // Fecha de Ingreso
            )
            ->where('p.codper', $user->codper)
            ->orderBy('hp.fecdoc', 'desc') // Traer el historial más reciente
            ->first();

        return view('empleado.dashboard', compact('user', 'datosNomina'));
    }

    /**
     * Vista de perfil del usuario
     */
    public function perfil()
    {
        $user = Auth::user();
        return view('empleado.perfil', compact('user'));
    }

    /**
     * Lógica para actualizar datos básicos y contraseña
     */
    public function actualizarPerfil(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|min:8|confirmed',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('new_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'La contraseña actual no es correcta']);
            }
            $user->password = Hash::make($request->new_password);
        }

        $user->save();

        return back()->with('success', 'Perfil actualizado exitosamente');
    }

    /**
     * Sección de tareas o actividades (Opcional)
     */
public function misRecibos()
{
    // 1. Obtenemos el usuario y formateamos su código de personal
    $user = Auth::user();
    $codper = str_pad($user->codper, 10, "0", STR_PAD_LEFT);

    // 2. Obtener las nóminas habilitadas desde la configuración dinámica
    // Usamos el año actual para filtrar la configuración activa
    $anioActual = date('Y');
    $config = DB::table('arc_parametros')->where('anio', $anioActual)->first();

    // Si existe configuración, usamos esos códigos; si no, inicializamos array vacío
    $nominasHabilitadas = $config ? json_decode($config->nominas) : [];

    // 3. Consulta a SIGESP
    $query = DB::connection('sigesp')
        ->table('sno_hperiodo as hp')
        ->join('sno_hresumen as hr', function($join) {
            $join->on('hp.codnom', '=', 'hr.codnom')
                 ->on('hp.codperi', '=', 'hr.codperi');
        })
        ->select(
            'hp.codnom',
            'hp.codperi',
            'hp.fecdesper',
            'hp.fechasper',
            'hr.monnetres'
        )
        ->where('hr.codper', '=', $codper)
        ->where('hr.monnetres', '>', 0);

    // 4. Aplicamos el filtro de nóminas solo si hay configuración guardada
    // Esto permite que el sistema sea flexible si aún no se han cargado parámetros
    if (!empty($nominasHabilitadas)) {
        $query->whereIn('hp.codnom', $nominasHabilitadas);
    }

    $recibos = $query->orderBy('hp.fecdesper', 'desc')
        ->paginate(12);

    return view('empleado.reportes.recibos', compact('recibos'));
}
public function menuReportes()
{
    return view('empleado.reportes.menu');
}


public function descargarPDF($codnom, $codperi, ArcService $arcService)
{
    $user = Auth::user();
    $v_codnom  = str_pad($codnom, 4, "0", STR_PAD_LEFT);
    $v_codperi = str_pad($codperi, 3, "0", STR_PAD_LEFT);
    $v_codper  = str_pad($user->codper, 10, "0", STR_PAD_LEFT);
    $anioActual = date('Y');

    if (ob_get_level()) ob_end_clean();

    try {
        // 1. Datos del encabezado (Inyectamos Join de Unidad Administrativa)
        $resumen = DB::connection('sigesp')
            ->table('sno_hresumen as hr')
            ->join('sno_nomina as n', 'hr.codnom', '=', 'n.codnom')
            ->join('sno_hperiodo as hp', function($join) {
                $join->on('hr.codnom', '=', 'hp.codnom')->on('hr.codperi', '=', 'hp.codperi');
            })
            ->join('sno_personalnomina as pn', function($join) {
                $join->on('hr.codnom', '=', 'pn.codnom')->on('hr.codper', '=', 'pn.codper');
            })
            ->join('sno_personal as p', 'hr.codper', '=', 'p.codper')
            // Join necesario para evitar error de propiedad desuniadm
            ->join('sno_unidadadmin as u', function($join) {
                $join->on('pn.minorguniadm', '=', 'u.minorguniadm')
                     ->on('pn.uniuniadm', '=', 'u.uniuniadm')
                     ->on('pn.depuniadm', '=', 'u.depuniadm')
                     ->on('pn.prouniadm', '=', 'u.prouniadm');
            })
            ->select(
                'hr.*', 
                'hr.monnetres as neto_cobrar', 
                'n.desnom', 
                'hp.fecdesper', 
                'hp.fechasper',
                'pn.fecingper', 
                'pn.codcueban', 
                'p.nomper', 
                'p.apeper', 
                'p.cedper',
                'u.desuniadm' // <--- Campo ahora disponible
            )
            ->where([['hr.codnom', $v_codnom], ['hr.codperi', $v_codperi], ['hr.codper', $v_codper]])
            ->first();

        if (!$resumen) return back()->with('error', "No se encontró el historial de pago.");

        $resumen->ctabanper = $resumen->codcueban ?? 'N/A';

        // 2. Obtener movimientos (Sumatoria y agrupación)
        $movimientos = DB::connection('sigesp')
            ->table('sno_hsalida as hs')
            ->leftJoin('sno_concepto as c', function($join) {
                $join->on('hs.codnom', '=', 'c.codnom')->on('hs.codconc', '=', 'c.codconc');
            })
            ->select(
                'hs.codconc as codcon',
                'c.nomcon',
                DB::raw('SUM(hs.valsal) as valcalcur')
            )
            ->where([['hs.codnom', '=', $v_codnom], ['hs.codperi', '=', $v_codperi], ['hs.codper', '=', $v_codper]])
            ->whereIn('hs.tipsal', ['A', 'D', 'P1', 'P2'])
            ->groupBy('hs.codconc', 'c.nomcon')
            ->get();

        // 3. Clasificación con ArcService
        $asigConfiguradas = $arcService->getAsignacionesConfiguradas();
        $config = DB::table('arc_parametros')->where('anio', $anioActual)->first();
        $mapa = json_decode($config->clasificacion ?? '{}')->mapa ?? (object)[];
        $retConfiguradas = collect($mapa->deducciones ?? [])->map(fn($c) => ltrim(trim($c), '0'))->toArray();

        $asignaciones = $movimientos->filter(function($m) use ($asigConfiguradas) {
            $cod = ltrim(trim($m->codcon), '0');
            return in_array($cod, $asigConfiguradas) || $m->valcalcur > 0;
        })->filter(fn($m) => $m->valcalcur > 0);

        $deducciones = $movimientos->filter(function($m) use ($retConfiguradas) {
            $cod = ltrim(trim($m->codcon), '0');
            return in_array($cod, $retConfiguradas) || $m->valcalcur < 0;
        })->filter(fn($m) => $m->valcalcur < 0);

        $totalAsignaciones = $asignaciones->sum('valcalcur');
        $totalDeducciones = $deducciones->sum('valcalcur');

        // --- LÓGICA DE LOGO DINÁMICO (BLINDADA) ---
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
        // ------------------------------------------

        // 4. Auditoría
        DB::table('reporte_descargas')->insert([
            'cedula' => $resumen->cedper,
            'nombre_empleado' => strtoupper($resumen->nomper . ' ' . $resumen->apeper),
            'tipo_reporte' => 'Recibo de Pago',
            'detalles' => "Neto: " . number_format($resumen->neto_cobrar, 2, ',', '.'),
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $data = [
            'resumen' => $resumen,
            'user' => $user,
            'asignaciones' => $asignaciones,
            'deducciones' => $deducciones,
            'totalAsignaciones' => $totalAsignaciones,
            'totalDeducciones' => $totalDeducciones,
            'logoFona' => $logoInstitucion,
            'logoRepublica' => public_path('images/logo_ministerio.png'),
            'fecha' => date('d/m/Y')
        ];

        return \Barryvdh\DomPDF\Facade\Pdf::loadView('empleado.reportes.recibo_pdf', $data)
            ->setPaper('letter', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => true,
                'chroot'               => [public_path(), storage_path('app/public')],
            ])
            ->download(strtoupper("Recibo_{$resumen->cedper}_P{$v_codperi}.pdf"));

    } catch (\Exception $e) {
        if (ob_get_length()) ob_end_clean();
        Log::error("Error en Recibo PDF: " . $e->getMessage());
        return back()->with('error', "Error al generar el recibo.");
    }
}

// --- SOLUCIÓN ERROR METHOD DOES NOT EXIST ---
private function cargarLogo($nombreArchivo)
{
    $path = public_path('images/' . $nombreArchivo);
    if (!file_exists($path)) return '';
    $type = pathinfo($path, PATHINFO_EXTENSION);
    $data = file_get_contents($path);
    return 'data:image/' . $type . ';base64,' . base64_encode($data);
}
}
