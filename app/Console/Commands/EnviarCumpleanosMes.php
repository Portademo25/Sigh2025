<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\CumpleanerosMesMail;

class EnviarCumpleanosMes extends Command
{
    protected $signature = 'rrhh:enviar-cumpleanos';
    protected $description = 'Envía el listado de cumpleañeros del mes a la Dirección de RRHH usando correo institucional';

    public function handle()
    {
        $this->info('Iniciando proceso de envío de cumpleañeros...');
        $mesActual = date('m');

        // 1. Obtener cumpleañeros (Filtramos personal activo y mes actual)
        $cumpleaneros = DB::connection('sigesp')->table('sno_personal as p')
            ->join('sno_personalnomina as pn', 'p.codper', '=', 'pn.codper')
            ->select(
                'p.nomper',
                'p.apeper',
                'p.fecnacper',
                'p.cedper',
                DB::raw("EXTRACT(DAY FROM p.fecnacper) as dia")
            )
            ->whereMonth('p.fecnacper', $mesActual)
            ->where('pn.staper', '1') // Personal activo en nómina
            ->distinct()
            ->orderBy(DB::raw("EXTRACT(DAY FROM p.fecnacper)"), 'ASC')
            ->get();

        if ($cumpleaneros->isEmpty()) {
            $this->warn('No se encontraron cumpleañeros para el mes actual.');
            return;
        }

        // 2. BUSCAR A LA AUTORIDAD DE GESTIÓN HUMANA
        // Usamos 'coreleins' para obtener el correo institucional del FONA
        $director = DB::connection('sigesp')->table('sno_personal as p')
            ->join('sno_personalnomina as pn', 'p.codper', '=', 'pn.codper')
            ->join('sno_cargo as c', function($join) {
                $join->on('pn.codnom', '=', 'c.codnom')
                     ->on('pn.codcar', '=', 'c.codcar');
            })
            ->join('sno_unidadadmin as u', function($join) {
                $join->on('pn.minorguniadm', '=', 'u.minorguniadm')
                     ->on('pn.uniuniadm', '=', 'u.uniuniadm')
                     ->on('pn.depuniadm', '=', 'u.depuniadm')
                     ->on('pn.prouniadm', '=', 'u.prouniadm');
            })
            ->select('p.coreleins as email', 'p.nomper', 'p.apeper')
            ->where('pn.staper', '1')
            ->whereRaw("TRIM(u.prouniadm) = '04'") // Gestión Humana (usamos TRIM por seguridad)
            ->where(function($query) {
                $query->where('c.descar', 'ILIKE', '%DIRECTOR%')
                      ->orWhere('c.descar', 'ILIKE', '%DIRECTORA%')
                      ->orWhere('c.descar', 'ILIKE', '%JEFE%');
            })
            ->first();

        // 3. Determinar destino y Enviar Correo
        $emailDestino = ($director && !empty($director->email)) ? $director->email : 'gestionhumana@fona.gob.ve';

        try {
            Mail::to($emailDestino)->send(new CumpleanerosMesMail($cumpleaneros));

            if ($director && !empty($director->email)) {
                $this->info("Reporte enviado exitosamente a la autoridad: {$director->nomper} {$director->apeper} ({$emailDestino})");
            } else {
                $this->warn("No se encontró correo institucional de la autoridad en SIGESP. Enviado al respaldo: {$emailDestino}");
            }

        } catch (\Exception $e) {
            Log::error("Error enviando correo automático de cumpleañeros: " . $e->getMessage());
            $this->error("Error crítico al intentar enviar el correo. Revisa los logs.");
        }
    }
}
