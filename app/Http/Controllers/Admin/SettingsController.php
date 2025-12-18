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
    set_time_limit(600); // 10 minutos

    try {
        // 1. Obtenemos la data de SIGESP
        $personalSigesp = DB::connection('sigesp')
            ->table('public.sno_personal')
            ->select('codemp', 'codper', 'cedper', 'nomper', 'apeper', 'coreleper', 'fecingper')
            ->get();

        if ($personalSigesp->isEmpty()) {
            return redirect()->back()->with('error', 'No se encontraron datos en el servidor SIGESP.');
        }

        $contador = 0;

        // 2. Procesamos
        DB::transaction(function () use ($personalSigesp, &$contador) {
            foreach ($personalSigesp as $p) {
                // Usamos updateOrInsert: si existe por cédula, actualiza; si no, inserta.
                DB::table('sno_personal')->updateOrInsert(
                    ['cedper' => trim($p->cedper)], // Clave única de búsqueda
                    [
                        'codemp'     => trim($p->codemp),
                        'codper'     => trim($p->codper),
                        'nomper'     => trim($p->nomper),
                        'apeper'     => trim($p->apeper),
                        'coreleper'  => trim($p->coreleper),
                        'fecingper'  => ($p->fecingper && $p->fecingper != '1900-01-01') ? $p->fecingper : null,
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
                $contador++;
            }
        });

        return redirect()->back()->with('success', "Sincronización exitosa. Se procesaron $contador trabajadores.");

    } catch (\Exception $e) {
        // Si falla, nos dirá exactamente en qué columna o por qué
        return redirect()->back()->with('error', 'Error crítico: ' . $e->getMessage());
    }
}


    public function sigesp()
    {
        // Obtenemos las configuraciones específicas de SIGESP
      $lastSync = \App\Models\Setting::where('key', 'sigesp_last_sync')->value('value') ?? 'Nunca';

         return view('admin.settings.sigesp', compact('lastSync'));
    }
}
