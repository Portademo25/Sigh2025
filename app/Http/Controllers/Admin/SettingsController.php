<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\User; // Asegúrate de tener tu modelo de usuarios
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use App\Models\SnoPersonal;
use Illuminate\Support\Facades\Log;


class SettingsController extends Controller
{
    public function index()
    {
        // Obtenemos todas las configuraciones y las convertimos en un array clave => valor
        $settings = Setting::pluck('value', 'key')->all();

        return view('admin.settings.index', compact('settings'));
    }

  public function update(Request $request)
{
    // 1. Validamos los campos de texto
    $data = $request->validate([
        'max_attempts'     => 'required|integer',
        'active_threshold' => 'required|integer',
        'app_name'         => 'required|string|max:255',
        'support_email'    => 'required|email',
    ]);

    // 2. Manejamos el site_offline (Checkbox)
    // Si el checkbox viene en el request, guardamos '1', de lo contrario '0'
    $data['site_offline'] = $request->has('site_offline') ? '1' : '0';

    // 3. Guardamos/Actualizamos en la base de datos
    foreach ($data as $key => $value) {
        \App\Models\Setting::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    // 4. Limpiamos caché para que el cambio de nombre de app sea inmediato
    \Illuminate\Support\Facades\Artisan::call('config:clear');
    Setting::updateOrCreate(['key' => 'app_name'], ['value' => $request->app_name]);
    return redirect()->back()->with('success', 'Configuraciones actualizadas con éxito.');
}

public function syncSigesp(Request $request)
{
    set_time_limit(0);
    ini_set('memory_limit', '1024M');

    try {
        $reporte = [];

        // 1. EMPRESAS
        $empresas = DB::connection('sigesp')->table('public.sigesp_empresa')->get();
        foreach ($empresas as $e) {
            DB::table('sigesp_empresa')->updateOrInsert(
                ['codemp' => trim($e->codemp)],
                ['nombre' => trim($e->nombre ?? $e->nomemp), 'rif' => trim($e->rif ?? $e->rifemp), 'updated_at' => now()]
            );
        }
        $reporte[] = count($empresas) . " empresas";

        // 2. PERSONAL
        $totalPersonal = 0;
        DB::connection('sigesp')->table('public.sno_personal')->orderBy('codper', 'asc')
            ->chunk(500, function ($personal) use (&$totalPersonal) {
                foreach ($personal as $p) {
                    DB::table('sno_personal')->updateOrInsert(
                        ['codemp' => trim($p->codemp), 'codper' => trim($p->codper)],
                        [
                            'cedper' => trim($p->cedper), 'nomper' => trim($p->nomper), 'apeper' => trim($p->apeper),
                            'coreleper' => trim($p->coreleper), 'fecingper' => ($p->fecingper != '1900-01-01' ? $p->fecingper : null),
                            'updated_at' => now()
                        ]
                    );
                    $totalPersonal++;
                }
            });
        $reporte[] = "$totalPersonal trabajadores";

        // 3. NÓMINAS
        $nominas = DB::connection('sigesp')->table('public.sno_nomina')->get();
        foreach ($nominas as $n) {
            DB::table('sno_nomina')->updateOrInsert(
                ['codemp' => trim($n->codemp), 'codnom' => trim($n->codnom)],
                ['desnom' => trim($n->desnom), 'updated_at' => now()]
            );
        }
        $reporte[] = count($nominas) . " nóminas";

        // 4. PERIODOS
        $periodos = DB::connection('sigesp')->table('public.sno_hperiodo')->select('codemp', 'codnom', 'codperi', 'fecdesper', 'fechasper', 'cerper')->get();
        foreach ($periodos as $per) {
            DB::table('sno_hperiodo')->updateOrInsert(
                ['codemp' => trim($per->codemp), 'codnom' => trim($per->codnom), 'codperid' => trim($per->codperi)],
                ['fecdesper' => $per->fecdesper, 'fechasper' => $per->fechasper, 'cerper' => $per->cerper, 'updated_at' => now()]
            );
        }
        $reporte[] = count($periodos) . " periodos";

        // 5. HISTORIAL PERSONAL NÓMINA
        $totalHPN = 0;
        DB::connection('sigesp')->table('public.sno_hpersonalnomina')->orderBy('codperi', 'desc')
            ->chunk(1000, function ($registros) use (&$totalHPN) {
                foreach ($registros as $reg) {
                    DB::table('sno_hpersonalnomina')->updateOrInsert(
                        [
                            'codemp' => trim($reg->codemp), 'codnom' => trim($reg->codnom),
                            'codperid' => trim($reg->codperi), 'codper' => trim($reg->codper)
                        ],
                        ['codtab' => trim($reg->codtab ?? '0000'), 'updated_at' => now()]
                    );
                    $totalHPN++;
                }
            });
        $reporte[] = "$totalHPN asignaciones";

        Setting::updateOrCreate(['key' => 'sigesp_last_sync'], ['value' => now()->format('d/m/Y h:i A')]);

        return redirect()->back()->with('success', "¡Sincronización Completa! " . implode(' | ', $reporte));

    } catch (\Exception $e) {
        Log::error("Error final: " . $e->getMessage());
        return redirect()->back()->with('error', 'Error en: ' . $e->getMessage());
    }
}
public function sigesp()
    {
        // Obtenemos las configuraciones específicas de SIGESP
      $lastSync = \App\Models\Setting::where('key', 'sigesp_last_sync')->value('value') ?? 'Nunca';

         return view('admin.settings.sigesp', compact('lastSync'));
    }
}
