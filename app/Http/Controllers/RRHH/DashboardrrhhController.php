<?php

namespace App\Http\Controllers\RRHH;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class DashboardrrhhController extends Controller
{
    /**
     * Función principal que carga el Panel de Control.
     */
    public function index()
    {
        // 1. Obtener cumpleañeros
        $cumpleaneros = $this->consultarCumpleanerosSigesp();

        // 2. Obtener configuración de nóminas
        $anioActual = date('Y');
        $config = DB::table('arc_parametros')->where('anio', $anioActual)->first();

        // Limpiamos las nóminas para que coincidan con SIGESP (quitar ceros o nombres extras)
        $nominasHabilitadas = [];
        if ($config) {
            $nominasRaw = json_decode($config->nominas, true) ?? [];
            $nominasHabilitadas = collect($nominasRaw)->map(fn($n) => trim(explode('|', $n)[0]))->toArray();
        }

        // 3. Estadísticas: Filtro dinámico basado en la configuración
        $stats = DB::connection('sigesp')->table('sno_personal as p')
            ->join('sno_personalnomina as n', 'p.codper', '=', 'n.codper')
            ->select(DB::raw("
                COUNT(DISTINCT CASE
                    WHEN TRIM(p.estper) = '1'
                    AND n.staper = '1' " .
                    (!empty($nominasHabilitadas) ? "AND n.codnom IN ('" . implode("','", $nominasHabilitadas) . "')" : "") . "
                    THEN p.cedper END) as activos,

                COUNT(DISTINCT CASE
                    WHEN TRIM(p.estper) = '3'
                    THEN p.cedper END) as egresados,

                COUNT(DISTINCT p.cedper) as total_en_sistema
            "))
            ->first();

        // 4. Cestaticket (Local)
        $montoCestaticket = DB::table('settings')
            ->where('key', 'monto_cestaticket')
            ->value('value') ?? 0;

        // 5. Últimos Ingresos (Limitado a 4 para el diseño del dashboard)
        $queryIngresos = DB::connection('sigesp')->table('sno_personal as p')
            ->join('sno_personalnomina as n', 'p.codper', '=', 'n.codper')
            ->select('p.nomper', 'p.apeper', 'p.cedper', 'n.fecingper')
            ->where('n.staper', '1');

        if (!empty($nominasHabilitadas)) {
            $queryIngresos->whereIn('n.codnom', $nominasHabilitadas);
        }

        $ultimosIngresos = $queryIngresos->orderBy('n.fecingper', 'desc')
            ->limit(4)
            ->get();

        return view('rrhh.dashboard', compact(
            'stats',
            'montoCestaticket',
            'ultimosIngresos',
            'cumpleaneros'
        ));
    }

    /**
     * Función PRIVADA para obtener los datos de SIGESP para el widget.
     */
    private function consultarCumpleanerosSigesp()
    {
        try {
            return DB::connection('sigesp')->table('sno_personal as p')
                ->join('sno_personalnomina as pn', 'p.codper', '=', 'pn.codper')
                ->select(
                    'p.nomper',
                    'p.apeper',
                    'p.cedper',
                    'p.fecnacper',
                    DB::raw("EXTRACT(DAY FROM p.fecnacper) as dia")
                )
                ->whereMonth('p.fecnacper', date('m'))
                ->where('pn.staper', '1')
                ->distinct()
                ->orderBy(DB::raw("EXTRACT(DAY FROM p.fecnacper)"), 'ASC')
                ->get();
        } catch (\Exception $e) {
            Log::error("Error en cumpleañeros SIGESP: " . $e->getMessage());
            return collect();
        }
    }

    /**
     * Disparar el correo de prueba a la Directora.
     */
    public function dispararPruebaCumpleaneros()
    {
        $meses = [
            '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril',
            '05' => 'Mayo', '06' => 'Junio', '07' => 'Julio', '08' => 'Agosto',
            '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre'
        ];

        $mesActualId = date('m');
        $nombreMes = $meses[$mesActualId];

        $cumpleaneros = $this->consultarCumpleanerosSigesp();

        if ($cumpleaneros->isEmpty()) {
            return back()->with('info', "No se encontraron cumpleañeros para el mes de $nombreMes.");
        }

        $data = [
            'mesActual' => $nombreMes,
            'cumpleaneros' => $cumpleaneros,
            'logoFona' => $this->cargarLogo('logo_fona.png')
        ];

        try {
            $emailDirectora = 'directora_rrhh@fona.gob.ve';

            Mail::send('emails.cumpleaneros', $data, function($message) use ($nombreMes, $emailDirectora) {
                $message->to($emailDirectora)
                        ->subject("🎂 Reporte Mensual de Cumpleañeros - $nombreMes");
            });

            return back()->with('success', "Prueba disparada exitosamente hacia $emailDirectora");

        } catch (\Exception $e) {
            Log::error("Error enviando correo de cumpleañeros: " . $e->getMessage());
            return back()->with('error', "Error al disparar la prueba: " . $e->getMessage());
        }
    }

    /**
     * REQUISITO CRÍTICO: Método para procesar el logo en Base64 para el correo.
     */
    private function cargarLogo($nombreArchivo)
    {
        $path = public_path('images/' . $nombreArchivo);
        if (!file_exists($path)) {
            return '';
        }
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        return 'data:image/' . $type . ';base64,' . base64_encode($data);
    }
}
