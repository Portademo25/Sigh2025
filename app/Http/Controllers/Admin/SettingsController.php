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
use Illuminate\Support\Facades\Auth;
use App\Models\SnoPersonal;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Exception;
use Illuminate\Support\Facades\Storage;


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
    // 1. CONFIGURACIÓN DINÁMICA DE CONEXIÓN
    // Obtenemos los valores desde la tabla settings
    $config = DB::table('settings')->pluck('value', 'key');

    // Validamos que existan los datos mínimos para conectar
    if (!isset($config['db_sigesp_host'], $config['db_sigesp_name'], $config['db_sigesp_user'])) {
        return redirect()->back()->with('error', 'Faltan parámetros de configuración de SIGESP.');
    }

    try {
        // Configuramos la conexión 'sigesp' al vuelo antes de usarla
        config(['database.connections.sigesp' => [
            'driver'   => 'pgsql',
            'host'     => $config['db_sigesp_host'],
            'port'     => $config['db_sigesp_port'] ?? '5432',
            'database' => $config['db_sigesp_name'],
            'username' => $config['db_sigesp_user'],
            // Desencriptamos la contraseña guardada
            'password' => isset($config['db_sigesp_pass']) ? decrypt($config['db_sigesp_pass']) : '',
            'charset'  => 'utf8',
            'prefix'   => '',
            'schema'   => 'public',
            'sslmode'  => 'prefer',
        ]]);

        // Limpiar cualquier conexión previa para asegurar que use la nueva configuración
        DB::purge('sigesp');

        // --- INICIO DE LÓGICA DE SINCRONIZACIÓN ---
        set_time_limit(0);
        ini_set('memory_limit', '1024M');

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
        Log::error("Error en sincronización SIGESP: " . $e->getMessage());
        return redirect()->back()->with('error', 'Error en la conexión o proceso: ' . $e->getMessage());
    }
}
    public function sigesp()
    {
        // Obtenemos las configuraciones específicas de SIGESP
      $lastSync = \App\Models\Setting::where('key', 'sigesp_last_sync')->value('value') ?? 'Nunca';

         return view('admin.settings.sigesp', compact('lastSync'));
    }

    public function editMailSettings()
{
    // Obtenemos la configuración actual (si existe)
    $mail = DB::table('mail_settings')->first();
    return view('admin.settings.mail', compact('mail'));
}

public function updateMailSettings(Request $request)
{
    $request->validate([
        'host' => 'required|string',
        'port' => 'required|numeric',
        'username' => 'required|string',
        'password' => 'required|string',
        'encryption' => 'required|in:tls,ssl',
        'from_address' => 'required|email',
        'from_name' => 'required|string',
    ]);

    $data = $request->only([
        'host', 'port', 'username', 'password',
        'encryption', 'from_address', 'from_name'
    ]);

    // Usamos updateOrInsert para asegurar que siempre sea el registro ID 1
    DB::table('mail_settings')->updateOrInsert(['id' => 1], $data);

    return back()->with('success', 'Servidor de correo configurado correctamente.');
}
   public function testMailSettings(Request $request)
{
    try {
        // 1. Forzamos la carga de la configuración guardada en DB
        $mail = DB::table('mail_settings')->first();

        if (!$mail) {
            return back()->withErrors(['test' => 'No hay configuración guardada para probar.']);
        }

        // 2. Aplicamos la configuración dinámicamente solo para este envío
        config([
            'mail.mailers.smtp.host' => $mail->host,
            'mail.mailers.smtp.port' => $mail->port,
            'mail.mailers.smtp.encryption' => $mail->encryption,
            'mail.mailers.smtp.username' => $mail->username,
            'mail.mailers.smtp.password' => $mail->password,
            'mail.from.address' => $mail->from_address,
            'mail.from.name' => $mail->from_name,
        ]);

        $userEmail = Auth::user()->email;

        Mail::raw('Prueba de conexión exitosa desde el Panel Administrativo del Sistema de Nómina.', function ($message) use ($userEmail, $mail) {
            $message->to($userEmail)
                    ->subject('Prueba de Servidor SMTP - ' . $mail->from_name);
        });

        return back()->with('success', '¡Conexión exitosa! Revisa la bandeja de entrada de: ' . $userEmail);

    } catch (Exception $e) {
        // Esto atrapará errores de "Authentication Failed", "Connection Timeout", etc.
        return back()->withErrors(['test' => 'Error de conexión: ' . $e->getMessage()]);
    }
}

public function securityIndex()
    {
        // Esta ruta corresponde a: /admin/security
      $config = DB::table('settings')->pluck('value', 'key');
        return view('admin.security.index', compact('config'));
    }


    public function policiesIndex()
    {
        // Obtenemos los valores actuales de la tabla settings (key-value)
        $config = DB::table('settings')->pluck('value', 'key');

        // Esta ruta corresponde a: /admin/security/policies
        return view('admin.security.policies', compact('config'));
    }

    /**
     * Procesa la actualización de las políticas
     */

    public function handleSecurityAction(Request $request)
    {
        $action = $request->input('action');

        switch ($action) {
            case 'unlock_all_users':
                User::where('is_locked', true)->update(['is_locked' => false]);
                return back()->with('success', 'Todos los usuarios han sido desbloqueados.');

            case 'clear_login_attempts':
                // Limpiamos los intentos de login para todos los usuarios
                \Illuminate\Support\Facades\RateLimiter::clear();
                return back()->with('success', 'Los intentos de inicio de sesión han sido reiniciados para todos los usuarios.');

            default:
                return back()->withErrors(['action' => 'Acción no válida.']);
        }
    }


    // app/Http/Controllers/Admin/SettingsController.php

   public function updateSecurityPolicies(Request $request)
{
    // 1. Validar los datos (incluyendo los archivos)
    $request->validate([
        'intentos_maximos' => 'required|integer',
        'duracion_bloqueo' => 'required|integer',
        'expiracion_sesion' => 'required|integer',
        'ssl_certificate' => 'nullable|file|mimes:crt,pem,txt,cer',
        'ssl_key' => 'nullable|file|mimes:key,pem,txt',
    ]);

    // 2. Guardar configuraciones de texto
    $data = $request->only(['intentos_maximos', 'duracion_bloqueo', 'expiracion_sesion']);
    foreach ($data as $key => $value) {
        DB::table('settings')->updateOrInsert(
            ['key' => $key],
            ['value' => $value, 'updated_at' => now()]
        );
    }

    // 3. Manejo del Certificado (.crt)
    if ($request->hasFile('ssl_certificate')) {
        // Guardar en storage/app/ssl (no accesible desde la web)
        $certPath = $request->file('ssl_certificate')->store('ssl');

        DB::table('settings')->updateOrInsert(
            ['key' => 'ssl_certificate_path'],
            ['value' => $certPath, 'updated_at' => now()]
        );
    }

    // 4. Manejo de la Llave Privada (.key)
    if ($request->hasFile('ssl_key')) {
        $keyPath = $request->file('ssl_key')->store('ssl');

        DB::table('settings')->updateOrInsert(
            ['key' => 'ssl_key_path'],
            ['value' => $keyPath, 'updated_at' => now()]
        );
    }

    return back()->with('success', 'Políticas y archivos de seguridad actualizados correctamente.');
}

public function toggleMaintenance()
{
    // 1. Obtener el estado actual
    $currentStatus = DB::table('settings')->where('key', 'site_offline')->value('value') ?? '0';

    // 2. Invertir el estado
    $newStatus = ($currentStatus == '1') ? '0' : '1';

    // 3. Guardar en la base de datos
   DB::table('settings')->updateOrInsert(
        ['key' => 'site_offline'],
        ['value' => $newStatus, 'updated_at' => now()]
    );

    $mensaje = ($newStatus == '1') ? 'El sistema ahora está en mantenimiento.' : 'El sistema vuelve a estar en línea.';

    return back()->with('success', $mensaje);
}


public function generalIndex()
{
    // Usamos pluck para obtener un array clave => valor
    $config = DB::table('settings')->pluck('value', 'key')->toArray();

    // Aseguramos que si no existe la llave 'monto_cestaticket', pase un valor por defecto
    if (!isset($config['monto_cestaticket'])) {
        $config['monto_cestaticket'] = '0.00';
    }

    return view('admin.settings.general', compact('config'));
}

public function updateGeneral(Request $request)
{
    // 1. Validación estricta (Coincide con las reglas de tu JS)
    $rules = [
        'institucion_nombre'    => 'required|string|max:255',
        'institucion_rif'       => ['required', 'string', 'regex:/^[VGJ]-[0-9]{8}-[0-9]$/i'],
        'institucion_siglas'    => 'required|string|max:50',
        'institucion_direccion' => 'nullable|string|max:1000',
        'monto_cestaticket'     => 'required|numeric|min:0',
        
        // Validaciones para bases de datos
        'db_local_host'         => 'required|string',
        'db_local_name'         => 'required|string',
        'db_local_user'         => 'required|string',
        'db_sigesp_host'        => 'required|string',
        'db_sigesp_port'        => 'required|numeric',
        'db_sigesp_name'        => 'required|string',
        'db_sigesp_user'        => 'required|string',
        
        // Logo
        'logo_archivo'          => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
    ];

    $request->validate($rules);

    try {
        DB::beginTransaction();

        // 2. Lista de campos de texto simple
        $fields = [
            'institucion_nombre', 'institucion_rif', 'institucion_siglas', 
            'institucion_direccion', 'monto_cestaticket',
            'db_local_host', 'db_local_name', 'db_local_user',
            'db_sigesp_host', 'db_sigesp_port', 'db_sigesp_name', 'db_sigesp_user'
        ];

        foreach ($fields as $field) {
            DB::table('settings')->updateOrInsert(
                ['key' => $field],
                ['value' => $request->get($field), 'updated_at' => now()]
            );
        }

        // 3. Manejo de contraseñas (Solo si el usuario escribió algo)
        // Usamos encrypt() para que no viajen en texto plano en la BD
        if ($request->filled('db_local_pass')) {
            DB::table('settings')->updateOrInsert(
                ['key' => 'db_local_pass'],
                ['value' => encrypt($request->db_local_pass), 'updated_at' => now()]
            );
        }

        if ($request->filled('db_sigesp_pass')) {
            DB::table('settings')->updateOrInsert(
                ['key' => 'db_sigesp_pass'],
                ['value' => encrypt($request->db_sigesp_pass), 'updated_at' => now()]
            );
        }

        // 4. Manejo Inteligente de Logo
        if ($request->hasFile('logo_archivo')) {
            // Obtener el path del logo anterior para borrarlo
            $oldPath = DB::table('settings')->where('key', 'logo_path')->value('value');
            
            if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }

            // Guardar nuevo logo en storage/app/public/branding
            $newPath = $request->file('logo_archivo')->store('branding', 'public');
            
            DB::table('settings')->updateOrInsert(
                ['key' => 'logo_path'],
                ['value' => $newPath, 'updated_at' => now()]
            );
        }

        DB::commit();
        return back()->with('success', 'Configuración institucional actualizada con éxito.');

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error("Error al actualizar configuración general: " . $e->getMessage());
        return back()->withErrors('Error crítico: ' . $e->getMessage());
    }
}

// Método para probar SIGESP
public function testSigespConnection(Request $request)
{
    try {
        // Mismo proceso: si no escribe contraseña nueva, usamos la cifrada de la BD
        $password = $request->db_sigesp_pass;
        if (empty($password)) {
            $encrypted = DB::table('settings')->where('key', 'db_sigesp_pass')->value('value');
            $password = $encrypted ? decrypt($encrypted) : '';
        }

        config(['database.connections.temp_sigesp' => [
            'driver'   => 'pgsql', 
            'host'     => $request->db_sigesp_host,
            'port'     => $request->db_sigesp_port ?? '5432',
            'database' => $request->db_sigesp_name,
            'username' => $request->db_sigesp_user,
            'password' => $password,
            'charset'  => 'utf8',
            'connect_timeout' => 5,
            'sslmode'  => 'prefer',
        ]]);

        DB::connection('temp_sigesp')->getPdo();

        return response()->json(['success' => true, 'message' => '¡Conexión SIGESP Exitosa!']);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => 'Error SIGESP: ' . $e->getMessage()]);
    }
}


public function updateEmail(Request $request, User $user)
{
    try {
        $request->validate([
            'email' => 'required|email|unique:users,email,' . $user->id
        ]);

        $user->update(['email' => $request->email]);

        return response()->json([
            'success' => true,
            'message' => 'Correo actualizado correctamente.'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ], 422);
    }
}

public function fetchUsers(Request $request)
{
    $search = $request->input('search');

    $users = \App\Models\User::when($search, function ($query, $search) {
        return $query->where('cedula', 'like', "%{$search}%")
                     ->orWhere('name', 'like', "%{$search}%")
                     ->orWhere('email', 'like', "%{$search}%");
    })
    ->orderBy('name', 'asc')
    ->paginate(10);

    // IMPORTANTE: Verifica que este archivo exista en:
    // resources/views/admin/settings/partials/users_table.blade.php
    return view('admin.settings.partials.users_table', compact('users'))->render();
}
public function updateCestaTicket(Request $request)
{
    $request->validate([
        'monto' => 'required|numeric|min:0'
    ]);

    DB::table('settings')->updateOrInsert(
        ['key' => 'monto_cestaticket'],
        ['value' => $request->monto, 'updated_at' => now()]
    );

    return back()->with('success', 'Monto de Cestaticket actualizado correctamente.');
}

public function testLocalConnection(Request $request)
{
    try {
        // Si el usuario no escribió una contraseña nueva en el input, 
        // buscamos la que ya tenemos cifrada en la base de datos.
        $password = $request->db_local_pass;
        if (empty($password)) {
            $encrypted = DB::table('settings')->where('key', 'db_local_pass')->value('value');
            $password = $encrypted ? decrypt($encrypted) : '';
        }

        // Configuramos una conexión temporal al vuelo
        config(['database.connections.temp_local' => [
            'driver'   => 'pgsql',
            'host'     => $request->db_local_host,
            'port'     => $request->db_local_port ?? '5432',
            'database' => $request->db_local_name,
            'username' => $request->db_local_user,
            'password' => $password,
            'charset'  => 'utf8',
            'connect_timeout' => 5, // No esperar más de 5 segundos
            'sslmode'  => 'prefer',
        ]]);

        // Intentamos obtener la instancia de PDO (el apretón de manos real)
        DB::connection('temp_local')->getPdo();

        return response()->json([
            'success' => true, 
            'message' => '¡Conexión Local Establecida con Éxito!'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false, 
            'message' => 'Error de conexión local: ' . $e->getMessage()
        ]);
    }
}
}
