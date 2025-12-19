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
use App\Models\SigespEmpresa;
use App\Models\SnoNomina;
use App\Models\SnoPersonal;
use App\Models\SnoHperiodo;
use App\Models\SnoHpersonalnomina;


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
    set_time_limit(1200);
       $periodoSigesp = DB::connection('sigesp')
        ->table('sno_periodo')
        ->select('codnom', 'codper', 'cerper', 'conper')
        ->where('peract', 1)
        ->first();

    if (!$periodoSigesp) {
        return back()->with('error', 'Error: No se encontró un periodo activo en SIGESP.');
    }

    // 2. Validar si está CERRADA (cerper = 1) y CONTABILIZADA (conper = 1)
    // Nota: En SIGESP, 0 = Abierto, 1 = Cerrado/Sí
    if ($periodoSigesp->cerper != 1) {
        return back()->with('error', "La nómina [{$periodoSigesp->codnom}] periodo [{$periodoSigesp->codper}] aún está ABIERTA en SIGESP.");
    }

    if ($periodoSigesp->conper != 1) {
        return back()->with('error', "La nómina [{$periodoSigesp->codnom}] está cerrada pero aún NO ha sido CONTABILIZADA.");
    }

    try {
        // 1. OBTENER COLUMNAS REALES DE SIGESP (Autodetección)
        $columnasReales = DB::connection('sigesp')
            ->getSchemaBuilder()
            ->getColumnListing('sigesp_empresa');

        // Función auxiliar para buscar el nombre correcto de la columna
        $findCol = function($options) use ($columnasReales) {
            foreach ($options as $opt) {
                if (in_array($opt, $columnasReales)) return $opt;
            }
            return null;
        };

        // Mapeamos dinámicamente según lo que exista en SIGESP
        $colNombre = $findCol(['nomemp', 'nombemp', 'nombre']);
        $colRif    = $findCol(['rifemp', 'rif']);
        $colDir    = $findCol(['diremp', 'diratemp', 'dirlibemp', 'direccion']);
        $colTel    = $findCol(['telemp', 'telefono']);

        // Extraemos solo las columnas que encontramos
        $queryCols = array_filter(['codemp', $colNombre, $colRif, $colDir, $colTel]);

        $empresaSigesp = DB::connection('sigesp')
            ->table("public.sigesp_empresa")
            ->select($queryCols)
            ->get();

        foreach ($empresaSigesp as $row) {
            SigespEmpresa::updateOrCreate(
                ['codemp' => trim($row->codemp)],
                [
                    'nombre'    => trim($row->$colNombre ?? 'Empresa S.A'),
                    'rif'       => trim($row->$colRif ?? ''),
                    'dirlibemp' => trim($row->$colDir ?? ''),
                    'telemp'    => trim($row->$colTel ?? ''),
                ]
            );
        }

        // 2. RESTO DE TABLAS (Normalmente estándar)
       // ... dentro de tu try-catch ...

       $config = [
    [
        'model' => new SnoNomina(),
        'pk' => 'codnom',
        'columns' => ['codemp', 'codnom', 'desnom', 'tipnom']
    ],
    [
        'model' => new SnoPersonal(),
        'pk' => 'cedper',
        'columns' => ['codemp', 'codper', 'cedper', 'nomper', 'apeper', 'coreleper', 'fecingper', 'codger']
    ],
    [
        'model' => new SnoHperiodo(),
        'pk' => 'codperi',
        // ELIMINAMOS 'cerperi' de aquí, dejamos solo el que SIGESP sí tiene
        'columns' => ['codemp', 'codnom', 'codperi', 'fecdesper', 'fechasper', 'cerper']
    ],
    [
        'model' => new SnoHpersonalnomina(),
        'pk' => 'codper',
        // ELIMINAMOS 'codque' de aquí si SIGESP usa 'codage'
        'columns' => ['codemp', 'codnom', 'codper', 'codage', 'codasicar']
    ],
];

      foreach ($config as $item) {
    $model = $item['model'];
    $tablaNombre = $model->getTable();

    // Intentamos obtener las columnas reales, pero si falla, usamos las del config
    $columnasExistentesEnSigesp = DB::connection('sigesp')->getSchemaBuilder()->getColumnListing($tablaNombre);

    // Si SIGESP devuelve columnas, intersectamos. Si no, usamos las que definimos por defecto.
    $colsToSelect = !empty($columnasExistentesEnSigesp)
        ? array_intersect($item['columns'], $columnasExistentesEnSigesp)
        : $item['columns'];

    // SEGURIDAD: Si por alguna razón codemp no quedó en la lista, lo agregamos a la fuerza
    if (!in_array('codemp', $colsToSelect)) {
        $colsToSelect[] = 'codemp';
    }

    $dataRaw = DB::connection('sigesp')
        ->table("public.$tablaNombre")
        ->select($colsToSelect)
        ->get();

    $dataToInsert = [];

    foreach ($dataRaw as $row) {
        $payload = [];
        $hasData = false;

        foreach ($colsToSelect as $col) {
            $targetCol = match($col) {
                'codage'  => 'codque',
                'cerper'  => 'cerperi',
                default   => $col
            };

            $valor = $row->$col;
            $payload[$targetCol] = is_string($valor) ? trim($valor) : $valor;

            // Verificamos que realmente estemos trayendo datos aparte de codemp
            if (!empty($payload[$targetCol]) && $targetCol !== 'codemp') {
                $hasData = true;
            }
        }

        // Solo agregamos al insert si la fila tiene contenido real
        if (!empty($payload['codemp'])) {
            $payload['created_at'] = now();
            $payload['updated_at'] = now();
            $dataToInsert[] = $payload;
        }
    }

    if (!empty($dataToInsert)) {
        // Insertamos en bloques para mayor seguridad
        foreach (array_chunk($dataToInsert, 500) as $chunk) {
            DB::table($tablaNombre)->insertOrIgnore($chunk);
        }
    }
}

        \App\Models\Setting::updateOrCreate(['key' => 'sigesp_last_sync'], ['value' => now()->format('d/m/Y h:i A')]);

        return redirect()->back()->with('success', 'Sincronización masiva completada con éxito.');

    } catch (\Exception $e) {
        // Si falla, el error nos dirá ahora cuál de las otras tablas es la del problema
        dd("ERROR TÉCNICO EN TABLA: " . ($tablaNombre ?? 'Empresa'), $e->getMessage());
    }
}


   public function sigesp()
    {
        // 1. Obtenemos la última fecha de sincronización
        $lastSync = Setting::where('key', 'sigesp_last_sync')->value('value') ?? 'Nunca';

        // 2. Generamos el estado de las tablas locales para la vista
        $status = [
            ['nombre' => 'Empresa', 'tabla' => 'sigesp_empresa', 'total' => SigespEmpresa::count()],
            ['nombre' => 'Nóminas', 'tabla' => 'sno_nomina', 'total' => SnoNomina::count()],
            ['nombre' => 'Personal', 'tabla' => 'sno_personal', 'total' => SnoPersonal::count()],
            ['nombre' => 'Historial Periodos', 'tabla' => 'sno_hperiodo', 'total' => SnoHperiodo::count()],
            ['nombre' => 'Historial Nómina', 'tabla' => 'sno_hpersonalnomina', 'total' => SnoHpersonalnomina::count()],
        ];

        // 3. Pasamos ambas variables a la vista
        return view('admin.settings.sigesp', compact('lastSync', 'status'));
    }


}
